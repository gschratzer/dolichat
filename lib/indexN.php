
<?php 
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");			// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");		// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");		// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");		// to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");

	ini_set('display_errors', '0');
	$eintrag = $_GET['eintrag'];
	$cuser = $_GET['cuser'];
	$nick = $_GET['nick'];
	$rowid = $_GET['rowid'];
	$boxid = $_GET['boxid'];
	$del = $_GET['delimg'];

	$sql ="DELETE FROM " . MAIN_DB_PREFIX . "chatpic Where MsgID = ".$rowid." AND PicName = '".$del."'";
	$res = $db->query($sql);

	$sql ="SELECT rowid FROM " . MAIN_DB_PREFIX . "chattext ";
	$sql.="ORDER BY rowid  DESC ";
	$sql.="LIMIT 0 , 1";
	$res = $db->query($sql);
	$row = $db->fetch_object($res);
	
	$rowid = $row->rowid + 1;
	if($_GET['rowid']){
		$rowid = $_GET['rowid'];
	}
?>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8"/>
		<title>Mini Ajax File Upload Form</title>

		<!-- Google web fonts -->
		<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel='stylesheet' />
		
		<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/js/jquery.min.js"></script>
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
	print '<form id="upload" method="post" action="upload.php?N=1&eintrag='.$eintrag.'&cuser='.$cuser.'&nick='.$nick.'&rowid='.$rowid.'" enctype="multipart/form-data">';	
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

			<ul style="list-style: none;display: none;">
				<!-- The file uploads will be shown here -->
			</ul>

		</form>

		<script>
			$('.UploadPicImg'+'<?php print $boxid; ?>', window.parent.document).click(function() {
    			$('#upl').trigger('click');
			});
			var sending = false;
			$(document).ready(function(){
				$('form#upload').submit(function( event ){
					if(sending == false)
					{
					    event.preventDefault();
					    var r = confirm("Upload?");
					    if (r == true) {
					        sending = true;
					        $('form#upload').submit();
					    } else {
					        //event.preventDefault();
					    }
					}
				});
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