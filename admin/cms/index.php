<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("createPage") == "true" || privileges("editPage") == "true" || privileges("deletePage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Check to see if pages exist
	$pageCheck = mysql_query("SELECT * FROM pages WHERE `position` = 1", $connDBA);
	
	if (mysql_fetch_array($pageCheck)) {
		$pageGrabber = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);
	} else {
		$pageGrabber = 0;
	}

//Reorder pages	
	if (privileges("editPage") == "true") {
		if (isset ($_GET['action']) && $_GET['action'] == "modifySettings" && isset($_GET['id']) && isset($_GET['position']) && isset($_GET['currentPosition'])) {
		//Grab all necessary data	
		  //Grab the id of the moving item
		  $id = $_GET['id'];
		  //Grab the new position of the item
		  $newPosition = $_GET['position'];
		  //Grab the old position of the item
		  $currentPosition = $_GET['currentPosition'];
			  
		  //Do not process if item does not exist
		  //Get item name by URL variable
		  $getPageID = $_GET['position'];
	  
		  $pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE position = {$getPageID}", $connDBA);
		  $pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		  $pageCheckResult = $pageCheckArray['position'];
			   if (isset ($pageCheckResult)) {
				   $pageCheck = 1;
			   } else {
				  $pageCheck = 0;
			   }
		
		//If the item is moved up...
			if ($currentPosition > $newPosition) {
			//Update the other items first, by adding a value of 1
				$otherPostionReorderQuery = "UPDATE pages SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
				
			//Update the requested item	
				$currentItemReorderQuery = "UPDATE pages SET position = '{$newPosition}' WHERE id = '{$id}'";
				
			//Execute the queries
				$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
				$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
		
			//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
				header ("Location: index.php");
				exit;
		//If the item is moved down...
			} elseif ($currentPosition < $newPosition) {
			//Update the other items first, by subtracting a value of 1
				$otherPostionReorderQuery = "UPDATE pages SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
		
			//Update the requested item		
				$currentItemReorderQuery = "UPDATE pages SET position = '{$newPosition}' WHERE id = '{$id}'";
			
			//Execute the queries
				$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
				$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
				
			//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
				header ("Location: index.php");
				exit;
			}
		}
	}
	
//Set page avaliability
	if (privileges("editPage") == "true") {
		if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
			$id = $_POST['id'];
			
			if (!$_POST['option']) {
				$option = "";
			} else {
				$option = $_POST['option'];
			}
			
			$setAvaliability = "UPDATE pages SET `visible` = '{$option}' WHERE id = '{$id}'";
			mysql_query($setAvaliability, $connDBA);
			
			if (isset($_POST['redirect'])) {
				header ("Location:" . $root . "index.php?page=" . $id);
				exit;
			}
			
			header ("Location: index.php");
			exit;
		}
	}
	
//Delete a page
	if (privileges("deletePage") == "true") {
		if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['page']) && isset($_GET['id'])) {
			//Do not process if page does not exist
			//Get page name by URL variable
			$getPageID = $_GET['page'];
		
			$pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE position = {$getPageID}", $connDBA);
			$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
			$pageCheckResult = $pageCheckArray['position'];
				 if (isset ($pageCheckResult)) {
					 $pageCheck = 1;
				 } else {
					$pageCheck = 0;
				 }
		 
			if (!isset ($_GET['id']) || $_GET['id'] == 0 || $pageCheck == 0) {
				header ("Location: index.php");
				exit;
			} else {
				$deletePage = $_GET['id'];
				$pageLift = $_GET['page'];
				
				$pagePositionGrabber = mysql_query("SELECT * FROM pages WHERE position = {$pageLift}", $connDBA);
				$pagePositionFetch = mysql_fetch_array($pagePositionGrabber);
				$pagePosition = $pagePositionFetch['position'];
				
				$otherPagesUpdateQuery = "UPDATE pages SET position = position-1 WHERE position > '{$pagePosition}'";
				$deletePageQueryResult = mysql_query($otherPagesUpdateQuery, $connDBA);
				
				$deletePageQuery = "DELETE FROM pages WHERE id = {$deletePage}";
				$deletePageQueryResult = mysql_query($deletePageQuery, $connDBA);
				$deleteStatsQuery = "DELETE FROM pagehits WHERE page = {$deletePage}";
				$deleteStatsQueryResult = mysql_query($deleteStatsQuery, $connDBA);
				
				header ("Location: index.php");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Pages Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script src="../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Pages Control Panel</h2>
<p>This is the pages control panel, where you can add, edit, delete, and reorder pages.</p>
<p>&nbsp;</p>
<?php
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	
	if ($settings["autoPublishPage"] == "1") {
		$pageAccessCheck = mysql_query("SELECT * FROM `pages`", $connDBA);
	} else {
		$pageAccessCheck = mysql_query("SELECT * FROM `pages` WHERE `published` != '0'", $connDBA);
	}
	
	$pageAccess = mysql_fetch_array($pageAccessCheck);
	
	if (privileges("createPage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true" || $pageAccess) {
		echo "<div class=\"toolBar\">";
	}

	if (privileges("createPage") == "true") {
		echo "<a class=\"toolBarItem new\" href=\"manage_page.php\">Create New Page</a>";
	}
	
	if (privileges("siteSettings") == "true") {
		echo "<a class=\"toolBarItem settings\" href=\"site_settings.php\">Manage Site Settings</a>";
	}
	
	if (privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true") {
		echo "<a class=\"toolBarItem sideBar\" href=\"sidebar.php\">Manage Sidebar</a>";
	}
	
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		echo "<a class=\"toolBarItem external\" href=\"external.php\">External Content</a>";
	}
	
	if (privileges("viewStatistics") == "true") {
		echo "<a class=\"toolBarItem statistics\" href=\"../statistics/index.php\">Statistics</a>";
	}
	
	if ($pageAccess) {
		echo "<a class=\"toolBarItem search\" href=\"../../index.php\">Preview this Site</a>";
	}
	
	if (privileges("createPage") == "true" || privileges("siteSettings") == "true" || privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true" || privileges("viewStatistics") == "true" || $pageAccess) {
		echo "</div>";
	}

//Display message updates
	if (isset ($_GET['added']) && $_GET['added'] == "page") {
		if (privileges("publishPage") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") != "true") {
			successMessage("The page was successfully added");
		} elseif (privileges("publishPage") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") == "true") {
			successMessage("The page was successfully added");
		} elseif (privileges("publishPage") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") == "true") {
			successMessage("The page was successfully added");
		} elseif (privileges("publishPage") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") != "true") {
			successMessage("The page was successfully added. An administrator must approve the page before it can be displayed.");
		} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
			successMessage("The page was successfully added");
		} elseif(privileges("autoPublishPage") == "true") {
			successMessage("The page was successfully added");
		}
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "page") {
		if (privileges("publishPage") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") != "true") {
			successMessage("The page was successfully updated");
		} elseif (privileges("publishPage") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") == "true") {
			successMessage("The page was successfully updated");
		} elseif (privileges("publishPage") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") == "true") {
			successMessage("The page was successfully updated");
		} elseif (privileges("publishPage") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishPage") != "true") {
			//successMessage("The page was successfully updated. An administrator must approve the update before the update can be displayed.");
			successMessage("The page was successfully updated.");
		} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
			successMessage("The page was successfully updated");
		} elseif(privileges("autoPublishPage") == "true") {
			successMessage("The page was successfully updated");
		}
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "logo") {
		successMessage("The logo was successfully updated. It may take a few moments to update system wide.");
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "icon") {
		successMessage("The browser icon was successfully updated. It may take a few moments to update system wide.");
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "siteInfo") {
		successMessage("The site information was successfully updated");
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "theme") {
		successMessage("The theme was successfully updated");
	}
	
	if (!isset($_GET['added']) && !isset($_GET['updated'])) {
		echo "<br />";
	}

//Table header, only displayed if pages exist
	if ($pageGrabber !== 0) {
	echo "<table class=\"dataTable\"><tbody><tr>";
	
	if (privileges("editPage") == "true") {
		echo "<th width=\"25\" class=\"tableHeader\"></th>";
	}
	
	if (privileges("autoPublishPage", "true") != "true") {
		echo "<th width=\"50\" class=\"tableHeader\">Status</th>";
	}
	
	if (privileges("editPage") == "true") {
		echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
	}
		
	echo "<th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th>";
	
	if (privileges("editPage") == "true") {
		echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
	}
	
	if (privileges("deletePage") == "true") {
		echo "<th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
	}
	
	//Loop through each page.
		while($pageData = mysql_fetch_array($pageGrabber)) {
			echo "<tr";
		//Alternate the color of each row.
			if ($pageData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
			if (privileges("editPage") == "true") {
				if ($pageData['published'] == "0" && privileges("autoPublishPage", "true") != "true") {
					echo "<td width=\"25\"><div align=\"center\"><span class=\"noShow\" onmouseover=\"Tip('This page must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\"></span></div></div></td>";
				} else {
					echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $pageData['id'] . "\" class=\"visible"; if ($pageData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $pageData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $pageData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($pageData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
				}
			}
			
			if (privileges("autoPublishPage", "true") != "true") {
				echo "<td width=\"50\">";
				
				if (privileges("publishPage") == "true") {
					switch ($pageData['published']) {
						case "0" : echo "<a href=\"javascript:void\" class=\"notPublished\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve_page.php?id=" . $pageData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('This page must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
						case "1" : echo "<a href=\"javascript:void\" class=\"updatePending\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve_page.php?id=" . $pageData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('This update must be approved <br />before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
						case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This page is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
					}
				} else {
					switch ($pageData['published']) {
						case "0" : echo "<span class=\"notPublished\" style=\"text-decoration:none;\" onmouseover=\"Tip('This page must be approved by an <br />administrator before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
						case "1" : echo "<span class=\"updatePending\" style=\"text-decoration:none;\" onmouseover=\"Tip('This update must be approved by an <br />administrator before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
						case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This page is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
					}
				}
				
				echo "</td>";
			}
			
			if (privileges("editPage") == "true") {
				echo "<td width=\"75\"><form name=\"pages\" action=\"index.php\"><input type=\"hidden\" name=\"id\" value=\"" . $pageData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $pageData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
				
				$pageCount = mysql_num_rows($pageGrabber);
				for ($count=1; $count <= $pageCount; $count++) {
					echo "<option value=\"{$count}\"";
					if ($pageData ['position'] == $count) {
						echo " selected=\"selected\"";
					}
					echo ">" . $count . "</option>";
				}
				
				echo "</select></form></td>";
			}
			
			if (privileges("autoPublishPage", "true") != "true") {
				if ($pageData['published'] == "0") {
					echo "<td width=\"200\">";
					
					if ($pageData['position'] == "1") {
						echo "<span class=\"homePage\">" . commentTrim(30, $pageData['title']) . "</span>";
					} else {
						echo commentTrim(30, $pageData['title']);
					}
					
					echo "</td>";
				} else {
					echo "<td width=\"200\">";
					
					if ($pageData['position'] == "1") {
						echo "<span class=\"homePage\"><a href=\"../../index.php?page=" . $pageData['id'] . "\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\">" . commentTrim(30, $pageData['title']) . "</a></span>";
					} else {
						echo "<a href=\"../../index.php?page=" . $pageData['id'] . "\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\">" . commentTrim(30, $pageData['title']) . "</a>";
					}
					
					echo "</td>";
				}
			} else {
				echo "<td width=\"200\">";
				
				if ($pageData['position'] == "1") {
					echo "<span class=\"homePage\"><a href=\"../../index.php?page=" . $pageData['id'] . "\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\">" . commentTrim(30, $pageData['title']) . "</a></span>";
				} else {
					echo "<a href=\"../../index.php?page=" . $pageData['id'] . "\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\">" . commentTrim(30, $pageData['title']) . "</a>";
				}
				
				echo "</td>";
			}
			
			echo "<td>";
			
			if (privileges("autoPublishPage", "true") != "true") {
				if ($pageData['message'] != "1") {
					if ($pageData['published'] == "0") {
						echo "<span class=\"notAssigned\">Waiting for approval</span>";
					} else {
						if ($pageData['display'] == "1") {
							echo commentTrim(100, $pageData['content1']);
						} else {
							echo commentTrim(100, $pageData['content2']);
						}
					}
				} else {
					echo "<span class=\"alertNotAssigned\">Improvements required prior to publication</span>";
				}
			} else {
				if ($pageData['display'] == "1") {
					echo commentTrim(100, $pageData['content1']);
				} else {
					echo commentTrim(100, $pageData['content2']);
				}
			}
			
			echo "</td>";
			
			if (privileges("editPage") == "true") {
				if (privileges("publishPage") == "true") {
					echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_page.php?id=" . $pageData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\"></a></td>";
				} else {
					if ($pageData['published'] != "0") {
						echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_page.php?id=" . $pageData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\"></a></td>";
					} else {
						echo "<td width=\"50\"><span class=\"action noEdit\" onmouseover=\"Tip('This page must be approved first')\" onmouseout=\"UnTip()\"></span></td>";
					}
				}
			}
			
			if (privileges("deletePage") == "true") {
				echo "<td width=\"50\"><a class=\"action delete\" href=\"index.php?action=delete&page=" . $pageData['position'] . "&id=" . $pageData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\"></a></td>";
			}
		}
		
		echo "</tr></tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no pages.";
		
		if (privileges("createPage") == "true") {
			echo " <a href=\"manage_page.php\">Create one now</a>.</div>";
		} else {
			echo " You do not have the privileges to create a page.";
		}
	 }
?>
<?php footer(); ?>
</body>
</html>