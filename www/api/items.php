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
					/**$select = "SELECT id, name, inventory, unit, usual_use_size, IF(slope_days IS NOT NULL, slope_days, estimated_daily_use) as model, tracked_since
						FROM item WHERE user_id = {$_GET["user_id"]}";**/
					$select = $conn->prepare("SELECT id, name, inventory, unit, usual_use_size, IF(slope_days IS NOT NULL, slope_days, estimated_daily_use) as model, tracked_since FROM item WHERE user_id = ?");
					$select->bind_param("s", $_GET["user_id"]);
					$select->execute();
					$result = $select->get_result()->fetch_all(MYSQLI_ASSOC);
					header('Content-Type: application/json');
					echo json_encode($result);
					$select->close();
				} else if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					if(!isset($_POST['name']) && !isset($_POST['unit']) && !isset($_POST['usual_use_size']) && !isset($_POST['inventory']) && !isset($_POST['estimated_daily_use'])){
						http_response_code(400);
					} else {
						/**$insert = "INSERT INTO item (user_id, name, unit, usual_use_size, tracked_since, inventory, estimated_daily_use) 
							VALUES(\"{$_GET["user_id"]}\", \"{$_POST['name']}\", \"{$_POST['unit']}\", \"{$_POST['usual_use_size']}\", \"{$dateDDMMYYYY}\",
							\"{$_POST['inventory']}\", \"{$_POST['estimated_daily_use']}\");";**/
						$insert = $conn->prepare("INSERT INTO item (user_id, name, unit, usual_use_size, tracked_since, inventory, estimated_daily_use) VALUES( ?, ?, ?, ?, ?, ?, ?)");
						$insert->bind_param("sssiid", $_GET["user_id"], $_POST['name'], $_POST['unit'], $_POST['usual_use_size'], $dateDDMMYYYY, $_POST['inventory'], $_POST['estimated_daily_use']);
						$insert->execute();
						if ($insert->affected_rows === 1) {
							//Change http response code 
							http_response_code(201);
						} else {//Error
							//Change http response code 
							error_log("Error in item insertion!");
							header('HTTP/1.1 500 Internal server error');
							header('Content-Type: application/json');
							echo json_encode(["message" =>  "An error occurred during the procedure." ]);
						}
						$insert->close();
					}
				} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE'){
					//Will use the url to pass the information (can use $_GET)
					if(isset($_GET['item_id'])){
						/**$delete = "DELETE FROM item WHERE id = {$_GET['item_id']}";**/
						$delete = $conn->prepare("DELETE FROM item WHERE id = ?");
						$delete->bind_param("s", $_GET['item_id']);
						$delete->execute();
						if ($delete->affected_rows === 1) {
							//Change http response code 
							http_response_code(204);
						} else {//Error
							//Change http response code 
							error_log("Error in item deletion!");
							header('HTTP/1.1 500 Internal server error');
							header('Content-Type: application/json');
							echo json_encode(["message" =>  "An error occurred during the procedure."]);
						}
						$delete->close();
					} else {
						//Change http response code 
						http_response_code(400);//Bad request
					}
				} else if ($_SERVER['REQUEST_METHOD'] == 'PATCH'){
					//Will use the url to pass the information (can use $_GET)
					if(isset($_GET['item_id'])&&isset($_GET['item_qty'])){
						/**$update = "UPDATE item SET inventory = {$_GET['item_qty']} WHERE id = {$_GET['item_id']}";**/
						$update = $conn->prepare("UPDATE item SET inventory = ? WHERE id = ?");
						$update->bind_param("is", $_GET['item_qty'], $_GET['item_id']);
						$update->execute();
						if ($update->affected_rows === 1) {
							//Change http response code 
							http_response_code(200);
						} else {//Error
							//Change http response code 
							error_log("Error in item inventory updating!");
							header('HTTP/1.1 500 Internal server error');
							header('Content-Type: application/json');
							echo json_encode(["message" =>  "An error occurred during the procedure."]);
						}
						$update->close();
					} else {
						//Change http response code 
						http_response_code(400);//Bad request
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
