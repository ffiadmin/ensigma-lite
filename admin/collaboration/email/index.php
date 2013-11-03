<?php require_once('../../../Connections/connDBA.php'); ?>
<?php
	if (privileges("sendEmail") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Send Email"); ?>
<?php headers(); ?>
</head>

<body>
<?php topPage(); ?>
<h2>Send Email</h2>
<p>Send an email to multiple users, or roles within this system. Please note that this is not an online email system. This is only used to send a mass email.</p>
<p>&nbsp;</p>
<div align="center" class="emailContainer">
<a class="emailBase emailAll" href="email_all.php"></a>
<a class="emailBase emailSelected" href="email_selected.php"></a>
<a class="emailBase emailRole" href="email_role.php"></a>
</div>
<div align="center" class="emailContainer">
<a class="emailBase automatedEmail" href="scheduled.php"></a>
<a class="emailBase welcomeEmail" href="welcome_email.php"></a>
<a class="emailBase goodbyeEmail" href="goodbye_email.php"></a>
</div>
<div align="center" class="emailContainer">
<a class="emailBase emailHistory" href="history.php"></a>
</div>
<?php footer(); ?>
</body>
</html>