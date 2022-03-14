<?php 
require 'config.php';
include 'auth_session.php';

$id = $_REQUEST['id'];

$sql = "SELECT * FROM checkout WHERE id='".$id."'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Update Record</title>
<link rel="stylesheet" href="css/style.css" />
</head>

<body>
<?php echo $row['id']; ?>
</body>

</html>