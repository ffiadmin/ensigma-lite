<?php require_once('Connections/connDBA.php'); ?>
<?php login(); ?>
<?php
//Check to see if any pages exist
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	
	if ($settings['autoPublishPage'] == "1") {
		$pagesExistGrabber = mysql_query("SELECT * FROM pages WHERE position = '1'", $connDBA);
	} else {
		$pagesExistGrabber = mysql_query("SELECT * FROM pages WHERE position = '1' AND `published` != '0'", $connDBA);
	}
	
	$pagesExistArray = mysql_fetch_array($pagesExistGrabber);
	$pagesExistResult = $pagesExistArray['position'];
	
	if (isset ($pagesExistResult)) {
		$pagesExist = 1;
	} else {
		$pagesExist = 0;
	}
	
//Block access to unpublished pages
	if (isset ($_GET['page'])) {
		$pageAccessGrabber = mysql_query("SELECT * FROM pages WHERE `id` = '{$_GET['page']}'");
		$pageAccess = mysql_fetch_array($pageAccessGrabber);
		
		if ($settings['autoPublishPage'] == "0" && $pageAccess['published'] == "0") {
			header("Location: index.php");
			exit;
		}
	}
	
//If no page URL variable is defined, then choose the home page
	if (!isset ($_GET['page']) || $_GET['page'] == "") {
	//Grab the page data
		if ($settings['autoPublishPage'] == "1") {
			$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE position = '1'", $connDBA));
		} else {
			$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE position = '1' AND `published` != '0'", $connDBA));
		}
		
	//Hide the admin menu if an incorrect page displays		
		if ($pagesExist == "1") {
			$privilegesCheckGrabber = mysql_query("SELECT * FROM privileges WHERE id = '1'", $connDBA);
			$privilegesCheck = mysql_fetch_array($privilegesCheckGrabber);
			
			if ($privilegesCheck['autoPublishPage'] == "1") {
				$pageCheck = 1;
			} else {
				if ($pageInfo['published'] == "0") {
					$pageCheck = 0;
				} else {
					$pageCheck = 1;
				}
			}
		} else {
			$pageCheck = 0;
		}
	} else {		
	//Grab the page data
		$getPageID = $_GET['page'];
		$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE id = {$getPageID}", $connDBA));
		
	//Hide the admin menu if an incorrect page displays
		$pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE id = {$getPageID}", $connDBA);
		$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		$pageCheckResult = $pageCheckArray['position'];
		
		if (isset ($pageCheckResult)) {
			$privilegesCheckGrabber = mysql_query("SELECT * FROM privileges WHERE id = '1'", $connDBA);
			$privilegesCheck = mysql_fetch_array($privilegesCheckGrabber);
			
			if ($privilegesCheck['autoPublishPage'] == "1") {
				$pageCheck = 1;
			} else {
				if ($pageCheckArray['published'] == "0") {
					$pageCheck = 0;
				} else {
					$pageCheck = 1;
				}
			}
		} else {
			$pageCheck = 0;
		}	
	}
//Grab the sidebar	
	if ($settings['autoPublishSideBar'] == "1") {
		$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on'", $connDBA);
	} else {
		$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' AND published != '0'", $connDBA);
	}
	
	$sideBarResult = mysql_fetch_array($sideBarCheck);
	
//Process the comments
	if (isset($_POST['submit']) && !empty($_POST['id']) && !empty($_POST['comment'])) {
		$pageID = $_GET['page'];
		$id = $_POST['id'];
		$comment = $_POST['comment'];
		$date = date("D, M j, Y") . " at " . date("g:i a");
		
		if ($pageID == "") {
			$oldDataGrabber = mysql_query("SELECT * FROM `pages` WHERE `position` = '1'", $connDBA);
		} else {
			$oldDataGrabber = mysql_query("SELECT * FROM `pages` WHERE `id` = '{$pageID}'", $connDBA);
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
			mysql_query("UPDATE `pages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
		} else {
			mysql_query("UPDATE `pages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
		}
		
		header("Location: index.php?page=" . $pageID . "&message=added");
		exit;
	}
	
//Delete a comment
	if (isset($_SESSION['MM_UserGroup']) && privileges("deleteComments") == "true") {
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
				$oldDataGrabber = mysql_query("SELECT * FROM `pages` WHERE `id` = '{$pageID}'", $connDBA);
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
					mysql_query("UPDATE `pages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
				} else {
					mysql_query("UPDATE `pages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
				}
				
				header("Location: index.php?page=" . $pageID . "&message=deleted");
				exit;
		//If all comments are deleted
			} else {
				$comments = "";
				$names = "";
				$dates = "";
				
				if ($pageID == "") {
					mysql_query("UPDATE `pages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `position` = '1'", $connDBA);
				} else {
					mysql_query("UPDATE `pages` SET `name` = '{$names}', `date` = '{$dates}', `comment` = '{$comments}' WHERE `id` = '{$pageID}'", $connDBA);
				}
				
				header("Location: index.php?page=" . $pageID . "&message=deletedAll");
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
		$title = "Setup Required";
		
		if (!isset($_SESSION['MM_Username'])) {
			$content = "Please <a href=\"login.php\">login</a> to create your first page.";
		} else {
			$content = "Please <a href=\"admin/cms/manage_page.php\">create your first page</a>.";
		}
	} elseif ($pageInfo == 0 && $pagesExist == 1) {
		$title = "Page Not Found";
		$content = "<p>The page you are looking for was not found on our system</p><p>&nbsp;</p><p align=\"center\"><input type=\"button\" name=\"continue\" id=\"continue\" value=\"Continue\" onclick=\"history.go(-1)\" /></p>";
	} else {
		if ($pageInfo['display'] == "1") {
			$title = $pageInfo['title'];
			$content = $pageInfo['content1'];
			$commentsDisplay = $pageInfo['comments1'];
		} else {
			$title = $pageInfo['title'];
			$content = $pageInfo['content2'];
			$commentsDisplay = $pageInfo['comments2'];
		}
	}
	
	title(stripslashes(htmlentities($title))); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<?php meta(); ?>
<script src="javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body>
<?php tooltip(); ?>
<?php
	topPage("public");
?>
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

//Display content based on login status
	if (isset($_SESSION['MM_Username']) && isset($pageCheck) && $pageCheck !== 0) {
	//The admin toolbox div
		echo "<form name=\"pages\" method=\"post\" action=\"admin/cms/index.php\"><div class=\"toolBar noPadding\"><div align=\"center\">";
		
		if (privileges("editPage") == "true") {
			echo "<a href=\"admin/cms/manage_page.php?id=" . $pageInfo['id'] . "\">Edit This Page</a>  | Visible: <input type=\"hidden\" name=\"action\" value=\"setAvaliability\" /><input type=\"hidden\" name=\"id\" value=\"" .  $pageInfo['id'] . "\" /><input type=\"hidden\" name=\"redirect\" value=\"true\" /><select name=\"option\" onchange=\"this.form.submit();\"><option value=\"on\""; 
			if ($pageInfo['visible'] == "on") {echo " selected=\"selected\"";} 
			echo ">Yes</option><option value=\"\""; 
			if ($pageInfo['visible'] == "") {echo " selected=\"selected\"";} 
			echo ">No</option></select> | ";
		}
			
		echo "<a href=\"admin/index.php\">Back to Staff Home Page</a> | <a href=\"admin/cms/index.php\">Back to Pages</a> | <a href=\"admin/cms/sidebar.php\">Back to Sidebar</a></div></div></form>";
	}
	
	if (isset($_SESSION['MM_Username']) && $pageInfo == 0 && $pagesExist == 0) {
	//The admin toolbox div
		echo "<div class=\"toolBar noPadding\"><div align=\"center\"><a href=\"admin/index.php\">Back to Staff Home Page</a> | <a href=\"admin/cms/index.php\">Back to Pages</a> | <a href=\"admin/cms/sidebar.php\">Back to Sidebar</a></div></div>";
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
	echo "<h2>" . stripslashes($title) . "</h2>" . stripslashes($content);
	
//Display the comments
	if (isset($commentsDisplay) && $commentsDisplay == "1") {
		$arrayCheck = unserialize($pageInfo['comment']);
		
		if (is_array($arrayCheck) && !empty($arrayCheck)) {
			$values = sizeof(unserialize($pageInfo['date'])) - 1;
			$names = unserialize($pageInfo['name']);
			$dates = unserialize($pageInfo['date']);
			$comments = unserialize($pageInfo['comment']);
			
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Comments";
			
			if (isset($_SESSION['MM_UserGroup'])) {
				if (privileges("deleteComments") == "true" && !empty($comments)) {
					if (isset ($_GET['page'])) {
						$processor = "?page=" . $_GET['page'] . "&";
					} else {
						$processor = "?";
					}
					
					echo "<a class=\"action smallDelete\" href=\"index.php" . $processor . "action=delete&comment=all\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete all comments')\" onmouseout=\"UnTip()\"></a>";
				}
			}
			
			echo "</p>";
			
			for ($count = 0; $count <= $values; $count++) {
				echo "<div class=\"commentBox\">";
				
				if (is_numeric($names[$count])) {
					$userID = $names[$count];
					$userGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$userID}'", $connDBA);
					$user = mysql_fetch_array($userGrabber);
					echo "<p class=\"commentTitle\">" . $user['firstName'] . " " . $user['lastName'] . " commented on " . $dates[$count];
				} else {
					echo "<p class=\"commentTitle\">" . $names[$count] . " commented on " . $dates[$count];
				}
				
				if (isset($_SESSION['MM_UserGroup'])) {
					if (privileges("deleteComments") == "true") {
						if (isset ($_GET['page'])) {
							$processor = "?page=" . $_GET['page'] . "&";
						} else {
							$processor = "?";
						}
						
						$commentID = $count + 1;
						
						echo "<a class=\"action smallDelete\" href=\"index.php" . $processor . "action=delete&comment=" . $commentID . "\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete this comment')\" onmouseout=\"UnTip()\"></a>";
					}
				}
				
				echo "</p>";
				echo commentTrim(0, stripslashes($comments[$count]), true);
				echo "</div>";
				
				unset($userGrabber);
				unset($user);
			}
		} else {
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Comments";
			
			if (isset($_SESSION['MM_UserGroup'])) {
				if (privileges("deleteComments") == "true" && !empty($comments)) {
					if (isset ($_GET['page'])) {
						$processor = "?page=" . $_GET['page'] . "&";
					} else {
						$processor = "?";
					}
					
					echo "<a class=\"action smallDelete\" href=\"index.php" . $processor . "action=delete&comment=all\" onclick=\"return confirm('This action cannot be undone. Continue?')\" onmouseover=\"Tip('Delete all comments')\" onmouseout=\"UnTip()\"></a>";
				}
			}
			
			echo "</p><div class=\"noResults\">No comments yet! Be the first to comment.</div>";
		}
		
		if (isset ($_GET['page'])) {
			$processor = "?page=" . $_GET['page'];
		} else {
			$processor = "";
		}
		
		if (isset($_SESSION['MM_UserGroup'])) {
			$userName = $_SESSION['MM_Username'];
			$userGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}'", $connDBA);
			$user = mysql_fetch_array($userGrabber);
			
			echo "<form name=\"comments\" id=\"validate\" action=\"index.php" . $processor . "\" method=\"post\" onsubmit=\"return errorsOnSubmit(this)\"><input type=\"hidden\" name=\"id\" id=\"id\" value=\"" . $user['id'] . "\" />";
			echo "<blockquote><textarea name=\"comment\" id=\"comment\" style=\"width:450px;\" class=\"validate[required]\"></textarea><br /><p>";
			submit("submit", "Add Comment");
			echo "</p></blockquote></form>";
		} else {
			echo "<form name=\"comments\" id=\"validate\" action=\"index.php" . $processor . "\" method=\"post\"><p>Your name:</p><blockquote><input type=\"text\" name=\"id\" id=\"id\" class=\"validate[required]\" size=\"50\" autocomplete=\"off\" /></blockquote>";
			echo "<p>Comment:</p><blockquote><textarea name=\"comment\" id=\"comment\" style=\"width:450px;\" class=\"validate[required]\"></textarea><br /><p>";
			submit("submit", "Add Comment");
			echo "</p></blockquote></form>";
		}
	}
	
//Display the sidebar
	if ($sideBarResult) {
		if ($settings['autoPublishSideBar'] == "1") {
			$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' ORDER BY `position` ASC", $connDBA);
		} else {
			$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' AND published != '0' ORDER BY `position` ASC", $connDBA);
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
				$content = stripslashes($sideBar['content1']);
			} else {
				$content = stripslashes($sideBar['content2']);
			}
			
			switch ($sideBar['type']) {
			//If this is a custom content box
				case "Custom Content" : 				
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "</h2></div></div><div class=\"content\">" . $content . "</div></div>";
					} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") != "true") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "</h2></div></div><div class=\"content\">" . $content . "</div></div>";
					} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") == "true") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "&nbsp;<a class=\"smallEdit\" href=\"admin/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div><div class=\"content\">" . stripslashes($content) . "</div></div>";
					} break;
			//If this is a login box	
				case "Login" : 
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "</h2></div></div><div class=\"content\"><form id=\"login\" name=\"login\" method=\"post\" action=\"index.php\"><div align=\"center\"><div style=\"width:75%;\"><p>User name: <input type=\"text\" name=\"username\" id=\"username\" autocomplete=\"off\" /><br />Password: <input type=\"password\" name=\"password\" id=\"password\" autocomplete=\"off\" /></p><p><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Login\" /></p></div></div></form></div></div>";
					} elseif (isset($_SESSION['MM_Username']) && privileges("editSideBar") == "true") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . stripslashes($sideBar['title']) . "&nbsp;<a class=\"smallEdit\" href=\"admin/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div></div>";
					} break;
			  }
		}
		
		echo "</div></div>";
	}
?>
<?php
	stats("true");
	footer("public");
?>
</body>
</html>