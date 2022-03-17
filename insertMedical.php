<?php 
include 'config.php'; //database connection

if(isset($_POST['submit'])){
	if(!empty($_POST['assetTag']) && !empty($_POST['serialNumber'])
        && !empty($_POST['name']) && !empty($_POST['itemDesc'])) { //checks to see if all input fields have data
			
			$assetTag = $_POST['assetTag'];
			$serialNumber = $_POST['serialNumber'];
			$name = $_POST['name'];
			$itemDesc = $_POST['itemDesc'];
            $date = $_POST['date'];

			$query = "INSERT INTO `medicalsupplies` (`assetTag`, `serialNum`, `name`, `itemDesc`, `followUp`) 
                values('$assetTag', '$serialNumber', '$name', '$itemDesc', '$date')";	//runs insert query based off of user input
				
				
				if ($run = $conn->query($query)){ //Run checks query output to database
					echo "query is set";
				} else{
					echo "$assetTag', '$serialNumber', '$name', '$itemDesc', '$date'";
					echo '<script>alert("Query failed")</script>';
				}
	} else{
		echo "all fields required";
	}
	
	header( "refresh:0.1; url=tracking.php" ); //wait for .1 second before redirecting to Inventory page
}
?>