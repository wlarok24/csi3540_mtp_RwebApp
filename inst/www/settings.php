<!DOCTYPE html>
<html>
<head>
	<title>Placeholder - My Settings</title>
	<meta charset="utf-8">
	<!-- stylesheets and javascripts imports -->
	  <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/navbar.css">
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
		  <li class="nav-item signed-out">
			<a class="nav-link btn btn-success whiteText" href="#" data-toggle="modal" data-target="#login-modal">Sign in</a>
		  </li>
		  <li class="nav-item signed-out">
			<a class="nav-link disabled whiteText for-non-collapsed" href="#" id="nav_or">or</a>
		  </li>
		  <li class="nav-item signed-out">
			<a class="nav-link btn btn-success whiteText" href="signup.php">Sign up</a>
		  </li>
		  <li class="nav-item dropdown signed-in for-non-collapsed">
			<a class="nav-link dropdown-toggle btn btn-success whiteText" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  My account
			</a>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
			  <a class="dropdown-item" href="hub.php">My hub</a>
			  <a class="dropdown-item" href="settings.php">My Settings</a>
			  <button type="button" class="dropdown-item btn btn-success" >Sign out</button>
			</div>
		  </li>
		  <li class="nav-item signed-in for-collapsed">
			<a class="nav-link btn btn-success whiteText" href="hub.php">My hub</a>
		  </li>
		  <!--<li class="nav-item signed-in for-collapsed">
			<a class="nav-link btn btn-success whiteText" href="settings.php">My Settings</a>
		  </li>-->
		  <li class="nav-item signed-in for-collapsed">
			<a class="nav-link btn btn-success whiteText" href="#">Sign out</a>
		  </li>
		</ul>
	</div>
</nav>
<!-- Log in modal -->
<div class="modal" tabindex="-1" role="dialog" id="login-modal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sign in</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="login-form">
        <form>
		  <div class="form-group">
			<label for="exampleInputEmail1">Email address</label>
			<input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
		  </div>
		  <div class="form-group">
			<label for="exampleInputPassword1">Password</label>
			<input type="password" class="form-control" id="password" placeholder="Password">
		  </div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success">Sign in</button>
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Page content -->

</body>
</html>