<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();

/**
 * Step 3: Define the Slim application routes
 *
 * Here we define several Slim application routes that respond
 * to appropriate HTTP request methods. In this example, the second
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`
 * is an anonymous function.
 */

function connect_db() {
	$server = 'localhost'; // this may be an ip address instead
	$user = 'root';
	$pass = ''; // fill in with your database password
	$database = ''; //fill in with your database name
	$connection = new mysqli($server, $user, $pass, $database);

	return $connection;
}

$app->get('/', function ()  use ($app){
	$app->view()->setTemplatesDirectory('./view');
	$app->render('index.php');
});

$app->get('/ping', 'ping');

$app->get('/pingAll', 'pingAll');

$app->post('/register', function() use($app){
	$data = $app->request->getBody();
	$json = json_decode($data);
	$npm = $json->user_id;
	$nama = $json->nama;
	$ip = $json->ip_domisili;
	
	if (quorum() >= 5) {
		register($npm, $nama, $ip);	
	}
	
});

$app->get('/getSaldo/:npm', function($npm) {
	if (quorum() >= 5) {
		getSaldo($npm);	
	}
});

$app->get('/getTotalSaldo/:npm', function($npm) {
	if (quorum() >= 8) {
		getTotalSaldo($npm);	
	}
});

$app->post('/transfer', function() use($app) {
	$data = $app->request->getBody();
	$json = json_decode($data);
	$npm = $json->user_id;
	$jumlah_uang = $json->nilai;
	
	transfer($npm, $jumlah_uang);
	
});

$app->post('/calculation', function() use($app) {
	$data = $app->request->getBody();
	$json = json_decode($data);
	$npm = $json->user_id;
	$nilai = $json->nilai;
	$ip_tujuan = $json->ip_tujuan;

	if (quorum() >= 5) {
		doTransfer($npm, $nilai, $ip_tujuan);	
	}
});

function ping() {
	$app = \Slim\Slim::getInstance();
	$output = array();
	$output = array("pong" => 1);
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode($output);
}

function pingAll() {
	$app = \Slim\Slim::getInstance();
	$pong = 0;
	$endpoints = "";
	
	$condition = "All of end points are fine";
	$urls = array(
				"https://prayogo.sisdis.ui.ac.id/ewallet/ping",
				"https://izzunaqi.sisdis.ui.ac.id/ewallet/ping",
				"https://rahman.sisdis.ui.ac.id/ewallet/ping",
				"https://saprikan.sisdis.ui.ac.id/ewallet/ping",
				"https://aziz.sisdis.ui.ac.id/ewallet/ping/",
				"https://mardhika.sisdis.ui.ac.id/ewallet/ping",
				"https://atma.sisdis.ui.ac.id/ewallet/ping",
				"https://hadhi.sisdis.ui.ac.id/ewallet/ping"
			);

	foreach ($urls as $value) {
		$ch = curl_init($value);
		curl_setopt( $ch, CURLOPT_URL, $value);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		$json = json_decode($response, true);
		$pong += $json['pong'];
		curl_close($ch);

		if ($json['pong'] != 1) {
			$condition = "There are some sick end points";
			$endpoints = $endpoints . " " . $value;
		}
	}

	$output = array("pong" => $pong, "total_cabang" => $pong/sizeof($urls), "condition" => $condition, "sick_endpoint" => $endpoints);
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode($output);
}

function quorum() {
	$app = \Slim\Slim::getInstance();
	
	$pong = 0;
	$counter = 0;
	$kondisi = "All end points are fine";
	$urls = array(
				"https://prayogo.sisdis.ui.ac.id/ewallet/ping",
				"https://izzunaqi.sisdis.ui.ac.id/ewallet/ping",
				"https://rahman.sisdis.ui.ac.id/ewallet/ping",
				"https://saprikan.sisdis.ui.ac.id/ewallet/ping",
				"https://aziz.sisdis.ui.ac.id/ewallet/ping/",
				"https://mardhika.sisdis.ui.ac.id/ewallet/ping",
				"https://atma.sisdis.ui.ac.id/ewallet/ping",
				"https://hadhi.sisdis.ui.ac.id/ewallet/ping"
			);

	foreach ($urls as $value) {
		$ch = curl_init($value);
		curl_setopt( $ch, CURLOPT_URL, $value);
		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		$json = json_decode($response, true);
			
		$pong += $json['pong'];
		curl_close($ch);

		if ($json['pong'] != 1) {
			$kondisi = "There are some sick end points";
		}
	}

	$output = array("pong" => $pong, "condition" => $kondisi);
	$app->response()->headers->set('Content-Type', 'application/json');
	
	return $pong;
}

function register($npm, $nama, $ip) {
	$app = \Slim\Slim::getInstance();
	$output = array();
	$result = "";

	$conn = connect_db();
	$insert_query = "INSERT INTO nasabah (user_id, nama, saldo, ip_domisili) VALUES ('$npm', '$nama',500000, '$ip')";
	if (mysqli_query($conn, $insert_query)) {
		$output = array("success" => 1);
		$result = "User Registered";
	} else {
		$output = array("success" => 0);
		$result = "User Not Registered";
	}

	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode($output);
}

function getSaldo($npm) {
	$app = \Slim\Slim::getInstance();
	$output = array();

	$conn = connect_db();
	$query = "SELECT saldo from nasabah where user_id = $npm";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	$row_count = mysqli_num_rows($result);
	
	if ($row_count == 1) {
		$output = array("nilai_saldo" => $row['saldo']);
	} else {
		$output = array("nilai_saldo" => -1);
	}

	$app->response()->headers->set('Content-Type', 'application/json');

	echo json_encode($output);
	return $row['saldo'];
}

function getTotalSaldo($npm) {
	$app = \Slim\Slim::getInstance();
	$output = array();
	$saldo = 0;

	$conn = connect_db();
	$query = "SELECT * from nasabah where user_id = $npm";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	$saldo += $row['saldo'];

	$endpoint = array(
					"https://prayogo.sisdis.ui.ac.id/ewallet/getSaldo",
					"https://izzunaqi.sisdis.ui.ac.id/ewallet/getSaldo",
					"https://rahman.sisdis.ui.ac.id/ewallet/getSaldo",
					"https://saprikan.sisdis.ui.ac.id/ewallet/getSaldo",
					"https://aziz.sisdis.ui.ac.id/ewallet/getSaldo/",
					"https://mardhika.sisdis.ui.ac.id/ewallet/pigetSaldong",
					"https://atma.sisdis.ui.ac.id/ewallet/getSaldo",
					"https://hadhi.sisdis.ui.ac.id/ewallet/getSaldo"
				);

	foreach($endpoint as $url) {
		$url = $url . "/" . $npm;
		$ch = curl_init($url);
		curl_setopt( $ch, CURLOPT_URL, $url);
		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		$response_saldo = json_decode($response, true);
		
		$saldo_akun = $response_saldo['nilai_saldo'];
		if($saldo_akun >= 0) {
			$saldo += $saldo_akun;
		}
	}

	$output = array("nilai_saldo" => $saldo);
	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode($output);
}

function doTransfer($user_id, $nilai, $ip_tujuan) {
	$app = \Slim\Slim::getInstance();
    $app_info = array();
    
 	$conn = connect_db();
    $field= "saldo";
    $name = "nama";
    $domisili = "ip_domisili";
    
    $select_query = "SELECT * FROM nasabah WHERE user_id = '$user_id'" ;
    $result = mysqli_query($conn, $select_query);
    $row = mysqli_fetch_array($result);
    $init_saldo = (int) $row[$field];
    
    $payload = json_encode(array("user_id"=> $user_id, "nilai"=> $nilai));
    $getsaldo_payload = json_encode(array("user_id"=> $user_id)); 
    
    $url = $ip_tujuan . "/ewallet/getSaldo/" . $user_id;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $response_getSaldo = json_decode($response, true);
    $nilai_saldo = $response_getSaldo['nilai_saldo'];
    
    if ($init_saldo == 0) {
        $app_info = array("status_transfer" => -1, "isRegistered" => -2); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    } else if ($nilai_saldo == -1) {
     	$app_info = array("status_transfer" => -1, "isRegistered" => -1); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    } else if ($init_saldo > $nilai) {
        $url = $ip_tujuan . "/ewallet/transfer";
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_URL => $url,
        ));
        $response = curl_exec($ch);

        $latest_saldo = $init_saldo - $nilai;
        $update_query = "UPDATE nasabah SET saldo = '$latest_saldo' WHERE user_id = '$user_id'";
        $updated = mysqli_query($conn, $update_query);
        
        $app_info = array("status_transfer" => 0, "isRegistered" => 0); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    } else {
        $app_info = array("status_transfer" => -1, "isRegistered" => 0); 
        $app->response()->headers->set('Content-Type', 'application/json');
        echo json_encode($app_info);
    }
    mysqli_close($conn);
}

function transfer($npm, $jumlah_uang) {
	$app = \Slim\Slim::getInstance();

	$url = "https://izzunaqi.sisdis.ui.ac.id/ewallet/getSaldo/" . $npm;
	$ch = curl_init();
	
	curl_setopt( $ch, CURLOPT_URL, $url);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);

	$json = json_decode($response);
	$saldo = $json->nilai_saldo;

	if ($saldo != -1) {
		$conn = connect_db();
		$current_saldo = $saldo + $jumlah_uang;
		$update_query = "UPDATE nasabah set saldo = $current_saldo where user_id = $npm";
		mysqli_query($conn, $update_query);
		$output = array("status_transfer" => 0);
	} else {
		$output = array("status_transfer" => -1);
	}

	$app->response()->headers->set('Content-Type', 'application/json');
	echo json_encode($output);
}

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();