<?php
//access User id
header('P3P: CP="CAO PSA OUR"');
session_start();
// inital db
$db = mysqli_connect('192.168.1.3', 'Client', 'Y&0E1{8u){S?', 'Budget');
$uid = uniqid();
$date = date('Y-m-d');
$global_section = section(date('d'));
$user = $_SESSION['User'];
// 0 pending 1 charged

function uuid() {
$uuid = md5(uniqid(rand(1000,9518)));
return $uuid;
}

function deposit($title,$desc,$acnt_name,$amnt,$catg,$flag){
global $db, $date, $acnt_num, $status, $user;
$uid = uuid();
$catg = sanatize_category($catg);
if( $flag == "name"){
$acnt_num = fetch_account($acnt_name,$user);
} else {
  $acnt_num = $acnt_name;
}

$query = "INSERT INTO Budget.Income ( Income_owner, uid, Income_title, Income_desc, Income_amnt, Income_acnt, Income_date, Income_catg, Income_stus)
VALUES( '$user', '$uid', '$title', '$desc', '$amnt', '$acnt_num', '$date', '$catg', '0' )";
// echo $query;
if(mysqli_query($db, $query)){
  $status = '0';
} else {
  $status = '1';
}

Tlog($uid);
}

function input_check($net){
global $db,$user;

$formated_net = convert_number($net);

$query = "SELECT Account_name FROM Budget.Accounts WHERE Account_owner = '$user' AND Account_primary = '1'";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
  $account = $row['Account_name'];
}
// Deposit saving
$title = "Pay Check";
$desc = "Automated pay stub";
$acnt_name = $account;
$catg = "check";
$flag = "name";
deposit($title,$desc,$acnt_name,$formated_net,$catg,$flag);
    // Transfer for investment account
}

function withdrawal($title,$desc,$acnt_name,$amnt,$catg,$flag){
global $db, $date, $acnt_num, $status,$user;
$catg = sanatize_category($catg);
$uid = uuid();

if ( $flag == "name" ){
$acnt_num = fetch_account($acnt_name,$user);
} else {
$acnt_num = $acnt_name;
}

$query = "INSERT INTO Budget.Outcome ( Outcome_owner, uid, Outcome_title, Outcome_desc, Outcome_amnt, Outcome_acnt, Outcome_date, Outcome_catg, Outcome_stus)
VALUES( '$user', '$uid', '$title', '$desc', '$amnt', '$acnt_num', '$date', '$catg', '0' )";
if(mysqli_query($db, $query)){
  $status = '0';
} else {
  $status = '1';
}

Tlog($uid);
}

function add_bill($title,$desc,$acnt_name,$amnt_1,$day,$split) {
global $db, $date, $user, $global_section;
$uid = uuid();
//fetching account number
$number = fetch_account($acnt_name,$user);
// formatting the bill amount correctly ( supporting thousands )
$amnt = convert_number($amnt_1);
// Setting section for 2 week calculation.
$section = section($day);

// mysqli query to return results
$stmt = mysqli_prepare($db, "INSERT INTO Budget.Bills ( Bill_owner, uid, Bill_title, Bill_desc, Bill_amnt, Bill_acnt, Bill_date, Bill_stus, Bill_chrg_date, Bill_split, Bill_sec)
VALUES(?,?,?,?,?,?,?,?,?,?,?)" );

//cheap work-around
$zero = 0;

mysqli_stmt_bind_param($stmt, "sssssssssss", $user, $uid, $title, $desc, $amnt, $number, $date, $zero, $day, $split, $section );

// mysqli_stmt_execute($stmt);

if ($stmt->execute()) {

return true;

} else {

return false;

}


}

function add_account($name,$type,$cur_balance,$number,$monitor) {
global $db, $date, $user;
$uid = uuid();
$int_balance = $cur_balance;

if (!isset($monitor)){
$monitor = '0';
}

$query = "INSERT INTO Budget.Accounts ( Account_owner, uid, Account_name, Account_type, Account_int_balance, Account_cur_balance, Account_number, Account_monitor)
VALUES( '$user', '$uid', '$name', '$type', '$int_balance', '$cur_balance', '$number', '$monitor' )";

//echo $query;
mysqli_query($db, $query);
}

function summary_accounts() {
global $db, $user;
$query = "SELECT * FROM Budget.Accounts WHERE Account_owner='$user'";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
$name = $row['Account_name'];
$balance = $row['Account_cur_balance'];
$exempt = $row['Account_monitor'];

echo "<tr>";
echo "<td>$name</td>";
if ($exempt == '1'){
$confy = 20;
if ( between($balance, $confy, 50.00)){
$color = "color:red";
} elseif ($balance <= 0) {
$color = "color:gray";
} elseif ($balance <= $confy) {
$color = "color:orange";
} else {
$color = "color:green";
}
} else {
$color = "color:black";
}
echo "<td style = \"$color\">$balance</td>";
$button = "<td style=\"text-align:center; width:10%; \">";
$button .= "<a href=\"https://budget.dwdata.tk/account_delete.php?item=$name \">";
$button .= "<button style=\"font-size: 50%; width: 100%; border-radius: 0px;\" type=\"submit\" class=\"btn-login\" name=\"Flag_Events\" > DELETE </button>";
$button .= "</a>";
$button .= "</td>";
echo $button;
echo "</tr>";
}
}

function summary_bills() {
global $db, $user;
$query = "SELECT * FROM Budget.Bills WHERE Bill_owner='$user'";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
$name = $row['Bill_title'];
$amount = $row['Bill_amnt'];
$date = $row['Bill_chrg_date'];

echo "<tr style=\"text-align:center;\">";
echo "<td>$name</td>";
echo "<td>$amount</td>";
echo "<td>$date</td>";
$button = "<td style=\"text-align:center; width:10%; \">";
$button .= "<a href=\"https://budget.dwdata.tk/bill_delete.php?item=$name \">";
$button .= "<button style=\"font-size: 60%; width: 100%; border-radius: 0px;\" type=\"submit\" class=\"btn-login\" name=\"Flag_Events\" > DELETE </button>";
$button .= "</a>";
$button .= "</td>";
echo $button;

echo "</tr>";
}
}

function summary_budget() {
global $db, $user;
$query = "SELECT * FROM Budget.Target_$user";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
$name = $row['Category'];
$name = display_category($name);
$amount = $row['Amount'];

echo "<tr>";
echo "<td>$name</td>";
echo "<td>$amount</td>";
echo "</tr>";
// // updating the last pay on the budget
// $array = array("Food", "Transportation", "Gifts", "Entertainment", "Grocery", "Self", "Investment");
// foreach ($array as $value) {
//  ${$value} = convert_number(fetch_target($value));
// }
}
}

function summary_category() {
global $db, $user;
$query = "SELECT * FROM Budget.Category_$user";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
$name1 = $row['Category'];
$name = display_category($name1);
echo "<tr>";
echo "<td style=\"text-align:center;\">$name</td>";
$button = "<td style=\"text-align:center;\">";
$button .= "<a href=\"https://budget.dwdata.tk/category_delete.php?item=$name \">";
$button .= "<button style=\"border-radius: 0px; font-size: 60%; width: 100%; \" type=\"submit\" class=\"btn-login\" name=\"Flag_Events\" > DELETE </button>";
$button .= "</a>";
$button .= "</td>";
echo $button;

echo "</tr>";
// // updating the last pay on the budget
// $array = array("Food", "Transportation", "Gifts", "Entertainment", "Grocery", "Self", "Investment");
// foreach ($array as $value) {
//  ${$value} = convert_number(fetch_target($value));
// }
}
}

function fetch_account($acnt_name,$owner) {
global $db;

$query = "SELECT Account_number FROM Budget.Accounts WHERE Account_name LIKE '%$acnt_name%' AND Account_owner = '$owner'";
$result = mysqli_query($db, $query);
$data = mysqli_fetch_assoc($result);
$acnt_num = $data['Account_number'];
return $acnt_num;
}

function update_accounts() {
global $db, $user;
$query = "SELECT * FROM Budget.Accounts WHERE Account_Owner = '$user'";
$result = mysqli_query($db, $query);

// first loop. Taking account
while($row = mysqli_fetch_assoc($result)) {
  // defining the number and total
  $Account_num = $row['Account_number'];
  $Account_cur_balance = $row['Account_cur_balance'];
  $Account_old_balance = $row['Account_cur_balance'];

  // second loop taking all incomes matching account number
  $query_2 = "SELECT * FROM Budget.Income WHERE Income_acnt = '$Account_num' AND Income_stus = '0'";
  $result_2 = mysqli_query($db, $query_2);
  while($row_2 = mysqli_fetch_assoc($result_2)) {
    $Income_amnt = $row_2['Income_amnt'];
    $Income_uid = $row_2['uid'];
    $income = number_format($Income_amnt, 2, '.', '');
    $add_total += $income;
    $query_3 = "UPDATE Budget.Income SET Income_stus = '1' WHERE uid = '$Income_uid' ";
    mysqli_query($db, $query_3);

  }
  // THIRD LOOP PULLING OUTCOMES AND CREDTING THEM
  $query_2 = "SELECT * FROM Budget.Outcome WHERE Outcome_acnt = '$Account_num' AND Outcome_stus = '0'";
  $result_2 = mysqli_query($db, $query_2);
  while($row_2 = mysqli_fetch_assoc($result_2)) {
    $Outcome_amnt = $row_2['Outcome_amnt'];
    $Outcome_uid = $row_2['uid'];
    $Outcome = number_format($Outcome_amnt, 2, '.', '');
    $sub_total -= $Outcome;
    $query_3 = "UPDATE Budget.Outcome SET Outcome_stus = '1' WHERE uid = '$Outcome_uid' ";
    mysqli_query($db, $query_3);
  }

  $old_balance = number_format($Account_old_balance, 2, '.', '');
  $total = $add_total + $sub_total;
  $total += $old_balance;

  $query_3 = "UPDATE Budget.Accounts SET Account_cur_balance = '$total' WHERE Account_number = '$Account_num' ";
 mysqli_query($db, $query_3);

  unset($old_balance, $total, $add_total, $sub_total, $income, $Outcome);

}

}

function Tlog($uid) {
global $db, $user;
$query = "INSERT INTO Budget.Trans_record ( uid, owner )
VALUES( '$uid', '$user' )";
mysqli_query($db, $query);
}

function read_log() {
global $db, $user;
$query = "SELECT * FROM Budget.Trans_record WHERE owner='$user' ORDER BY id DESC LIMIT 20";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
$uid = $row['uid'];

  $query_2 = "SELECT * FROM Budget.Income WHERE uid = '$uid'";
  $result_2 = mysqli_query($db, $query_2);

      while($row_2 = mysqli_fetch_assoc($result_2)) {
        $title = $row_2['Income_title'];
        $amount = "+ ";
        $amount .= $row_2['Income_amnt'];
        $account = $row_2['Income_acnt'];
        $date = $row_2['Income_date'];
        $category = $row_2['Income_catg'];
        $category = display_category($category);
        $uid_2 = $row_2['uid'];
        echo "<tr>";
        echo "<td>$title</td>";
        echo "<td>$amount</td>";
        echo "<td>$uid_2</td>";
        echo "<td>$date</td>";
        echo "<td>$category</td>";
        echo "</tr>";

      }

      $query_2 = "SELECT * FROM Budget.Outcome WHERE uid = '$uid'";
      $result_2 = mysqli_query($db, $query_2);
          while($row_2 = mysqli_fetch_assoc($result_2)) {
            $title = $row_2['Outcome_title'];
            $amount = "- ";
            $amount .= $row_2['Outcome_amnt'];
            $account = $row_2['Outcome_acnt'];
            $date = $row_2['Outcome_date'];
            $uid_2 = $row_2['uid'];
            $category = $row_2['Outcome_catg'];
            $category = display_category($category);
            echo "<tr>";
            echo "<td>$title</td>";
            echo "<td>$amount</td>";
            echo "<td>$uid_2</td>";
            echo "<td>$date</td>";
            echo "<td>$category</td>";
            echo "</tr>";
      }

}
}

function pie_peice() {
global $db, $user;

$query = "SELECT 1 FROM Budget.Budget_$user LIMIT 1";
$val = mysqli_query($db, $query);


if($val == FALSE)
{
  $class = "CREATE TABLE IF NOT EXISTS Budget.Budget_$user (
          Category VARCHAR(255) NOT NULL PRIMARY KEY,
          Created DECIMAL(8,2) DEFAULT NULL,
          Actual DECIMAL(8,2) DEFAULT NULL
  )";
          mysqli_query($db, $class);


          // deleting rows to add new category
          $query = "TRUNCATE TABLE Budget_$user";
          mysqli_query($db, $query);
          // Recreating the pie chart structure
          $array = category_array();
          foreach ($array as $value) {
          $vaule = sanatize_category($value);
          $query = "INSERT INTO Budget.Budget_$user ( Category, Actual )
          VALUES( '$value', '00.00' )";
          mysqli_query($db, $query);
          }

}


$Categories = category_array();

foreach($Categories as $catg){
  $catg = sanatize_category($catg);
  $query = "SELECT * FROM Budget.Outcome WHERE Outcome_catg = '$catg' AND Outcome_stus = '1' AND Outcome_owner = '$user' ";
  $result = mysqli_query($db, $query);

  // first loop. Taking Outcome info
  while($row = mysqli_fetch_assoc($result)) {
    // defining the number and total
    $Outcome_uid = $row['uid'];
    $Outcome_amnt = $row['Outcome_amnt'];
    $Outcome_catg = $row['Outcome_catg'];

    $catg_cost = number_format($Outcome_amnt, 2, '.', '');
    $total_catg += $catg_cost;

  }
  unset($catg_cost);

  $query = "SELECT * FROM Budget.Income WHERE Income_catg = '$catg' AND Income_stus = '1' AND Income_owner = '$user' ";
  $result = mysqli_query($db, $query);

  // first loop. Taking Outcome info
  while($row = mysqli_fetch_assoc($result)) {
    // defining the number and total
    $Income_uid = $row['uid'];
    $Income_amnt = $row['Income_amnt'];
    $Income_catg = $row['Income_catg'];

    $catg_cost = number_format($Income_amnt, 2, '.', '');
    $total_catg -= $catg_cost;

  }

  if($total_catg == ""){
    $tmp = number_format(0.00, 2, '.', '');
    $total_catg = $tmp;
  }
  $update_pie = "UPDATE Budget.Budget_$user SET Actual = '$total_catg' WHERE Category = '$catg' ";
  mysqli_query($db, $update_pie);

  unset($total_catg);

}


}

function clear_slate(){
  global $db, $user;
  //wiping chart data
  $query = "DELETE FROM Trans_record WHERE owner = '$user'";
  // mysqli_query($db, $query);
  // Whiping chart data
  $query = "TRUNCATE TABLE Budget_$user";
  mysqli_query($db, $query);
  // Recreating the pie chart structure
  $array = category_array();
  foreach ($array as $value) {
  $query = "INSERT INTO Budget.Budget_$user ( Category, Actual )
  VALUES( '$value', '00.00' ) ON DUPLICATE KEY UPDATE Actual = '0.00'";
  echo $query;
  mysqli_query($db, $query);
  }
  //updating privious logs to status 2
  $set_status = "UPDATE Budget.Outcome SET Outcome_stus = '2' WHERE Outcome_stus = '1' AND Outcome_owner = '$user' ";
  mysqli_query($db, $set_status);
  //updating privious logs to status 2
  $set_status = "UPDATE Budget.Incomecome SET Income_stus = '2' WHERE Income_stus = '1' AND Income_owner = '$user' ";
  mysqli_query($db, $set_status);
}

function recalculate(){
global $db;
$query = "SELECT * FROM Budget.Accounts";
$result = mysqli_query($db, $query);

// first loop. Taking account
while($row = mysqli_fetch_assoc($result)) {
  // defining the number and total
  $Account_num = $row['Account_number'];
  $Account_cur_balance = $row['Account_cur_balance'];
  $Account_old_balance = $row['Account_cur_balance'];
  // set cur balance to init bbalance
  // set every trans
  $zero_account = "UPDATE Budget.Accounts SET Account_cur_balance = '$Account_old_balance' WHERE Account_number = '$Account_num' ";
  mysqli_query($db, $zero_account);
  $e = 'y';
  ;

}

}

function transfer($from_account,$to_account,$amnt,$name,$desc){
global $db, $acnt_num, $balance_dirty, $message, $stat, $user;

$name .= " (Transfer)";
$catg = "Transfers";
// logic: check balace before withdrawal

$acnt_num = fetch_account($from_account,$user);
$query = "SELECT Account_cur_balance FROM Budget.Accounts WHERE Account_number = $acnt_num";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
$balance_dirty = $row['Account_cur_balance'];
}
$balance = number_format($balance_dirty, 2, '.', '');

// Verifying balance and transfering
if ( $balance >= $amnt ){
  // potential error system;
  $message = "Transfer of $$amnt approved\n";
  $stat = "0";
  $flag = "name";
  withdrawal($name,$desc,$from_account,$amnt,$catg,$flag);
  deposit($name,$desc,$to_account,$amnt,$catg,$flag);
} else {

  $message = "Transfer of $$amnt declined, Insufficient balance\n";
}
}

function check_bills(){
global $db;
// echo "ZA WORLDO";
$date = date('d');

// Clearing already charged bills
$clear = "UPDATE Budget.Bills SET Bill_stus = '0' WHERE Bill_stus = '1' AND Bill_chrg_date != $date";
mysqli_query($db, $clear);

$query = "SELECT * FROM Budget.Bills WHERE Bill_chrg_date = $date AND Bill_stus = '0'";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {

$Bid = $row['uid'];
$amnt = $row['Bill_amnt'];
$name = $row['Bill_title'];
$desc = $row['Bill_desc'];
$from_account = $row['Bill_acnt'];
$catg = "Bills";
$flag = "number";
withdrawal($name,$desc,$from_account,$amnt,$catg,$flag);
$update = "UPDATE Budget.Bills SET Bill_stus = '1' WHERE Bill_title = '$name'";

echo "$update";
mysqli_query($db, $update);
}


}

function generate_date($due) {
// days
return date('Y-m-d', strtotime($due));
echo "</br>";
//months
}

function create_budget_entry($uid, $date, $term, $term_num, $paid) {
  global $db, $user;

  $paid = convert_number($paid);
  $date_data = date_create("$date");
  $new_day = date_add($date_data, date_interval_create_from_date_string("$term"));

  $end_date = date_format($new_day, 'Y-m-d');
  $san_start = date_create("$date");
  $start_date = date_format($san_start, 'Y-m-d');
  // print_r($new_day);

  $start = generate_date($start_date);
  $end = generate_date($end_date);

    // $term_num = convert_number($term_num);
    $update_index = "INSERT INTO Budget.Budget_date (uid, Lastdate, Nextdate, Legnth, Paid) VALUES ('$uid', '$start',  '$end', '$term_num', '$paid')
    ON DUPLICATE KEY UPDATE Lastdate = '$start', Nextdate = '$end', Paid = '$paid'";
    // echo $update_index;
    mysqli_query($db, $update_index);

  // $update_index = "INSERT INTO Budget.Budget_date (uid, Lastdate, Nextdate, Paid) VALUES('$uid', '$start',  '$end',  '00.00')";
  // echo "$update_index";
  // mysqli_query($db, $update_index)

}

function relax(){
  ;
}

function trim_budget($array,$target,$pay) {
$size = count($array);


for($i = 0; $i < $size; $i++)
{
$category = $array[$i]["Category"];
$budget = $array[$i]["Budget"];

if ($category !== 'Transportation' && $category !== 'Bills') {
  if ($budget >= 0.50){
  $array[$i]["Budget"] = $array[$i]["Budget"] - .50;
}

// } // delete this on uncomment
}
// elseif ($category == 'Bills' && $budget > calc_bills()) {
//   $array[$i]["Budget"] = $array[$i]["Budget"] - .01;
//       // fucking broke exit clause
//       if ($budget < calc_bills()) {
//         exit("unable to create budget.");
//       }
//     } else { relax(); }
  }


  $val = calc_cost($array);
  $min = $pay - 1.00;
  $max = $pay;
  if (between($val, $min, $max) == "1") {
    echo "Budget within means \n";
    printf("Right before submit_budget");
    submit_budget($array);
  } elseif ( calc_cost($array) <= $pay - 2) {
    echo "Adding... \n";
    echo calc_cost($array);
    echo "</br> \n";
    add_budget($array,$target,$pay);
  } else {
    echo "Trimming again... \n";
    echo calc_cost($array);
    echo "</br> \n";
    trim_budget($array,$target,$pay);
  }



}

// add to budget
function add_budget($array,$target,$pay) {
$size = count($array);


for($i = 0; $i < $size; $i++)
{
$category = $array[$i]["Category"];
$budget = $array[$i]["Budget"];

  $array[$i]["Budget"] = $array[$i]["Budget"] + 1.00;

}

$val = calc_cost($array);
$min = $pay - 1.00;
$max = $pay;
if (between($val, $min, $max) == "1") {
  echo "Budget within means \n";
  printf("Right before submit_budget");
  submit_budget($array);
} elseif ( calc_cost($array) <= $pay - 2) {
  echo "Adding... \n";
  echo calc_cost($array);
  echo "</br> \n";
  add_budget($array,$target,$pay);
} else {
  echo "Trimming again... \n";
  echo calc_cost($array);
  echo "</br> \n";
  trim_budget($array,$target,$pay);
}

}
//

function convert_number($num) {
// $number = number_format($num);
$number = number_format($num, 2, '.', '');
return $number;
}

function calc_cost($array) {
  $size = count($array);
  // echo "$size </br>";
  $total = convert_number('0');
  for($i = 1; $i < $size; $i++)
  {
    $num = $array["$i"]['Budget'];
    echo "$num-$i \n";

    $total += convert_number("$num");
  }
  $total_san = convert_number($total);
  return $total_san;

}

function create_budget($pay) {
  global $db, $user;
  // updating the last pay on the budget
  $key = category_row_count();
  $array = category_array();
  $Bills = calc_bills($user);
  $array_budget = array();


  for($i = 0; $i < $key; $i++){
  if ($array[$i] != 'Bills') {
  $tmp_num = convert_number(fetch_target($array[$i]));

  $tmp = array(
            "Category" => "$array[$i]",
            "Budget" => "$tmp_num"
  );

  array_push($array_budget, $tmp);
  } else {

    $tmp = array(
              "Category" => "$array[$i]",
              "Budget" => "$Bills"
    );
  array_push($array_budget, $tmp);
  }
  }
   // total of ideal budget
   // original value was 305.00 but the function calc_cost
   // will be used for the sake of accuracy if any changes
   // are made
   $budget_max = calc_cost($array_budget);
   echo "Minumum required $budget_max, Paid $pay \n";


   if ($pay < $budget_max) {

    // test_trim_budget($array_budget,$budget_max,$pay);
    echo '<script type="text/javascript">';
    echo "alert('Cannot create budget please try again. ');";
    echo 'window.location.href = "budget.php";';
    echo '</script>';
    exit("Insufficient funds");

  }  else {
    // echo "Budget vs pay valid \n";

    add_budget($array_budget,$budget_max,$pay);
    // printf("add function called \n");
    return true;
  }
  // defining $Categories
  // $Categories []="Corrections";
  // $Categories []="Bills";
  // $Categories []="Entertainment";
  // $Categories []="Gifts";
  // $Categories []="Transportation";
  // $Categories []="Transfers";
  // $Categories []="Other";
  // $Categories []="Grocery";
  // $Categories []="Self";

}

function submit_budget($array) {
global $db, $user;
$size = count($array);
// printf("the function was called");

// print_r($array);
// clear old category info
$query = "TRUNCATE TABLE Budget_$user";
mysqli_query($db, $query);

// First query to populate and set the table.
for($i = 0; $i < $size; $i++) {
$category = $array[$i]["Category"];
$category = sanatize_category($category);
$budget = $array[$i]["Budget"];
$query = "INSERT INTO Budget_$user (Category, Created, Actual) VALUES ('$category', '$budget', '00.00') ON DUPLICATE KEY UPDATE Category = '$category', Created = '$budget', Actual = '00.00'";
mysqli_query($db, $query);
}

// Just viewing the final budget to be commited

  for($i = 0; $i < $size; $i++)
  {
  $category = $array[$i]["Category"];
  $budget = $array[$i]["Budget"];
  // echo "$category </br>";
  // echo "$budget </br>";
  }
  // echo $query;
}

/// EDIT NEXT
function reset_budget() {
global $db, $user;
// pulling data
$query = "SELECT Lastdate, Nextdate, Legnth, Paid FROM Budget.Budget_date WHERE uid = '$user'";
$result = mysqli_query($db, $query);
$data = mysqli_fetch_assoc($result);
$last_date_san = $data['Lastdate'];
$next_date_san = $data['Nextdate'];
$legnth = $data['Legnth'];
$paid = $data['Paid'];
$legnth = number_format($legnth, 0, '', ',');
$legnth_day = $legnth .= " days";
// sanatizign the data
$old_date = generate_date($last_date_san);
$last_date = generate_date($next_date_san);
// $legnth   = convert_number($legnth_san);
create_budget_entry($user, $last_date, $legnth, $data['Legnth'], $paid);
}

function update_bill($name, $price, $bill_id) {
global $db, $user;

$new_price = convert_number($price);
$query = "UPDATE Budget.Bills SET Bill_amnt = '$new_price' WHERE Bill_owner = '$user' AND uid = '$bill_id' ";
mysqli_query($db, $query);
echo '<script type="text/javascript">';
echo "alert('Bill: ". $bill_id ." updated !');";
echo 'window.location.href = "bills.php";';
echo '</script>';
}

function define_section() {
global $db, $user;

$query = "SELECT * FROM Budget.Bills WHERE Bill_owner = '$user'";
// printf("$query \n");
$result = mysqli_query($db,$query);
// Defining the sections
// devided by week ie: day 1 to 7 are section one
while($row = mysqli_fetch_array($result)){
$uid = $row['uid'];
$charge_day = $row['Bill_chrg_date'];
// $uid = $row['uid'];
$section = section($charge_day);

$update = "UPDATE Budget.Bills SET Bill_sec = '$section' WHERE Bill_owner = '$user' AND uid = '$uid'";
// printf("$update \n");
mysqli_query($db, $update);

}

}

function section($day) {
  // global $section;
  if (between($day, '1', '7') == true) { return '1'; }
  if (between($day, '8', '15') == true) { return '2'; }
  if (between($day, '15', '22') == true) { return '3'; }
  if (between($day, '22', '31') == true) { return '4'; }
}

function between($val, $min, $max) {
if($val >= $min && $val <= $max) return true;
return false;
}

function calc_bills($user) {
global $db, $global_section;

$section1 = $global_section;
$section2 = $global_section + 1;

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
  // printf("$bill_name - $bill_tmp \n");
  $bill_total += $total;
}
return $bill_total;
unset($bill_total);

}

function test_calc_bills($user) {
global $db, $user, $global_section;

$section1 = $global_section;
$section2 = $global_section + 1;


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
  printf("$bill_tmp - $bill_name \n");
  $bill_total += $total;
}
return $bill_total;

}

function fetch_target($name) {
global $db, $user;
$name = sanatize_category($name);
$query = "SELECT * FROM Budget.Target_$user WHERE Category = '$name'";
$result = mysqli_query($db, $query);
while($row = mysqli_fetch_assoc($result)) {
  $category = $row['Category'];
  $amount = $row['Amount'];
}

return $amount;
}

function create_blank_target() {
global $db, $user;

// $query = "TRUNCATE TABLE Target_$user";
// mysqli_query($db, $query);

$array = category_array();
foreach ($array as $value) {
$query = "INSERT INTO Budget.Target_$user ( Category, Amount ) VALUES( '$value', '00.00' ) ON DUPLICATE KEY UPDATE Category = '$value'";
echo $query;
mysqli_query($db, $query);

}

}

function create_default_category() {
global $db, $user;
$uid = uuid();

$array = array("Transportation", "Gifts", "Entertainment", "Bills", "Grocery", "Self", "Investment");
foreach ($array as $value) {
$query = "INSERT INTO Budget.Category_$user ( Uid, Category ) VALUES( '$uid', '$value' )";
echo $query;
mysqli_query($db, $query);
}

}


function category_array() {
global $db, $user;

$query = "SELECT * FROM Budget.Category_$user";
$result = mysqli_query($db, $query);
$category_array = array();

while($row = mysqli_fetch_assoc($result)) {
  $category = $row['Category'];
  $name = display_category($category);
  array_push($category_array, $name);
}

 return $category_array;
}


function category_row_count() {
global $db, $user;

if ($result = mysqli_query($db, "SELECT * FROM Budget.Category_$user")) {
$row_cnt = mysqli_num_rows($result);
mysqli_free_result($result);
return $row_cnt;
}
}

function category_add($name) {
global $db, $user;
// $uuid = md5(uniqid(rand(1000,9518)));
$uid = uuid();
$name = sanatize_category($name);
$query = "INSERT INTO Budget.Target_$user ( Category, Amount ) VALUES ( '$name', '0.00' )";
mysqli_query($db, $query);
// unset($stmt);
$stmt = mysqli_prepare($db, "INSERT INTO Budget.Category_$user ( Uid, Category ) VALUES (?,?)" );
mysqli_stmt_bind_param($stmt, "ss", $uid, $name );
// mysqli_stmt_execute($stmt);
if ($stmt->execute()) {
return true;
} else {
return false;
}
printf($query ,"\n");

}

function display_category($name) {

$name = preg_replace("/[-]/", " ", $name);

return $name;
}

function sanatize_category($name) {

  //Make alphanumeric (removes all other characters)
  $name = preg_replace("/[^a-zA-Z0-9_\s-]/", "", $name);
  //Clean up multiple dashes or whitespaces
  $name = preg_replace("/[\s-]+/", " ", $name);
  //Convert whitespaces and underscore to dash
  $name = preg_replace("/[\s_]/", "-", $name);
  //Convert first letter to capital
  $name = ucwords("$name");

return $name;
}
// reset_budget();
// $user = "602d5b998a635";
// $pay = 645.45;
// create_budget($pay);
 // Last function add a function
// to move the budget window every term day




// function fetch_budget() {
//   global $user, $db, $dataPoints;
//   $s = 6;
//   for($i = 0; $i < $s; $i++) {
//   $dataPoints = array();
//   $query = "SELECT * FROM Budget.Budget_$user WHERE id_num = '$i' LIMIT 1";
//   $result = mysqli_query($db, $query);
//
//
//   while($row = mysqli_fetch_array($result)) {
//    $category = $row['Category'];
//    $created = $row['Created'];
//    $data = "$category, $created";
//   }
// }
// }

// fetch_budget();
 ?>
