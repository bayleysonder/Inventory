<?php 

// connect to database

    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $db = "inv";

    // using oop php
    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n" . $conn -> error);

?>
