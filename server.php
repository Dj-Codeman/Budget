<?php
header('P3P: CP="CAO PSA OUR"');
session_start();
$errors = array();
$uid = uniqid();
$salt = random_int(18, 952018);

#### Server php file handeles user logins and registration
# log in register and forgeot password will have there php code here
#

//database connection initialization
$db = mysqli_connect('192.168.0.6', 'Client', 'Y&0E1{8u){S?', 'Budget');
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

?>
