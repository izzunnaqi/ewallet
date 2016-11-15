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
					<h3 >Register</h3>
					<form id="register" method="POST" action="">
					  <div class="form-group">
					    <label for="user_id">User ID</label>
					    <p><small>Isi dengan NPM anda</small></p>
					    <input type="text" class="form-control" name="user_id" placeholder="Contoh: 1306381774">
					  </div>
					  <div class="form-group">
					    <label for="nama">Nama</label>
					    <input type="text" class="form-control" name="nama" placeholder="Nama">
					  </div>
					  <div class="form-group">
					    <label for="ip_domisili">IP Domisili</label>
					    <input type="text" class="form-control" name="ip_domisili" placeholder="IP Domisili">
					  </div>

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
								$nama = $_POST['nama'];
								$ip = $_POST['ip_domisili'];
							
								$url = "https://izzunaqi.sisdis.ui.ac.id/ewallet/register";
								$ch = curl_init();
								$arr = array('user_id' => $npm, 'nama' => $nama, 'ip_domisili' => $ip);
								$args = json_encode($arr);

								curl_setopt( $ch, CURLOPT_URL, $url);
								curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt( $ch, CURLOPT_POSTFIELDS, $args);
								curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
								
								$response = curl_exec($ch);
								$json = json_decode($response, true);

								
								if ($json['success'] == 1) {
									echo "User Registered";
								} else {
									echo "User Not Registered";
								}
								
							}
						?>
					</h4>
				</div>
			</div>

		</div>

	</body>
</html>