<?php
session_start();
$user = $_SESSION['User'];

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
// include('server.php');
update_accounts();

if (isset($_POST['Transfer'])) {
  $title     = mysqli_real_escape_string($db, $_POST['Transfer_name']);
  $desc      = mysqli_real_escape_string($db, $_POST['Transfer_desc']);
  $to_account = mysqli_real_escape_string($db, $_POST['Transfer_to']);
  $from_account = mysqli_real_escape_string($db, $_POST['Transfer_from']);
  $amnt_dirty     = mysqli_real_escape_string($db, $_POST['Transfer_amnt']);
	$amnt = number_format($amnt_dirty, 2, '.', ',');
	// array_push($errors, "NULL");


  transfer($from_account,$to_account,$amnt,$title,$desc);
	update_accounts();


  echo '<script type="text/javascript">';
  echo "alert('".$message." !');";
  echo 'window.location.href = "index.php";';
  echo '</script>';

}

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



<form style="height:100%; width:90%; margin: auto;" method="post" action="Transfer.php" enctype="multipart/form-data">
	<!-- Transfer Name -->
	<div class="input-group" style="width:100%; margin:auto; overflow: hidden;">
	    <label>Transfer Name</label>
	    <input type="text" name="Transfer_name" >
	  </div>
		<!-- Transfer Desc -->
		<div class="input-group" style="width:100%; margin:auto; overflow: hidden;">
				<label>Transfer Description</label>
				<input type="text" name="Transfer_desc" >
			</div>
		<!-- Transfer Amount -->
		<div class="input-group" style="width:100%; margin:auto; overflow: hidden;">
				<label>Transfer Amount</label>
				<input placeholder="$" type="text" name="Transfer_amnt" >
			</div>

	<!-- From account -->
			<label>Transfer From</label>
	<select style="width:100%; height:70%;" name="Transfer_from" id="Transfer_from">
		<?php
		$query = "SELECT * FROM Budget.Accounts WHERE Account_Owner = '$user' ";
		$result = mysqli_query($db, $query);
		// Counting system
		$x = 0;
		while($row = mysqli_fetch_assoc($result)) {
		$account_name = $row['Account_name'];
		echo " <option value=\"$account_name\"> $account_name </option>";
		$x++;
		}

		?>
	</select>
<!-- To account -->
	<label>Transfer To</label>
<select style="width:100%; height:70%;" name="Transfer_to" id="Transfer_to">
<?php
$query = "SELECT * FROM Budget.Accounts WHERE Account_Owner = '$user' ";
$result = mysqli_query($db, $query);
// Counting system
$x = 0;
while($row = mysqli_fetch_assoc($result)) {
$account_name = $row['Account_name'];
echo " <option value=\"$account_name\"> $account_name </option>";
$x++;
}

?>
</select>

<div class="input-group">
	<button style="margin-left:75%;" type="submit" class="btn-login" name="Transfer">Complete Transfer</button>
</div>

</form>


</body>
</html>
