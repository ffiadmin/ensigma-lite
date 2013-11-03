<?php require_once('Connections/connDBA.php'); ?>
<?php
//Check the site settings
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	
//Select the tabs
	if ($settings['autoPublishExternal'] == "1") {
		$contentCheck = mysql_query("SELECT * FROM external WHERE `visible` = 'on'", $connDBA);
		$content = mysql_fetch_array($contentCheck);
		$tabGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' ORDER BY `position` ASC", $connDBA);
		$contentGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' ORDER BY `position` ASC", $connDBA);
	} else {
		$contentCheck = mysql_query("SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0'", $connDBA);
		$content = mysql_fetch_array($contentCheck);
		$tabGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0' ORDER BY `position` ASC", $connDBA);
		$contentGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0' ORDER BY `position` ASC", $connDBA);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("External Content"); ?>
<?php headers(); ?>
<script src="javascripts/tabbedPanels/tabbedPanels.js" type="text/javascript"></script>
<script src="javascripts/tabbedPanels/getURLParameter.js" type="text/javascript"></script>
<link href="styles/common/tabbedPanels.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var params = Spry.Utils.getLocationParamsAsObject(); 
</script>
</head>

<body class="overrideBackground">
<?php
	if ($content) {
		echo "<div id=\"TabbedPanels1\" class=\"TabbedPanels\">";
	
	//Display the titles
		echo "<ul class=\"TabbedPanelsTabGroup\">";
		
		while ($tab = mysql_fetch_array($tabGrabber)) {
			echo "<li class=\"TabbedPanelsTab\" tabindex=\"0\">" . stripslashes($tab['title']) . "</li>";
		}
		
	//Display the content
		echo "</ul><div class=\"TabbedPanelsContentGroup\">";
		
		while ($content = mysql_fetch_array($contentGrabber)) {
			  if ($content['display'] == "1") {
				  echo "<div class=\"TabbedPanelsContent\">" . stripslashes($content['content1']) . "</div>";
			  } else {
				  echo "<div class=\"TabbedPanelsContent\">" . stripslashes($content['content2']) . "</div>";
			  }
		}
		
		echo "</div>";
	} else {
		echo "<div align=\"center\"><p>No content is avaliable. Please check back later.</p></div>";
	}
?>
<script type="text/javascript">
<!--
var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1", {defaultTab: params.tab ? params.tab : 0}); 
//-->
</script>
</body>
</html>