<?php
session_start();

if (!isset($_SESSION['auth'])) {
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
}
if (isset($_GET['logout'])) {
	session_destroy();
	unset($_SESSION['auth']);
	header("location: login.php");
}

include('functions.php');
update_accounts();
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

</head>
<body>

<div class="header" style="border-bottom: 8px solid #f2e711;">
    <h2 class="Welcome">Nigga rigged accounting (beta)</h2>
    <div class="relative">
    <style>

    a {
      text-decoration: none;
      color: #ffffff;
    }
    </style>


  </div>


  <?php $site = basename(__FILE__, '.php');
	include('server.php');
	home_bar($site);
	?>


<!-- table style  -->
  <style>
  table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  }
  </style>


 </div>
 <form style="height:100%; width:90%; margin: auto;" method="post" action="check.php" enctype="multipart/form-data">
   <?php include('errors.php'); ?>
   <!-- Event Name -->
   <div class="input-group" style="width:50%; margin:auto; overflow: hidden;">
       <label>Net income</label>
       <input type="text" name="Net" >
     </div>

     <div class="input-group">
       <button style="margin-left:25%;" type="submit" class="btn-login" name="Deposit">Add Check</button>
     </div>

 </form>

 <?php
 if (isset($_POST['Deposit'])) {
 	$net   = mysqli_real_escape_string($db, $_POST['Net']);
 	// array_push($errors, "NULL");


	clear_slate();
	reset_budget();
  if (create_budget($net)){
		input_check($net);
		echo '<script type="text/javascript">';
		echo "alert('Check Submitted ".$net." !');";
		echo 'window.location.href = "index.php";';
		echo '</script>';
	}







 }

  ?>
