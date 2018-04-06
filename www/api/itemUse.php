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
		header('Content-Type: application/json');
		echo json_encode(["message" =>  "An error occurred during the procedure."]);
	} else {
		//Verify user token
		/**$verification = "SELECT * FROM user WHERE id = '{$_GET["user_id"]}' AND token = '{$_GET["user_token"]}'";
		$ver_result = mysqli_query($conn, $verification);**/
		try{
			$verification = $conn->prepare("SELECT * FROM user WHERE id = ? AND token = ?");
			$verification->bind_param("ss", $_GET["user_id"], $_GET["user_token"]);
			$verification->execute();
			if($verification->get_result()->num_rows !== 1){ //Wrong token
				//Change http response code 
				header('HTTP/1.1 401 Unauthorized');
				echo json_encode(["message" => "Invalid session"]);
			} else {
				//API implementation
				if($_SERVER['REQUEST_METHOD'] == 'GET'){
					$select = null;
					if($_GET["op"] == "today"){
						if(!isset($_GET["item_id"])){
							//Change http response code 
							http_response_code(400);
							return;
						} else {
							//$select = "SELECT item_id, date_nbr, date, qty FROM item_use WHERE item_id = {$_GET["item_id"]} and date = {$dateDDMMYYYY};";
							$select = $conn->prepare("SELECT item_id, date_nbr, date, qty FROM item_use WHERE item_id = ? and date = ?");
							$select->bind_param("ss", $_GET["item_id"], $dateDDMMYYYY);
						}
					} else if($_GET["op"] == "archive"){
						if(!isset($_GET["item_id"])){
							//Change http response code 
							http_response_code(400);
							return;
						} else {
							//$select = "SELECT item_id, date_nbr, date, qty FROM item_use WHERE item_id = {$_GET["item_id"]};";
							$select = $conn->prepare("SELECT item_id, date_nbr, date, qty FROM item_use WHERE item_id = ?");
							$select->bind_param("s", $_GET["item_id"]);
						}
					} else {
						//Change http response code 
						http_response_code(400);
						return;
					}
					$select->execute();
					//$result = mysqli_fetch_all($conn->query($select), MYSQLI_ASSOC);
					$result = $select->get_result()->fetch_all(MYSQLI_ASSOC);
					header('Content-Type: application/json');
					echo json_encode($result);
					$select->close();
				} else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					if(!isset($_POST['items'])){
						http_response_code(400);
					} else {
						//error_log(json_encode($_POST['items']));
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
							$query = null;
							$prev_qty = 0;
							if($item["update"] != "false"){
								if(!isset($item['prev_qty'])){
									//Change http response code 
									http_response_code(400);
									return;
								}
								$prev_qty = $item['prev_qty']; //Update value of prev_qty to previous quantity
								//$query = "UPDATE item_use SET qty = {$item['qty']} WHERE item_id = {$item['item_id']} and date = {$dateDDMMYYYY}";
								$query = $conn->prepare("UPDATE item_use SET qty = ? WHERE item_id = ? and date = ?");
								$query->bind_param("iss", $item['qty'], $item['item_id'], $dateDDMMYYYY);
							} else {
								//$query = "INSERT INTO item_use (item_id, date_nbr, date, qty) VALUES(\"{$item['item_id']}\", \"{$date_nbr}\", \"{$dateDDMMYYYY}\", \"{$item['qty']}\");";
								$query = $conn->prepare("INSERT INTO item_use (item_id, date_nbr, date, qty) VALUES(?, ?, ?, ?)");
								$query->bind_param("sisi", $item["item_id"], $date_nbr, $dateDDMMYYYY, $item['qty']);
							}
							$query->execute();
							if ($query->affected_rows === 1) {
								//Decrement inventory
								//$update = "UPDATE item SET inventory = inventory - {$item['qty']} + {$prev_qty} WHERE id = {$item['item_id']}";
								$update = $conn->prepare("UPDATE item SET inventory = inventory - ? + ? WHERE id = ?");
								$update->bind_param("iis", $item['qty'], $prev_qty, $item['item_id']);
								$update->execute();
								if ($update->affected_rows === 1) {
									$successful = $successful && TRUE;
									
									//Call updateModels on OpenCPU Server using curl
									$Rcall = curl_init();
									curl_setopt($Rcall, CURLOPT_URL, "https://wlarok.ca/ocpu/library/csi3540RwebApp/R/updateModels");
									curl_setopt($Rcall, CURLOPT_POST, 1); //Call is POST
									curl_setopt($Rcall, CURLOPT_POSTFIELDS, "item_id={$item['item_id']}"); //POST variables
									curl_setopt($Rcall, CURLOPT_RETURNTRANSFER, false); //Will not wait for response (might be long)
									//error_log("Calling OpenCPU");
									curl_exec($Rcall); //Execute call
									curl_close($Rcall); //Free resource
								} else {//Error
									error_log(mysqli_error($conn));
									$successful = $successful && FALSE;
								}
								$update->close();
							} else {//Error
								error_log(mysqli_error($conn));
								$successful = $successful && FALSE;
							}
							$query->close();
							//error_log("end loop");
						}
						//error_log("successful (after) = {$successful}");
						if($successful){
							//Change http response code 
							http_response_code(201);
						} else {
							//Change http response code 
							header('HTTP/1.1 500 Internal server error');
							header('Content-Type: application/json');
							echo json_encode(["message" =>  "An error occurred during the procedure." ]);
						}
					}
				} else {
					// Invalid Request Method
					http_response_code(405);
				}
			}
			$verification->close();
		} catch(Exception $e) {
			error_log(mysqli_error($conn)); //Log error
			//Change http response code 
			header('HTTP/1.1 500 Internal server error');
			header('Content-Type: application/json');
			echo json_encode(["message" =>  "An error occurred when creating your account." ]);
		}
	}
	$conn->close(); //Close connection
?>