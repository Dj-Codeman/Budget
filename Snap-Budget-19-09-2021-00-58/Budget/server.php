<?php
header('P3P: CP="CAO PSA OUR"');
session_start();
$errors = array();
$uid = uniqid();
$salt = random_int(18, 952018);

//database connection initialization
$db = mysqli_connect('192.168.1.3', 'Client', 'Y&0E1{8u){S?', 'Budget');
  // DIE statment
  if (mysqli_connect_errno()) {
  	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
  }

  // LOGIN USER
  if (isset($_POST['login'])) {
  	// login is escaped
    if ($embeded != "1"){
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['passphrase']);
    }
    if (empty($password)) {
    	array_push($errors, "Password is required");
    }

    if (empty($username)) {
      array_push($errors, "Who are you !?");
    }

    if (count($errors) == 0) {

  	$query = " SELECT password, salt, uid FROM Users WHERE username='$username'" ;

  	$result = mysqli_query($db, $query);
  	$row = mysqli_fetch_array($result);
  	$salt = $row['salt'];
  	$oldpassword = $row['password'];
    $user = $row['uid'];


  	$newpassword = crypt($password, $salt);


  	if ( $newpassword == $oldpassword ) {
  			$_SESSION['auth'] = '952018';
  			$_SESSION['success'] = "You are now logged in";
        $_SESSION['User'] = $user;

        $class = "CREATE TABLE IF NOT EXISTS Budget.Budget_$user (
                Category VARCHAR(255) NOT NULL,
                Created DECIMAL(8,2) DEFAULT NULL,
                Actual DECIMAL(8,2) DEFAULT NULL,
                id_num INT(11) DEFAULT NULL PRIMARY KEY AUTO_INCREMENT
        )";
                mysqli_query($db, $class);

        $category = "CREATE TABLE IF NOT EXISTS Budget.Category_$user (
                Uid VARCHAR(255) PRIMARY KEY,
                Category VARCHAR(255) NOT NULL
        )";
                mysqli_query($db, $category);

        $budget = "CREATE TABLE IF NOT EXISTS Budget.Budget_date (
                uid VARCHAR(255) PRIMARY KEY NOT NULL,
                Lastdate DATE,
                Nextdate DATE,
                Legnth INT(11) NOT NULL,
                Paid DECIMAL(8,2) DEFAULT NULL,
                id_num INT(11)

        )";
                mysqli_query($db, $budget);


        $target = "CREATE TABLE IF NOT EXISTS Budget.Target_$user (
                Category VARCHAR(255) NOT NULL PRIMARY KEY,
                Amount DECIMAL(8,2) DEFAULT NULL
        )";

                mysqli_query($db, $target);

  			header('location: index.php');
  	} else {
  		array_push($errors, "Wrong username/password combination ");
  	}


      }
    }


// REGISTERING USER
if (isset($_POST['register'])) {
  $username = mysqli_real_escape_string($db, $_POST['Username']);
  $password1 = mysqli_real_escape_string($db, $_POST['Password_1']);
  $password2 = mysqli_real_escape_string($db, $_POST['Password_2']);

  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($password1)) { array_push($errors, "Password is required"); }
  if ($password1 != $password2) { array_push($errors, "The two passwords do not match");  }

  if ($password1 == $username){
  	  array_push($errors, "please pick a diffrent username or password ");
  }
  if (strlen($password1) > 20 || strlen($password1) < 5) {
  	array_push($errors, "Password must be between 5 and 20 characters long!");
  }

  $user_check_query = "SELECT * FROM Budget.Users WHERE username='$username' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user['username'] === $username) {
    array_push($errors, "This Username is registered with and account");
  }

  if (count($errors) == 0) {
    $status = '0';
  	$password0 = crypt($password1, $salt); // Password hashed with salt
	// This password is one way encryped

  $query = "INSERT INTO Budget.Users ( uid, username, password, salt )
        VALUES( '$uid', '$username', '$password0', '$salt' )";
  mysqli_query($db, $query);


array_push($errors, "You have been registered! ");

}

}

/////////////////////////////////////////////// HOME PAGE BAR
function home_bar($site) {
if ( $site == 'index')     {  $highlight1 = 'current';  }
if ( $site == 'income')    {  $highlight2 = 'current';  }
if ( $site == 'outcome')   {  $highlight3 = 'current';  }
if ( $site == 'Transfer')  {  $highlight4 = 'current';  }
if ( $site == 'addaccount'){  $highlight5 = 'current';  }
if ( $site == 'budget')    {  $highlight6 = 'current';  }
if ( $site == 'bills')     {  $highlight7 = 'current';  }
if ( $site == 'category')  {  $highlight8 = 'current';  }
if ( $site == 'check')     {  $highlight9 = 'current';  }
// if ( $site == 'index'){  $highlight = 'current';  }
echo "<div class=\"input-group-top\">";

  echo "<button type=\"button\" class=\"btn-home $highlight1 \" name=\"events_page\"><a href=\"index.php\"           >Home</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight2 \" name=\"home_page\"><a href=\"income.php\"            >Deposit</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight3 \" name=\"log_page\"><a href=\"outcome.php\"            >Expense</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight4 \" name=\"contact_page\"><a href=\"Transfer.php\"       >Transfers</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight5 \" name=\"contact_page\"><a href=\"addaccount.php\"     >Add Account</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight9 \" name=\"contact_page\"><a href=\"check.php\"          >Pay day</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight7 \" name=\"contact_page\"><a href=\"bills.php\"          >Bills</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight6 \" name=\"contact_page\"><a href=\"budget.php\"         >Budget</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight8 \" name=\"contact_page\"><a href=\"category.php\"       >Categories</a></button>";
  echo "<button type=\"button\" class=\"btn-home $highlight10 \" name=\"logout\"><a href=\"logout.php\">Logout</a>";
  echo "<i style=\"color:white;\" class=\"fas fa-lock\"></i>";
  echo "</button>";

echo "</div>";
echo "</div>";


}
?>
