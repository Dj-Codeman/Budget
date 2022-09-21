<?php
// Include section
// include('file.php');

include('server.php');

// Dependent Variables
// $user = ...
// $id = ...
// $user = '610b4dab4efeb';
$user = '602d5b998a635';
// $user = '610efb74a2b98';

// Code to test
// echo something
$array = name_array();
$count = name_row_count();

// print_r($array);
 echo $uuid;


for($i = 0; $i < $count; $i++){
$uid = $array[$i]['uid'];
$uuid = uuid();
$query = "UPDATE Budget.Accounts SET uid = '$uuid' WHERE uid = '$uid'";
printf($query, "\n");
mysqli_query($db, $query);
}




function uuid() {
$uuid = base64_encode(uniqid(rand(1000,9518)));
return $uuid;
}

function name_row_count() {
global $db, $user;

$query = "SELECT * FROM Budget.Accounts";
if ($result = mysqli_query($db, $query)) {
$row_cnt = mysqli_num_rows($result);
mysqli_free_result($result);
return $row_cnt;
}
}

function name_array() {
global $db, $user;

$query = "SELECT * FROM Budget.Accounts";
$result = mysqli_query($db, $query);
$name_array = array();

while($row = mysqli_fetch_assoc($result)) {
  $name = $row['Account_name'];
  $uid = $row['uid'];
  $tmp = array(
            "Name" => "$name",
            "uid" => "$uid"
  );

  array_push($name_array, $tmp);
}

 return $name_array;
}
 ?>
