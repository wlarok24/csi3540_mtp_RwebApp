<!DOCTYPE html>
<html>
<head>
	<title>Placeholder - My Hub</title>
	<meta charset="utf-8">
	<!-- stylesheets and javascripts imports -->
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/navbar.css">
	  <link rel="stylesheet" href="css/hub.css">
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
						<a class=\"nav-link dropdown-toggle btn btn-success whiteText\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
							{$_SESSION["user_name"]}
						</a>
						<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink\">
						  <a class=\"dropdown-item\" href=\"settings.php\">My Settings</a>
						  <a class=\"dropdown-item\" href=\"signout.php\">Sign out</a>
						</div>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-success whiteText\" href=\"settings.php\">My Settings</a>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-success whiteText\" href=\"signout.php\">Sign out</a>
					</li>";
			} else {
			  echo "<script>window.location.replace(\"index.php\");</script>"; //If not signed in redirect to home page
			}
		  ?>
		</ul>
	</div>
</nav>

<!-- Page content -->
<h1 id="myhub" class="title">My hub</h1>
<div class="container-fluid extra-padding">
	<div class="row">
	<div class="col-12">
		<div class="card text-center" id="cards-items">
			<div class="card-header bg-success">
				<h3 class="whiteText" id="inventory">Inventory</h3>
			</div>
			<div class="card-body">
				<form action="" method="post"><table class="table">
					<thead><tr>
						<th>Item</th>
						<th>Quantity</th>
						<th>Usual use size</th>
						<th>Today's use</th>
					</tr></thead>
					<tbody></tbody>
					<tfoot><tr>
						<th>Item</th>
						<th>Quantity</th>
						<th>Usual use size</th>
						<th>Today's use</th>
					</tr></tfoot>
				</table>
				<div class="btn-group">
					<button type="button" class="btn btn-success" name='add_ingredient' id='add_ingredient'>Add</button>
					<button type="button" class="btn btn-success" name='remove_ingredient' id='remove_ingredient'>Remove</button>
					<button type="button" class="btn btn-success" name='submit_use' id='submit_use'>Submit use</button>
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-12">
		<div class="card text-center" id="cards-hub">
			<div class="card-header bg-success">
				<h3 class="whiteText" id="graph">Graph reports</h3>
			</div>
			<div class="card-body">
			</div>
		</div>
	</div>
	</div>
</div>
</body>
</html>