<!DOCTYPE html>
<html lang="en">
	<head>
	  <title>e-Wallet | 152.118.33.74</title>
	  <meta charset="utf-8">
	  <meta name="viewport" content="width=device-width, initial-scale=1">
	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>

		<div class="container">
			<h1 class="text-center"><em>e-Wallet</em></h1>
			<p class="text-center">Kantor Cabang 152.118.33.74</p> 
			
			&nbsp

			<div class="row">
				
				<h5><a href="/ewallet"><span class="glyphicon glyphicon-menu-left"></span> Back</a></h5>				
				<div class="col-xs-12 col-md-8">		
					<h3>Transfer</h3>
					<form method="POST" action="">
					  	<div class="form-group">
					    	<label for="user_id">User ID</label>
					  		<input type="text" class="form-control" name="user_id" placeholder="User ID">
					  	</div>
					  	<div class="form-group">
					    	<label for="nilai">Jumlah</label>
					    	<input type="text" class="form-control" name="nilai" placeholder="Jumlah">
					  	</div>
					  	<label for="ip_tujuan">Tujuan</label>
						<select class="form-control" name="ip_tujuan">
						  <option value="https://hadhi.sisdis.ui.ac.id">hadhi.sisdis.ui.ac.id</option>
						  <option value="https://prayogo.sisdis.ui.ac.id">prayogo.sisdis.ui.ac.id</option>
						  <option value="https://rahman.sisdis.ui.ac.id">rahman.sisdis.ui.ac.id</option>
						  <option value="https://mardhika.sisdis.ui.ac.id">mardhika.sisdis.ui.ac.id</option>
						  <option value="https://aziz.sisdis.ui.ac.id">aziz.sisdis.ui.ac.id</option>
						  <option value="https://atma.sisdis.ui.ac.id">atma.sisdis.ui.ac.id</option>
						  <option value="https://saprikan.sisdis.ui.ac.id">saprikan.sisdis.ui.ac.id</option>
						  <option value="https://kurnia.sisdis.ui.ac.id">kurnia.sisdis.ui.ac.id</option>
						</select>
						<br>
					  	<button type="submit" class="btn btn-primary">Submit</button>
					</form>	
				</div>
				
				
			</div>
			
			<br>
			<br>

			<div class="row">
				<div class="container panel panel-default">
					<div class="panel-heading"> 
						<h4>
							<b>Output</b>
						</h4>
					</div>
					<h4>
						<!-- Place your php code here -->
						<?php
							if (isset($_POST['user_id'])) {
								$npm = $_POST['user_id'];
								$nilai = $_POST['nilai'];
								$ip_tujuan = $_POST['ip_tujuan'];

								$url = "https://izzunaqi.sisdis.ui.ac.id/ewallet/calculation";
								$ch = curl_init();
								$arr = array('user_id' => $npm, 'nilai' => $nilai, 'ip_tujuan' => $ip_tujuan);
								$args = json_encode($arr);

								curl_setopt( $ch, CURLOPT_URL, $url);
								curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt( $ch, CURLOPT_POSTFIELDS, $args);
								curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
								
								$response = curl_exec($ch);
								$json = json_decode($response, true);

								
								if ($json['status_transfer'] == 0) {
									echo "Transfer berhasil";
								} else {
									echo "Transfer gagal";
								}
							}
							
						?>
					</h4>
				</div>
			</div>

		</div>

	</body>
</html>