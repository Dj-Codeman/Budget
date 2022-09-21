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

  <div>
  <form style="height:100%; width:90%; margin: auto;" method="post" action="addaccount.php" enctype="multipart/form-data">
    <?php include('errors.php'); ?>
  <!-- Event Name -->
  <div class="input-group" style="width:50%; margin:auto;">
      <label>Account name</label>
      <input type="text" name="Account_name" >
    </div>
  <!-- Event Description -->
  <div class="input-group" style=" position: relative; width:50%;  margin:auto;">
      <label>Account Type</label>
      <textarea class="input-group" type="text" style="height:5%;" name="Account_type" ></textarea>
    </div>
    <!-- Event Description -->
    <div class="input-group" style=" position: relative; width:50%;  margin:auto;">
        <label>Current Balance</label>
        <textarea class="input-group" type="text" style="height:5%;" name="Account_balance" ></textarea>
      </div>
      <!-- Event Description -->
      <div class="input-group" style=" position: relative; width:50%;  margin:auto;">
          <label>Account Number</label>
          <textarea placeholder="No, it doesn't have to be the real number" class="input-group" type="text" style="height:5%;" name="Account_Number" ></textarea>
        </div>

				<!-- event head email -->
				<div class="input-group" style="width:50%; margin:auto;">
						<label>Color code account ?</label>
						<input style="left:0;" type="checkbox" name="monitor">
					</div>

        <div class="input-group">
          <button style="margin-left:25%;" type="submit" class="btn-login" name="addaccount">Add Account</button>
        </div>
  </form>

  </body>
  </html>

  <?php
  if (isset($_POST['addaccount'])) {
  $name            = mysqli_real_escape_string($db, $_POST['Account_name']);
  $type            = mysqli_real_escape_string($db, $_POST['Account_type']);
  $cur_balance     = mysqli_real_escape_string($db, $_POST['Account_balance']);
  $number          = mysqli_real_escape_string($db, $_POST['Account_Number']);
  // $monitor          = mysqli_real_escape_string($db, $_POST['monitor']);
	if ($_POST['monitor'] == 'Yes') {
		$monitor = '1';
	} else {
		$monitor = '0';
	}
	add_account($name,$type,$cur_balance,$number,$monitor);


    echo '<script type="text/javascript">';
    echo "alert('Account Created !');";
    echo 'window.location.href = "index.php";';
    echo '</script>';
  }
  ?>
