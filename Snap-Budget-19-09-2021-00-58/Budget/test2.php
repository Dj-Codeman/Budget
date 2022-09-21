<?php
$uuid = md5(uniqid(rand(1000,9518)));
$uid = uniqid();
$id = md5(rand());

for($i = 0; $i > 5; $i++){
echo "$uuid-$uid-$id";
}
?>
