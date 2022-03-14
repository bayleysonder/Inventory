<?php
include 'config.php'; //database connection
	if (isset($_POST['submit'])) {
		//checks to see if all input fields have data
		if (!empty($_POST['assetid']) && !empty($_POST['empid'])
			&& !empty($_POST['qtyOut']) && !empty($_POST['dateOut'])) {

			$assetid = $_POST['assetid'];
			$empid = $_POST['empid'];
			$qtyOut = $_POST['qtyOut'];
			$dateOut = $_POST['dateOut'];

			// runs insert query based off of user input
			$sql = "INSERT INTO `checkout` (`assetid`, `empid`, `qtyOut`, `dateOut`) 
                values('$assetid', '$empid', '$qtyOut', '$dateOut')";

			// checks query output to database
			if ($run = $conn->query($sql)) {
				echo "query is set";
			} else {
				echo "$assetTag', '$serialNumber', '$name', '$itemDesc', '$date'";
				echo '<script>alert("Query failed")</script>';
			}

			$sql = "UPDATE assets SET qtyOut = $qtyOut WHERE id = $assetid";
			$conn->query($sql);
		} else {
			echo "all fields required";
		}

		header("refresh:0.1; url=computers.php"); //wait for .1 second before redirecting to Inventory page
	}


//header("refresh:0.1; url=computers.php"); //wait for .1 second before redirecting to Inventory page
?>