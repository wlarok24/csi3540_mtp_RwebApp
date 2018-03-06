<?php
	/*
		CSI3540 - Projet
		Par : William LaRocque (8397424)
		Script pour populer la base de données
	*/
	require(dirname(__FILE__)."/../www/api/apiCredentials.php");
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		echo "Database connection error!\n";
	} else {
		$userID = 0;
		$eggsID = 0;
		$carrotsID = 0;
		//1. Create test user
		$salt = strval(md5(strval(rand()), FALSE));
		$hashed_password = strval(hash("sha256", "password" . $salt, FALSE));
		$insert = "INSERT INTO user (name, email, salt, password) VALUES(\"Test user\", 
			\"test_user@example.com\", \"{$salt}\", \"{$hashed_password}\")";
		if ($conn->query($insert) === TRUE) {
			echo "Test user was created\n";
			$userID = mysqli_insert_id($conn); //Get id of inserted user
			//2. Create two items for that user
			$insert = "INSERT INTO item (user_id, name, unit, usual_use_size, tracked_since, inventory, estimated_daily_use) 
						VALUES(\"{$userID}\", \"Eggs\", \"unit\", \"2\", \"27022018\",\"16\", \"0.750\");";
			if ($conn->query($insert) === TRUE) {
				echo "Item Eggs was created\n";
				$eggsID = mysqli_insert_id($conn);
			} else {
				echo "Item Eggs was not created.\n";
			}
			$insert = "INSERT INTO item (user_id, name, unit, usual_use_size, tracked_since, inventory, estimated_daily_use) 
						VALUES(\"{$userID}\", \"Carrots\", \"unit\", \"1\", \"03032018\",\"10\", \"1.3333333\");";
			if ($conn->query($insert) === TRUE) {
				echo "Item Carrots was created\n";
				$carrotsID = mysqli_insert_id($conn);
			} else {
				echo "Item Carrots was not created.\n";
			}
			//3. Add use data for those items
			if($eggsID != 0){
				//If eggs were successfully inserted
				$insert = "INSERT INTO item_use (item_id, date_nbr, date, qty) VALUES
				(\"{$eggsID}\", \"1\", \"27022018\", \"2\"),
				(\"{$eggsID}\", \"5\", \"03032018\", \"4\");";
				if ($conn->query($insert) === TRUE) {
					echo "Use data for Eggs inserted.\n";
				} else {
					echo "Use data for Eggs not inserted.\n";
				}
			}
			if($carrotsID != 0){
				//If carrots were successfully inserted
				$insert = "INSERT INTO item_use (item_id, date_nbr, date, qty) VALUES
				(\"{$carrotsID}\", \"1\", \"03032018\", \"1\"),
				(\"{$carrotsID}\", \"2\", \"04032018\", \"1\"),
				(\"{$carrotsID}\", \"4\", \"06032018\", \"2\");";
				if ($conn->query($insert) === TRUE) {
					echo "Use data for Carrots inserted.\n";
				} else {
					echo "Use data for Carrots not inserted.\n";
				}
			}
		} else {
			echo "Test user was not created. Exiting script.\n";
			$continue = false;
		}
		echo "Script Finished. Test user credentials are \"test_user@example.com\" with password \"password\"\n";
	}
?>