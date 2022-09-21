<?php
session_start();
// include('server.php');
include('functions.php');
$name = $_GET['item'];

$select = "SELECT uid FROM Budget.Bills WHERE Bill_owner = '$user' AND Bill_title LIKE '%$name%' LIMIT 1";
$result = mysqli_query($db, $select);
$data = mysqli_fetch_assoc($result);
$uid = $data['uid'];
$delete = "DELETE FROM Budget.Bills WHERE uid = '$uid'";
mysqli_query($db, $delete);

echo '<script type="text/javascript">';
echo "alert('Bill deleted: ".$name." !');";
echo 'window.location.href = "bills.php";';
echo '</script>';

?>
