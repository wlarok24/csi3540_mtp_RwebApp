<!DOCTYPE html>
<html>
<head>
	<title>Placeholder - Sign up</title>
	<meta charset="utf-8">
	<!-- stylesheets and javascripts imports -->
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/navbar.css">
	  <link rel="stylesheet" href="css/signup.css">
	  <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
		  rel="stylesheet">-->
	  <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	  <script type="text/javascript" src="js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="js/sweetalert2.all.js"></script>
</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-md bg-success navbar-dark">
	<a class="navbar-brand" href="index.php">Placeholder Title</a>
	<!-- The user is signing up and shouldn't see the rest of the nav bar -->
	<!--<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
		<ul class="navbar-nav">
		</ul>
	</div>-->
</nav>

<!-- Page content -->
<div class="container-fluid">
	<h1 class="center-xs">Sign up</h1>
	<?php
		$servername = "localhost";
		$username = "CSI3540PHP";
		$password = "Alpha2595!";
		$dbname = "CSI3540DB";
		if(isset($_POST['signup'])){
			if(isset($_POST['datawaiver']) && !empty($_POST['name']) && !empty($_POST['email']) 
			&& !empty($_POST['password']) && !empty($_POST['passwordRepeat'])){
				if($_POST['password'] != $_POST['passwordRepeat']){
					echo "<div class=\"alert alert-danger\">
						<strong>Feedback</strong><br>The two passwords don't match</div>";
				} else {
					//Create the salt and the hashed password
					$salt = strval(md5(strval(rand()), FALSE));
					$hashed_password = strval(hash("sha256", $_POST['password'] . $salt, FALSE));
					$insert = "INSERT INTO user (name, email, salt, password) VALUES(\"{$_POST['name']}\", 
						\"{$_POST['email']}\", \"{$salt}\", \"{$hashed_password}\")";
					// Create connection
					$conn = new mysqli($servername, $username, $password, $dbname);
					// Check connection
					if ($conn->connect_error) {
						die("Connection failed: " . $conn->connect_error);
					} 
					if ($conn->query($insert) === TRUE) {
						echo "<div class=\"alert alert-success\"><strong>Success!</strong><br>
							Your account was created!</div>";
					} else {
						echo "<div class=\"alert alert-danger\"><strong>Error</strong><br>"
								. $insert . "<br>" . $conn->error . "</div>"; //For debug only
					}
					$conn->close();
				}
			} else {
				echo "<div class=\"alert alert-danger\">
						<strong>Feedback</strong><br>Please fill out all the fields</div>";
			}
		}
	  ?>
	<form action="" method="post">
		<div class="form-group row">
			<label for="name" class="col-sm-4">Your name</label>
			<input type="text" class="form-control col-sm-8" id="name" name="name" placeholder="Enter your name" required>
		</div>
		<div class="form-group row">
			<label for="email" class="col-sm-4">Your email address</label>
			<input type="email" class="form-control col-sm-8" id="email" name="email" placeholder="Enter your email" required>
		</div>
		<div class="form-group row">
			<label for="password" class="col-sm-4">Your password</label>
			<input type="password" class="form-control col-sm-8" id="password" name="password" placeholder="Password" required>
		</div>
		<div class="form-group row">
			<label for="password" class="col-sm-4">Repeat your password</label>
			<input type="password" class="form-control col-sm-8" id="passwordRepeat" name="passwordRepeat" placeholder="Password" required>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="checkbox" value="" id="datawaiver" name="datawaiver" required>
			<label class="form-check-label" for="datawaiver">
				I agree that the data submitted to this site can be anonymously used for statistical purposes.
			</label>
		</div>
		<button type="submit" class="btn btn-success center-xs" name='signup'>Sign up</button>
	</form>
</div>
</body>
</html>