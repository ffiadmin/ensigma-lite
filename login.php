<?php require_once('Connections/connDBA.php'); ?>
<?php login(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php title("Login"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("public"); ?>
      <form method="post" action="login.php<?php if (isset($_GET['accesscheck'])) {echo "?accesscheck=" .  urlencode($_GET['accesscheck']);} ?>" name="login" id="login">
        <h1>Login</h1>
        <p>Login with your username and password to access your account.</p>
        <?php
		//Display a login failed alert
			if (isset($_GET['alert'])) {
				errorMessage("Your user name or password is incorrect.");
			}
			
			if (isset($_GET['accesscheck'])) {
				errorMessage("Either you are not logged in, or do not have the appropriate privileges to perform this action.");
			}
		?>
        <table width="100%" border="0" align="center">
          <tr>
            <td width="30%"><div align="right">User name:</div></td>
          <td width="70%"><div align="left">
                <label>
                <input type="text" name="username" id="username" />
                </label>
            </div></td>
          </tr>
          <tr>
            <td width="30%"><div align="right">Password:</div></td>
          <td width="70%"><div align="left">
                <label>
                <input type="password" name="password" id="password" />
                </label>
            </div></td>
          </tr>
          <tr>
            <td width="30%">&nbsp;</td>
            <td width="70%"><div align="left">
              <input type="submit" name="submit" id="submit" value="Login" />
              </div>
                </label></td>
          </tr>
          <tr><td width="30%" height="40">&nbsp;</td><td width="70%"><p><a href="forgot_password.php">Forgot your password?</a><br />
          </p></td></tr>
        </table>
</form>
<?php footer("public"); ?>
</body>
</html>