<?php
// Include section
// include('file.php');

include('functions.php');

// Dependent Variables
// $user = ...
// $id = ...
// $user = '610b4dab4efeb';
$user = '602d5b998a635';
// $user = '610efb74a2b98';

$bill = verbose_calc_bills($user);
printf($bill);


function verbose_calc_bills($user) {
global $db, $global_section;

$section1 = 1;
$section2 = 2;
$section3 = 3;
$section4 = 4;

// debuging
// printf("$section1 $section2 \n");
// Calculating bill Budget
$bill_total = 0;

$query = "SELECT * FROM Budget.Bills WHERE Bill_owner = '$user' AND Bill_sec = '$section1' OR Bill_owner = '$user' AND Bill_sec = '$section2' OR Bill_owner = '$user' AND Bill_split = '1'";
// echo "$query_2 </br>";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
  $bill_amnt = $row['Bill_amnt'];
  $bill_uid = $row['uid'];
  $bill_name = $row['Bill_title'];
  $bill_split = $row['Bill_split'];
  if ( $bill_split == '0') { $bill_tmp = $bill_amnt; }
  if ( $bill_split == '1') { $bill_tmp = $bill_amnt / 2; }
  $total = number_format($bill_tmp, 2, '.', ',');
// debuging
  printf("$bill_name - $bill_tmp \n");
  $bill_total += $total;
}
return $bill_total;
unset($bill_total);

}

?>
