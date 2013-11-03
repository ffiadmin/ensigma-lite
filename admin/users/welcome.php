<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Administrator"); ?>
<?php
//Grab the contents of the message
	if (exist("welcome")) {
		$contents = query("SELECT * FROM `welcome` ORDER BY `id` DESC LIMIT 1");
	}
	
//Insert updates into the history bank
	if (isset($_POST['submit']) && !empty($_POST['title']) && !empty($_POST['content']) && is_numeric($_POST['sendEmail'])) {
		$title = escape($_POST['title']);
		$content = escape($_POST['content']);
		$sendEmail = $_POST['sendEmail'];
		
		if ($_POST['title'] !== $contents['title'] || $_POST['content'] !== $contents['content'] || $sendEmail !== $contents['sendEmail'] || is_uploaded_file($_FILES['attachment']['tmp_name'])) {
			if (is_uploaded_file($_FILES['attachment']['tmp_name'])) {
			//Grab the attachment
				$fileTempName = $_FILES['attachment']['tmp_name'];
				$fileName = basename($_FILES['attachment'] ['name']);
				
			//Add a random hash on the end
				$fileParts = explode(".", $fileName);
				$fileName = "";
				
				for($count = 0; $count <= sizeof($fileParts) - 1; $count++) {
					if ($count == sizeof($fileParts) - 2) {
						$fileName .= $fileParts[$count] . "_" . randomValue(10) . ".";
					} elseif ($count == sizeof($fileParts) - 1) {
						$fileName .= $fileParts[$count];
					} else {
						$fileName .= $fileParts[$count] . ".";
					}
				}
				
				$fileName = stripslashes($fileName);
				
			//Upload the file
				move_uploaded_file($fileTempName, "welcome_files/" . $fileName);
				
				$fileName = escape($fileName);
				
			//Insert into the database
				query("INSERT INTO `welcome` (
					  `id`, `title`, `content`, `attachment`, `sendEmail`
					  ) VALUES (
					  NULL, '{$title}', '{$content}', '{$fileName}', '{$sendEmail}'
					  )");
			} else {
			//Check to see if there was a file in the previous version and copy its name over
				$oldFile = query("SELECT * FROM `welcome` ORDER BY `id` ASC LIMIT 2", "raw");
				$count = 1;
				$fileName = "";
				
				while($file = mysql_fetch_array($oldFile)) {
					if ($count == 2) {
						$fileName = escape(stripslashes($file['attachment']));
					} else {
						$count++;
					}
				}
				
			//Insert into the database
				query("INSERT INTO `welcome` (
					  `id`, `title`, `content`, `attachment`, `sendEmail`
					  ) VALUES (
					  NULL, '{$title}', '{$content}', '{$fileName}', '{$sendEmail}'
					  )");
			}
		
			redirect("index.php?updated=welcome");
		} else {
			redirect("index.php");
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Welcome Message"); ?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage(); ?>
<h2>Welcome Message</h2>
<p>If so desired, users can recieve a confirmation message welcoming them to this site when they are enrolled. This message will be sent in the form of an email, and it can be designed to your specifications. Below the message is a setting where you can turn this automated feature on or off.</p>
<p>Note that there are several dynamic variables that can be placed inside of the title and message body, which will be replaced with customized values for each user. Here are the variables that can be included:</p>
<ul>
  <li>{name} - The user's full name</li>
  <li>{first name} - The user's first name</li>
  <li>{last name} - The user's last name</li>
  <li>{email address} - The user's primary email address</li>
  <li>{username} - The user's username</li>
  <li>{password} - The user's password [Highly discouraged!!! Will not be included for security reasons if this user's role is an Administrator.]</li>
  <li>{role} - The user's role</li>
  <li>{site name} - The name of this site</li>
  <li>{url} - The URL of this site</li>
  <li>{login} - The URL of this site's login page</li>
</ul>
<p>For example, if a user by the name of &quot;John Doe&quot; was recently created, with a username of &quot;jdoe&quot;, then a customized welcome message like this, &quot;Welcome to this site, {first name}! Your username is {username}.&quot;, will be converted to this, &quot;Welcome to this site, John! Your username is jdoe.&quot;.</p>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" name="sendEmail" id="validate" onsubmit="return errorsOnSubmit(this);">
  <div class="catDivider one">Message</div>
  <div class="stepContent">
    <blockquote>
      <p>Title<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The title of the automated email. <br />This also supports the dynamic variables listed above.')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($contents)) {
					echo " value=\"" . htmlentities($contents['title']) . "\"";
				}
			?> />
        </p>
      </blockquote>
      <p>Content<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the email message')" onmouseout="UnTip()" /> </p>
      <blockquote>
        <p>
          <textarea name="content" id="content1" cols="45" rows="5" style="width:640px; height:320px;" class="validate[required]" />
          <?php 
			if (isset ($contents)) {
				echo stripslashes($contents['content']);
			}
		  ?>
          </textarea>
        </p>
      </blockquote>
    </blockquote>
  </div>
  <div class="catDivider two">Attachment</div>
  <div class="stepContent">
    <blockquote>
      <p>Add an attachment: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('An attachment to be included with the email.')" onmouseout="UnTip()" /></p>
      <blockquote>
      <?php
		  if (isset ($contents) && !empty($contents['attachment'])) {
			  echo "Current file: <a href=\"welcome_files/" . $contents['attachment'] . "\" target=\"_blank\">" . $contents['attachment'] . "</a><br />";
		  }
	  ?>
        <input type="file" name="attachment" id="attachment" size="50" />
      </blockquote>
    </blockquote>
  </div>
  <div class="catDivider three">Send Email</div>
  <div class="stepContent">
    <blockquote>
      <p>Send email to new users: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('Set whether or not this email should be sent to new users.')" onmouseout="UnTip()" /></p>
      <blockquote>
        <p>
          <label>
            <input type="radio" name="sendEmail" value="1" id="sendEmail_0"<?php
				if (isset ($contents) && $contents['sendEmail'] == "1") {
					echo " checked=\"checked\"";
				}
			?> />
            Yes</label>
          <label>
            <input type="radio" name="sendEmail" value="0" id="sendEmail_1"<?php
				if (isset ($contents) && $contents['sendEmail'] == "0") {
					echo " checked=\"checked\"";
				}
			?> />
            No</label>
          <br />
        </p>
      </blockquote>
    </blockquote>
  </div>
  <div class="catDivider four">Submit</div>
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