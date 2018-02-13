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
				$select = "";
				if($_GET["op"] == "today"){
					if(!isset($_GET["item_id"])){
						//Change http response code 
						http_response_code(400);
						return;
					} else {
						$select = "SELECT item_id, date_nbr, date, qty FROM item_use WHERE item_id = {$_GET["item_id"]} and date = {$dateDDMMYYYY};";
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
				if(!isset($_POST['items'])){
					http_response_code(400);
				} else {
					error_log(json_encode($_POST['items']));
					//$items = json_decode($_POST['items'], true);
					$items = $_POST['items'];
					$successful = (count($items) > 0);
					//error_log("successful = {$successful}");
					foreach($items as $item){
						//error_log("start loop" . json_encode($item));
						//Get the date number
						//Date format : YYYY-MM-DD
						$date1 = new DateTime(substr($dateDDMMYYYY, 4, 4) . '-' . substr($dateDDMMYYYY, 2, 2) . '-' . substr($dateDDMMYYYY, 0, 2));
						$date2 = new DateTime(substr($item["tracked_since"], 4, 4) . '-' . substr($item["tracked_since"], 2, 2) . '-' . substr($item["tracked_since"], 0, 2));
						$diff = $date2->diff($date1)->format("%a"); //Get the difference in days
						$date_nbr = $diff + 1;
						
						//Create the query
						$query = "";
						$prev_qty = 0;
						if($item["update"] != "false"){
							if(!isset($item['prev_qty'])){
								//Change http response code 
								http_response_code(400);
								return;
							}
							$prev_qty = $item['prev_qty']; //Update value of prev_qty to previous quantity
							$query = "UPDATE item_use SET qty = {$item['qty']} WHERE item_id = {$item['item_id']} and date = {$dateDDMMYYYY}";
						} else {
							$query = "INSERT INTO item_use (item_id, date_nbr, date, qty) VALUES(\"{$item['item_id']}\", \"{$date_nbr}\", \"{$dateDDMMYYYY}\", \"{$item['qty']}\");";
						}
						//error_log("Query" . $query);
						if ($conn->query($query) === TRUE) {
							//Decrement inventory
							$update = "UPDATE item SET inventory = inventory - {$item['qty']} + {$prev_qty} WHERE id = {$item['item_id']}";
							if ($conn->query($update) === TRUE) {
								$successful = $successful && TRUE;
							} else {//Error
								error_log(mysqli_error($conn));
								$successful = $successful && FALSE;
							}
						} else {//Error
							error_log(mysqli_error($conn));
							$successful = $successful && FALSE;
						}
						//error_log("end loop");
					}
					//error_log("successful (after) = {$successful}");
					if($successful){
						//Change http response code 
						http_response_code(201);
					} else {
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