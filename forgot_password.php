<?php require_once('Connections/connDBA.php'); ?>
<?php
	if (isset ($_SESSION['MM_Username'])) {
		header("Location: admin/index.php");
	}
?>
<?php
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['userName']) && !empty($_POST['emailAddress'])) {
		$userName = $_POST['userName'];
		$emailAddress = $_POST['emailAddress'];
		$userDataGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' AND `emailAddress1` = '{$emailAddress}'", $connDBA);
		$userData = mysql_fetch_array($userDataGrabber);
		
		if ($userData) {
			$password = randomValue(10, "alphanum");
			mysql_query ("UPDATE `users` SET `password` = '{$password}', `changePassword` = 'on' WHERE `userName` = '{$userName}' AND `emailAddress1` = '{$emailAddress}'", $connDBA);
			mail($userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress1'] . ">", "Password Reset", "Your password has been been set to: \"" . $password . "\". Please login with this password, and you will be prompted to change this to a more suitable password.");
			
			header("Location: forgot_password.php?message=processed");
			exit;
		} else {
			header("Location: forgot_password.php?message=processed");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Password Recovery"); ?>
<?php headers(); ?>
<?php validate(); ?>
<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<h2>Password Recovery</h2>
<p>Enter  your user name and your primary email address to recover your password.</p>
<?php
//Display message updates
	if (isset($_GET['message'])) {
		successMessage("If the user name and password you entered were correct, then an email has been sent to you with instructions on how to change your password. Click <a href=\"login.php\">here to login</a>.");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<form name="resetPassword" id="validate" action="forgot_password.php" method="post" onsubmit="return errorsOnSubmit(this)">
<blockquote>
<p>User name:</p>
<blockquote>
  <p>
    <input type="text" name="userName" id="userName" size="50" autocomplete="off" class="validate[required]" />
  </p>
</blockquote>
<p>Email Address:</p>
<blockquote>
  <p>
    <input type="text" name="emailAddress" id="emailAddress" size="50" autocomplete="off" class="validate[required,custom[email]]" />
  </p>
  <p>
    <input type="submit" name="submit" id="submit" value="Submit" />
  </p>
  <?php formErrors(); ?>
</blockquote>
</blockquote>
</form>
<?php footer(); ?>
</body>
</html>
