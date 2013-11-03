<?php require_once('../../../Connections/connDBA.php'); ?>
<?php
	if (privileges("sendEmail") == "true") {
		loginCheck("User,Administrator");
	} else {
		loginCheck("Administrator");
	}
?>
<?php
//Process the form
	if (isset ($_POST['submit']) && !empty ($_POST['from']) && !empty ($_POST['subject']) && !empty ($_POST['priority']) && !empty ($_POST['message'])) {
	//Get all of the form fields
		$from = $_POST['from'];
		$toDetirmine = $_POST['toDetirmine'];
		$toImport = $_POST['toImport'];
		$subject = stripslashes($_POST['subject']);
		$priority = $_POST['priority'];
		$body = stripslashes($_POST['message']);
		
	//Select the site name to conceal the "to" list
		$siteNameGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
		$siteName = mysql_fetch_array($siteNameGrabber);
		
	//Detirmine what kind of mass email is being sent
		$toGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
		$to = "";
		
		while($toData = mysql_fetch_array($toGrabber)) {
			$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
		}
		
	//Generate the header
		$random = md5(time());
		$mimeBoundary = "==Multipart_Boundary_x{$random}x";
		
		$header = "From: " . $from . "\n" .
					  "Reply-To: " . $from . "\n"  .
					  "X-Mailer: PHP/" . phpversion() . "\n" .
					  "X-Priority: " . $priority . "\n" .
					  "MIME-Version: 1.0\n" .
					  "Content-Type: multipart/mixed;\n" .
					  " boundary=\"{$mimeBoundary}\"";
				  
		//The message of the email
			$message = "--{$mimeBoundary}\n" .
					   "Content-Type: text/html; charset=\"iso-8859-1\"\n" .
					   "Content-Transfer-Encoding: 7bit\n\n" .
					   $body . "\n\n";
		
		if (is_uploaded_file($_FILES['attachment']['tmp_name'])) {
			//Grab the attachment
				$fileTempName = $_FILES['attachment']['tmp_name'];
				$fileType = $_FILES['attachment']['type'];
				$fileName = basename($_FILES['attachment'] ['name']);	
			
			//Grab the attachment info
				$file = fopen($fileTempName, 'rb');
				$data = fread($file, filesize($fileTempName));
				fclose ($file);	
				
			//Processing			
				$data = chunk_split(base64_encode($data));
				$message .= "--{$mimeBoundary}\n" .
							"Content-Type: {$fileType};\n" . 
							" name = \"{$fileName}\"\n" . 
							"Content-Transfer-Encoding: base64\n\n" . 
							$data . "\n\n" .    
							"--{$mimeBoundary}--\n";
		} else {
			$message .= "--{$mimeBoundary}\n" .
						"Content-Type:  text/html;\n" . 
						" name = \"attachment.html\"\n" . 
						"Content-Transfer-Encoding: base64\n\n" . 
						chunk_split(base64_encode("<!--placeholder//-->")) . "\n\n" .    
						"--{$mimeBoundary}--\n";
		}
		
	//Processor
		$mailTo = explode(",", trim($to, ","));
		
		foreach ($mailTo as $to) {
			mail($to, $subject, $message, $header);
		}
		
	//Redirect
		header("Location: index.php?email=success");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
<head>
<?php title("Send an Email"); ?>
<?php headers(); ?>
<?php validate(); ?>
<?php tinyMCEAdvanced(); ?>
<script src="../../../javascripts/jQuery/scheduledAssist.jquery.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?>>
<?php topPage(); ?>
<h2>Scheduled Email</h2>
<p>If so desired, users can recieve an automated email which can be sent on specified basis. This message can be designed to your specifications.   Below the message is a setting where you can turn this automated feature   on or off.</p>
<p>Note that there are several dynamic variables that can be placed   inside of the title and message body, which will be replaced with   customized values for each user. Here are the variables that can be   included:</p>
<ul>
  <li>{name} - The user's full name</li>
  <li>{first name} - The user's first name</li>
  <li>{last name} - The user's last name</li>
  <li>{email address} - The user's primary email address</li>
  <li>{username} - The user's username</li>
  <li>{password} - The user's password [Highly discouraged!!! Will not   be included for security reasons if this user's role is an   Administrator.]</li>
  <li>{role} - The user's role</li>
  <li>{site name} - The name of this site</li>
  <li>{url} - The URL of this site</li>
  <li>{login} - The URL of this site's login page</li>
</ul>
<p>For example,  for a user by the name of &quot;John Doe&quot; and a username of &quot;jdoe&quot;, then a customized  message   like &quot;Welcome to this site, {first name}! Your username is   {username}.&quot;, will be converted into &quot;Welcome to this site, John!   Your username is jdoe.&quot;.</p>
<p>&nbsp;</p>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" name="sendEmail" id="validate" onsubmit="return errorsOnSubmit(this, true, 'attachment', false);">
  <div class="catDivider one">Settings</div>
  <div class="stepContent">
    <blockquote>
      <p>From:</p>
      <blockquote>
        <?php
	//Select the from email address
		$userName = $_SESSION['MM_Username'];
		$fromGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		$from = mysql_fetch_array($fromGrabber);
		
		if ($from['emailAddress2'] == "" && $from['emailAddress3'] == "") {
			echo "<input type=\"hidden\" name=\"from\" id=\"from\" value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress1'] . ">\" /><p><strong>" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress1'] . "&gt;</strong></p>";
		} else {
			echo "<select name=\"from\" id=\"from\"><option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress1'] . ">\" selected=\"selected\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress1'] . "&gt;</option>";
			
			if ($from['emailAddress2'] != "") {
				echo "<option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress2'] . ">\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress2'] . "&gt;</option>";
			}
			
			if ($from['emailAddress3'] != "") {
				echo "<option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress3'] . ">\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress3'] . "&gt;</option>";
			}
			
			echo "</select>";
		}
	?>
      </blockquote>
      <p>To:</p>
      <blockquote>
        <p><strong>This email will be sent to all registered users.</strong></p>
      </blockquote>
      <p>Subject<span class="require">*</span>:</p>
      <blockquote>
        <p>
          <input name="subject" type="text" id="subject" autocomplete="off" size="50" class="validate[required]" />
        </p>
      </blockquote>
      <p>Priority:</p>
      <blockquote>
        <p>
          <select name="priority" id="priority">
            <option value="5">Low</option>
            <option value="3" selected="selected">Normal</option>
            <option value="1">High</option>
          </select>
        </p>
      </blockquote>
      <p>Send this email once a
        <select name="day" id="day">
          <option value="day">day</option>
          <option value="week" selected="selected">week</option>
          <option value="month">month</option>
          <option value="year">year</option>
        </select>
        <span class="hideDay"> on <span class="monthDetails" style="display:none;">the </span>
        <select name="month" id="month" style="display:none;">
          <option value="Janurary" selected="selected">Janurary</option>
          <option value="February">February</option>
          <option value="March">March</option>
          <option value="April">April</option>
          <option value="May">May</option>
          <option value="June">June</option>
          <option value="July">July</option>
          <option value="August">August</option>
          <option value="September">September</option>
          <option value="October">October</option>
          <option value="November">November</option>
          <option value="December">December</option>
        </select>
        <select name="weekDay" id="weekDay">
          <option value="sunday">Sunday</option>
          <option value="monday" selected="selected">Monday</option>
          <option value="tuesday">Tuesday</option>
          <option value="wednesday">Wednesday</option>
          <option value="thursday">Thursday</option>
          <option value="friday">Friday</option>
          <option value="saturday">Saturday</option>
        </select>
        </span> <span class="monthDetails" style="display:none;">day of the month </span>at
        <select name="toTime" id="toTime">
          <option value="00:00"<?php if (isset ($agenda) && $agenda['toTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
          <option value="00:30"<?php if (isset ($agenda) && $agenda['toTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
          <option value="01:00"<?php if (isset ($agenda) && $agenda['toTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
          <option value="01:30"<?php if (isset ($agenda) && $agenda['toTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
          <option value="02:00"<?php if (isset ($agenda) && $agenda['toTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
          <option value="02:30"<?php if (isset ($agenda) && $agenda['toTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
          <option value="03:00"<?php if (isset ($agenda) && $agenda['toTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
          <option value="03:30"<?php if (isset ($agenda) && $agenda['toTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
          <option value="04:00"<?php if (isset ($agenda) && $agenda['toTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
          <option value="04:30"<?php if (isset ($agenda) && $agenda['toTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
          <option value="05:00"<?php if (isset ($agenda) && $agenda['toTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
          <option value="05:30"<?php if (isset ($agenda) && $agenda['toTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
          <option value="06:00"<?php if (isset ($agenda) && $agenda['toTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
          <option value="06:30"<?php if (isset ($agenda) && $agenda['toTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
          <option value="07:00"<?php if (isset ($agenda) && $agenda['toTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
          <option value="07:30"<?php if (isset ($agenda) && $agenda['toTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
          <option value="08:00"<?php if (isset ($agenda) && $agenda['toTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
          <option value="08:30"<?php if (isset ($agenda) && $agenda['toTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
          <option value="09:00"<?php if (isset ($agenda) && $agenda['toTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
          <option value="09:30"<?php if (isset ($agenda) && $agenda['toTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
          <option value="10:00"<?php if (isset ($agenda) && $agenda['toTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
          <option value="10:30"<?php if (isset ($agenda) && $agenda['toTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
          <option value="11:00"<?php if (isset ($agenda) && $agenda['toTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
          <option value="11:30"<?php if (isset ($agenda) && $agenda['toTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
          <option value="12:00"<?php if (isset ($agenda) && $agenda['toTime'] == "12:00") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
          <option value="12:30"<?php if (isset ($agenda) && $agenda['toTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
          <option value="13:00"<?php if (isset ($agenda) && $agenda['toTime'] == "13:00") {echo " selected=\"selected\"";} elseif (!isset ($agenda)) {echo " selected=\"selected\"";} elseif ($agenda['toTime'] == "") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
          <option value="13:30"<?php if (isset ($agenda) && $agenda['toTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
          <option value="14:00"<?php if (isset ($agenda) && $agenda['toTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
          <option value="14:30"<?php if (isset ($agenda) && $agenda['toTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
          <option value="15:00"<?php if (isset ($agenda) && $agenda['toTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
          <option value="15:30"<?php if (isset ($agenda) && $agenda['toTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
          <option value="16:00"<?php if (isset ($agenda) && $agenda['toTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
          <option value="16:30"<?php if (isset ($agenda) && $agenda['toTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
          <option value="17:00"<?php if (isset ($agenda) && $agenda['toTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
          <option value="17:30"<?php if (isset ($agenda) && $agenda['toTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
          <option value="18:00"<?php if (isset ($agenda) && $agenda['toTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
          <option value="18:30"<?php if (isset ($agenda) && $agenda['toTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
          <option value="19:00"<?php if (isset ($agenda) && $agenda['toTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
          <option value="19:30"<?php if (isset ($agenda) && $agenda['toTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
          <option value="20:00"<?php if (isset ($agenda) && $agenda['toTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
          <option value="20:30"<?php if (isset ($agenda) && $agenda['toTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
          <option value="21:00"<?php if (isset ($agenda) && $agenda['toTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
          <option value="21:30"<?php if (isset ($agenda) && $agenda['toTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
          <option value="22:00"<?php if (isset ($agenda) && $agenda['toTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
          <option value="22:30"<?php if (isset ($agenda) && $agenda['toTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
          <option value="23:00"<?php if (isset ($agenda) && $agenda['toTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
          <option value="23:30"<?php if (isset ($agenda) && $agenda['toTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
        </select>
        , and do so <span class="hideRepeat" style="display:none;">for the next </span>
        <select name="repeat" id="repeat">
          <option value="forever">forever</option>
        </select>
        .</p>
    </blockquote>
  </div>
  <div class="catDivider two">Message</div>
  <div class="stepContent">
    <blockquote>
      <p> Enter the message of the email below<span class="require">*</span>:</p>
      <blockquote>
        <p>
          <textarea name="message" id="message" cols="45" rows="5" style="width:640px; height:320px;" />
          </textarea>
        </p>
      </blockquote>
    </blockquote>
  </div>
  <div class="catDivider three">Attachment</div>
  <div class="stepContent">
    <blockquote>
      <p>Add an attachment:</p>
      <blockquote>
        <input type="file" name="attachment" id="attachment" size="50" />
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