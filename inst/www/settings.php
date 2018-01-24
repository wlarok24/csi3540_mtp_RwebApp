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
<body class="bg-dark themeText">
<!-- Navigation bar -->
<nav class="navbar navbar-expand-md bg-theme navbar-light fixed-top">
	<a class="navbar-brand" href="index.html">Placeholder Title</a>
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
			<div class="card-body bg-dark themeText" id="collapseOne" class="collapse show">
				<form action="" method="post">
					<div class="form-group row">
						<label for="email" class="col-sm-4">Your old password</label>
						<input type="password" class="form-control col-sm-8" id="email" name="email" placeholder="Password" required>
					</div>
					<div class="form-group row">
						<label for="password" class="col-sm-4">Your new password</label>
						<input type="password" class="form-control col-sm-8" id="password" name="password" placeholder="Password" required>
					</div>
					<div class="form-group row">
						<label for="password" class="col-sm-4">Repeat your new password</label>
						<input type="password" class="form-control col-sm-8" id="passwordRepeat" name="passwordRepeat" placeholder="Password" required>
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
			<div class="card-body bg-dark themeText">
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