<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("viewStaffPage") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Check to see if any pages exist
	$pagesExistGrabber = mysql_query("SELECT * FROM staffpages WHERE position = '1'", $connDBA);
	$pagesExistArray = mysql_fetch_array($pagesExistGrabber);
	$pagesExistResult = $pagesExistArray['position'];
	
	if ($pagesExistGrabber) {
		$pagesExist = 1;
	} else {
		$pagesExist = 0;
	}
	
//If no page URL variable is defined, then choose the first page
	if (!isset ($_GET['page']) || $_GET['page'] == "") {
	//Grab the page data	 
		$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM staffpages WHERE position = '1'", $connDBA));
		
	//Redirect if an incorrect page displays
		$pageCheckGrabber = mysql_query("SELECT * FROM staffpages WHERE position = '1'", $connDBA);
		$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		$pageCheckResult = $pageCheckArray['position'];
		
		if (isset ($pageCheckResult)) {
			if (privileges("autoPublishStaffPage") == "true") {
				$pageCheck = 1;
			} else {
				if ($pageCheckArray['published'] != "0")  {
					$pageCheck = 1;
				} else {
					header("Location: index.php");
					exit;
				}
			}
		} else {
			header("Location: index.php");
			exit;
		}
	} else {		
	//Grab the page data
		$getPageID = $_GET['page'];
		$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM staffpages WHERE id = {$getPageID}", $connDBA));
		
	//Redirect if an incorrect page displays
		$pageCheckGrabber = mysql_query("SELECT * FROM staffpages WHERE id = {$getPageID}", $connDBA);
		$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		$pageCheckResult = $pageCheckArray['position'];
		
		if (isset ($pageCheckResult)) {
			if (privileges("autoPublishStaffPage") == "true") {
				$pageCheck = 1;
			} else {
				if ($pageCheckArray['published'] != "0")  {
					$pageCheck = 1;
				} else {
					header("Location: index.php");
					exit;
				}
			}
		} else {
			header("Location: index.php");
			exit;
		}	
	}
	
//Process the comments
	if (privileges("addStaffComments") == "true") {
		if (isset($_POST['submit']) && !empty($_POST['id']) && !empty($_POST['comment'])) {
			$pageID = $_GET['page'];
			$id = $_POST['id'];
			$comment = $_POST['comment'];
			$date = date("D, M j, Y") . " at " . date("g:i a");
			
			if ($pageID == "") {
					$oldDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `position` = '1'", $connDBA);
			} else {
				$oldDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `id` = '{$pageID}'", $connDBA);
			}
			
			$oldData = mysql_fetch_array($oldDataGrabber);
			$oldComments = unserialize($oldData['comment']);
			$oldNames = unserialize($oldData['name']);
			$oldDates = unserialize($oldData['date']);
	
			if (is_array($oldComments)) {
				array_push($oldComments, $comment);
				array_push($oldNames, $id);
				array_push($oldDates, $date);
					
				$comments = mysql_real_escape_string(serialize($oldComments));
				$names = mysql_real_escape_string(serialize($oldNames));
				$dates = mysql_real_escape_string(serialize($oldDates));
			} else {
				$comments = mysql_real_escape_string(serialize(array($comment)));
				$names = mysql_real_escape_string(serialize(array($id)));
				$dates = mysql_real_escape_string(serialize(array($date)));
			}
			
			if ($pageID == "") {
				mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
			} else {
				mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
			}
			
			header("Location: page.php?page=" . $pageID . "&message=added");
			exit;
		}
	}
	
//Delete a comment
	if (privileges("deleteStaffComments") == "true") {
		if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['comment'])) {
			if (!$_GET['page']) {
				$pageIDGrabber = mysql_query("SELECT * FROM `pages` WHERE `position` = '1'", $connDBA);
				$pageIDArray = mysql_fetch_array($pageIDGrabber);
				$pageID = $pageIDArray['id'];
			} else {
				$pageID = $_GET['page'];
			}
			
			$comment = $_GET['comment'];
			
		//If only a single comment is deleted
			if (is_numeric($comment)) {
				$oldDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `id` = '{$pageID}'", $connDBA);
				$oldData = mysql_fetch_array($oldDataGrabber);
				$values = sizeof(unserialize($oldData['date'])) - 1;
				$oldComments = unserialize($oldData['comment']);
				$oldNames = unserialize($oldData['name']);
				$oldDates = unserialize($oldData['date']);
				
				for ($count = 0; $count <= $values; $count++) {
					if ($count == $comment - 1) {
						unset($oldComments[$count]);
						unset($oldNames[$count]);
						unset($oldDates[$count]);
					}
				}
				
				$comments = mysql_real_escape_string(serialize(array_merge($oldComments)));
				$names = mysql_real_escape_string(serialize(array_merge($oldNames)));
				$dates = mysql_real_escape_string(serialize(array_merge($oldDates)));
				
				if ($pageID == "") {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
				} else {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
				}
				
				header("Location: page.php?page=" . $pageID . "&message=deleted");
				exit;
		//If all comments are deleted
			} else {
				$comments = "";
				$names = "";
				$dates = "";
				
				if ($pageID == "") {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
				} else {
					mysql_query("UPDATE `staffpages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
				}
				
				header("Location: page.php?page=" . $pageID . "&message=deletedAll");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	$grabContent = $pageInfo['display'];

	if ($pageInfo == 0 && $pagesExist == 0) {
		header("Location: index.php");
		exit;
	} else {
		if ($pageInfo['display'] == "1") {
			$content = $pageInfo['content1'];
			$commentsDisplay = $pageInfo['comments1'];
		} else {
			$content = $pageInfo['content2'];
			$commentsDisplay = $pageInfo['comments2'];
		}
	}
	
	title($pageInfo['title']); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
</head>
<body>
<?php tooltip(); ?>
<?php topPage(); ?>
<?php
//Display content based on login status
	if (isset($_SESSION['MM_Username']) && isset($pageCheck) && $pageCheck !== 0) {
	//The admin toolbox div
		echo "<div class=\"toolBar\">";
		
		if (privileges("editStaffPage") == "true") {
			echo "<a class=\"toolBarItem editTool\" href=\"manage_page.php?id=" . $pageInfo['id'] . "\">Edit This Page</a>";
		}
		
		echo "<a class=\"toolBarItem back\" href=\"index.php\">Back to Staff Pages</a></div>";
	}
	
//Display message updates
	if (isset($_GET['message'])) {
		if ($_GET['message'] == "added") {
			 successMessage("Your comment was added");
		} elseif ($_GET['message'] == "deleted") {
			successMessage("The comment was deleted");
		} elseif ($_GET['message'] == "deletedAll") {
			successMessage("All comments were deleted");
		}
	}
	
//Display the page content	
	echo "<h2>" . $pageInfo['title'] . "</h2>" . $content;
	
//Display the comments
	if ($commentsDisplay == "1") {
		$arrayCheck = unserialize($pageInfo['comment']);
		
		if (is_array($arrayCheck) && !empty($arrayCheck)) {
			$values = sizeof(unserialize($pageInfo['date'])) - 1;
			$names = unserialize($pageInfo['name']);
			$dates = unserialize($pageInfo['date']);
			$comments = unserialize($pageInfo['comment']);
			
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Comments";
			
			if (privileges("deleteStaffComments") == "true" && !empty($comments)) {
				if (isset ($_GET['page'])) {
					$processor = "?page=" . $_GET['page'] . "&";
				} else {
					$processor = "?";
				}
				
				echo "<a class=\"action smallDelete\" href=\"page.php" . $processor . "action=delete&comment=all\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete all comments')\" onmouseout=\"UnTip()\"></a>";
			}
			
			echo "</p>";
			
			for ($count = 0; $count <= $values; $count++) {
				$userID = $names[$count];
				$userGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$userID}'", $connDBA);
				$user = mysql_fetch_array($userGrabber);
				
				echo "<div class=\"commentBox\">";
				echo "<p class=\"commentTitle\">" . $user['firstName'] . " " . $user['lastName'] . " commented on " . $dates[$count];
				
				if (privileges("deleteStaffComments") == "true") {
					if (isset ($_GET['page'])) {
						$processor = "?page=" . $_GET['page'] . "&";
					} else {
						$processor = "?";
					}
					
					$commentID = $count + 1;
					
					echo "<a class=\"action smallDelete\" href=\"page.php" . $processor . "action=delete&comment=" . $commentID . "\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete this comment')\" onmouseout=\"UnTip()\"></a>";
				}
				
				echo "</p>";
				echo stripslashes($comments[$count]);
				echo "</div>";
				
				unset($userGrabber);
				unset($user);
			}
		} else {
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Comments";
			
			if (privileges("deleteStaffComments") == "true" && !empty($comments)) {
				if (isset ($_GET['page'])) {
					$processor = "?page=" . $_GET['page'] . "&";
				} else {
					$processor = "?";
				}
				
				echo "<a class=\"action smallDelete\" href=\"page.php" . $processor . "action=delete&comment=all\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete all comments')\" onmouseout=\"UnTip()\"></a>";
			}
			
			echo "</p><div class=\"noResults\">No comments yet! Be the first to comment.</div>";
		}
		
		if (privileges("addStaffComments") == "true") {
			$userName = $_SESSION['MM_Username'];
			$userGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}'", $connDBA);
			$user = mysql_fetch_array($userGrabber);
			if (isset ($_GET['page'])) {
				$processor = "?page=" . $_GET['page'];
			} else {
				$processor = "";
			}
			
			echo "<form name=\"comments\" id=\"validate\" action=\"page.php" . $processor . "\" method=\"post\"><input type=\"hidden\" name=\"id\" id=\"id\" value=\"" . $user['id'] . "\" />";
			echo "<blockquote><textarea name=\"comment\" id=\"comment\" style=\"width:450px;\" class=\"validate[required]\"></textarea><br/><p>";
			submit("submit", "Add Comment");
			echo "</p></blockquote></form>";
		}
	}
?>
<?php footer(); ?>
</body>
</html>