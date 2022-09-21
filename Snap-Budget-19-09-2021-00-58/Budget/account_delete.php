<?php
session_start();
// include('server.php');
include('functions.php');
$name = $_GET['item'];


$delete = "DELETE FROM Budget.Accounts WHERE Account_name = '$name' AND Account_owner = '$user'";
mysqli_query($db, $delete);

echo '<script type="text/javascript">';
echo "alert('Account deleted: ".$name." !');";
echo 'window.location.href = "index.php";';
echo '</script>';

?>
