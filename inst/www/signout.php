<?php
	//Signing out
	session_start();
	session_destroy();
	//Delete cookies
	if (isset($_COOKIE['user_name'])) {
		unset($_COOKIE['user_name']);
		setcookie('user_name', '', time() - 3600); // empty value and old timestamp, thus browser will delete
	}
	if (isset($_COOKIE['user_email'])) {
		unset($_COOKIE['user_email']);
		setcookie('user_email', '', time() - 3600); // empty value and old timestamp, thus browser will delete
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Placeholder - Sign out</title>
	<meta charset="utf-8">
	 <!-- <link rel="stylesheet" href="css/bootstrap.min.css">
	  <link rel="stylesheet" href="css/theme.css">
	  <link rel="stylesheet" href="css/cards.css">
	  <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
	  <script type="text/javascript" src="js/bootstrap.min.js"></script>
	  <script type="text/javascript" src="js/sweetalert2.all.js"></script> -->
</head>
<body>
	<!-- Redirect to index using javascript -->
	<script>window.location.replace("index.php");</script>
</body>
</html>