
<?php 
$res = 0;
if (!$res && file_exists(__DIR__ . '/../main.inc.php')) {
    $res = @include __DIR__ . '/../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../main.inc.php')) {
    $res = @include __DIR__ . '/../../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../../main.inc.php')) {
    $res = @include __DIR__ . '/../../../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../../../main.inc.php')) {
    $res = @include __DIR__ . '/../../../../main.inc.php';
}
if (!$res) {
    die('Include of main fails');
}
dol_include_once('/dolichat/class/dolichat.class.php');

	ini_set('display_errors', '0');
	$dolichat = new dolichat($db);
	$eintrag = GETPOST('eintrag', 'restricthtml');
	$cuser = GETPOST('cuser', 'alphanohtml');
	$nick = GETPOST('nick', 'alpha');
	$rowid = GETPOSTINT('rowid');
	$boxid = GETPOST('boxid', 'alpha');
	$del = GETPOST('delimg', 'alpha');

	$dolichat->deletePictureByMessageAndName($rowid, $del);

	$rowid = max(1, $dolichat->getLatestChatRowId() + 1);
	if (GETPOSTINT('rowid') > 0) {
		$rowid = GETPOSTINT('rowid');
	}
?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8"/>
		<title>Mini Ajax File Upload Form</title>

		<!-- Google web fonts -->
		<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel='stylesheet' />
		
		<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
		<!-- The main CSS file 
		<link href="assets/css/style.css" rel="stylesheet" />-->
		<style>
		#drop input {
			display: none;
		}		
		</style>
	</head>

	<body style="margin: 0px;">
<?php
	print '<form id="upload" method="post" action="upload.php?eintrag='.$eintrag.'&cuser='.$cuser.'&nick='.$nick.'&rowid='.$rowid.'" enctype="multipart/form-data">';	
	if(($phone == 0 && $tablet == 0 ) && $_GET['mobile'] == 0){
			print '<div id="drop">';
			print '<a style="position: absolute; top: 0px; left: 0px;"><img id="'.$boxid.'uplicon" src="../images/upload.png" style="width: 26px;"></a>';
			print '<input type="file" name="upl" id="upl" multiple accept="image/*" capture="camera"/>';
	}else{
			print '	<div style="background-image: url(\'../images/upload.png\');background-repeat: no-repeat; background-size: 26px; background-position: 0px 0px; width: 26px; height: 26px;">
									<input type="file" src="../images/upload.png" name="upl" id="upl" multiple="" accept="image/*" capture="camera" style=" width: 26px; height: 26px;opacity: 0;">
							</div>';
	}
	
	print '<input type="hidden" value="0" id="count">
				 <input type="hidden" value="'.$boxid.'" id="boxid">				
				 <input type="hidden" id="rowid" value="'.$rowid.'">';
		 ?>
				
			</div>

			<ul style="list-style: none;">
				<!-- The file uploads will be shown here -->
			</ul>

		</form>

		<script>
			$('.UploadPicImg'+'<?php print $boxid; ?>', window.parent.document).click(function() {
    			$('#upl').trigger('click');
			});
		</script>
        
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