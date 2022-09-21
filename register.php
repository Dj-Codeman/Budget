<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Nigga Rigged Accounting</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header-login">
  	<h2>Register</h2>
  </div>

<form method="post" action="register.php">
  <?php include('errors.php'); ?>
<!-- First Name -->
<div class="input-group">
    <label>Username</label>
    <input type="text" name="Username">
  </div>
<!-- First Name -->
<div class="input-group">
    <label>Password</label>
    <input type="password" name="Password_1">
    </div>
<!-- First Name -->
<div class="input-group">
    <label>Verify Password</label>
    <input type="password" name="Password_2">
    </div>

    <div class="input-group">
      <button type="submit" class="btn-login" name="register">Register</button>
    </div>
    <p>
      Already a member? <a href="login.php">Sign in</a>
    </p>
  </form>
</body>
</html>
