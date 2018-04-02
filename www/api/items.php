<?php 
	require("apiCredentials.php");
	if(!isset($_GET["user_id"]) && !isset($_GET["user_token"])){
		//Change http response code 
		http_response_code(400);
		return;
	}
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
				$select = "SELECT id, name, inventory, unit, usual_use_size, IF(slope_days IS NOT NULL, slope_days, estimated_daily_use) as model, tracked_since
					FROM item WHERE user_id = {$_GET["user_id"]}";
				$result = mysqli_fetch_all($conn->query($select), MYSQLI_ASSOC);
				header('Content-Type: application/json');
				echo json_encode($result);
			} else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
				if(!isset($_POST['name']) && !isset($_POST['unit']) && !isset($_POST['usual_use_size']) && !isset($_POST['inventory']) && !isset($_POST['estimated_daily_use'])){
					http_response_code(400);
				} else {
					$insert = "INSERT INTO item (user_id, name, unit, usual_use_size, tracked_since, inventory, estimated_daily_use) 
						VALUES(\"{$_GET["user_id"]}\", \"{$_POST['name']}\", \"{$_POST['unit']}\", \"{$_POST['usual_use_size']}\", \"{$dateDDMMYYYY}\",
						\"{$_POST['inventory']}\", \"{$_POST['estimated_daily_use']}\");";
					if ($conn->query($insert) === TRUE) {
						//Change http response code 
						http_response_code(201);
					} else {//Error
						//Change http response code 
						error_log(mysqli_error($conn));
						header('HTTP/1.1 500 Internal server error');
						echo json_encode(["message" =>  "An error occurred during the procedure." ]);
					}
				}
			} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
				//Will use the url to pass the information (can use $_GET)
				$id = $_GET['item_id'];
				if(isset($id)){
					$delete = "DELETE FROM item WHERE id = {$id}";
					if ($conn->query($delete) === TRUE) {
						//Change http response code 
						http_response_code(204);
					} else {//Error
						//Change http response code 
						error_log(mysqli_error($conn));
						header('HTTP/1.1 500 Internal server error');
						echo json_encode(["message" =>  "An error occurred during the procedure."]);
					}
				} else {
					//Change http response code 
					http_response_code(400);//Bad request
				}
			} else if ($_SERVER['REQUEST_METHOD'] == 'PATCH'){
				//Will use the url to pass the information (can use $_GET)
				$id = $_GET['item_id'];
				$qty = $_GET['item_qty'];
				if(isset($id)&&isset($qty)){
					$update = "UPDATE item SET inventory = {$qty} WHERE id = {$id}";
					if ($conn->query($update) === TRUE) {
						//Change http response code 
						http_response_code(200);
					} else {//Error
						//Change http response code 
						error_log(mysqli_error($conn));
						header('HTTP/1.1 500 Internal server error');
						echo json_encode(["message" =>  "An error occurred during the procedure."]);
					}
				} else {
					//Change http response code 
					http_response_code(400);//Bad request
				}
			} else {
				// Invalid Request Method
				http_response_code(405);
			}
		}
	}
	$conn->close(); //Close connection
?>
