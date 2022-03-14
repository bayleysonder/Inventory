<?php
require 'config.php';
include 'auth_session.php';
date_default_timezone_set('America/Los_Angeles');
$elevated_err = $_REQUEST['elevated_err'];

if (empty($elevated_err)) {
    $id = $_REQUEST['id'];
    $assetTag = $_REQUEST["assetTag"];
    $date = date('Y-m-d');

    // update checkout table with current date for return date
    $sql = "UPDATE checkout SET returnDate = '$date' WHERE id = $id";

    if ($conn->query($sql)) {
        echo 'qry set';
    } else {
        echo 'failed return item in checkout';
        echo $date;
    }

    // update asset quantity in inventory
    $sql = "UPDATE assets SET qtyOut = 0 WHERE assetTag = '$assetTag'";
    if ($conn->query($sql)) {
        echo 'qry set';
    } else {
        echo "failed to update qtyOut for '$assetTag' in assets";
    }

    header('location: computers.php');
}
?>