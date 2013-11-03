<?php require_once('../Connections/connDBA.php'); ?>
<?php
//Grab the sidebar
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	
	if ($settings['autoPublishSideBar'] == "1") {
		$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on'", $connDBA);
	} else {
		$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' AND published != '0'", $connDBA);
	}
	
	$sideBarResult = mysql_fetch_array($sideBarCheck);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Access Denied"); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<?php
//Use the layout control if the page is displaying a sidebar
	$sideBarLocationGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
	$sideBarLocation = mysql_fetch_array($sideBarLocationGrabber);
		
	if ($sideBarResult) {
		echo "<div class=\"layoutControl\"><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "contentRight";
		} else {
			echo "contentLeft";
		}
		echo "\">";
	}

//Display the error content
	echo "<h2>Access Denied</h2>";
	
	if (isset($_GET['error']) && $_GET['error'] == "403") {
		echo "<p>You do not have premission to access this content</p>";
	} elseif (isset($_GET['error']) && $_GET['error'] == "404") {
		echo "<p>The page you are looking for was not found on our system</p>";
	} else {
		echo "<p>You do not have premission to access this content</p>";
	}
	
	echo "<p>&nbsp;</p><p align=\"center\"><input type=\"button\" name=\"continue\" id=\"continue\" value=\"Continue\" onclick=\"history.go(-1)\" /></p>";

//Display the sidebar
	if ($sideBarResult) {
		if ($settings['autoPublishSideBar'] == "1") {
			$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on'", $connDBA);
		} else {
			$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' AND published != '0'", $connDBA);
		}
		
		echo "</div><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "dataLeft";
		} else {
			echo "dataRight";
		}
		
		echo "\"><br /><br /><br />";
		
		while ($sideBar = mysql_fetch_array($sideBarCheck)) {
			if ($sideBar['display'] == "1") {
				  $content = $sideBar['content1'];
			  } else {
				  $content = $sideBar['content2'];
			  }
			
			if ($sideBar['published'] != "0") {
				switch ($sideBar['type']) {
				//If this is a custom content box
					case "Custom Content" : 				
						if (!isset($_SESSION['MM_Username'])) {
							echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\">" . $content . "</div></div>";
						} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") != "true") {
							echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\">" . $content . "</div></div>";
						} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") == "true") {
							echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"admin/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div><div class=\"content\">" . $content . "</div></div>";
						} break;
				//If this is a login box	
					case "Login" : 
						if (!isset($_SESSION['MM_Username'])) {
							echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\"><form id=\"login\" name=\"login\" method=\"post\" action=\"index.php\"><div align=\"center\"><div style=\"width:75%;\"><p>User name: <input type=\"text\" name=\"username\" id=\"username\" autocomplete=\"off\" /><br />Password: <input type=\"password\" name=\"password\" id=\"password\" autocomplete=\"off\" /></p><p><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Login\" /></p></div></div></form></div></div>";
						} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") == "true") {
							echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"admin/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div></div>";
						} break;
				}
			}
		}
		
		echo "</div></div>";
	}
?>
<?php footer(); ?>
</body>
</html>