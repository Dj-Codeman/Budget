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
check_bills();
define_section();
category_bills($user);
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
	home_bar($site);
	?>


<!-- table style  -->
  <style>
  table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  }
  </style>






<!-- Test table with db data -->
<table style="margin-left:10%; width: 80%; border-radius:0px;" border="2">
<tbody>
<tr>
<strong>
<td><strong>Account Name</strong></td>
<td><strong>Account Balance</strong></td>
<td><strong>Delete ?</strong></td>
</tr>
<?php
summary_accounts();
?>
</tbody>
</table>

<div class="home-events" style="overflow:hidden; margin-top: 3%; margin-left:5%; width:90%;  height: auto;">
	<h2 style=" text-align: center; margin-left:1%; width:auto;"> Transaction history </h2>
	<table style="position:static; width:100%; border-radius:50px;" border="2">
	<tbody>
	<tr>
	<strong>
	<td><strong>Transaction name</strong></td>
	<td><strong>Transaction amount</strong></td>
	<td><strong>Transaction id</strong></td>
	<td><strong>Transaction date</strong></td>
	<td><strong>Transaction category</strong></td>
	</tr>
	<?php read_log(); ?>
	</tbody>
	</table>

</div>



<?php
pie_peice();
// fetch_budget();
require "config.php";// Database connection

if($stmt = $connection->query("SELECT Category, Created, Actual FROM Budget_$user")){

  // echo "No of records : ".$stmt->num_rows."<br>";
$php_data_array = Array(); // create PHP array
  echo "<table style = \"margin-top:2%; margin-left:0%; width:100%;text-align:center;\" >
<tr> <th>Category</th><th>Set Budget</th><th>Actually Spent</th></tr>";
while ($row = $stmt->fetch_row()) {
		$row[0] = display_category($row[0]);
		$confy = $row[1] - 10;
	if ($row[1] <= $row[2] && $row[2] > 0.00){
		$color = "color:red";
	} elseif ( between($row[2], $confy, $row[1]) && $row[2] > 0.00 ) {
		$color = "color:orange";
	} elseif ($row[2] < 0) {
		$color = "color:blue";
	} else {
		$color = "color:green";
	}
   echo "<tr><td>$row[0]</td><td>$row[1]</td><td style = \"$color\">$row[2]</td></tr>";
	 unset($color);
   $php_data_array[] = $row; // Adding to array
   }
echo "</table>";
}else{
echo $connection->error;
}
//print_r( $php_data_array);
// You can display the json_encode output here.
// echo json_encode($php_data_array);
// echo json_encode($php_data_array);

// Transfor PHP array to JavaScript two dimensional array
echo "<script>
        var my_2d = ".json_encode($php_data_array)."
</script>";
?>


<br><br>
<a href=https://www.plus2net.com/php_tutorial/chart-database.php>Pie Chart from MySQL database</a>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
 google.charts.load('current', {'packages':['corechart']});
     // Draw the pie chart when Charts is loaded.
      google.charts.setOnLoadCallback(draw_my_chart);
      // Callback that draws the pie chart
      function draw_my_chart() {
        // Create the data table .
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'language');
        data.addColumn('number', 'Nos');
		for(i = 0; i < my_2d.length; i++)
    data.addRow([my_2d[i][0], parseInt(my_2d[i][1])]);
// above row adds the JavaScript two dimensional array data into required chart format
    var options = {title:'The computer generated budget.',
                       width:600,
                       height:500};

        // Instantiate and draw the chart
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
</script>

<div style = "margin-left:35%; width:100%;" id="chart_div"></div>
</body>
</html>
