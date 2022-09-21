<?php header('P3P: CP="CAO PSA OUR"'); include('server.php'); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Nigga Rigged Accounting</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="header-login">
  	<h2>Login</h2>
  </div>

  <form method="post" action="login.php">
  	<?php include('errors.php'); ?>
    <div class="input-group">
      <label>Username</label>
      <input type="text" name="username" >
    </div>
    <div class="input-group">
  		<label>Passphrase</label>
  		<input type="password" name="passphrase" >
  	</div>
  	<div class="input-group">
  		<button type="submit" class="btn-login" name="login">Login</button>
  	</div>
    <p>
  		Not a member? <a href="register.php">Register</a>
  	</p>
  </form>
</body>
</html>
