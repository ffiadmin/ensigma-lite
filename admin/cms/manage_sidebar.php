<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (!isset ($_GET['id'])) {
		if (privileges("createSideBar") == "true") {
			loginCheck("User,Administrator");
		} else {
			loginCheck("Administrator");
		}
	} else {
		if (isset($_GET['content'])) {
			if (privileges("editSideBar") == "true" && privileges("publishSideBar") == "true") {
				loginCheck("User,Administrator");
			} else {
				loginCheck("Administrator");
			}
		} else {
			if (privileges("editSideBar") == "true") {
				loginCheck("User,Administrator");
			} else {
				loginCheck("Administrator");
			}
		}
	}
?>
<?php
//Check to see if the item is being edited
	if (isset ($_GET['id'])) {
		$sideBar = $_GET['id'];
		$sideBarGrabber = mysql_query("SELECT * FROM sidebar WHERE `id` = '{$sideBar}'", $connDBA);
		
		if ($sideBarCheck = mysql_fetch_array($sideBarGrabber)) {
			$item = $sideBarCheck;
			
			if (privileges("publishSideBar") != "true" && $sideBarCheck['published'] == "0") {
				header ("Location: sidebar.php");
				exit;
			}
			
			if (isset($_GET['content']) && $sideBarCheck['published'] == "1") {
				if ($_GET['content'] == "1") {
					if (!empty($sideBarCheck['content1'])) {
						$contentEditor = "content1";
						$contentDisplay = $sideBarCheck['content1'];
					} else {
						header ("Location: sidebar.php");
						exit;
					}
				} elseif ($_GET['content'] == "2") {
					if (!empty($sideBarCheck['content2'])) {
						$contentEditor = "content2";
						$contentDisplay = $sideBarCheck['content2'];
					} else {
						header ("Location: sidebar.php");
						exit;
					}
				} else {
					header ("Location: sidebar.php");
					exit;
				}
			} elseif (isset($_GET['content']) && $sideBarCheck['published'] == "2") {
				header("Location: sidebar.php");
				exit;
			} elseif (isset($_GET['content']) && $_GET['content'] == "2" && $sideBarCheck['published'] == "0") {
				header("Location: sidebar.php");
				exit;
			} else {
				if ($sideBarCheck['display'] == "1") {
					$contentEditor = "content1";
					$contentDisplay = $sideBarCheck['content1'];
				} else {
					$contentEditor = "content2";
					$contentDisplay = $sideBarCheck['content2'];
				}
			}
		} else {
			header ("Location: sidebar.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title'])) {	
		if (!isset ($sideBar)) {
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			$type = $_POST['type'];
			
			if (privileges("publishSideBar") == "true") {
				$published = "2";
			} else {
				$published = "0";
			}
			
			$positionGrabber = mysql_query ("SELECT * FROM sidebar ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
			
			$newSideBarQuery = "INSERT INTO sidebar (
									`id`, `position`, `visible`, `published`, `message`, `display`, `type`, `title`, `content1`, `content2`
								) VALUES (
									NULL, '{$position}', 'on', '{$published}', '', '1', '{$type}', '{$title}', '{$content}', ''
								)";
			
			mysql_query($newSideBarQuery, $connDBA);
			header ("Location: sidebar.php?added=item");
			exit;
		} else {
			$sideBar = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			
			$sideBarDataGrabber = mysql_query ("SELECT * FROM sidebar WHERE `id` = '{$sideBar}' LIMIT 1", $connDBA);
			$sideBarData = mysql_fetch_array($sideBarDataGrabber);
			
			if ($sideBarData['display'] == "1") {
				$contentEditor = "content1";
			} else {
				$contentEditor = "content2";
			}
			
			if ($sideBarData['title'] === $_POST['title'] && $sideBarData[$contentEditor] === $_POST['content'] && $sideBarData['type'] === $_POST['type']) {
			//Redirect back to the main page, no changes were made
				header("Location: sidebar.php");
				exit;
			} elseif (($sideBarData['title'] !== $_POST['title'] || $sideBarData['type'] !== $_POST['type']) || ($sideBarData['title'] !== $_POST['title'] && $sideBarData['type'] !== $_POST['type']) && $sideBarData[$contentEditor] === $_POST['content']) {
				$editSideBarQuery = "UPDATE sidebar SET title = '{$title}' WHERE `id` = '{$sideBar}'";
				
				mysql_query($editSideBarQuery, $connDBA);
				header ("Location: sidebar.php");
				exit;
			} else {
				if (isset($_GET['content'])) {	
					if ($sideBarData['published'] != "0") {
						if ($sideBarData['display'] == "1") {			
							$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '1', message = '', {$contentEditor} = '{$content}' WHERE `id` = '{$sideBar}'";
						} else {
							$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '1', message = '', {$contentEditor} = '{$content}' WHERE `id` = '{$sideBar}'";
						}
					} else {
						$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '0', message = '', {$contentEditor} = '{$content}' WHERE `id` = '{$sideBar}'";
					}
				} else {
					if ($sideBarData['published'] = "2") {
						if ($sideBarData['display'] == "1") {
							if (privileges("publishSideBar") == "true") {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '2', display = '2', message = '', content2 = '{$content}' WHERE `id` = '{$sideBar}'";
							} else {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '1', message = '', content2 = '{$content}' WHERE `id` = '{$sideBar}'";
							}
						} else {
							if (privileges("publishSideBar") == "true") {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '2', display = '1', message = '', content1 = '{$content}' WHERE `id` = '{$sideBar}'";
							} else {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '1',  message = '', content1 = '{$content}' WHERE `id` = '{$sideBar}'";
							}
						}
					} else {
						if ($sideBarData['display'] == "1") {
							if (privileges("publishSideBar") == "true") {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '2', display = '1', message = '', content1 = '{$content}' WHERE `id` = '{$sideBar}'";
							} else {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '1', message = '', content1 = '{$content}' WHERE `id` = '{$sideBar}'";
							}
						} else {
							if (privileges("publishSideBar") == "true") {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '2', display = '2', message = '', content2 = '{$content}' WHERE `id` = '{$sideBar}'";
							} else {
								$editSideBarQuery = "UPDATE sidebar SET title = '{$title}', published = '1', message = '', content2 = '{$content}' WHERE `id` = '{$sideBar}'";
							}
						}
					}
				}
			}
			
			mysql_query($editSideBarQuery, $connDBA);
			header ("Location: sidebar.php?updated=item");
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($item)) {
		$title = "Edit the " . stripslashes(htmlentities($item['title'])) . " Box";
	} else {
		$title =  "Create a New Box";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/showHide.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
      
    <h2>
      <?php if (isset ($item)) {echo "Edit the \"" . $item['title'] . "\" Box";} else {echo "Create New Box";} ?>
    </h2>
<p>Use this page to <?php if (isset ($item)) {echo "edit the content of the \"<strong>" . stripslashes(htmlentities($item['title'])) . "</strong>\" box";} else {echo "create a new box";} ?>.</p>
    <p>&nbsp;</p>
    <form action="manage_sidebar.php<?php 
		if (isset ($item)) {
			echo "?id=" . $item['id'];
		}
	?>" method="post" name="manageItem" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The text that will display on the top-left of each box')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($item)) {
					echo " value=\"" . stripslashes(htmlentities($item['title'])) . "\"";
				}
			?> />
          </p>
        </blockquote>
        <p>Type<?php if (!isset($item)) {echo "<span class=\"require\">*</span>";} ?>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The type of content that will be displayed in the text box.<br />Different ones will be avaliable at different times, <br />depending on their current use.<br /><br /><strong>Custom Content</strong> - A box which can contain any desired content.<br /><strong>Login</strong> - A box with a pre-built form to log in a user.')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
              <?php
				  if (isset($item) && $item['type'] == "Login") {
					  echo "<strong>Login</strong>";
				  } elseif (isset($item) && $item['type'] == "Custom Content") {
					  echo "<strong>Custom Content</strong>";
				  } else {
					  echo "<select name=\"type\" id=\"type\" class=\"validate[required]\" onchange=\"toggleTypeDiv(this.value);\"><option value=\"Custom Content\">Custom Content</option><option value=\"Login\" >Login</option></select>";
				  }
			  ?>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Content</div>
      	<div class="stepContent">
        <div id="contentAdvanced"<?php if (isset ($item)) if ($item['type'] != "Login") {echo " class=\"contentShow\"";} else {echo " class=\"contentHide\"";} else {echo " class=\"contentShow\"";} ?>>
        <blockquote>
        <p>Content: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the box')" onmouseout="UnTip()" /> </p>
        <blockquote>
        <p><textarea name="content" id="content1" cols="45" rows="5" style="width:450px;" /><?php 
				if (isset ($item)) {
					echo stripslashes($item[$contentEditor]);
				}
			?></textarea></p>
        </blockquote>
      </blockquote>
      </div>
        <div id="contentMessage" <?php if (isset ($item) && $item['type'] == "Login") {echo "class=\"noResults contentShow\"";} else {echo "class=\"noResults contentHide\"";} ?>>The system has filled out the rest of the needed information. No further input is needed.</div>
      </div>
      <div class="catDivider three">Finish</div>
      <div class="stepContent">
	  <blockquote>
      	<p>
          <?php submit("submit", "Submit"); ?>
			<input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','sidebar.php');return document.MM_returnValue" value="Cancel" />
        </p>
          <?php formErrors(); ?>
      </blockquote>
      </div>
    </form>
<?php footer(); ?>
</body>
</html>
