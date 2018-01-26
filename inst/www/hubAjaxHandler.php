<?php 
	session_start();
	if(!isset($_SESSION["user_id"])){
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
		echo $conn->connect_error;
	} else {	
		/* Different controller functions. One for each possible action on the page */
		function getItemsTable($user_id){
			global $conn;
			//Change http response code 
			http_response_code(501); //Not implemented
		}
		function getLatestModels($user_id){
			global $conn;
			//Change http response code 
			http_response_code(501); //Not implemented
		}
		function addItem($data){
			global $conn, $dateDDMMYYYY;
			if(!isset($data['name'], $data['unit'], $data['usual_use_size'], $data['inventory'], $data['estimated_daily_use'])){
				http_response_code(400);
				echo "All fields are not filled properly"; //Bad request
			} else {
				$insert = "INSERT INTO item (user_id, name, unit, usual_use_size, tracked_since, inventory, estimated_daily_use) 
					VALUES({$_SESSION["user_id"]}, {$data['name']}, {$data['unit']}, {$data['usual_use_size']}, {$dateDDMMYYYY},
					{$data['inventory']}, {$data['estimated_daily_use'])})";
				if ($conn->query($insert) === TRUE) {
					//Change http response code 
					http_response_code(201);
				} else {//Error
					//Change http response code 
					http_response_code(500);
				}
			}
		}
		function addOrUpdateItemUse($data){
			global $conn;
			//Change http response code 
			http_response_code(501); //Not implemented
		}
		function deleteItem($id){
			global $conn;
			$delete = "DELETE FROM item WHERE id = {$id}";
			if ($conn->query($insert) === TRUE) {
				//Change http response code 
				http_response_code(204);
			} else {//Error
				//Change http response code 
				http_response_code(500);
			}
		}
		
		if($_SERVER['REQUEST_METHOD'] == 'GET'){
			//Change http response code 
			http_response_code(501); //Not implemented
		} else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			addItem($_POST['data']);
		} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
			//Will use the url to pass the information (can use $_GET)
			$id = $_GET['item_id'];
			if(isset($id)){
				deleteItem($id);
			} else {
				//Change http response code 
				http_response_code(400);//Bad request
			}
		} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
			//Get put data
			$_PUT = file_get_contents('php://input');
			//Change http response code 
			http_response_code(200);
		} else {
			// Invalid Request Method
			http_response_code(405);
		}
	}
	$conn->close(); //Close connection
?>