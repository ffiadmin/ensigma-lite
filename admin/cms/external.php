<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Check to see if tabs exist
	$tabCheck = mysql_query("SELECT * FROM external WHERE `position` = 1", $connDBA);
	if (mysql_fetch_array($tabCheck)) {
		$tabGrabber = mysql_query("SELECT * FROM external ORDER BY position ASC", $connDBA);
	} else {
		$tabGrabber = 0;
	}

//Reorder tabs	
	if (privileges("editExternal") == "true") {
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
		  $getTabID = $_GET['position'];
	  
		  $tabCheckGrabber = mysql_query("SELECT * FROM external WHERE position = {$getTabID}", $connDBA);
		  $tabCheckArray = mysql_fetch_array($tabCheckGrabber);
		  $tabCheckResult = $tabCheckArray['position'];
			   if (isset ($tabCheckResult)) {
				   $tabCheck = 1;
			   } else {
				  $tabCheck = 0;
			   }
		
		//If the item is moved up...
			if ($currentPosition > $newPosition) {
			//Update the other items first, by adding a value of 1
				$otherPostionReorderQuery = "UPDATE external SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
				
			//Update the requested item	
				$currentItemReorderQuery = "UPDATE external SET position = '{$newPosition}' WHERE id = '{$id}'";
				
			//Execute the queries
				$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
				$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
		
			//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
				header ("Location: external.php");
				exit;
		//If the item is moved down...
			} elseif ($currentPosition < $newPosition) {
			//Update the other items first, by subtracting a value of 1
				$otherPostionReorderQuery = "UPDATE external SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
		
			//Update the requested item		
				$currentItemReorderQuery = "UPDATE external SET position = '{$newPosition}' WHERE id = '{$id}'";
			
			//Execute the queries
				$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
				$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
				
			//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
				header ("Location: external.php");
				exit;
			}
		}
	}
	
//Set tab avaliability
	if (privileges("editExternal") == "true") {
		if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
			$id = $_POST['id'];
			
			if (!$_POST['option']) {
				$option = "";
			} else {
				$option = $_POST['option'];
			}
			
			$setAvaliability = "UPDATE external SET `visible` = '{$option}' WHERE id = '{$id}'";
			mysql_query($setAvaliability, $connDBA);
			
			header ("Location: external.php");
			exit;
		}
	}
	
//Delete a tab
	if (privileges("deleteExternal") == "true") {
		if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['tab']) && isset($_GET['id'])) {
			//Do not process if tab does not exist
			//Get tab name by URL variable
			$getTabID = $_GET['tab'];
		
			$tabCheckGrabber = mysql_query("SELECT * FROM external WHERE position = {$getTabID}", $connDBA);
			$tabCheckArray = mysql_fetch_array($tabCheckGrabber);
			$tabCheckResult = $tabCheckArray['position'];
				 if (isset ($tabCheckResult)) {
					 $tabCheck = 1;
				 } else {
					$tabCheck = 0;
				 }
		 
			if (!isset ($_GET['id']) || $_GET['id'] == 0 || $tabCheck == 0) {
				header ("Location: external.php");
				exit;
			} else {
				$deleteTab = $_GET['id'];
				$tabLift = $_GET['tab'];
				
				$tabPositionGrabber = mysql_query("SELECT * FROM external WHERE position = {$tabLift}", $connDBA);
				$tabPositionFetch = mysql_fetch_array($tabPositionGrabber);
				$tabPosition = $tabPositionFetch['position'];
				
				$otherTabsUpdateQuery = "UPDATE external SET position = position-1 WHERE position > '{$tabPosition}'";
				$deleteTabQueryResult = mysql_query($otherTabsUpdateQuery, $connDBA);
				
				$deleteTabQuery = "DELETE FROM external WHERE id = {$deleteTab}";
				$deleteTabQueryResult = mysql_query($deleteTabQuery, $connDBA);
				
				header ("Location: external.php");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("External Content Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script src="../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>External Content Control Panel</h2>
<p>This is the external content control panel. Content created here can be embedded as a &quot;mini-site&quot; on other sites or blogs. Multiple pages can be embedded in this mini-site. Each of these pages are embedded as a tab. Copy the following HTML into your website or blog to view the mini-site.</p>
<div class="code">
&lt;div align=&quot;center&quot;&gt;&lt;iframe src=&quot;<?php echo $root; ?>external.php&quot; width=&quot;320&quot; height=&quot;240&quot; frameborder=&quot;0&quot;&gt;&lt;/div&gt;</div><br />
<?php
	$settingsGrabber = mysql_query("SELECT * FROM `privileges` WHERE `id` = '1'", $connDBA);
	$settings = mysql_fetch_array($settingsGrabber);

	if ($settings["autoPublishExternal"] == "1") {
		$tabAccessCheck = mysql_query("SELECT * FROM `external`", $connDBA);
	} else {
		$tabAccessCheck = mysql_query("SELECT * FROM `external` WHERE `published` != '0'", $connDBA);
	}
	
	$tabAccess = mysql_fetch_array($tabAccessCheck);
	
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		echo "<div class=\"toolBar\">";
	}

	if (privileges("createExternal") == "true") {
		echo "<a class=\"toolBarItem new\" href=\"manage_external.php\">Create New Tab</a>";
	}
	
	echo "<a class=\"toolBarItem back\" href=\"index.php\">Back to Pages</a>";
	
	if ($tabAccess) {
		echo "<a class=\"toolBarItem search\" href=\"javascript:void\"onclick=\"MM_openBrWindow('../../external.php','','status=yes,scrollbars=yes,resizable=yes,width=320,height=240')\">Preview this Content</a>";
	}
	
	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
		echo "</div>";
	}

	if (privileges("createExternal") == "true" || privileges("editExternal") == "true" || privileges("deleteExternal") == "true") {
	//Display message updates
		if (isset ($_GET['added']) && $_GET['added'] == "tab") {
			if (privileges("publishExternal") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") != "true") {
				successMessage("The tab was successfully added");
			} elseif (privileges("publishExternal") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") == "true") {
				successMessage("The tab was successfully added");
			} elseif (privileges("publishExternal") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") == "true") {
				successMessage("The tab was successfully added");
			} elseif (privileges("publishExternal") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") != "true") {
				successMessage("The tab was successfully added. An administrator must approve the tab before it can be displayed.");
			} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
				successMessage("The tab was successfully added");
			} elseif(privileges("autoPublishExternal") == "true") {
				successMessage("The tab was successfully added");
			}
		}
		
		if (isset ($_GET['updated']) && $_GET['updated'] == "tab") {
			if (privileges("publishExternal") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") != "true") {
				successMessage("The tab was successfully updated");
			} elseif (privileges("publishExternal") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") == "true") {
				successMessage("The tab was successfully updated");
			} elseif (privileges("publishExternal") == "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") == "true") {
				successMessage("The tab was successfully updated");
			} elseif (privileges("publishExternal") != "true" && $_SESSION['MM_UserGroup'] == "User" && privileges("autoPublishExternal") != "true") {
				//successMessage("The tab was successfully updated. An administrator must approve the update before the update can be displayed.");
				successMessage("The tab was successfully updated.");
			} elseif ($_SESSION['MM_UserGroup'] == "Administrator") {
				successMessage("The tab was successfully updated");
			} elseif(privileges("autoPublishExternal") == "true") {
				successMessage("The tab was successfully updated");
			}
		}
		
		if (!isset($_GET['added']) && !isset($_GET['updated'])) {echo "<br />";}
	
	//Table header, only displayed if tabs exist
		if ($tabGrabber !== 0) {
		echo "<table class=\"dataTable\"><tbody><tr>";
		
		if (privileges("editExternal") == "true") {
			echo "<th width=\"25\" class=\"tableHeader\"></th>";
		}
		
		if (privileges("autoPublishExternal", "true") != "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Status</th>";
		}
		
		if (privileges("editExternal") == "true") {
			echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
		}
			
		echo "<th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th>";
		
		if (privileges("editExternal") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
		}
		
		if (privileges("deleteExternal") == "true") {
			echo "<th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		}
		
		//Loop through each tab.
			while($tabData = mysql_fetch_array($tabGrabber)) {
				echo "<tr";
			//Alternate the color of each row.
				if ($tabData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				
				if (privileges("editExternal") == "true") {
					if ($tabData['published'] == "0" && privileges("autoPublishExternal", "true") != "true") {
						echo "<td width=\"25\"><div align=\"center\"><span class=\"noShow\" onmouseover=\"Tip('This tab must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\"></span></div></div></td>";
					} else {
						echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"external.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $tabData['id'] . "\" class=\"visible"; if ($tabData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $tabData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $tabData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($tabData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
					}
				}
				
				if (privileges("autoPublishExternal", "true") != "true") {
					echo "<td width=\"50\">";
					
					if (privileges("publishExternal") == "true") {
						switch ($tabData['published']) {
							case "0" : echo "<a href=\"javascript:void\" class=\"notPublished\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve_external.php?id=" . $tabData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('This tab must be approved <br />before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
							case "1" : echo "<a href=\"javascript:void\" class=\"updatePending\" style=\"text-decoration:none;\" onclick=\"MM_openBrWindow('approve_external.php?id=" . $tabData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('This update must be approved <br />before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</a>"; break;
							case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This tab is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
						}
					} else {
						switch ($tabData['published']) {
							case "0" : echo "<span class=\"notPublished\" style=\"text-decoration:none;\" onmouseover=\"Tip('This tab must be approved by an <br />administrator before it can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
							case "1" : echo "<span class=\"updatePending\" style=\"text-decoration:none;\" onmouseover=\"Tip('This update must be approved by an <br />administrator before the update can be viewed')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
							case "2" : echo "<span class=\"published\" style=\"text-decoration:none;\" onmouseover=\"Tip('This tab is published')\" onmouseout=\"UnTip()\">&nbsp;</span>"; break;
						}
					}
					
					echo "</td>";
				}
				
				if (privileges("editExternal") == "true") {
					echo "<td width=\"75\"><form name=\"tabs\" action=\"external.php\"><input type=\"hidden\" name=\"id\" value=\"" . $tabData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $tabData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
					
					$tabCount = mysql_num_rows($tabGrabber);
					for ($count=1; $count <= $tabCount; $count++) {
						echo "<option value=\"{$count}\"";
						if ($tabData ['position'] == $count) {
							echo " selected=\"selected\"";
						}
						echo ">" . $count . "</option>";
					}
					
					echo "</select></form></td>";
				}
				
				if (privileges("autoPublishExternal", "true") != "true") {
					if ($tabData['published'] == "0") {
						echo "<td width=\"200\">" .  commentTrim(25, $tabData['title']) . "</td>";
					} else {
						$tab = $tabData['position'] - 1;
						
						echo "<td width=\"200\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('../../external.php?tab=" . $tab . "','','status=yes,scrollbars=yes,resizable=yes,width=320,height=240')\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($tabData['title']) . "</strong> tab')\" onmouseout=\"UnTip()\">" . commentTrim(25, $tabData['title']) . "</a></td>";
					}
				} else {
					$tab = $tabData['position'] - 1;
					
					echo "<td width=\"200\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('../../external.php?tab=" . $tab . "','','status=yes,scrollbars=yes,resizable=yes,width=320,height=240')\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($tabData['title']) . "</strong> tab')\" onmouseout=\"UnTip()\">" . commentTrim(25, $tabData['title']) . "</a></td>";
				}
				
				echo "<td>";
				
				if (privileges("autoPublishExternal", "true") != "true") {
					if ($tabData['message'] != "1") {
						if ($tabData['published'] == "0") {
							echo "<span class=\"notAssigned\">Waiting for approval</span>";
						} else {
							if ($tabData['display'] == "1") {
								echo commentTrim(75, $tabData['content1']);
							} else {
								echo commentTrim(75, $tabData['content2']);
							}
						}
					} else {
						echo "<span class=\"alertNotAssigned\">Improvements required prior to publication</span>";
					}
				} else {
					if ($tabData['display'] == "1") {
						echo commentTrim(75, $tabData['content1']);
					} else {
						echo commentTrim(75, $tabData['content2']);
					}
				}
				
				echo "</td>";
				
				if (privileges("editExternal") == "true") {
					if (privileges("publishExternal") == "true") {
						echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_external.php?id=" . $tabData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($tabData['title']) . "</strong> tab')\" onmouseout=\"UnTip()\"></a></td>";
					} else {
						if ($tabData['published'] != "0" || $tabData['message'] == "1") {
							echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_external.php?id=" . $tabData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($tabData['title']) . "</strong> tab')\" onmouseout=\"UnTip()\"></a></td>";
						} else {
							echo "<td width=\"50\"><span class=\"action noEdit\" onmouseover=\"Tip('This tab must be approved first')\" onmouseout=\"UnTip()\"></span></td>";
						}
					}
				}
				
				if (privileges("deleteExternal") == "true") {
					echo "<td width=\"50\"><a class=\"action delete\" href=\"external.php?action=delete&tab=" . $tabData['position'] . "&id=" . $tabData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($tabData['title']) . "</strong> tab')\" onmouseout=\"UnTip()\"></a></td>";
				}
			}
			
			echo "</tr></tbody></table>";
		 } else {
			echo "<div class=\"noResults\">This site has no external content.";
			
			if (privileges("createExternal") == "true") {
				echo " <a href=\"manage_external.php\">Create a new tab now</a>.</div>";
			} else {
				echo " You do not have the privileges to create a tab.";
			}
		 }
	}
?>
<?php footer(); ?>
</body>
</html>