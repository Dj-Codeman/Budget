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

    <h2 style="text-align:center" class="Welcome">Summary of bills</h2>
  <!-- table style  -->
    <style>
    table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    }
    </style>


    <!-- Test table with db data -->
    <table style="margin-left:0%; width: 100%; border-radius:0px; text-align:center;" border="2">
    <tbody>
    <tr>
    <strong>
    <td><strong>Bill Name</strong></td>
    <td><strong>Bill Amount</strong></td>
    <td><strong>Bill D.O.M</strong></td>
    <td><strong>Delete ?</strong></td>
    </tr>
    <?php
    summary_bills();
    ?>
    </tbody>
    </table>
  </br>
    <h2 style="text-align:center" class="Welcome">Update bills</h2>

    <div>
    <form style="height:100%; width:90%; margin: auto;" method="post" action="bills.php" enctype="multipart/form-data">
      <?php include('errors.php'); ?>

      <!-- event head -->
      <div class="input-group" style="width:50%; margin:auto;">
          <label>Select the bill</label>
      		<select style="width:100%; height:70%;" name="bill_name" id="bill_name">

      			<?php
      			$query = "SELECT * FROM Budget.Bills WHERE Bill_owner = '$user'";
      			$result = mysqli_query($db, $query);
      			// Counting system
      			$x = 1;
      			while($row = mysqli_fetch_assoc($result)) {
      			$name = $row['Bill_title'];
      			echo " <option value=\"$name\"> $x. $name </option>";
      			$x++;
      			}
      			?>
          </select>
        </div>

        <!-- event head email -->
        <div class="input-group" style="width:50%; margin:auto;">
            <label>Set new amount</label>
            <input type="text" name="bill_amount">
          </div>

        <div class="input-group">
          <button style="margin-left:25%;" type="submit" class="btn-login" name="bill_update">Submit Change</button>

        </div>
      </form>

			<!--  THE FOR FOR CREATING THE BUDGET DATE -->

			<h2 style="text-align:center" class="Welcome">Setting your budget timeframe</h2>

			<div>
			<form style="height:100%; width:90%; margin: auto;" method="post" action="bills.php" enctype="multipart/form-data">
				<?php include('errors.php'); ?>

					<!-- event head -->
					<div class="input-group" style="width:50%; margin:auto;">
							<label>number of days in your pay cycle</label>
							<select style="width:100%; height:70%;" name="budget_day" id="budget_day">
							<?php
							$size = 30;
	      			for($i = 0; $i < $size; $i++) {
	      			echo " <option value=\"$i\"> $i </option>";
	      			}
	      			?>
							</select>
						</div>

						<!-- event head email -->
						<div class="input-group" style="width:50%; margin:auto;">
								<label>Last pay day</label>
								<input type="date" name="last_day">
							</div>

							<!-- event head email -->
							<div class="input-group" style="width:50%; margin:auto;">
									<label>Last check</label>
									<input type="text" name="last_pay">
								</div>

						<div class="input-group">
							<button style="margin-left:25%;" type="submit" class="btn-login" name="budget_update">Submit Change</button>

						</div>
					</form>

<!-- Creating New bills -->

<h2 style="text-align:center" class="Welcome">Adding new bills</h2>

<div>
<form style="height:100%; width:90%; margin: auto;" method="post" action="bills.php" enctype="multipart/form-data">
	<?php include('errors.php'); ?>

	<div class="input-group" style="width:50%; margin:auto;">
			<label>Name of the bill</label>
			<input type="text" name="name">
		</div>


		<div class="input-group" style="width:50%; margin:auto;">
				<label>Description</label>
				<input type="text" name="description">
			</div>

			<div class="input-group" style="width:50%; margin:auto;">
				<label>Account</label>
			<select style="width:100%; height:70%;" name="account" id="account">
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

				<div class="input-group" style="width:50%; margin:auto;">
						<label> Amount </label>
						<input type="number" name="amount" step="0.25" min="0">
					</div>


		<!-- event head -->
		<div class="input-group" style="width:50%; margin:auto;">
				<label>Day of the month charged</label>
				<select style="width:100%; height:70%;" name="charge_day" id="charge_day">
				<?php
				$size = 31;
				for($i = 1; $i < $size; $i++) {
				echo " <option value=\"$i\"> $i </option>";
				}
				?>
				</select>
			</div>

			<!-- event head email -->
			<div class="input-group" style="width:50%; margin:auto;">
					<label>Split between check ?</label>
					<input style="left:0;" type="checkbox" name="split">
				</div>


			<div class="input-group">
				<button style="margin-left:25%;" type="submit" class="btn-login" name="budget_bill">Submit Bill</button>

			</div>
		</form>
	<!-- DONE -->
    </body>

    <?php
    if (isset($_POST['bill_update'])) {
      $name     = mysqli_real_escape_string($db, $_POST['bill_name']);
      $price      = mysqli_real_escape_string($db, $_POST['bill_amount']);

      $query = "SELECT uid FROM Budget.Bills WHERE Bill_title = '$name'";
      $result = mysqli_query($db, $query);
      $data = mysqli_fetch_assoc($result);
      $bill_id = $data['uid'];
      update_bill($name, $price, $bill_id);
			}

  	if (isset($_POST['budget_update'])) {
			$term_num     = mysqli_real_escape_string($db, $_POST['budget_day']);
			$term = "$term_num days";
			$date     = mysqli_real_escape_string($db, $_POST['last_day']);
			$paid     = mysqli_real_escape_string($db, $_POST['last_pay']);

			// echo "$user $date $term $term_num $paid";
			create_budget_entry($user, $date, $term, $term_num, $paid);

		}

		if (isset($_POST['budget_bill'])) {

			$charge_day  = mysqli_real_escape_string($db, $_POST['charge_day']);
			$charge_amnt = mysqli_real_escape_string($db, $_POST['amount']);
			$charge_acnt = mysqli_real_escape_string($db, $_POST['account']);
			$charge_desc = mysqli_real_escape_string($db, $_POST['description']);
			$charge_name = mysqli_real_escape_string($db, $_POST['name']);


			if ( $_POST['split'] == 'Yes' ) {
				$split = 1;
			} else {
				$split = 0;
			}



			if (add_bill($charge_name,$charge_desc,$charge_acnt,$charge_amnt,$charge_day,$split) == 'true') {
			echo '<script type="text/javascript">';
			echo "alert('Bill created ".$charge_name." !');";
			echo 'window.location.href = "bills.php";';
			echo '</script>';
		}

		}

     ?>
