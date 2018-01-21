<!DOCTYPE html>
<html>
<head>
	<title>Placeholder - Home</title>
	<meta charset="utf-8">
	<!-- stylesheets and javascripts imports -->
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/navbar.css">
	  <link rel="stylesheet" href="css/cards.css">
	  <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
		  rel="stylesheet">-->
	  <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	  <script type="text/javascript" src="js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="js/sweetalert2.all.js"></script>
</head>
<body>
<?php
	$message = "";
	session_start();
	// if it is a login attempt
	if(isset($_POST['signin'])){
		$servername = "localhost";
		$username = "CSI3540PHP";
		$password = "Alpha2595!";
		$dbname = "CSI3540DB";
		if(!empty($_POST['login-email']) && !empty($_POST['login-password'])){
			// Create connection
			$conn = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$query = "SELECT name, email, salt, password FROM user where email = '" . $_POST['login-email'] . "'";
			$user = mysqli_fetch_array(mysqli_query($conn, $query));
			if($user){ //query got a valid user
				$hashed_password = strval(hash("sha256", $_POST['login-password'] . $user['salt'], FALSE));
				if($hashed_password == $user['password']){
					//user provided the correct password
					$_SESSION["user_name"] = $user['name'];
					$_SESSION["user_email"] = $user['email'];
					
					//Remember me 
					if(isset($_POST['login-rememberme'])){
						//Create cookies (expire in a year)
						setcookie("user_name", $user['name'], time() + (365*24*60*60));
						setcookie("user_email", $user['email'], time() + (365*24*60*60));
					} else {
						//Delete cookies
						if (isset($_COOKIE['user_name'])) {
							unset($_COOKIE['user_name']);
							setcookie('user_name', '', time() - 3600); // empty value and old timestamp, thus browser will delete
						}
						if (isset($_COOKIE['user_email'])) {
							unset($_COOKIE['user_email']);
							setcookie('user_email', '', time() - 3600); // empty value and old timestamp, thus browser will delete
						}
					}
				} else {
					$message = "Invalid password";
				}
			} else {
				$message = "Invalid email or password";
			}
			$conn->close();
		} else {
			$message = "Please enter both your email and password.";
		}
	} else {
		//Check cookies for logged in user
		if (isset($_COOKIE['user_name']) && isset($_COOKIE['user_email'])){
			$_SESSION["user_name"] = $_COOKIE['user_name'];
			$_SESSION["user_email"] = $_COOKIE['user_email'];
		}
	}
	if($message){
		echo '<div class=\"alert alert-danger\">
						<strong>Error</strong><br>' . $message . '</div>';
	}
?>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-md bg-success navbar-dark">
	<a class="navbar-brand" href="">Placeholder Title</a>
	<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
		<ul class="navbar-nav">
		  <?php
			if(isset($_SESSION["user_email"])){
			  //user is logged in
			  echo "<li class=\"nav-item dropdown signed-in for-non-collapsed\">
						<a class=\"nav-link dropdown-toggle btn btn-success whiteText\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
							{$_SESSION["user_name"]}
						</a>
						<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink\">
						  <a class=\"dropdown-item\" href=\"hub.php\">My hub</a>
						  <a class=\"dropdown-item\" href=\"settings.php\">My Settings</a>
						  <a class=\"dropdown-item\" href=\"signout.php\">Sign out</a>
						</div>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-success whiteText\" href=\"hub.php\">My hub</a>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-success whiteText\" href=\"settings.php\">My Settings</a>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-success whiteText\" href=\"signout.php\">Sign out</a>
					</li>";
			} else {
			  echo "<li class=\"nav-item signed-out\">
						<a class=\"nav-link btn btn-success whiteText\" href=\"#\" data-toggle=\"modal\" data-target=\"#login-modal\">Sign in</a>
					</li>
					<li class=\"nav-item signed-out\">
						<a class=\"nav-link disabled whiteText for-non-collapsed\" href=\"#\" id=\"nav_or\">or</a>
					</li>
					<li class=\"nav-item signed-out\">
						<a class=\"nav-link btn btn-success whiteText\" href=\"signup.php\">Sign up</a>
					</li>";
			}
		  ?>
		</ul>
	</div>
</nav>
<!-- Log in modal -->
<div class="modal" tabindex="-1" role="dialog" id="login-modal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="index.php" method="post">
		  <div class="modal-header">
			<h5 class="modal-title">Sign in</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" id="login-form">
			  <div class="form-group">
				<label for="email">Email address</label>
				<input type="email" class="form-control" id="email" placeholder="Enter email" id="login-email" name="login-email" required>
			  </div>
			  <div class="form-group">
				<label for="password">Password</label>
				<input type="password" class="form-control" id="password" placeholder="Password"  id="login-password" name="login-password" required>
			  </div>
			  <div class="form-check">
				<input class="form-check-input" type="checkbox" value="" id="login-rememberme" name="login-rememberme">
				<label class="form-check-label" for="rememberme">
					Remember me
				</label>
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-success" name='signin' onclick="form_submit()">Sign in</button>
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
		  </div>
	  </form>
    </div>
  </div>
</div>

<!-- Page content -->
<div class="jumbotron jumbotron-fluid bg-white">
  <div class="container">
    <h1 class="display-2">Placeholder</h1>
    <h2 class="display-8">You will never run out of the things you rely on ever again.</h2>
  </div>
</div>
<div class="container-fluid">
	<div class="row">
	<div class="col-xs-12 col-md-6">
		<div class="card text-center" id="cards-items">
			<div class="card-header bg-success">
				<h3 class="whiteText">Never run out of ________</h3>
			</div>
			<div class="card-body">
				<p class="card-text">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-6">
		<div class="card text-center" id="cards-hub">
			<div class="card-header bg-success">
				<h3 class="whiteText">Your hub</h3>
			</div>
			<div class="card-body">
				<p class="card-text">
					Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
				</p>
			</div>
		</div>
	</div>
	</div>
</div>
</body>
</html>