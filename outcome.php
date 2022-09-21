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


<div>
<form style="height:100%; width:90%; margin: auto;" method="post" action="outcome.php" enctype="multipart/form-data">
  <?php include('errors.php'); ?>
<!-- Event Name -->
<div class="input-group" style="width:50%; margin:auto; overflow: hidden;">
    <label>Expense Name</label>
    <input type="text" name="Expense_name" >
  </div>
<!-- Event Description -->
<div class="input-group" style=" position: relative; width:50%;  margin:auto;">
    <label>Expense Description</label>
    <textarea class="input-group" type="text" style="height:5%;" name="Expense_desc" ></textarea>
  </div>


<!-- event head -->
<div class="input-group" style="width:50%; margin:auto;">
    <label>Expense Category</label>
		<select style="width:100%; height:70%;" name="Expense_catg" id="Expense_catg">

			<?php
			$query = "SELECT * FROM Budget.Budget_$user ";
			$result = mysqli_query($db, $query);
			// Counting system
			$x = 1;
			while($row = mysqli_fetch_assoc($result)) {
			$category = $row['Category'];
			$san_category = display_category($category);
			echo " <option value=\"$category\"> $x. $san_category </option>";
			$x++;
			}


			?>


		</select>
  </div>

<div class="input-group" style="width:50%; margin:auto;">
	<label>Account</label>
<select style="width:100%; height:70%;" name="Account_name" id="Account_name">
<?php
$query = "SELECT * FROM Budget.Accounts WHERE Account_Owner = '$user' ";
$result = mysqli_query($db, $query);
// Counting system
$x = 1;
while($row = mysqli_fetch_assoc($result)) {
$account_name = $row['Account_name'];
echo " <option value=\"$account_name\"> $x. $account_name </option>";
$x++;
}

?>
</select>
  </div>
  <!-- event head email -->
  <div class="input-group" style="width:50%; margin:auto;">
      <label>Amount</label>
      <input type="text" name="Expense_amount">
    </div>


  <div class="input-group">
    <button style="margin-left:25%;" type="submit" class="btn-login" name="Expense">Add Transactions</button>


  </div>


</form>

</body>
</html>

<?php
if (isset($_POST['Expense'])) {
  $title     = mysqli_real_escape_string($db, $_POST['Expense_name']);
  $desc      = mysqli_real_escape_string($db, $_POST['Expense_desc']);
  $acnt_name = mysqli_real_escape_string($db, $_POST['Account_name']);
  $amnt      = mysqli_real_escape_string($db, $_POST['Expense_amount']);
	$catg      = mysqli_real_escape_string($db, $_POST['Expense_catg']);
	$flag      = "name";
  // array_push($errors, "NULL");

  withdrawal($title,$desc,$acnt_name,$amnt,$catg,$flag);
	update_accounts();

  if ($status == '0') {
  echo '<script type="text/javascript">';
  echo "alert('Expense Submitted ".$amnt." !');";
  echo 'window.location.href = "index.php";';
  echo '</script>';
}






}




 ?>
