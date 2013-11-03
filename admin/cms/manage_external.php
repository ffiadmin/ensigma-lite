<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (!isset ($_GET['id'])) {
		if (privileges("createExternal") == "true") {
			loginCheck("User,Administrator");
		} else {
			loginCheck("Administrator");
		}
	} else {
		if (isset($_GET['content'])) {
			if (privileges("editExternal") == "true" && privileges("publishExternal") == "true") {
				loginCheck("User,Administrator");
			} else {
				loginCheck("Administrator");
			}
		} else {
			if (privileges("editExternal") == "true") {
				loginCheck("User,Administrator");
			} else {
				loginCheck("Administrator");
			}
		}
	}
?>
<?php
//Check to see if the tab is being edited
	if (isset ($_GET['id'])) {
		$tab = $_GET['id'];
		$tabGrabber = mysql_query("SELECT * FROM external WHERE `id` = '{$tab}'", $connDBA);
		
		if ($tabCheck = mysql_fetch_array($tabGrabber)) {
			$tab = $tabCheck;
			
			if (privileges("publishExternal") != "true" && $tabCheck['published'] == "0") {
				header ("Location: external.php");
				exit;
			}
			
			if (isset($_GET['content']) && $tabCheck['published'] == "1") {
				if ($_GET['content'] == "1") {
					if (!empty($tabCheck['content1'])) {
						$contentEditor = "content1";
						$contentDisplay = $tabCheck['content1'];
					} else {
						header ("Location: external.php");
						exit;
					}
				} elseif ($_GET['content'] == "2") {
					if (!empty($tabCheck['content2'])) {
						$contentEditor = "content2";
						$contentDisplay = $tabCheck['content2'];
					} else {
						header ("Location: external.php");
						exit;
					}
				} else {
					header ("Location: external.php");
					exit;
				}
			} elseif (isset($_GET['content']) && $tabCheck['published'] == "2") {
				header("Location: external.php");
				exit;
			} elseif (isset($_GET['content']) && $_GET['content'] == "2" && $tabCheck['published'] == "0") {
				header("Location: external.php");
				exit;
			} else {
				if ($tabCheck['display'] == "1") {
					$contentEditor = "content1";
					$contentDisplay = $tabCheck['content1'];
				} else {
					$contentEditor = "content2";
					$contentDisplay = $tabCheck['content2'];
				}
			}
		} else {
			header ("Location: external.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['content'])) {	
		if (!isset ($tab)) {
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			
			if (privileges("publishExternal") == "true") {
				$published = "2";
			} else {
				$published = "0";
			}
			
			$positionGrabber = mysql_query ("SELECT * FROM external ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
			
			$newTabQuery = "INSERT INTO external (
									`id`, `title`, `position`, `visible`, `published`, `message`, `display`, `content1`, `content2`
								) VALUES (
									NULL, '{$title}', '{$position}', 'on', '{$published}', '', '1', '{$content}', ''
								)";
			
			mysql_query($newTabQuery, $connDBA);
			header ("Location: external.php?added=tab");
			exit;
		} else {
			$tab = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			
			$tabDataGrabber = mysql_query ("SELECT * FROM external WHERE `id` = '{$tab}' LIMIT 1", $connDBA);
			$tabData = mysql_fetch_array($tabDataGrabber);
			
			if ($tabData['display'] == "1") {
				$contentEditor = "content1";
			} else {
				$contentEditor = "content2";
			}
			
			if ($tabData['title'] === $_POST['title'] && $tabData[$contentEditor] === $_POST['content']) {
			//Redirect back to the main page, no changes were made
				header("Location: external.php");
				exit;
			} elseif ($tabData['title'] !== $_POST['title'] && $tabData[$contentEditor] === $_POST['content']) {
				$editSideBarQuery = "UPDATE external SET title = '{$title}' WHERE `id` = '{$tab}'";
				
				mysql_query($editSideBarQuery, $connDBA);
				header ("Location: external.php");
				exit;
			} else {
				if (isset($_GET['content'])) {	
					if ($tabData['published'] != "0") {
						if ($tabData['display'] == "1") {			
							$editTabQuery = "UPDATE external SET title = '{$title}', published = '1', message = '', {$contentEditor} = '{$content}' WHERE `id` = '{$tab}'";
						} else {
							$editTabQuery = "UPDATE external SET title = '{$title}', published = '1', message = '', {$contentEditor} = '{$content}' WHERE `id` = '{$tab}'";
						}
					} else {
						$editTabQuery = "UPDATE external SET title = '{$title}', published = '0', message = '', {$contentEditor} = '{$content}' WHERE `id` = '{$tab}'";
					}
				} else {
					if ($tabData['published'] = "2") {
						if ($tabData['display'] == "1") {
							if (privileges("publishExternal") == "true") {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '2', display = '2', message = '', content2 = '{$content}' WHERE `id` = '{$tab}'";
							} else {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '1', message = '', content2 = '{$content}' WHERE `id` = '{$tab}'";
							}
						} else {
							if (privileges("publishExternal") == "true") {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '2', display = '1', message = '', content1 = '{$content}' WHERE `id` = '{$tab}'";
							} else {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '1',  message = '', content1 = '{$content}' WHERE `id` = '{$tab}'";
							}
						}
					} else {
						if ($tabData['display'] == "1") {
							if (privileges("publishExternal") == "true") {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '2', display = '1', message = '', content1 = '{$content}' WHERE `id` = '{$tab}'";
							} else {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '1', message = '', content1 = '{$content}' WHERE `id` = '{$tab}'";
							}
						} else {
							if (privileges("publishExternal") == "true") {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '2', display = '2', message = '', content2 = '{$content}' WHERE `id` = '{$tab}'";
							} else {
								$editTabQuery = "UPDATE external SET title = '{$title}', published = '1', message = '', content2 = '{$content}' WHERE `id` = '{$tab}'";
							}
						}
					}
				}
			}
			
			mysql_query($editTabQuery, $connDBA);
			header ("Location: external.php?updated=tab");
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($tab)) {
		$title = "Edit the " . stripslashes(htmlentities($tab['title'])) . " Tab";
	} else {
		$title =  "Create a New Tab";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
      
    <h2>
      <?php if (isset ($tab)) {echo "Edit the \"" . $tab['title'] . "\" Tab";} else {echo "Create New Tab";} ?>
    </h2>
<p>Use this page to <?php if (isset ($tab)) {echo "edit the content of \"<strong>" . stripslashes(htmlentities($tab['title'])) . "</strong>\"";} else {echo "create a new tab";} ?>.</p>
	<?php
	//Let users know an update is pending if one is pending
		if (isset ($tab) && !isset($_GET['content'])) {
			if ($tab['published'] == "1" && privileges("publishExternal") != "true") {
				alert("An more recent version of this tab is awaiting approval. You are currently editing the older version. Any changes made to this verison will be applied to the pending version.");
			} elseif ($tab['published'] == "1" && privileges("publishExternal") == "true") {
				alert("An more recent version of this tab is awaiting approval. You are currently editing the older version. Any changes made to this verison will be applied to the pending version. Please <a href=\"external.php\" onclick=\"MM_openBrWindow('approve_external.php?id=" . $tab['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\">approve the newer version</a> if you wish to see the results.");
			} else {
				echo "<p>&nbsp;</p>";
			}
		} else {
			echo "<p>&nbsp;</p>";
		}
	?>
    <form action="manage_external.php<?php 
		if (isset ($tab)) {
			echo "?id=" . $tab['id'];
		}
		
		if (isset($_GET['content'])) {
			echo "&content=" . $_GET['content'];
		}
	?>" method="post" name="manageTab" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The text that will display in big letters on the top-left of each tab')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($tab)) {
					echo " value=\"" . stripslashes(htmlentities($tab['title'])) . "\"";
				}
			?> />
          </p>
        </blockquote>
        <p>Content<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the tab')" onmouseout="UnTip()" /> </p>
        <blockquote>
        <p>
            <textarea name="content" id="content1" cols="45" rows="5" style="width:640px; height:320px;" class="validate[required]" /><?php 
				if (isset ($tab)) {
					echo stripslashes($contentDisplay);
				}
			?></textarea>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Finish</div>
      <div class="stepContent">
	  <blockquote>
      	<p>
          <?php submit("submit", "Submit"); ?>
			<input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','external.php');return document.MM_returnValue" value="Cancel" />
        </p>
          <?php formErrors(); ?>
      </blockquote>
      </div>
    </form>
<?php footer(); ?>
</body>
</html>
