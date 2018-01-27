<?php 
	if(!isset($_GET["user_id"])){
		//Change http response code 
		http_response_code(400);
		return;
	}
	$servername = "localhost";
	$username = "CSI3540PHP";
	$password = "Alpha2595!";
	$dbname = "CSI3540DB";
	$conn = new mysqli($servername, $username, $password, $dbname);
	$dateDDMMYYYY = date("d") . date("m") . date("Y");
	// Check connection
	if ($conn->connect_error) {
		http_response_code(500);
	} else {	

		function addOrUpdateItemUse($data){
			global $conn;
			//Change http response code 
			http_response_code(501); //Not implemented
		}

	if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
			//Get put data
			$_PUT = file_get_contents('php://input');
			//Change http response code 
			http_response_code(501);
		} else {
			// Invalid Request Method
			http_response_code(405);
		}
	}
	$conn->close(); //Close connection
?>