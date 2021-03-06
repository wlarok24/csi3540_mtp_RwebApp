<?php
	require("apiCredentials.php");
	$conn = new mysqli($servername, $username, $password, $dbname);
	$dateDDMMYYYY = date("d") . date("m") . date("Y");
	// Check connection
	if ($conn->connect_error) {
		http_response_code(500);
	} else {
		/* Define the different functions */
		/*
			Function use for the signup functionnality
			Uses $_POST parameters
			Responds using http response with a message included as data
		*/
		function signup(){
			global $conn;
			if(!empty($_POST["name"]) && !empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["passwordRepeat"])){
				if($_POST["password"] != $_POST["passwordRepeat"]){
					//Change http response code 
					header('HTTP/1.1 400 Bad Request');
					echo json_encode(["message" => "The two passwords don't match."]);
				} else {
					//Create the salt and the hashed password
					$salt = strval(md5(strval(rand()), FALSE));
					$hashed_password = strval(hash("sha256", $_POST['password'] . $salt, FALSE));
					/**$insert = "INSERT INTO user (name, email, salt, password) VALUES(\"{$_POST['name']}\", 
						\"{$_POST['email']}\", \"{$salt}\", \"{$hashed_password}\")";
					if ($conn->query($insert) === TRUE) {
						//Change http response code 
						header('HTTP/1.1 201 Created');
						echo json_encode(["message" => "Your account was created!"]);
					} else {
						error_log(mysqli_error($conn)); //Log error
						//Change http response code 
						header('HTTP/1.1 500 Internal server error');
						echo json_encode(["message" =>  "An error occurred when creating your account." ]);
					}**/
					//Prepared statement to protect against SQL injections
					try{
						$insert = $conn->prepare("INSERT INTO user (name, email, salt, password) VALUES(?, ?, ?, ?)");
						$insert->bind_param("ssss", $_POST['name'], $_POST['email'], $salt, $hashed_password);
						$insert->execute();
						//Query successful
						//Change http response code 
						header('HTTP/1.1 201 Created');
						header('Content-Type: application/json');
						echo json_encode(["message" => "Your account was created!"]);
						$insert->close();
					} catch(Exception $e) {
						error_log(mysqli_error($conn)); //Log error
						//Change http response code 
						header('HTTP/1.1 500 Internal server error');
						header('Content-Type: application/json');
						echo json_encode(["message" =>  "An error occurred when creating your account." ]);
					}
				}
			} else {
				//Change http response code 
				header('HTTP/1.1 400 Bad Request');
				header('Content-Type: application/json');
				echo json_encode(["message" => "Please fill out all the fields"]);
			}
		}
		/*
			Function use for the signin functionnality
			@param data parameters (associative array)
			Responds using http response with the user id and token as data (if successful)
		*/
		function signin(){
			global $conn;
			if(!empty($_POST["login-email"]) && !empty($_POST["login-password"])){ 
				/**$query = "SELECT id, name, email, salt, password FROM user where email = '" . $_POST["login-email"] . "'";
				$user = mysqli_fetch_array(mysqli_query($conn, $query));**/
				//Prepared statement to protect against SQL injections
				try{
					$query = $conn->prepare("SELECT id, name, email, salt, password FROM user where email = ?");
					$query->bind_param("s", $_POST["login-email"]);
					$query->execute();
					
					//Query successful
					$user = $query->get_result()->fetch_assoc();
					if($user){ //query got a valid user
						$hashed_password = strval(hash("sha256", $_POST["login-password"] . $user['salt'], FALSE));
						if($hashed_password == $user['password']){ //user provided the correct password
							//Generate the token
							$token = strval(md5(strval(rand()), FALSE));
							//$changeToken = "UPDATE user SET token = '{$token}' WHERE email = '{$_POST["login-email"]}'";
							$changeToken = $conn->prepare("UPDATE user SET token = ? WHERE email = ?");
							$changeToken->bind_param("ss", $token, $_POST["login-email"]);
							$changeToken->execute();
							if ($changeToken->affected_rows === 1) {
								//Change http response code 
								header('HTTP/1.1 200 OK');
								header('Content-Type: application/json');
								echo json_encode(["user_name" => $user['name'],
									"user_email" => $user['email'],
									"user_id" => $user['id'],
									"user_token" => $token]); 
							} else {
								error_log("Token update unsuccessful!"); //Log error
								//Change http response code 
								header('HTTP/1.1 500 Internal server error');
								header('Content-Type: application/json');
								echo json_encode(["message" =>  "An error occurred during the login procedure." ]);
							}
							$changeToken->close();
						} else {
							//Change http response code 
							header('HTTP/1.1 400 Bad Request');
							header('Content-Type: application/json');
							echo json_encode(["message" => "Invalid password"]);
						}
					} else {
						//Change http response code 
						header('HTTP/1.1 400 Bad Request');
						header('Content-Type: application/json');
						echo json_encode(["message" => "Invalid email or password"]);
					}
					$query->close();
				} catch(Exception $e) {
					error_log(mysqli_error($conn)); //Log error
					//Change http response code 
					header('HTTP/1.1 500 Internal server error');
					header('Content-Type: application/json');
					echo json_encode(["message" =>  "An error occurred when creating your account." ]);
				}
			} else {
				//Change http response code 
				header('HTTP/1.1 400 Bad Request');
				header('Content-Type: application/json');
				echo json_encode(["message" => "Please enter both your email and password."]);
			}
		}
		function changePassword(){
			global $conn;
			if(!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['new_passwordRepeat'])
			  && ($_POST['new_password'] == $_POST['new_passwordRepeat'])){
				/**$query = "SELECT name, email, salt, password FROM user where id = '{$_POST["user_id"]}' and token = '{$_POST["user_token"]}'";
				$user = mysqli_fetch_array(mysqli_query($conn, $query));**/
				//Prepared statement to protect against SQL injections
				try{
					$query = $conn->prepare("SELECT name, email, salt, password FROM user where id = ? and token = ?");
					$query->bind_param("ss", $_POST["user_id"], $_POST["user_token"]);
					$query->execute();
					//Query successful
					$user = $query->get_result()->fetch_assoc();
					//error_log(json_encode($user)); //Log error
					if($user){ //query got a valid user
						$old_hashed_password = strval(hash("sha256", $_POST['old_password'] . $user['salt'], FALSE));
						if($old_hashed_password == $user['password']){
							//error_log("Old password ok"); //Log error
							//user provided the correct password
							$new_hashed_password = strval(hash("sha256", $_POST['new_password'] . $user['salt'], FALSE));
							//$update = "UPDATE user SET password = '{$new_hashed_password}' WHERE id = '{$_POST["user_id"]}'";
							$update = $conn->prepare("UPDATE user SET password = ? WHERE id = ?");
							$update->bind_param("ss", $new_hashed_password, $_POST["user_id"]);
							$update->execute();
							//error_log($update->affected_rows);
							if ($update->affected_rows === 1) {
								//Change http response code 
								header('HTTP/1.1 200 OK');
								header('Content-Type: application/json');
								echo json_encode(["message" => "Your password was changed."]);
							} else {
								error_log("Password update unsuccessful!"); //Log error
								//Change http response code 
								header('HTTP/1.1 500 Internal server error');
								header('Content-Type: application/json');
								echo json_encode(["message" =>  "An error occurred during the password change." ]);
							}
							$update->close();
						} else {
							//Change http response code 
							header('HTTP/1.1 403 Bad Request');
							header('Content-Type: application/json');
							echo json_encode(["message" => "Invalid password"]);
						}
					} else {
						//Change http response code 
						header('HTTP/1.1 401 Unauthorized');
						header('Content-Type: application/json');
						echo json_encode(["message" => "Invalid session"]);
					}
					$query->close();
				} catch(Exception $e) {
					error_log(mysqli_error($conn)); //Log error
					//Change http response code 
					header('HTTP/1.1 500 Internal server error');
					header('Content-Type: application/json');
					echo json_encode(["message" =>  "An error occurred when creating your account." ]);
				}
			} else {
				if($_POST['new_password'] == $_POST['new_passwordRepeat']){
					//The fields were not all filled
					//Change http response code 
					header('HTTP/1.1 400 Bad Request');
					header('Content-Type: application/json');
					echo json_encode(["message" => "Please fill out all the fields in order to change your password."]);
				} else {
					//Change http response code 
					header('HTTP/1.1 400 Bad Request');
					header('Content-Type: application/json');
					echo json_encode(["message" => "Please ensure that both password fields match."]);
				}
			}
		}
		/* Respond to the different requests */
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			//error_log(json_encode($_POST)); //Log POST
			if($_GET["op"] == "signup"){
				signup();				
			} else if ($_GET["op"] == "signin"){
				signin();
			} else if ($_GET["op"] == "changepwd"){
				changePassword();
			} else if ($_GET["op"] == "changeinfo"){
				// Not implemented
				http_response_code(501);
			} else {
				// Invalid Request Method
				http_response_code(405);
			}
		} else {
			// Invalid Request Method
			http_response_code(405);
		}
	}
	$conn->close(); //Close connection
?>