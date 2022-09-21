<?php
session_start();
include('functions.php');
if (!isset($_SESSION['auth'])) {
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
}
if (isset($_GET['logout'])) {
	session_destroy();
	unset($_SESSION['auth']);
	header("location: login.php");
}

update_accounts();

$query = "SELECT 1 FROM Budget.Category_$user LIMIT 1";
$val = mysqli_query($db, $query);


if($val == FALSE)
{
create_default_category();
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

  <!-- Test table with db data -->
  <table style="margin-left:10%; width: 80%; border-radius:0px;" border="2">
  <tbody>
  <tr>
  <strong>
  <td style="text-align:center;"><strong>Category </strong></td>
  <td style="width:10%; text-align:center;"><strong>Delete ?</strong></td>
  <!-- <td><strong>Current set budget</strong></td> -->
  </tr>
  <?php
  summary_category();
  ?>
  </tbody>
  </table>

  <h2 style="text-align:center" class="Welcome">Add categories</h2>
  <form style="height:100%; width:90%; margin: auto;" method="post" action="category.php" enctype="multipart/form-data">
    <?php include('errors.php'); ?>

  <!-- event head email -->
  <div class="input-group" style="width:50%; margin:auto;">
      <label>Category name</label>
      <input type="text" name="category">
    </div>

  <div class="input-group">
    <button style="margin-left:25%;" type="submit" class="btn-login" name="category_add">Submit Change</button>

  </div>
</form>

</body>
</html>

<?php
if (isset($_POST['category_add'])) {
  $name = mysqli_real_escape_string($db, $_POST["category"]);
	// string sanatized in function.
  if (category_add($name)) {
  echo '<script type="text/javascript">';
  echo "alert('Category created ".$name." !');";
  echo 'window.location.href = "category.php";';
  echo '</script>';
}
}
?>
