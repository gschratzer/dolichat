
<?php 

	$eintrag = $_GET['eintrag'];
	$cuser = $_GET['cuser'];
	$nick = $_GET['nick'];

?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8"/>
		<title>Mini Ajax File Upload Form</title>

		<!-- Google web fonts -->
		<link href="http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel='stylesheet' />

		<!-- The main CSS file -->
		<link href="assets/css/style.css" rel="stylesheet" />
	</head>

	<body>
<?php
	print 
	'	<form id="upload" method="post" action="upload.php?eintrag='.$eintrag.'&cuser='.$cuser.'&nick='.$nick.'" enctype="multipart/form-data">
			<div id="drop">
				<a>Browse</a>
				<input type="file" name="upl" multiple />
			';
		
		 		if($eintrag!=""){
		 			print $eintrag; 
		 		}else{
		 			print 'Bild Hochladen';
		 		}
		 ?>
				
			</div>

			<ul>
				<!-- The file uploads will be shown here -->
			</ul>

		</form>
        
		<!-- JavaScript Includes -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="assets/js/jquery.knob.js"></script>

		<!-- jQuery File Upload Dependencies -->
		<script src="assets/js/jquery.ui.widget.js"></script>
		<script src="assets/js/jquery.iframe-transport.js"></script>
		<script src="assets/js/jquery.fileupload.js"></script>
		
		<!-- Our main JS file -->
		<script src="assets/js/script.js"></script>

	</body>
</html>