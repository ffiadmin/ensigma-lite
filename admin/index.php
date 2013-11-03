<?php require_once('../Connections/connDBA.php'); ?>
<?php loginCheck("User,Administrator"); ?>
<?php
//Process the agenda
	if (isset($_POST['action']) && $_POST['action'] == "setCompletion" && !empty($_POST['id']) && (!empty($_POST['oldValue']) || $_POST['oldValue'] == "0")) {
		$id = $_POST['id'];
		$option = $_POST['option'];
		$oldValue = $_POST['oldValue'];
		
		$oldDataGrabber = mysql_query("SELECT * FROM `collaboration` WHERE `id` = '{$id}'", $connDBA);
		$oldData = mysql_fetch_array($oldDataGrabber);
		$oldCompletion = unserialize($oldData['completed']);
		
		if (is_array($oldCompletion)) {
		//If a value is being inserted
			if (!in_array($oldValue, $oldCompletion)) {
				array_push($oldCompletion, $oldValue);
				
				$status = mysql_real_escape_string(serialize($oldCompletion));
		//If a value is being removed
			} else {
				foreach ($oldCompletion as $count => $value) {
					if ($oldCompletion[$count] == $oldValue) {
						unset($oldCompletion[$count]);
					}
				}
				
				$status = mysql_real_escape_string(serialize($oldCompletion));
			}
	//If a value is being inserted	
		} else {
			$status = mysql_real_escape_string(serialize(array($oldValue)));
		}
		
		mysql_query("UPDATE `collaboration` SET `completed` = '{$status}' WHERE `id` = '{$id}'", $connDBA);
		
		header("Location: index.php");
		exit;
	}
	
//Delete a file
	if (privileges("deleteFile") == "true") {
		if (isset($_GET['action']) && $_GET['action'] == "delete" && !empty($_GET['directory']) && !empty($_GET['name'])) {
			$directory = $_GET['directory'];
			$file = urldecode($_GET['name']);
			
			unlink("files/" . $directory . "/" . $file);
			
			header("Location: index.php?message=deleted");
			exit;
		}
	}
	
//Upload a file
	if (privileges("uploadFile") == "true") {
		if (isset($_POST['submit']) && !empty($_FILES['file']) && !empty($_POST['category'])) {
			$tempFile = $_FILES['file'] ['tmp_name'];
			$uploadDir = "files/" . $_POST['category'];
			
			$fileArray = explode(".", basename($_FILES['file'] ['name']));
			$fileExtension = end($fileArray);
			$arraySize = sizeof($fileArray) - 1;
			$targetFile = "";
			
			for ($count = 0; $count <= $arraySize; $count++) {
				if ($count != $arraySize) {
					$targetFile .= $fileArray[$count];
				} else {
					$targetFile .= "_" . randomValue(10, "alphanum") . "." . $fileExtension;
				}
			}
			
			if (extension($targetFile)) {
				move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile);
				
				header("Location: index.php?message=uploaded");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Staff Home Page"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("checkbox"); ?>
<?php validate(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php tooltip(); ?>
<?php topPage(); ?>
<h2>Staff Home Page</h2>
<?php
//Display message updates
	if (isset($_GET['message'])) {
		if ($_GET['message'] == "deleted") {
			 successMessage("The file was deleted");
		} elseif ($_GET['message'] == "uploaded") {
			successMessage("The file was uploaded");
		} else {
			echo "<p>&nbsp;</p>";
		}
	} else {
		echo "<p>&nbsp;</p>";
	}

//Display the toolbar, if the user is an administrator
	if ($_SESSION['MM_UserGroup'] == "Administrator") {
		echo "<div class=\"toolBar\"><a class=\"toolBarItem editTool\" href=\"collaboration/index.php\">Edit View</a></div><br />";
	}
//Display annoumcements, file share, and agenda modules
	$itemsCheck = mysql_query("SELECT * FROM `collaboration`", $connDBA);
	
	if (mysql_fetch_array($itemsCheck)) {
		$time = getdate();
		
		if (0 < $time['minutes'] && $time['minutes'] < 9) {
			$minutes = "0" . $time['minutes'];
		} else {
			$minutes = $time['minutes'];
		}
		
		$currentTime = $time['hours'] . ":" . $minutes;
		$currentDate = strtotime($time['mon'] . "/" . $time['mday'] . "/" . $time['year'] . " " . $currentTime);
		$itemGrabber = mysql_query("SELECT * FROM `collaboration` ORDER BY `position` ASC", $connDBA);
		
		function type($type) {
			global $item;
			global $connDBA;
			
			switch ($type) {
			//If this is an agenda module
				case "Agenda" :
					$values = unserialize($item['task']);
					$task = unserialize($item['task']);
					$assignee = unserialize($item['assignee']);
					$dueDate = unserialize($item['dueDate']);
					$priority = unserialize($item['priority']);
					$completed = unserialize($item['completed']);
					
					echo "<div class=\"agendaContent\"><p class=\"itemTitle\">" . stripslashes($item['title']) . "</p>" . stripslashes($item['content']) . "<br />
					<table class=\"dataTable\">";
						echo "<tr>";
							echo "<th class=\"tableHeader\">Task</th>";
							echo "<th class=\"tableHeader\" width=\"200\">Assignee</th>";
							echo "<th class=\"tableHeader\" width=\"200\">Due Date</th>";
							echo "<th class=\"tableHeader\" width=\"100\">Priority</th>";
							echo "<th class=\"tableHeader\" width=\"100\">Completion</th>";
						echo "</tr>";
					
					for($count = 0; $count <= sizeof($values) - 1; $count++) {
						if ($assignee[$count] != "anyone") {
							$assignedUser = $assignee[$count];
							$userGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$assignedUser}'", $connDBA);
							$user = mysql_fetch_array($userGrabber);
						}
						
						echo "<tr";
						if ($count & 1) {echo " class=\"even\">";} else {echo " class=\"odd\">";}
							echo "<td>" . $task[$count] . "</td>";
							
							if ($assignee[$count] != "anyone") {
								echo "<td>" . $user['firstName'] . " " . $user['lastName'] . "</td>";
							} else {
								echo "<td>Anyone</td>";
							}
							
							echo "<td>";
							
							if ($dueDate[$count] == "") {
								echo "<span class=\"notAssigned\">None</span>";
							} else {
								echo $dueDate[$count];
							}
							
							echo "</td>";
							echo "<td>";
							
							switch($priority[$count]) {
								case "1" : echo "Low"; break;
								case "2" : echo "Normal"; break;
								case "3" : echo "High"; break;
							}
							
							echo "</td>";
							echo "<td><form name=\"completion\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setCompletion\"><input type=\"hidden\" name=\"id\" value=\"" . $item['id'] . "\"><input type=\"hidden\" name=\"oldValue\" value=\"" . $count . "\"><div align=\"center\"><a href=\"#option" . $item['id'] . $count . "\" class=\"checked";
							
							if (is_array($completed) && in_array($count, $completed)) {
								echo "\"></a><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $item['id'] . $count . "\" value=\"" . $count . "\" onclick=\"Spry.Utils.submitForm(this.form);\" checked=\"checked\"></div>";
							} else {
								echo " unchecked\"></a><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $item['id'] . $count . "\" value=\"" . $count . "\" onclick=\"Spry.Utils.submitForm(this.form);\"></div>";
							}
							
							echo "</div></form></td>";
						echo "</tr>";
						
						if ($assignee[$count] != "anyone") {
							unset($userGrabber);
							unset($user);
						}
					}
					
					echo "</table></div>";
					break;	
				
			//If this is an announcement
				case "Announcement" :
					echo "<div class=\"announcementContent\"><p class=\"itemTitle\">" . stripslashes($item['title']) . "</p>" . stripslashes($item['content']) . "</div>";
					break;
			
			//If this is a file share module
				case "File Share" :
					echo "<div class=\"fileShareContent\"><p class=\"itemTitle\">" . stripslashes($item['title']) . "</p>" . stripslashes($item['content']) . "";
					
					if (is_array(unserialize($item['directories']))) {
						$directories = unserialize($item['directories']);
						
						while (list($categoryKey, $categoryArray) = each($directories)) {
							$filesDirectory = opendir("files/" . $categoryKey);
							$count = 1;
							
							echo "<br /><table class=\"dataTable\">";
								echo "<tr>";
									echo "<th class=\"tableHeader\">" . $categoryArray . "</th>";
									
									if (privileges("deleteFile") == "true") {
										echo "<th width=\"75\" class=\"tableHeader\">Delete</th>";
									}
									
								echo "</tr>";
							
							while ($files = readdir($filesDirectory)) {
								if ($files !== "." && $files !== "..") {
									$filesResult = "true";
									$count++;
									
									echo "<tr";
									if ($count & 1) {echo " class=\"even\">";} else {echo " class=\"odd\">";}
										$fileArray = explode(".", $files);
										$fileExtension = end($fileArray);
										$additionStrip = explode("_", $files);
										$arraySize = sizeof($additionStrip) - 1;
										$name = "";
										
										for ($i = 0; $i <= $arraySize; $i++) {
											if ($i == 0) {
												$name .= $additionStrip[$i];
											} elseif ($arraySize > $i && $i > 0) {
												$name .= "_" . $additionStrip[$i];
											} else {
												$name .= "." . $fileExtension;
											}
										}
										
										echo "<td><a href=\"gateway.php/files/" . $categoryKey . "/" . $files . "\" target=\"_blank\">" . $name . "</a></td>";
										
										if (privileges("deleteFile") == "true") {
											echo "<td width=\"75\"><a class=\"action smallDelete\" href=\"index.php?action=delete&directory=" . $categoryKey . "&name=" . urlencode($files) . "\" onmouseover=\"Tip('Click to delete &quot;<strong>" . $name . "</strong>&quot;');\" onmouseout=\"UnTip();\" onclick=\"return confirm('This action cannot be undone. Continue?');\"></a></td>";
										}
										
									echo "</tr>";
								}
							}
							
							if (!isset($filesResult)) {
								echo "<tr class=\"odd\"><td colspan=\"2\"><div class=\"noResults notAssigned\">There are no files in this category</div></td></tr>";
							}
							
          					echo "</table>";
							
							unset($filesResult);
						}
						
						if (privileges("uploadFile") == "true") {
							echo "<br /><br />";
							echo "<form name=\"upload\" id=\"validate\" action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\"><h2>Upload file</h2><blockquote><p><input type=\"file\" name=\"file\" id=\"file" . $item['id'] . "\" size=\"50\" class=\"validate[required]\"><br />Max file size: " . ini_get('upload_max_filesize') . "</p></blockquote><h2>Select category</h2><blockquote><p><select name=\"category\" id=\"category" . $item['id'] . "\" class=\"validate[required]\"><option value=\"\">- Select -</option>";
							$directories = unserialize($item['directories']);
							
							while (list($uploadKey, $uploadArray) = each($directories)) {
								echo "<option value=\"" . $uploadKey . "\">" . $uploadArray . "</option>";
							}
							
							echo "</select></p><p><input type=\"submit\" name=\"submit\" id=\"submit" . $item['id'] . "\" value=\"Upload File\" /></p></blockquote></form>";
						}
					} else {
						echo "<div class=\"noResults\">No categories found</div>";
					}
					
					echo "</div>";
					break;
			}
		}
		
		while($item = mysql_fetch_array($itemGrabber)) {
			if (($item['visible'] == "on" || $item['fromDate'] != "") || ($item['visible'] == "on" && $item['fromDate'] != "")) {
				$from = strtotime($item['fromDate'] . " " . $item['fromTime']);
				$to = strtotime($item['toDate'] . " " . $item['toTime']);
				
				if ($item['fromDate'] != "") {
					if ($from > $currentDate) {
						//Do nothing, this will display at a later time
					} elseif ($to <= $currentDate) {
						//Do nothing, this has expired
					} else {
						type($item['type']);
					}
				} else {
					type($item['type']);
				}
			}
		}
	}
?>
<?php footer(); ?>
</body>
</html>