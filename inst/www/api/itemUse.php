<?php 
	// Not implemented
	//http_response_code(501);
	
	if(!isset($_GET["user_id"]) && !isset($_GET["user_token"])){
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
		//Change http response code 
		header('HTTP/1.1 500 Internal server error');
		echo json_encode(["message" =>  "An error occurred during the procedure."]);
	} else {
		//Verify user token
		$verification = "SELECT * FROM user WHERE id = '{$_GET["user_id"]}' AND token = '{$_GET["user_token"]}'";
		$ver_result = mysqli_query($conn, $verification);
		if(mysqli_num_rows($ver_result) != 1){ //Wrong token
			//Change http response code 
			header('HTTP/1.1 401 Unauthorized');
			echo json_encode(["message" => "Invalid session"]);
		} else {
			//API implementation
			if($_SERVER['REQUEST_METHOD'] == 'GET'){
				$select = "";
				if($_GET["op"] == "today"){
					if(!isset($_GET["item_id"])){
						//Change http response code 
						http_response_code(400);
						return;
					} else {
						$select = "SELECT item_id, date_nbr, date, qty FROM item_use 
						WHERE item_id = {$_GET["item_id"]} and date = {$dateDDMMYYYY};";
					}
				} else if($_GET["op"] == "archive"){
					if(!isset($_GET["item_id"])){
						//Change http response code 
						http_response_code(400);
						return;
					} else {
						$select = "SELECT item_id, date_nbr, date, qty FROM item_use WHERE item_id = {$_GET["item_id"]};";
					}
				} else {
					//Change http response code 
					http_response_code(400);
					return;
				}
				$result = mysqli_fetch_all($conn->query($select), MYSQLI_ASSOC);
				header('Content-Type: application/json');
				echo json_encode($result);
			} else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(!isset($_POST['item_id']) && !isset($_POST['date_nbr']) && !isset($_POST['qty'])){
					http_response_code(400);
				} else {
					$insert = "INSERT INTO item_use (item_id, date_nbr, date, qty) 
						VALUES(\"{$_POST['item_id']}\", \"{$_POST['date_nbr']}\", \"{$dateDDMMYYYY}\", \"{$_POST['qty']}\");";
					if ($conn->query($insert) === TRUE) {
						//Change http response code 
						http_response_code(201);
					} else {//Error
						error_log(mysqli_error($conn));
						//Change http response code 
						header('HTTP/1.1 500 Internal server error');
						echo json_encode(["message" =>  "An error occurred during the procedure." ]);
					}
				}
			} else {
				// Invalid Request Method
				http_response_code(405);
			}
		}
	}
	$conn->close(); //Close connection
?>