<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Check for failed logins
	$loginsCheck = mysql_query("SELECT * FROM `failedLogins`", $connDBA);
	
	if (mysql_fetch_array($loginsCheck)) {
		$logins = "exist";
	} else {
		$logins = "empty";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Failed Logins"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Failed Logins</h2>
<p>Below is a list of details for failed logins on this site.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem back" href="index.php">Back to Users</a></div>
<br />
<?php
//If no logins exist	
	if (isset ($logins) && $logins == "empty") {
		echo "<div class=\"noResults\">No failed logins have taken place</div>";
//If logins exist
	} else {
		$loginsGrabber = mysql_query("SELECT * FROM `failedLogins`", $connDBA);
		$count = 1;
		
		echo "<table class=\"dataTable\">
		<tbody>
			<tr>";		
				echo "<th class=\"tableHeader\">User Name</th>
				<th width=\"400\" class=\"tableHeader\">Date</th>
				<th width=\"200\" class=\"tableHeader\">IP Address</th>
			</tr>";
		
		
		while($logins = mysql_fetch_array($loginsGrabber)) {
			echo "<tr";
			//Alternate the color of each row.
			if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
			echo "<td>" . prepare($logins['userName']) . "</td>" . 
			"<td width=\"400\">" . date("l, M j, Y \\a\\t h:i:s A", $logins['timeStamp']) . "</td>" . 
			"<td width=\"200\">" . $logins['IPAddress'] . "</td>" . 
			"</tr>";
		}
		
		echo "</tbody>
		</table>";
	}
?>
<?php footer(); ?>
</body>
</html>