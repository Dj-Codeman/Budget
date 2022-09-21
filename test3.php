<?php
include('functions.php');

// 608
// $user = '602d5b998a635';
$user = '610efb74a2b98';

$key = category_row_count();
$array = category_array();
$Bills = calc_bills($user);



print_r($array);
// printf(calc_cost($array_budget));
// printf(between(8, 1, 3));

// $pay = 200;
// if ( calc_cost($array_budget) <= $pay - 2) {
//   echo "yup";
// } else {
//   echo "nope";
// }




// print_r($array_budget);

  // array(
  //   "Category" => 'Transportation',
  //   "Budget" => "$Transportation"
  // ),
  // array(
  //   "Category" => 'Bills',
  //   "Budget" => "$Bills"
  // ),
  // array(
  //   "Category" => 'Gifts',
  //   "Budget" => "$Gifts"
  // ),
  // array(
  //   "Category" => 'Entertainment',
  //   "Budget" => "$Entertainment"
  // ),
  // array(
  //   "Category" => 'Grocery',
  //   "Budget" => "$Grocery"
  // ),
  // array(
  //   "Category" => 'Self',
  //   "Budget" => "$Self"
  // ),
  // array(
  //   "Category" => 'Investment',
  //   "Budget" => "$Investment"
  // )
 // );


?>
