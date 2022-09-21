<?php
session_start();
// include('server.php');
include('functions.php');
$uid = $_GET['item'];
$uid = sanatize_category($uid);
$query = "DELETE FROM Budget.Category_$user WHERE Category = '$uid'";
mysqli_query($db, $query);
$query = "DELETE FROM Budget.Target_$user WHERE Category = '$uid'";
mysqli_query($db, $query);

$tmp = display_category($uid);
echo '<script type="text/javascript">';
echo "alert('Category deleted ".$tmp." !');";
echo 'window.location.href = "category.php";';
echo '</script>';

?>
