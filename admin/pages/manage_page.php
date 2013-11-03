<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (!isset ($_GET['id'])) {
		if (privileges("createStaffPage") == "true") {
			loginCheck("User,Administrator");
		} else {
			loginCheck("Administrator");
		}
	} else {
		if (isset($_GET['content'])) {
			if (privileges("editStaffPage") == "true" && privileges("publishStaffPage") == "true") {
				loginCheck("User,Administrator");
			} else {
				loginCheck("Administrator");
			}
		} else {
			if (privileges("editStaffPage") == "true") {
				loginCheck("User,Administrator");
			} else {
				loginCheck("Administrator");
			}
		}
	}
?>
<?php
//Check to see if the page is being edited
	if (isset ($_GET['id'])) {
		$page = $_GET['id'];
		$pageGrabber = mysql_query("SELECT * FROM staffpages WHERE `id` = '{$page}'", $connDBA);
		
		if ($pageCheck = mysql_fetch_array($pageGrabber)) {
			$page = $pageCheck;
			
			if (privileges("publishStaffPage") != "true" && $pageCheck['published'] == "0") {
				header ("Location: index.php");
				exit;
			}
			
			if (isset($_GET['content']) && $pageCheck['published'] == "1") {
				if ($_GET['content'] == "1") {
					if (!empty($pageCheck['content1'])) {
						$contentEditor = "content1";
						$commentsEditor = "comments1";
						$contentDisplay = $pageCheck['content1'];
					} else {
						header ("Location: index.php");
						exit;
					}
				} elseif ($_GET['content'] == "2") {
					if (!empty($pageCheck['content2'])) {
						$contentEditor = "content2";
						$commentsEditor = "comments2";
						$contentDisplay = $pageCheck['content2'];
					} else {
						header ("Location: index.php");
						exit;
					}
				} else {
					header ("Location: index.php");
					exit;
				}
			} elseif (isset($_GET['content']) && $pageCheck['published'] == "2") {
				header("Location: index.php");
				exit;
			} elseif (isset($_GET['content']) && $_GET['content'] == "2" && $pageCheck['published'] == "0") {
				header("Location: index.php");
				exit;
			} else {
				if ($pageCheck['display'] == "1") {
					$contentEditor = "content1";
					$commentsEditor = "comments1";
					$contentDisplay = $pageCheck['content1'];
				} else {
					$contentEditor = "content2";
					$commentsEditor = "comments2";
					$contentDisplay = $pageCheck['content2'];
				}
			}
		} else {
			header ("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['content'])) {	
		if (!isset ($page)) {
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			$comments = $_POST['comments'];
			
			if (privileges("publishStaffPage") == "true") {
				$published = "2";
			} else {
				$published = "0";
			}
			
			$positionGrabber = mysql_query ("SELECT * FROM staffpages ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
			
			$newPageQuery = "INSERT INTO staffpages (
									`id`, `title`, `position`, `published`, `message`, `display`, `content1`, `content2`, `comments1`, `comments2`, `name`, `date`, `comment`
								) VALUES (
									NULL, '{$title}', '{$position}', '{$published}', '', '1', '{$content}', '', '{$comments}', '', '', '', ''
								)";
			
			mysql_query($newPageQuery, $connDBA);
			header ("Location: index.php?added=page");
			exit;
		} else {
			$page = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			$comments = $_POST['comments'];
			
			$pageDataGrabber = mysql_query ("SELECT * FROM staffpages WHERE `id` = '{$page}' LIMIT 1", $connDBA);
			$pageData = mysql_fetch_array($pageDataGrabber);
			
			if ($pageData['display'] == "1") {
				$contentEditor = "content1";
				$commentsEditor = "comments1";
			} else {
				$contentEditor = "content2";
				$commentsEditor = "comments2";
			}
			
			if ($pageData['title'] === $title && $pageData[$contentEditor] === $content && $pageData[$commentsEditor] === $comments) {
			//Redirect back to the main page, no changes were made
				header("Location: index.php");
				exit;
			} elseif ($pageData['title'] !== $title && $pageData[$contentEditor] === $content && $pageData[$commentsEditor] === $comments) {
				$editPageQuery = "UPDATE staffpages SET title = '{$title}' WHERE `id` = '{$page}'";
			} else {
				if (isset($_GET['content'])) {	
					if ($pageData['published'] != "0") {
						if ($pageData['display'] == "1") {			
							$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '1', message = '', {$contentEditor} = '{$content}', {$commentsEditor} = '{$comments}' WHERE `id` = '{$page}'";
						} else {
							$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '1', message = '', {$contentEditor} = '{$content}', {$commentsEditor} = '{$comments}' WHERE `id` = '{$page}'";
						}
					} else {
						$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '0', message = '', {$contentEditor} = '{$content}', {$commentsEditor} = '{$comments}' WHERE `id` = '{$page}'";
					}
				} else {
					if ($pageData['published'] = "2") {
						if ($pageData['display'] == "1") {
							if (privileges("publishStaffPage") == "true") {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '2', display = '2', message = '', content2 = '{$content}', comments2 = '{$comments}' WHERE `id` = '{$page}'";
							} else {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '1', message = '', content2 = '{$content}', comments2 = '{$comments}' WHERE `id` = '{$page}'";
							}
						} else {
							if (privileges("publishStaffPage") == "true") {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '2', display = '1', message = '', content1 = '{$content}', comments1 = '{$comments}' WHERE `id` = '{$page}'";
							} else {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '1',  message = '', content1 = '{$content}', comments1 = '{$comments}' WHERE `id` = '{$page}'";
							}
						}
					} else {
						if ($pageData['display'] == "1") {
							if (privileges("publishStaffPage") == "true") {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '2', display = '1', message = '', content1 = '{$content}', comments1 = '{$comments}' WHERE `id` = '{$page}'";
							} else {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '1', message = '', content1 = '{$content}', comments1 = '{$comments}' WHERE `id` = '{$page}'";
							}
						} else {
							if (privileges("publishStaffPage") == "true") {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '2', display = '2', message = '', content2 = '{$content}', comments2 = '{$comments}' WHERE `id` = '{$page}'";
							} else {
								$editPageQuery = "UPDATE staffpages SET title = '{$title}', published = '1', message = '', content2 = '{$content}', comments2 = '{$comments}' WHERE `id` = '{$page}'";
							}
						}
					}
				}
			}
			
			mysql_query($editPageQuery, $connDBA);
			header ("Location: index.php?updated=page");
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($page)) {
		$title = "Edit the " . stripslashes(htmlentities($page['title'])) . " Staff Page";
	} else {
		$title =  "Create a New Staff Page";
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
      <?php if (isset ($page)) {echo "Edit the \"" . $page['title'] . "\" Staff Page";} else {echo "Create New Staff Page";} ?>
    </h2>
<p>Use this page to <?php if (isset ($page)) {echo "edit the content of \"<strong>" . stripslashes(htmlentities($page['title'])) . "</strong>\"";} else {echo "create a new staff page";} ?>.</p>
	<?php
	//Let users know an update is pending if one is pending
		if (isset ($page) && !isset($_GET['content'])) {
			if ($page['published'] == "1" && privileges("publishStaffPage") != "true") {
				alert("An more recent version of this page is awaiting approval. You are currently editing the older version. Any changes made to this verison will be applied to the pending version.");
			} elseif ($page['published'] == "1" && privileges("publishStaffPage") == "true") {
				alert("An more recent version of this page is awaiting approval. You are currently editing the older version. Any changes made to this verison will be applied to the pending version. Please <a href=\"index.php\" onclick=\"MM_openBrWindow('approve.php?id=" . $page['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\">approve the newer version</a> if you wish to see the results.");
			} else {
				echo "<p>&nbsp;</p>";
			}
		} else {
			echo "<p>&nbsp;</p>";
		}
	?>
    <form action="manage_page.php<?php 
		if (isset ($page)) {
			echo "?id=" . $page['id'];
		}
		
		if (isset($_GET['content'])) {
			echo "&content=" . $_GET['content'];
		}
	?>" method="post" name="managePage" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The text that will display in big letters on the top-left of each page &lt;br /&gt;and at the top of the browser window')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($page)) {
					echo " value=\"" . stripslashes(htmlentities($page['title'])) . "\"";
				}
			?> />
          </p>
        </blockquote>
        <p>Content<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the webpage')" onmouseout="UnTip()" /> </p>
        <blockquote>
        <p>
            <textarea name="content" id="content1" cols="45" rows="5" style="width:640px; height:320px;" class="validate[required]" /><?php 
				if (isset ($page)) {
					echo stripslashes($contentDisplay);
				}
			?></textarea>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Settings</div>
      <div class="stepContent">
      	<blockquote>
        	<p>Allow Comments:</p>
            <blockquote>
            	<p>
                	<label><input type="radio" name="comments" id="comments_1" class="validate[required]" value="1"<?php 
						if (isset ($page)) {
							if (isset($_GET['content'])) {
								if ($page[$commentsEditor] == "1") {
									echo " checked=\"checked\"";
								}
							} else {
								if ($page['display'] == "1") {
									if ($page['comments1'] == "1") {
										echo " checked=\"checked\"";
									}
								} else {
									if ($page['comments2'] == "1") {
										echo " checked=\"checked\"";
									}
								}
							}
						}
					?> />Yes</label>
                    <label><input type="radio" name="comments" id="comments_0" class="validate[required]" value="0"<?php 
						if (isset ($page)) {
							if (isset($_GET['content'])) {
								if ($page[$commentsEditor] == "0") {
									echo " checked=\"checked\"";
								}
							} else {
								if ($page['display'] == "1") {
									if ($page['comments1'] == "0") {
										echo " checked=\"checked\"";
									}
								} else {
									if ($page['comments2'] == "0") {
										echo " checked=\"checked\"";
									}
								}
							}
						} else {
							echo " checked=\"checked\"";
						}
					?> />No</label>
                </p>
            </blockquote>
        </blockquote>
      </div>
      <div class="catDivider three">Finish</div>
      <div class="stepContent">
	  <blockquote>
      	<p>
          <?php submit("submit", "Submit"); ?>
			<input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
        </p>
          <?php formErrors(); ?>
      </blockquote>
      </div>
    </form>
<?php footer(); ?>
</body>
</html>
