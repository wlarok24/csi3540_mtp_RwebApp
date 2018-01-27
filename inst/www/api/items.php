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
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			$select = "SELECT * FROM item WHERE user_id = {$_GET["user_id"]}";
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
					error_log(mysqli_error($conn));
					//Change http response code 
					http_response_code(500);
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
					http_response_code(500);
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
	$conn->close(); //Close connection
?>