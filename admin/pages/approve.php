<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("publishStaffPage") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Grab the page data
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$pageDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `id` = '{$id}'", $connDBA);
		
		if ($pageDataGrabber) {
			$pageData = mysql_fetch_array($pageDataGrabber);
			
			if ($pageData['published'] == "2") {
				die(successMessage("This page is already published."));
			}
		} else {
			die(errorMessage("This page does not exist."));
		}
	} else {
		die(errorMessage("The page ID was not provided."));
	}
?>
<?php
//Process the form
	if (isset($_GET['id']) && isset($_GET['accepted'])) {
		$id = $_GET['id'];
		$accepted = $_GET['accepted'];
		$pageDataGrabber = mysql_query("SELECT * FROM `staffpages` WHERE `id` = '{$id}'", $connDBA);
		$pageData = mysql_fetch_array($pageDataGrabber);
		
		if ($accepted == "true") {
			if ($pageData['published'] != "0") {
				if ($pageData['display'] == "1") {
					mysql_query("UPDATE `staffpages` SET `published` = '2', `display` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				} else {
					mysql_query("UPDATE `staffpages` SET `published` = '2', `display` = '1', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				}
			} else {
				mysql_query("UPDATE `staffpages` SET `published` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
			}
			
			die("<script type=\"text/javascript\">window.opener.location.reload();window.close();</script>");
		} else {
			if ($pageData['published'] != "0") {
				if ($pageData['display'] == "1") {
					mysql_query("UPDATE `staffpages` SET `published` = '2', `display` = '1', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				} else {
					mysql_query("UPDATE `staffpages` SET `published` = '2', `display` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				}
			} else {
				mysql_query("UPDATE `staffpages` SET `message` = '1' WHERE `id` = '{$id}'", $connDBA);
			}
			
			die("<script type=\"text/javascript\">window.opener.location.reload();window.close();</script>");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Approve Page"); ?>
<?php headers(); ?>
</head>

<body class="overrideBackground">
<h2><?php echo $pageData['title']; ?></h2>
<p>&nbsp;</p>
<div class="toolBar">
  <a class="toolBarItem accept" href="approve.php?id=<?php echo $pageData['id']; ?>&accepted=true">Accept Pending Version</a>
  <?php
  //Only show if an update is pending
	  if ($pageData['published'] != "0") {
		  echo "<a class=\"toolBarItem reject\" href=\"approve.php?id=" . $pageData['id'] . "&accepted=false\">Revert to Currently Published</a>";
	  } else {
		  echo "<a class=\"toolBarItem reject\" href=\"approve.php?id=" . $pageData['id'] . "&accepted=false\">Reject Pending Version</a>";
	  }
  ?>
</div>
<br />
<?php
//Display the comparison layout only if an edited page is being approved
	if ($pageData['published'] == "1") { 
?>
<div class="layoutControl">
  <div class="halfLeft">
  <div>
    Pending Approval
    <?php
        if (privileges("editStaffPage") == "true") {
            echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_page.php?id=" . $pageData['id'] . "&content=";
            
                if ($pageData['display'] == "1") {
                    echo "2";
                } else {
                    echo "1";
                }
            
            echo "\"></a><br />";
        }
		
		if ($pageData['display'] == "1") {
			if ($pageData['comments2'] == "1") {
				echo "Comments: <strong>On</strong>";
			} else {
				echo "Comments: <strong>Off</strong>";
			}
		} else {
			if ($pageData['comments1'] == "1") {
				echo "Comments: <strong>On</strong>";
			} else {
				echo "Comments: <strong>Off</strong>";
			}
		}
  ?>
  </div>
  <div>
  <?php
      if ($pageData['display'] == "1") {
          echo stripslashes($pageData['content2']);
      } else {
          echo stripslashes($pageData['content1']);
      }
  ?>
  </div>
  </div>
  <div class="halfRight">
  <div>
    Curently Published
    <?php
        if (privileges("editStaffPage") == "true") {
            echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_page.php?id=" . $pageData['id'] . "&content=";
            
                if ($pageData['display'] == "1") {
                    echo "1";
                } else {
                    echo "2";
                }
            
            echo "\"></a><br />";
        }
		
		if ($pageData['display'] == "1") {
			if ($pageData['comments1'] == "1") {
				echo "Comments: <strong>On</strong>";
			} else {
				echo "Comments: <strong>Off</strong>";
			}
		} else {
			if ($pageData['comments2'] == "1") {
				echo "Comments: <strong>On</strong>";
			} else {
				echo "Comments: <strong>Off</strong>";
			}
		}
    ?>
  </div>
  <div>
  <?php
      if ($pageData['display'] == "1") {
          echo stripslashes($pageData['content1']);
      } else {
          echo stripslashes($pageData['content2']);
      }
  ?>
  </div>
  </div>
</div>
<?php
//Display the a single column for approving a new page
	} elseif ($pageData['published'] == "0") { 
?>
<div>Pending Approval
  <?php
      if (privileges("editStaffPage") == "true") {
          echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_page.php?id=" . $pageData['id'] . "&content=1\"></a><br />";
      }
	  
	  if ($pageData['comments1'] == "1") {
		  echo "Comments: <strong>On</strong>";
	  } else {
		  echo "Comments: <strong>Off</strong>";
	  }
		
	  echo stripslashes($pageData['content1']);
  ?>
</div>
<?php
	}
?>
</body>
</html>