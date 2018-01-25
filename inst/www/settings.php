<!DOCTYPE html>
<html>
<head>
	<title>Placeholder - My Settings</title>
	<meta charset="utf-8">
	<!-- stylesheets and javascripts imports -->
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/theme.css">
	  <link rel="stylesheet" href="css/settings.css">
	  <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
		  rel="stylesheet">-->
	  <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	  <script type="text/javascript" src="js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="js/sweetalert2.all.js"></script>
</head>
<body class="bg-dark whiteText">
<!-- Navigation bar -->
<nav class="navbar navbar-expand-md bg-theme navbar-light fixed-top">
	<a class="navbar-brand" href="index.php">Placeholder Title</a>
	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
		<ul class="navbar-nav">
		  <?php
			session_start();
			if(!isset($_SESSION["user_email"])){
				//Import cookies
				if (isset($_COOKIE['user_name']) && isset($_COOKIE['user_email'])){
					$_SESSION["user_name"] = $_COOKIE['user_name'];
					$_SESSION["user_email"] = $_COOKIE['user_email'];
				}
			}
			//Now check session data for a logged in user
			if(isset($_SESSION["user_email"])){
			  //user is logged in
			  echo "<li class=\"nav-item dropdown signed-in for-non-collapsed\">
						<a class=\"nav-link dropdown-toggle btn btn-theme blackText\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
							{$_SESSION["user_name"]}
						</a>
						<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink\">
						  <a class=\"dropdown-item\" href=\"hub.php\">My hub</a>
						  <a class=\"dropdown-item\" href=\"signout.php\">Sign out</a>
						</div>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-theme blackText\" href=\"hub.php\">My hub</a>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-theme blackText\" href=\"signout.php\">Sign out</a>
					</li>";
			} else {
			  echo "<script>window.location.replace(\"index.php\");</script>"; //If no user is logged in, redirect to home page
			}
		  ?>
		</ul>
	</div>
</nav>

<!-- Page content -->
<h1 id="myhub" class="title">My settings</h1>
<div class="container-fluid extra-padding" id="card_container">
	<div class="row">
	<div class="col-12">
		<div class="card text-center" id="cards-items">
			<div class="card-header bg-theme">
				<h3 class="blackText">Change your password</h3>
			</div>
			<div class="card-body bg-dark  whiteText" id="collapseOne" class="collapse show">
			<?php
				if(isset($_POST['submit_new_password'])){
					$servername = "localhost";
					$username = "CSI3540PHP";
					$password = "Alpha2595!";
					$dbname = "CSI3540DB";
					if(!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['new_passwordRepeat'])
					  && ($_POST['new_password'] == $_POST['new_passwordRepeat'])){
						// Create connection
						$conn = new mysqli($servername, $username, $password, $dbname);
						// Check connection
						if ($conn->connect_error) {
							die("Connection failed: " . $conn->connect_error);
						} 
						$query = "SELECT name, email, salt, password FROM user where email = '{$_SESSION["user_email"]}'";
						$user = mysqli_fetch_array(mysqli_query($conn, $query));
						if($user){ //query got a valid user
							$old_hashed_password = strval(hash("sha256", $_POST['old_password'] . $user['salt'], FALSE));
							if($old_hashed_password == $user['password']){
								//user provided the correct password
								$new_hashed_password = strval(hash("sha256", $_POST['new_password'] . $user['salt'], FALSE));
								$update = "UPDATE user SET password = '{$new_hashed_password}' WHERE email = '{$_SESSION["user_email"]}'";
								if ($conn->query($update) === TRUE) {
									echo "<div class=\"alert alert-success\"><strong>Success!</strong><br>
										Your password was changed!</div>";
								} else {
									$message =  $update . '<br>' . $conn->error . '</div>'; //For debug only
								}
							} else {
								$message = "Invalid password";
							}
						} else {
							$message = "Invalid email or password";
						}
						$conn->close();
					} else {
						if($_POST['new_password'] == $_POST['new_passwordRepeat']){
							//The fields were not all filled
							$message = "Please fill out all the fields in order to change your password.";
						} else {
							$message = "Please ensure that both password fields match.";
						}
					}
				}
				if(isset($message)){
					echo '<div class=\"alert alert-danger\">
									<strong>Error</strong><br>' . $message . '</div>';
				}
			?>
				<form action="" method="post">
					<div class="form-group row">
						<label for="old_parent" class="col-sm-4">Your old password</label>
						<input type="password" class="form-control bg-dark col-sm-8" id="old_password" name="old_password" placeholder="Your old password" required>
					</div>
					<div class="form-group row">
						<label for="password" class="col-sm-4">Your new password</label>
						<input type="password" class="form-control bg-dark col-sm-8" id="new_password" name="new_password" placeholder="Your new password" required>
					</div>
					<div class="form-group row">
						<label for="password" class="col-sm-4">Repeat your new password</label>
						<input type="password" class="form-control bg-dark col-sm-8" id="new_passwordRepeat" name="new_passwordRepeat" placeholder="Your new password again" required>
					</div>
					<button type="submit" class="btn btn-theme" name='submit_new_password'>Submit</button>
				</form>
			</div>
		</div>
	</div>
	<div class="col-12">
		<div class="card text-center" id="cards-hub">
			<div class="card-header bg-theme">
				<h3 class="blackText">Add additional information</h3>
			</div>
			<div class="card-body bg-dark  whiteText">
				<b class="">Note : the information is for statistical purposes only</b>
				<form action="" method="post">
				
				<button type="submit" class="btn btn-theme" name='submit_new_password'>Submit</button>
				</form>
			</div>
		</div>
	</div>
	</div>
</div>
</body>
</html>