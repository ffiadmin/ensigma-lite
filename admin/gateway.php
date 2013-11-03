<?php require_once('../Connections/connDBA.php'); ?>
<?php
//Script to selectively allow access to files
//If a file extension was handed into the gateway
	if (sizeof(explode("/", $_SERVER['REQUEST_URI'])) > sizeof(explode("/", $strippedRoot))) {
		$gatewayFile = urldecode(str_replace($strippedRoot . "admin/gateway.php/", "", urldecode($_SERVER['REQUEST_URI'])));
		
	//Expose the directory path and file type
		$directoryArray = explode("/", $gatewayFile);
		$directoryDepth = sizeof($directoryArray) - 1;
		$filePath = explode("/", $gatewayFile);
		$fileDepth = sizeof($filePath) - 1;
		$fileSize = filesize($gatewayFile);
		
		for ($count = 0; $count <= $fileDepth; $count++) {
			if ($count == $directoryDepth) {
				$fileName = $filePath[$count];
			}
		}
	
	//Check ot see if the file exists
		if (!file_exists($gatewayFile) || is_dir($gatewayFile)) {
			die("The file was not found");
		}
	
	//Site administrators will have access to lesson and answer files from modules
		if (isset($_SESSION['MM_UserGroup'])) {
			$mimeType = getMimeType($gatewayFile);
			
			header('Content-Description: File Transfer');
			header("Content-type: " . $mimeType);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . $fileSize);
			ob_clean();
			flush();
			readfile($gatewayFile);
			exit;
		}
	} else {
		die(centerDiv("A file was not provided"));
	}
?>