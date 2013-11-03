<?php require_once("../../../Connections/connDBA.php"); ?>
<?php
	if (privileges("viewStatistics") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Export as XML file
	header("Content-type: application/xml");
	
	if (isset($_GET['type'])) {
		switch ($_GET['type']) {
			case "daily" : 
				$statisticsNumber = query("SELECT * FROM `dailyhits`", "num");
				$statisticsCheck = mysql_query("SELECT * FROM `dailyhits` LIMIT 50");
				
				if (mysql_fetch_array($statisticsCheck)) {
					$firstItemGrabber = mysql_query("SELECT * FROM `dailyhits` ORDER BY `id` ASC LIMIT 1");
					$firstItemArray = mysql_fetch_array($firstItemGrabber);
					$firstItem = $firstItemArray['date'];
					$lastItemGrabber = mysql_query("SELECT * FROM `dailyhits` ORDER BY `id` DESC LIMIT 1");
					$lastItemArray = mysql_fetch_array($lastItemGrabber);
					$lastItem = $lastItemArray['date'];
					
					$statisticsGrabber = mysql_query("SELECT * FROM `dailyhits` ORDER BY `id` ASC");
					
					echo "<graph caption=\"Daily Hits\" subcaption=\"";
					
					if ($statisticsNumber > 50) {
						echo "Last 50 Days - ";
					}
					
					echo "From " . $firstItem . " to " . $lastItem ."\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\">";
					
					while($statistics = mysql_fetch_array($statisticsGrabber)) {
						echo "<set name=\"" . $statistics['date'] . "\" value=\"" . $statistics['hits'] . "\" hoverText=\"" . $statistics['date'] . "\"/>";
					}
					
					echo "</graph>";
				} else {
					echo "<graph caption=\"Daily Hits\" subcaption=\"No Data\" xAxisName=\"\" yAxisMinValue=\"10\" yAxisName=\"hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" numberPrefix=\"\" showNames=\"0\" showValues=\"0\"  showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\"></graph>";
				}
				
				break;
			
			case "page" : 
				$statisticsCheck = mysql_query("SELECT * FROM `pagehits`");
				
				if (mysql_fetch_array($statisticsCheck)) {
					$statisticsGrabber = mysql_query("SELECT * FROM `pagehits` ORDER BY `id` ASC");
					
					echo "<graph caption=\"Page Hits\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\">";
					
					while($statistics = mysql_fetch_array($statisticsGrabber)) {
						$id = $statistics['page'];
						$pageGrabber = mysql_query("SELECT * FROM `pages` WHERE `id` = '{$id}'", $connDBA);
						$pagePrep = mysql_fetch_array($pageGrabber);
						$page = unserialize($pagePrep['content' . $pagePrep['display']]);
						
						echo "<set name=\"" . stripslashes($page['title']) . "\" value=\"" . $statistics['hits'] . "\" hoverText=\"" . stripslashes($page['title']) . " (" . $statistics['hits'] . " Hits)\"/>";
					}
					
					echo "</graph>";
				} else {
					echo "<graph caption=\"Page Hits\" subcaption=\"No Data\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\"></graph>";
				}
				
				break;
		}
	}
?>