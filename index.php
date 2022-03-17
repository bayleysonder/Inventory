<?php
// Check if the user is logged in, if not then redirect him to login page
require_once 'auth_session.php';

// if user is logged in redirect to welcome page
if (isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] = true){
    header("location: welcome.php");
    exit;
}
?>