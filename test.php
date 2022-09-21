<?php
// Include section
// include('file.php');

include('functions.php');

// Dependent Variables
// $user = ...
// $id = ...
// $user = '610b4dab4efeb';
$user = "602d5b998a636";
// $user = '610efb74a2b98';

category_bills($user);
clear_slate();
recalculate();
reset_budget();
?>
