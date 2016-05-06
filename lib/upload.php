<?php
require '../../main.inc.php';
require_once(DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php');

global $db;


$eintrag = $_GET['eintrag'];
$eintrag = addslashes($eintrag);
$eintrag = str_replace('&lt;br&gt;','<br>',$eintrag);

$cuser = $_GET['cuser'];
if($cuser=="-1") $cuser = 0;
$testFgroup = $cuser[0];
$staticuser=new User($db);
$staticuser->fetch($cuser);
$upload = 1;
if($testFgroup == "G"){
    $rowidG = substr($cuser,1); 
    $sql ="SELECT * FROM  ".MAIN_DB_PREFIX."chatgroup where rowid = '".$rowidG."'";
    $resql=$db->query($sql);
    $obj = $db->fetch_object($resql);
    $upload = $obj->G_upload;        
}
if($upload == 1){
	$nick = $_GET['nick'];

	
	// A list of permitted file extensions
	$allowed = array('PNG', 'png', 'jpg', 'JPG', 'GIF', 'gif', 'bmp', 'BMP', 'wav', 'WAV', 'mp3', 'flv');
	print_r($_FILES);
	if($_FILES['upl']['error'] == 0){
	
		$uplname = preg_replace ( '/[^a-z0-9.]/i', '', $_FILES['upl']['name'] );
		$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
	
		if(!in_array(strtolower($extension), $allowed)){
			echo '{"status":"error extension"}';
			exit;
		}
		if(empty($_FILES['upl']['tmp_name'])) $_FILES['upl']['tmp_name'] = $_FILES['upl']['name'];
		$filesize = filesize($_FILES['upl']['tmp_name']);
	
		if($filesize < 10000000){ // Notiz -> Variable (Adminbereich)
	
			$dirodt=DOL_DATA_ROOT.'/dolichat/uploads/'.$user->id;
			dol_mkdir($dirodt);
		
			$sql ="SELECT rowid FROM ".MAIN_DB_PREFIX."chattext ";
			$sql.="ORDER BY rowid  DESC ";
			$sql.="LIMIT 0 , 1";
			$res = $db->query($sql);
			$row = $db->fetch_object($res);
		
			$rowid = $row->rowid + 1;
			if($_GET['rowid']){
				$rowid = $_GET['rowid'];
			}
			$path = DOL_DATA_ROOT.'/dolichat/uploads/'.$user->id.'/'.$rowid.'/'.$_FILES['upl']['name'];
			$t_path = DOL_DATA_ROOT.'/dolichat/uploads/'.$user->id.'/t_'.$rowid.'/'.$_FILES['upl']['name'];

			// For Rowid
			$dirodt=DOL_DATA_ROOT.'/dolichat/uploads/'.$user->id.'/'.$rowid;
			dol_mkdir($dirodt);

			// For Rowid T
			$dirodt=DOL_DATA_ROOT.'/dolichat/uploads/'.$user->id.'/t_'.$rowid;
			dol_mkdir($dirodt);

			if(move_uploaded_file($_FILES['upl']['tmp_name'], $path)){
				echo '{"status":"success"}';

					$sql ="INSERT INTO ".MAIN_DB_PREFIX."chatpic (";
	    	    	$sql.="rowid ,";
	    	    	$sql.="PicName ,";
	    	    	$sql.="MsgID ,";
	    	    	$sql.="UserID ";	    	    	
	    	    	$sql.=") ";
	    	    	$sql.="VALUES (";
	    	    	$sql.="'', '".$_FILES['upl']['name']."', '".$rowid."', '".$user->id."'";
	    	    	$sql.=");";
					//echo '{"sql":"'.$sql.'"}';
					$res = $db->query($sql);
					//echo '{"db":"'.var_dump($db).'"}';

					if($_GET['N'] == 1)
					{
						$text = "%picto=".$rowid."/".$_FILES['upl']['name'];

						$sql ="INSERT INTO ".MAIN_DB_PREFIX."chattext ";
						$sql.=" (`rowid`, `chattext`, `user`, `user_id`, `privat`, `privat_name`, `gesehen`, `gesehen_Broadcast`, `timestamp`) ";
						$sql.=" VALUES ";
						$sql.=" (NULL, '".$text."', '19', '".$user->id."', '".$cuser."', 'Bild', '0', '', CURRENT_TIMESTAMP);";
						echo $sql;
						$res = $db->query($sql);
					}

				$height = 473; //maximalhoehe
				$width = 498; //maximalbreite
				$img_size = getimagesize($path);
				$img_size[4] = $img_size[1]/$height;
				$faktor = 100/$img_size[4];
				$img_size[1] = $img_size[1]/100;
				$img_size[0] = $img_size[0]/100;
				$img_size[1] = $img_size[1]*$faktor;
				$img_size[0] = $img_size[0]*$faktor;
				 
				if($img_size[0] >= $width) {
				    $img_size[4] = $img_size[0]/$width;
				    $faktor2 = 100/$img_size[4];
				    if($faktor2 << $faktor) {
				        $img_size[1] = $img_size[1]/100;
				        $img_size[0] = $img_size[0]/100;
				        $img_size[1] = $img_size[1]*$faktor2;
				        $img_size[0] = $img_size[0]*$faktor2;
				    }
				}
				 
				$imgh = imagecreatetruecolor($img_size[0], $img_size[1]);
				if($extension == "jpg" || $extension == "JPG" || $extension == "png" || $extension == "PNG" || $extension == "gif" || $extension == "GIF")
				{
					if($extension == "jpg" || $extension == "JPG")
					{
						$imgh2 = imagecreatefromjpeg($path); // imagecreatefrompng
					}
					elseif($extension == "png" || $extension == "PNG")
					{
						$imgh2 = imagecreatefrompng($path); //imagecreatetruecolor 
					}
					

					if($extension == "gif" || $extension == "GIF")
					{
						copy($path, $t_path);
					}
					else
					{

						$imgsz = getimagesize($path);
						//$black = imagecolorallocate($imgh2, 0, 0, 0);
						 
						imagecopyresized($imgh, $imgh2, 0, 0, 0, 0, $img_size[0], $img_size[1], $imgsz[0], $imgsz[1]);
						 
						imagejpeg($imgh, $t_path, 60);
					}
				}
				else
				{
					copy($path, $t_path);
				}
				
				exit;
			}
		}else{
			print 'File size is too large';
			exit;
		}
		//if(!move_uploaded_file($_FILES['upl']['tmp_name'], DOL_DOCUMENT_ROOT.'/dolichat/lib/uploads/'.$_FILES['upl']['name'])){
		//	print ' DOUU ';
		//}
	} 
}
echo '{"status":"error"}';
exit;