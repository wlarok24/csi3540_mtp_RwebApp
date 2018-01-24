<!DOCTYPE html>
<html>
<head>
	<title>Placeholder - My Hub</title>
	<meta charset="utf-8">
	<!-- stylesheets and javascripts imports -->
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/theme.css">
	  <link rel="stylesheet" href="css/hub.css">
	  <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
		  rel="stylesheet">-->
	  <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	  <script type="text/javascript" src="js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="js/sweetalert2.all.js"></script>
</head>
<body class="bg-dark themeText">
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
			  echo "<li class=\"nav-item signed-in\">
						<a class=\"nav-link btn btn-theme blackText\" href=\"#inventory\">Inventory</a>
					</li>
					<li class=\"nav-item signed-in\">
						<a class=\"nav-link btn btn-theme blackText\" href=\"#graph\">Graph reports</a>
					</li>
					<li class=\"nav-item dropdown signed-in for-non-collapsed\">
						<a class=\"nav-link dropdown-toggle btn btn-theme blackText\" href=\"#\" id=\"navbarDropdownMenuLink\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
							{$_SESSION["user_name"]}
						</a>
						<div class=\"dropdown-menu dropdown-menu-right\" aria-labelledby=\"navbarDropdownMenuLink\">
						  <a class=\"dropdown-item\" href=\"settings.php\">My Settings</a>
						  <a class=\"dropdown-item\" href=\"signout.php\">Sign out</a>
						</div>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-theme blackText\" href=\"settings.php\">My Settings</a>
					</li>
					<li class=\"nav-item signed-in for-collapsed\">
						<a class=\"nav-link btn btn-theme blackText\" href=\"signout.php\">Sign out</a>
					</li>";
			} else {
			  echo "<script>window.location.replace(\"index.php\");</script>"; //If not signed in redirect to home page
			}
		  ?>
		</ul>
	</div>
</nav>

<!-- Add ingredient modal -->
<div class="modal" tabindex="-1" role="dialog" id="ingredient-modal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-dark themeText">
      <form action="index.php" method="post">
		  <div class="modal-header">
			<h5 class="modal-title">Sign in</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" id="ingredient-form">
			  <div class="form-group textTheme">
				<label for="ingredient-name">Ingredient name</label>
				<input type="text" class="form-control" placeholder="Enter Name" id="ingredient-name" name="ingredient-name" required>
			  </div>
			  <div class="form-group textTheme">
				<label for="ingredient-unit">Unit of measurement</label>
				<input type="text" class="form-control" placeholder="Enter unit (unit, mL, g, etc)" id="ingredient-unit" name="ingredient-unit" required>
			  </div>
			  <div class="form-group textTheme">
				<label for="ingredient-size">Usual use size</label>
				<input type="number" class="form-control" placeholder="Enter usual use size" id="ingredient-size" name="ingredient-size" required>
			  </div>
			  <div class="form-group textTheme">
				<label for="ingredient-inventory">Usual use size</label>
				<input type="number" class="form-control" placeholder="Enter your current inventory" id="ingredient-inventory" name="ingredient-inventory" required>
			  </div>
			  <div class="form-group textTheme">
				<label for="ingredient-estimate">Estimated daily use</label>
				<input type="number" class="form-control" placeholder="Enter your estimated daily use" id="ingredient-estimate" name="ingredient-estimate" required>
			  </div>
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-theme" name='add-ingredient' onclick="form_submit()">Add ingredient</button>
			<button type="button" class="btn btn-theme" data-dismiss="modal">Cancel</button>
		  </div>
	  </form>
    </div>
  </div>
</div>

<!-- Page content -->
<h1 id="myhub" class="title">My hub</h1>
<div class="container-fluid extra-padding">
	<div class="row">
	<div class="col-12">
		<div class="card text-center" id="cards-items">
			<div class="card-header bg-theme">
				<h3 class="blackText" id="inventory">Inventory</h3>
			</div>
			<div class="card-body bg-dark themeText">
				<form action="" method="post"><table class="table">
					<thead><tr>
						<th>Item</th>
						<th>Quantity</th>
						<th>Usual use size</th>
						<th>Today's use</th>
					</tr></thead>
					<tbody>
					<tr><td>Patatoes</td><td>20 units</td><td>2 units</td><td><input type="number" class="form-control" id="Patatoes" name="Patatoes" value=0 required></td></tr>
					<tr><td>Yogurt</td><td>3000 mL</td><td>200 mL</td><td><input type="number" class="form-control" id="Yogurt" name="Yogurt" value=0 required></td></tr>
					<tr><td>Toilet paper</td><td>48 units</td><td>1 units</td><td><input type="number" class="form-control" id="Toilet_paper" name="Toilet_paper" value=0 required></td></tr>
					</tbody>
					<tfoot><tr>
						<th>Item</th>
						<th>Quantity</th>
						<th>Usual use size</th>
						<th>Today's use</th>
					</tr></tfoot>
				</table>
				<div class="btn-group">
					<button type="button" class="btn btn-theme" name='add_ingredient' id='add_ingredient' data-toggle="modal" data-target="#ingredient-modal">Add</button>
					<button type="button" class="btn btn-theme" name='remove_ingredient' id='remove_ingredient'>Remove</button>
					<button type="button" class="btn btn-theme" name='submit_use' id='submit_use'>Submit use</button>
				</div>
				</form>
			</div>
		</div>
	</div>
	<div class="col-12">
		<div class="card text-center" id="cards-hub">
			<div class="card-header bg-theme">
				<h3 class="blackText" id="graph">Graph reports</h3>
			</div>
			<div class="card-body bg-dark themeText">
			</div>
		</div>
	</div>
	</div>
</div>
</body>
</html>