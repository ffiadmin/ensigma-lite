<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("publishPage") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Grab the tab data
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$tabDataGrabber = mysql_query("SELECT * FROM `external` WHERE `id` = '{$id}'", $connDBA);
		
		if ($tabDataGrabber) {
			$tabData = mysql_fetch_array($tabDataGrabber);
			
			if ($tabData['published'] == "2") {
				die(successMessage("This tab is already published."));
			}
		} else {
			die(errorMessage("This tab does not exist."));
		}
	} else {
		die(errorMessage("The tab ID was not provided."));
	}
?>
<?php
//Process the form
	if (isset($_GET['id']) && isset($_GET['accepted'])) {
		$id = $_GET['id'];
		$accepted = $_GET['accepted'];
		$tabDataGrabber = mysql_query("SELECT * FROM `external` WHERE `id` = '{$id}'", $connDBA);
		$tabData = mysql_fetch_array($tabDataGrabber);
		
		if ($accepted == "true") {
			if ($tabData['published'] != "0") {
				if ($tabData['display'] == "1") {
					mysql_query("UPDATE `external` SET `published` = '2', `display` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				} else {
					mysql_query("UPDATE `external` SET `published` = '2', `display` = '1', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				}
			} else {
				mysql_query("UPDATE `external` SET `published` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
			}
			
			die("<script type=\"text/javascript\">window.opener.location.reload();window.close();</script>");
		} else {
			if ($tabData['published'] != "0") {
				if ($tabData['display'] == "1") {
					mysql_query("UPDATE `external` SET `published` = '2', `display` = '1', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				} else {
					mysql_query("UPDATE `external` SET `published` = '2', `display` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				}
			} else {
				mysql_query("UPDATE `external` SET `message` = '1' WHERE `id` = '{$id}'", $connDBA);
			}
			
			die("<script type=\"text/javascript\">window.opener.location.reload();window.close();</script>");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Approve Tab"); ?>
<?php headers(); ?>
</head>

<body class="overrideBackground">
<h2><?php echo $tabData['title']; ?></h2>
<p>&nbsp;</p>
<div class="toolBar">
  <a class="toolBarItem accept" href="approve_external.php?id=<?php echo $tabData['id']; ?>&accepted=true">Accept Pending Version</a>
  <?php
  //Only show if an update is pending
	  if ($tabData['published'] != "0") {
		  echo "<a class=\"toolBarItem reject\" href=\"approve_external.php?id=" . $tabData['id'] . "&accepted=false\">Revert to Currently Published</a>";
	  } else {
		  echo "<a class=\"toolBarItem reject\" href=\"approve_external.php?id=" . $tabData['id'] . "&accepted=false\">Reject Pending Version</a>";
	  }
  ?>
</div>
<br />
<?php
//Display the comparison layout only if an edited tab is being approved
	if ($tabData['published'] == "1") { 
?>
<div class="layoutControl">
  <div class="halfLeft">
  <div>
    Pending Approval
    <?php
        if (privileges("editPage") == "true") {
            echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_external.php?id=" . $tabData['id'] . "&content=";
            
                if ($tabData['display'] == "1") {
                    echo "2";
                } else {
                    echo "1";
                }
            
            echo "\"></a><br />";
        }
  ?>
  </div>
  <div>
  <?php
      if ($tabData['display'] == "1") {
          echo stripslashes($tabData['content2']);
      } else {
          echo stripslashes($tabData['content1']);
      }
  ?>
  </div>
  </div>
  <div class="halfRight">
  <div>
    Curently Published
    <?php
        if (privileges("editPage") == "true") {
            echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_external.php?id=" . $tabData['id'] . "&content=";
            
                if ($tabData['display'] == "1") {
                    echo "1";
                } else {
                    echo "2";
                }
            
            echo "\"></a><br />";
        }
    ?>
  </div>
  <div>
  <?php
      if ($tabData['display'] == "1") {
          echo stripslashes($tabData['content1']);
      } else {
          echo stripslashes($tabData['content2']);
      }
  ?>
  </div>
  </div>
</div>
<?php
//Display the a single column for approving a new tab
	} elseif ($tabData['published'] == "0") { 
?>
<div>Pending Approval
  <?php
      if (privileges("editPage") == "true") {
          echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_external.php?id=" . $tabData['id'] . "&content=1\"></a><br />";
      }
		
	  echo stripslashes($tabData['content1']);
  ?>
</div>
<?php
	}
?>
</body>
</html>