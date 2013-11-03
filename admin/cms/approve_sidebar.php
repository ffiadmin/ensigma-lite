<?php require_once('../../Connections/connDBA.php'); ?>
<?php
	if (privileges("publishSideBar") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Grab the item data
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$sideBarDataGrabber = mysql_query("SELECT * FROM `sidebar` WHERE `id` = '{$id}'", $connDBA);
		
		if ($sideBarDataGrabber) {
			$sideBarData = mysql_fetch_array($sideBarDataGrabber);
			
			if ($sideBarData['published'] == "2") {
				die(successMessage("This box is already published"));
			}
		} else {
			die(errorMessage("This box does not exist"));
		}
	} else {
		die(errorMessage("The box ID was not provided"));
	}
?>
<?php
//Process the form
	if (isset($_GET['id']) && isset($_GET['accepted'])) {
		$id = $_GET['id'];
		$accepted = $_GET['accepted'];
		$sideBarDataGrabber = mysql_query("SELECT * FROM `sidebar` WHERE `id` = '{$id}'", $connDBA);
		$sideBarData = mysql_fetch_array($sideBarDataGrabber);
		
		if ($accepted == "true") {
			if ($sideBarData['published'] != "0") {
				if ($sideBarData['display'] == "1") {
					mysql_query("UPDATE `sidebar` SET `published` = '2', `display` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				} else {
					mysql_query("UPDATE `sidebar` SET `published` = '2', `display` = '1', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				}
			} else {
				mysql_query("UPDATE `sidebar` SET `published` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
			}
			
			die("<script type=\"text/javascript\">window.opener.location.reload();window.close();</script>");
		} else {
			if ($sideBarData['published'] != "0") {
				if ($sideBarData['display'] == "1") {
					mysql_query("UPDATE `sidebar` SET `published` = '2', `display` = '1', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				} else {
					mysql_query("UPDATE `sidebar` SET `published` = '2', `display` = '2', `message` = '0' WHERE `id` = '{$id}'", $connDBA);
				}
			} else {
				mysql_query("UPDATE `sidebar` SET `message` = '1' WHERE `id` = '{$id}'", $connDBA);
			}
			
			die("<script type=\"text/javascript\">window.opener.location.reload();window.close();</script>");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Approve Box"); ?>
<?php headers(); ?>
</head>

<body class="overrideBackground">
<h2><?php echo $sideBarData['title']; ?></h2>
<p>&nbsp;</p>
<div class="toolBar">
  <a class="toolBarItem accept" href="approve_sidebar.php?id=<?php echo $sideBarData['id']; ?>&accepted=true">Accept Pending Version</a>
  <?php
  //Only show if an update is pending
	  if ($sideBarData['published'] != "0") {
		  echo "<a class=\"toolBarItem reject\" href=\"approve_sidebar.php?id=" . $sideBarData['id'] . "&accepted=false\">Revert to Currently Published</a>";
	  } else {
		  echo "<a class=\"toolBarItem reject\" href=\"approve_sidebar.php?id=" . $sideBarData['id'] . "&accepted=false\">Reject Pending Version</a>";
	  }
  ?>
</div>
<br />
<?php
//Display the comparison layout only if an edited box is being approved
	if ($sideBarData['published'] == "1") { 
?>
<div class="layoutControl">
  <div class="halfLeft">
  <div>
    Pending Approval
    <?php
        if (privileges("editSideBar") == "true") {
            echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_sidebar.php?id=" . $sideBarData['id'] . "&content=";
            
                if ($sideBarData['display'] == "1") {
                    echo "2";
                } else {
                    echo "1";
                }
            
            echo "\"></a>";
        }
  ?>
  </div>
  <div>
  <?php
	  if ($sideBarData['type'] != "Login") {
		  if ($sideBarData['display'] == "1") {
			  echo $sideBarData['content2'];
		  } else {
			  echo $sideBarData['content1'];
		  }
	  } else {
		  echo "The system creates a simple login form. No other content is displayed in this box.";
	  }
  ?>
  </div>
  </div>
  <div class="halfRight">
  <div>
    Curently Published
    <?php
        if (privileges("editSideBar") == "true") {
            echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_sidebar.php?id=" . $sideBarData['id'] . "&content=";
            
                if ($sideBarData['display'] == "1") {
                    echo "1";
                } else {
                    echo "2";
                }
            
            echo "\"></a>";
        }
    ?>
  </div>
  <div>
  <?php
      if ($sideBarData['display'] == "1") {
          echo $sideBarData['content1'];
      } else {
          echo $sideBarData['content2'];
      }
  ?>
  </div>
  </div>
</div>
<?php
//Display the a single column for approving a new box
	} elseif ($sideBarData['published'] == "0") { 
?>
<div>Pending Approval
  <?php
      if (privileges("editSideBar") == "true") {
          echo "<a class=\"smallEdit\" target=\"_blank\" href=\"manage_sidebar.php?id=" . $sideBarData['id'] . "&content=1\"></a>";
      }
	  
	  if ($sideBarData['type'] != "Login") {
		  echo $sideBarData['content1'];
	  } else {
		  echo "<p>The system creates a simple login form. No other content is displayed in this box.</p>";
	  }
  ?>
</div>
<?php
	}
?>
</body>
</html>