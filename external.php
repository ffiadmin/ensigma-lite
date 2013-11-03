<?php require_once('Connections/connDBA.php'); ?>
<?php
//Check the site settings
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	
//Select the tabs
	$query = "SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0'";
	$tabGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0' ORDER BY `position` ASC", $connDBA);
	$contentGrabber = mysql_query("SELECT * FROM external WHERE `visible` = 'on' AND `published` != '0' ORDER BY `position` ASC", $connDBA);

        if (!isset($_GET['strip'])) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("External Content"); ?>
<?php headers(); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('#tabs').tabs();
	});
</script>
<style type="text/css">
.ui-tabs-nav li a:link,
.ui-tabs-nav li a:visited,
.ui-tabs-nav li a:hover,
.ui-tabs-nav li a:active { font-size:14px; padding:4px 1.2em 3px; }

.ui-tabs-panel { padding:20px 9px; font-size:12px; line-height:1.4; color:#000; }
</style>
</head>

<body class="overrideBackground">
<?php
        }

	if (query($query)) {
		echo "<div id=\"tabs\" class=\"TabbedPanels\">";
	
	//Display the titles
		$count = 1;
		
		echo "<ul>";
		
		while ($tab = mysql_fetch_array($tabGrabber)) {
			$title = unserialize($tab['content' . $tab['display']]);
			echo "<li><a href=\"#content" . $count . "\">" . stripslashes($title['title']) . "</a></li>";
			
			$count++;
		}
		
		echo "</ul>";
		
	//Display the content
		$count = 1;
		
		while ($content = mysql_fetch_array($contentGrabber)) {
			$body = unserialize($content['content' . $content['display']]);
			echo "<div id=\"content" . $count . "\">" . stripslashes($body['content']) . "</div>";
			
			$count++;
		}
		
		echo "</div>";
	} else {
		echo "<div align=\"center\"><p>No content is avaliable. Please check back later.</p></div>";
	}
?>
<?php
        if (!isset($_GET['strip'])) {
?>
</body>
</html>
<?php
        }
?>