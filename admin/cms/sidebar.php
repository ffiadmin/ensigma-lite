<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("createSideBar") == "true" || privileges("editSideBar") == "true" || privileges("deleteSideBar") == "true" || privileges("sideBarSettings") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Check to see if pages exist
	$itemCheck = mysql_query("SELECT * FROM sidebar WHERE `position` = 1", $connDBA);
	
	if (mysql_fetch_array($itemCheck)) {
		$itemGrabber = mysql_query("SELECT * FROM sidebar ORDER BY position ASC", $connDBA);
	} else {
		$itemGrabber = 0;
	}

//Reorder items	
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
	  $getitemID = $_GET['position'];
  
	  $itemCheckGrabber = mysql_query("SELECT * FROM sidebar WHERE position = {$getitemID}", $connDBA);
	  $itemCheckArray = mysql_fetch_array($itemCheckGrabber);
	  $itemCheckResult = $itemCheckArray['position'];
		   if (isset ($itemCheckResult)) {
			   $itemCheck = 1;
		   } else {
			  $itemCheck = 0;
		   }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE sidebar SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE sidebar SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that item when done.
			header ("Location: sidebar.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE sidebar SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE sidebar SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that item when done.
			header ("Location: sidebar.php");
			exit;
		}
	}

//Set item avaliability
	if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
		$id = $_POST['id'];
		if (!$_POST['option']) {
			$option = "";
		} else {
			$option = $_POST['option'];
		}
		
		$setAvaliability = "UPDATE sidebar SET `visible` = '{$option}' WHERE id = '{$id}'";
		mysql_query($setAvaliability, $connDBA);
		
		header ("Location: sidebar.php");
		exit;
	}
	
//Delete an item
	if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['item']) && isset($_GET['id'])) {
		//Do not process if item does not exist
		//Get item name by URL variable
		$getItemID = $_GET['item'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM sidebar WHERE position = {$getItemID}", $connDBA);
		$itemCheckArray = mysql_fetch_array($itemCheckGrabber);
		$itemCheckResult = $itemCheckArray['position'];
			 if (isset ($itemCheckResult)) {
				 $itemCheck = 1;
			 } else {
				$itemCheck = 0;
			 }
	 
		if (!isset ($_GET['id']) || $_GET['id'] == 0 || $itemCheck == 0) {
			header ("Location: sidebar.php");
			exit;
		} else {
			$deleteItem = $_GET['id'];
			$itemLift = $_GET['item'];
			
			$itemPositionGrabber = mysql_query("SELECT * FROM sidebar WHERE position = {$itemLift}", $connDBA);
			$itemPositionFetch = mysql_fetch_array($itemPositionGrabber);
			$itemPosition = $itemPositionFetch['position'];
			
			$otherItemsUpdateQuery = "UPDATE sidebar SET position = position-1 WHERE position > '{$itemPosition}'";
			$deleteItemQueryResult = mysql_query($otherItemsUpdateQuery, $connDBA);
			
			$deleteItemQuery = "DELETE FROM sidebar WHERE id = {$deleteItem}";
			$deleteItemQueryResult = mysql_query($deleteItemQuery, $connDBA);
			
			header ("Location: sidebar.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Sidebar Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script src="../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Sidebar Control Panel</h2>
<p>This is the sidebar control panel, where you can add, edit, delete, and reorder boxes. These boxes will contain content which can be accessed on a given side of every page on the public website.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem new" href="manage_sidebar.php">Create New Box</a>
<?php
	if (privileges("sideBarSettings") == "true") {
		echo "<a class=\"toolBarItem settings\" href=\"sidebar_settings.php\">Manage Sidebar Settings</a>";
	}
?>
<a class="toolBarItem back" href="index.php">Back to Pages</a>
<?php
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);
	
	if ($settings["autoPublishSideBar"] == "1") {
		$itemAccessCheck = mysql_query("SELECT * FROM `sidebar`", $connDBA);
	} else {
		$itemAccessCheck = mysql_query("SELECT * FROM `sidebar` WHERE `published` != '0'", $connDBA);
	}
	
	$itemAccess = mysql_fetch_array($itemAccessCheck);
	
	if ($itemAccess) {
		echo "<a class=\"toolBarItem search\" href=\"../../index.php\">Preview this Site</a>";
	}
?>
</div>
<?php
	//Display message updates
	if (isset ($_GET['added']) && $_GET['added'] == "item") {
		if (privileges("publishSideBar") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") != "true") {
			successMessage("The box was successfully added");
		} elseif (privileges("publishSideBar") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") == "true") {
			successMessage("The box was successfully added");
		} elseif (privileges("publishSideBar") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") == "true") {
			successMessage("The box was successfully added");
		} elseif (privileges("publishSideBar") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") != "true") {
			successMessage("The box was successfully added. An administrator must approve the box before it can be displayed.");
		} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
			successMessage("The box was successfully added");
		} elseif(privileges("autoPublishSideBar") == "true") {
			successMessage("The v was successfully added");
		}
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "item") {
		if (privileges("publishSideBar") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") != "true") {
			successMessage("The box was successfully updated");
		} elseif (privileges("publishSideBar") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") == "true") {
			successMessage("The box was successfully updated");
		} elseif (privileges("publishSideBar") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") == "true") {
			successMessage("The box was successfully updated");
		} elseif (privileges("publishSideBar") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishSideBar") != "true") {
			//successMessage("The box was successfully updated. An administrator must approve the update before the update can be displayed.");
			successMessage("The box was successfully updated.");
		} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
			successMessage("The box was successfully updated");
		} elseif(privileges("autoPublishSideBar") == "true") {
			successMessage("The box was successfully updated");
		}
	}
	
	if (isset ($_GET['updated']) && $_GET['updated'] == "settings") {
		successMessage("The sidebar settings were successfully updated.");
	}
	
	if (!isset ($_GET['updated']) && !isset ($_GET['added'])) {
		echo "<br />";
	}
?>
<?php
//Table header, only displayed if items exist.
	if ($itemGrabber !== 0) {
		echo "<table class=\"dataTable\"><tbody><tr>";
		
		if (privileges("editSideBar") == "true") {
			echo "<th width=\"25\" class=\"tableHeader\"></th>";
		}
		
		if (privileges("autoPublishSideBar", "true") != "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Status</th>";
		}
		
		if (privileges("editSideBar") == "true") {
			echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
		}
			
		echo "<th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\" width=\"150\">Type</th><th class=\"tableHeader\">Content</th>";
		
		if (privileges("editSideBar") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
		}
		
		if (privileges("deleteSideBar") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		}
		
	//Loop through each item.
		while($itemData = mysql_fetch_array($itemGrabber)) {
			echo "<tr";
		//Alternate the color of each row.
			if ($itemData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
			if (privileges("editSideBar") == "true") {
				if ($itemData['published'] == "0" && privileges("autoPublishSideBar", "true") != "true") {
					echo "<td width=\"25\"><div align=\"center\"><span class=\"noShow\" onmouseover=\"Tip('This box must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\"></span></div></div></td>";
				} else {
					echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"sidebar.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $itemData['id'] . "\" class=\"visible"; if ($itemData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $itemData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $itemData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($itemData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
				}
			}
			
			if (privileges("autoPublishSideBar", "true") != "true") {
				echo "<td width=\"50\">";
				
				if (privileges("publishSideBar") == "true") {
					switch ($itemData['published']) {
						case "0" : echo "<a href=\"javascript:void\" class=\"notPublished\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve_sidebar.php?id=" . $itemData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('This box must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
						case "1" : echo "<a href=\"javascript:void\" class=\"updatePending\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve_sidebar.php?id=" . $itemData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('This update must be approved <br />before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
						case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This box is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
					}
				} else {
					switch ($itemData['published']) {
						case "0" : echo "<span class=\"notPublished\" style=\"text-decoration:none;\" onmouseover=\"Tip('This box must be approved by an <br />administrator before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
						case "1" : echo "<span class=\"updatePending\" style=\"text-decoration:none;\" onmouseover=\"Tip('This update must be approved by an <br />administrator before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
						case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This box is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
					}
				}
				
				echo "</td>";
			}
			
			if (privileges("editSideBar") == "true") {
				echo "<td width=\"75\"><form name=\"items\" action=\"sidebar.php\"><input type=\"hidden\" name=\"id\" value=\"" . $itemData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $itemData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
				
				$itemCount = mysql_num_rows($itemGrabber);
				for ($count=1; $count <= $itemCount; $count++) {
					echo "<option value=\"{$count}\"";
					if ($itemData ['position'] == $count) {
						echo " selected=\"selected\"";
					}
					echo ">" . $count . "</option>";
				}
				
				echo "</select></form></td>";
			}
			
			echo "<td width=\"200\">" . commentTrim(25, $itemData['title']) . "</td>";
			echo "<td width=\"150\">" . $itemData['type'] . "</td>";
			echo "<td>";
			
			if (privileges("autoPublishSideBar", "true") != "true") {
				if ($itemData['message'] != "1") {
					if ($itemData['published'] == "0") {
						echo "<span class=\"notAssigned\">Waiting for approval</span>";
					} else {
						if ($itemData['display'] == "1") {
							if ($itemData['type'] == "Login") {
								echo "<span class=\"notAssigned\">None</span>";
							} else {
								echo commentTrim(50, $itemData['content1']);
							}
						} else {
							if ($itemData['type'] == "Login") {
								echo "<span class=\"notAssigned\">None</span>";
							} else {
								echo commentTrim(50, $itemData['content2']);
							}
						}
					}
				} else {
					echo "<span class=\"alertNotAssigned\">Improvements required prior to publication</span>";
				}
			} else {
				if ($itemData['display'] == "1") {
					if ($itemData['type'] == "Login") {
						echo "<span class=\"notAssigned\">None</span>";
					} else {
						echo commentTrim(50, $itemData['content1']);
					}
				} else {
					if ($itemData['type'] == "Login") {
						echo "<span class=\"notAssigned\">None</span>";
					} else {
						echo commentTrim(50, $itemData['content2']);
					}
				}
			}
			
			echo "</td>";
			
			if (privileges("editSideBar") == "true") {
				if (privileges("publishSideBar") == "true") {
					echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_sidebar.php?id=" . $itemData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($itemData['title']) . "</strong> page')\" onmouseout=\"UnTip()\"></a></td>";
				} else {
					if ($itemData['published'] != "0" || $itemData['message'] == "1") {
						echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_sidebar.php?id=" . $itemData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($itemData['title']) . "</strong> page')\" onmouseout=\"UnTip()\"></a></td>";
					} else {
						echo "<td width=\"50\"><span class=\"action noEdit\" onmouseover=\"Tip('This box must be approved first')\" onmouseout=\"UnTip()\"></span></td>";
					}
				}
			}
			
			if (privileges("deleteSideBar") == "true") {
				echo "<td width=\"50\"><a class=\"action delete\" href=\"sidebar.php?action=delete&item=" . $itemData['position'] . "&id=" . $itemData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($itemData['title']) . "</strong> box')\" onmouseout=\"UnTip()\"></a></td>";
			}
		}
		
		echo "</tr></tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no items. <a href=\"manage_sidebar.php\">Create one now</a>.</div>";
	 } 
?>
<?php footer(); ?>
</body>
</html>