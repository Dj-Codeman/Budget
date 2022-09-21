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

$query = "SELECT 1 FROM Budget.Target_$user LIMIT 1";
$val = mysqli_query($db, $query);


if($val == FALSE)
{
create_default_target();
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
	<td><strong>Category </strong></td>
	<td><strong>Current set budget</strong></td>
	</tr>
	<?php
	summary_budget();
	?>
	</tbody>
	</table>

	<form style="height:100%; width:90%; margin: auto;" method="post" action="budget.php" enctype="multipart/form-data">
		<?php include('errors.php'); ?>

		<?php
		  $bills = calc_bills($user);
			$query = "UPDATE Budget.Target_$user SET Amount = '$bills' WHERE Category = 'Bills'";
			mysqli_query($db,$query);

		// second loop taking all incomes matching account number
		$query = "SELECT * FROM Budget.Budget_$user";
		$result = mysqli_query($db, $query);
		while($row = mysqli_fetch_assoc($result)) {
		  $set_amount = $row['Created'];
		  // $amount = number_format($set_amount, 2, '.', '');
		  $amount = convert_number($set_amount);
		  $budget_total += $amount;
		  // printf("$budget_total \n");
		}

		echo "<H2> Changing your budget? Your current cycle budget is: $budget_total </H2>";
		?>
		<div>
		<!-- event head -->
		<form style="height:100%; width:90%; margin: auto;" method="post" action="bills.php" enctype="multipart/form-data">
			<?php include('errors.php'); ?>

				<?php
				$array = category_array();
				foreach ($array as $value) {
					if ( $value != 'Bills' ) {
				 $tmp = convert_number(fetch_target($value));
				 $san_value = sanatize_category($value);
				 echo "<div class=\"input-group\" style=\"width:50%; margin:auto;\"> ";
				 echo "<lable> $value </lable>";
				 echo "<input type=\"number\" placeholder=\"$tmp\" value=\"$tmp\" name=\"amount_$san_value\" step=\"0.25\" min=\"0\" >";
				 echo "</div>";
			 		}
				}

				?>


			<div class="input-group">
				<button style="margin-left:25%;" type="submit" class="btn-login" name="budget_update">Submit Change</button>

			</div>
		</form>



</body>
</html>

<?php
if (isset($_POST['budget_update'])) {
	$array = category_array();
	foreach ($array as $value) {
	 $san_value = sanatize_category($value);
	 $tmp = convert_number(mysqli_real_escape_string($db, $_POST["amount_$san_value"]));
	 $query = "UPDATE Budget.Target_$user SET Amount = '$tmp' WHERE Category = '$san_value'";
	 mysqli_query($db,$query);

	 echo '<script type="text/javascript">';
	 echo "alert('Budget set for ".$value." !');";
	 echo 'window.location.href = "bills.php";';
	 echo '</script>';
 }
}




?>
