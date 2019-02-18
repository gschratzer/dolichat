<?php
//Config auslesen 
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");     // to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");   // to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");   // to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");   // to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php');
$langs->load("dolichat@dolichat");
$sqlPic="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_SHOW_PIC';";
$resqlPic= $db->query($sqlPic);
$objPic = $db->fetch_object($resqlPic);
$Bildernum = $objPic->value;
$conf->global->dolichat_USE_DEL_TIME = 1;
$cuser = $_GET['cuser'];
$alldays = $_GET['alldays'];
$fromrow = $_GET['getnew'];


$neu = '';
$testFgroup = $cuser[0];

$Tage = $conf->global->dolichat_DEL_TIME;

//  Timestamp bsp.: 2014-08-12 09:35:20
$inTagen = strtotime(date('Y-m-d H:i:s') . ' -' . $Tage . ' day');
//  WHERE timestamp < '".$inTagen."'";

if($cuser > 0)
{
    $sql_m = "SELECT Count(rowid) as max FROM " . MAIN_DB_PREFIX . "chattext where ((privat =  ".$cuser." and user_id = ".$user->id.") OR (privat = ".$user->id." and user_id = ".$cuser."))";
    if($Tage > 0) $sql_m.= " AND timestamp > now() - INTERVAL ".$Tage." DAY ";
    $res_m = $db->query($sql_m);
    $max = $db->fetch_object($res_m);

    $sql = "SELECT * FROM " . MAIN_DB_PREFIX . "chattext ";
    $sql.= "WHERE ((privat =  ".$cuser." and user_id = ".$user->id.") OR (privat = ".$user->id." and user_id = ".$cuser."))";
    if($Tage > 0) $sql.= " AND timestamp > now() - INTERVAL ".$Tage." DAY ";
    $sql.= "ORDER BY `" . MAIN_DB_PREFIX . "chattext`.`timestamp` ASC ";

    if($max->max < 10) $max->max = 10;
    if(!$alldays)
    {
        $sql.= "LIMIT ".($max->max - 10)." , 10";
    }
    else
    {
        $sql.= "LIMIT 0 , ".($max->max - 10);
    }
}
elseif($cuser == 0)
{
    $sql = "SELECT * FROM " . MAIN_DB_PREFIX . "chattext where privat = 0 ORDER BY `" . MAIN_DB_PREFIX . "chattext`.`timestamp` ASC";
}
elseif($cuser == -1)
{
    if(!$alldays)
    {
        $sql = " SELECT * 
													FROM " . MAIN_DB_PREFIX . "chattext
														WHERE ((privat IN (0, ".$user->id.") OR user_id = ".$user->id."))";
        if($Tage > 0) $sql.= " AND timestamp > now() - INTERVAL ".$Tage." DAY ";
        $sql.= "
														AND rowid IN (														
															SELECT foo.rowid
															FROM ( 														
																SELECT rowid
																FROM  `" . MAIN_DB_PREFIX . "chattext` 
																ORDER BY rowid DESC
																LIMIT 10
																) AS foo
															)
												ORDER BY  `" . MAIN_DB_PREFIX . "chattext`.`timestamp` ASC
												Limit 10";
    }
    elseif($alldays)
    {
        $sql = "SELECT * 
												FROM " . MAIN_DB_PREFIX . "chattext
												  WHERE ((privat IN (0, ".$user->id.") OR user_id = ".$user->id."))";
        if($Tage > 0) $sql.= " AND timestamp > now() - INTERVAL ".$Tage." DAY ";
        $sql.=  "
												AND rowid NOT IN (														
													SELECT foo.rowid
													FROM ( 														
														SELECT rowid
														FROM  `" . MAIN_DB_PREFIX . "chattext` 
														ORDER BY rowid DESC 
														LIMIT 0 , 10
													) AS foo
												)
												ORDER BY  `" . MAIN_DB_PREFIX . "chattext`.`timestamp` ASC ";
    }
}


if($fromrow)
{
    $sql = "SELECT * FROM " . MAIN_DB_PREFIX . "chattext where (privat IN (0, ".$user->id.") OR user_id = ".$user->id.") AND rowid > ".$fromrow." ORDER BY `" . MAIN_DB_PREFIX . "chattext`.`timestamp` ASC LIMIT 0 , 10";
}
//echo $sql;
$ergebnis = $db->query($sql);
//print $sql;
$werbung_n = 1;
$werbungs_typ = 1;

if(!$fromrow && !$alldays)
{
    $neu.= '<div class="dateshower" style="text-align: center; display:none;">'.$langs->trans('Today').'</div>';
    //$days[] = date("Y-m-d");
    $neu.= '<div name="notlost1t"><div id="today" ></div></div>'; // DEBUG
}

$neu.= '<div>';

while($row = $db->fetch_object($ergebnis))
{
    // in $days stehen alle Tage im Format yyyy-mm-dd in welchen bereits die Datumszeile angezeigt wurde
    // es soll ja die Datumszeile nur einmal pro Tag angezeigt werden deshalb die Frage ob das Datum der Chatnachricht im Array $days vorkommt

    if(!$days || !in_array(substr($row->timestamp, 0, -9), $days))
    {
        // TEST
        $neu.= '</div>';
        $neu.= '<div id="'.date("d.m.Y",strtotime(substr($row->timestamp, 0, -9))).'">';

        if(!$fromrow && !$alldays) $name="lost";

        $days[] = substr($row->timestamp, 0, -9);
        end($days);
        if(strtotime(substr($row->timestamp, 0, -9)) >= strtotime("today")){
            $neu.= '<div class="date" id="'.key($days).$name.'datekeydiv"><div style="text-align: center;" name="notlost" id="today" id="'.key($days).'datekey">'.$langs->trans('Today').'</div></div>';
        }else if (strtotime(substr($row->timestamp, 0, -9)) >= strtotime("yesterday")){
            $neu.= '<div class="date" id="'.key($days).$name.'datekeydiv"><div style="text-align: center;" name="notlost" id="'.key($days).'datekey">'.$langs->trans('Yesterday').'</div></div>';
        }else{
            $neu.= '<div class="date" id="'.key($days).$name.'datekeydiv"><div style="text-align: center;" name="notlost" id="'.key($days).'datekey">'.date("d.m.Y",strtotime(substr($row->timestamp, 0, -9))).'</div></div>';
        }

        if($fromrow || $alldays){
            //print "<script>$('#1lostdatekeydiv').hide();</script>";
        }
        //Script
        $neu.= '<script>        							
	  							$(window).scroll(function () { 
			    					//You\'ve scrolled this much:
			    					var input = $(\'#'.key($days).'datekey\').text();
			    					var position = $(\'#'.key($days).'datekey\').position();
			    					// rgba(108, 108, 108, 0.26)
			    					if((position.top - $(window).scrollTop()) < 30){
			      					//$(\'.dateshower\').text("You\'ve scrolled " + $(window).scrollTop() + " pixels" + " tahts " + (position.top - $(window).scrollTop()) + " away from " + input + " date");
			      					$(\'.dateshower\').text(input);
			      					$(\'.dateshower\').show();
			      				}
									});		        						
	  						</script>';
    }

    if($row->privat == 0){
        $farbe = '#2A0000';
        $privatnachricht = ' sagt';
    }else{
        $farbe = '#0000DD';
        $privatnachricht = $row->privat_name;
    }
    //So Mo Di Mi Do Fr Sa
    $tage = array($langs->trans("So"), $langs->trans("Mo"), $langs->trans("Di"), $langs->trans("Mi"), $langs->trans("Do"), $langs->trans("Fr"), $langs->trans("Sa"));

    //$time = dol_print_date($row->timestamp,'%d %H:%M');
    $time = date('H:i', strtotime($row->timestamp));
    $day = date("w", strtotime($row->timestamp));
    $day = $tage[$day];
    //$day = date('D', $day);

    $privatnachricht3 = "";
    $privatnachricht2 = substr($privatnachricht, 8);
    if($privatnachricht2 == ""){
        $privatnachricht2 = "Broadcast";
        $privatnachricht3 = $langs->trans("zum")." Broadcast";
    }
    $privatnachricht2 = 'an '.$privatnachricht2;
    $user_to_you = $row->user;
    //print base64_decode($row->chattextblob);
    if(!empty($row->chattextblob)) $chattext = base64_decode($row->chattextblob);
    else $chattext = $row->chattext;

    $chattext = str_replace("<", "&lt;", $chattext);
    $chattext = str_replace(">", "&gt;", $chattext);
    $chattext = str_replace("\n", "<br>", $chattext);

    if(!empty($row->chattextblob)) $chattext_test = base64_decode($row->chattextblob);
    else $chattext_test = $row->chattext;
    //$chattext = wordwrap( $chattext, 37, "<br>\n", true);
    if($cuser>0){
        $privatnachricht2 = "";
        $user_to_you = "";
        $pxoffilter = 4;
        $zeigwho = "display:none;";
        $to_you_who_zeig = "display:none;";
    }elseif($cuser==0){
        $privatnachricht2="";
        $privatnachricht3="";
        $pxoffilter = 4;

    }else{
        $chattext_user=new User($db);
        $chattext_user->fetch($row->user_id);
        $hex = str_replace("#", "", $chattext_user->color);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $r_alt = 255 - $r;
        $g_alt = 255 - $g;
        $b_alt = 255 - $b; // 127 // 85 // 63
        //if(( $r + $g + $b ) > 256) $chattext_user_css_text_shadow = 'text-shadow: 0px 0px 4px black;';
        $chattext_user_css = 'font-size: 10px; border-radius: 30px; '.$chattext_user_css_text_shadow.' padding: 3px; background-color: rgba('.$r_alt.', '.$g_alt.', '.$b_alt.', 0.38)'; // text-shadow: 0px 0px 1px black;
        if($user->id != $row->user_id) $chattext_user_name = ' <span style="color:rgb('.$r.', '.$g.', '.$b.');'.$chattext_user_css.'"> '.$row->user.'</span>';
        $pxoffilter = 4;
        $chattext = $chattext.$chattext_user_name;
        unset($chattext_user_name);
    }

    $Bild ="";

    if(!empty($row->chattextblob)) $chattext_test_pic = base64_decode($row->chattextblob);
    else $chattext_test_pic = $row->chattext;
    $PIC = substr (strrchr ($chattext_test_pic, "%picto="), 7);
    $PIC = explode("%picto=", $chattext_test_pic); // Array ( [0] => TEST [1] => 807/del.png [2] => 807/OK.png [3] => 807/gesehen.png ) 1

    if ( strpos(strtolower($chattext_test_pic), 'youtube.com') !== false || strpos(strtolower($chattext_test_pic), 'youtu.be') !== false )
    {
        $link = explode("=", $chattext_test_pic);
        if(strpos($chattext_test_pic, '=') !== false)
        {
            $link = str_replace("\n", " ", $link[1]);
            $link = explode(" ", $link);
            $PIC[1] = '<br><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$link[0].'" frameborder="0" allowfullscreen></iframe>';
        }
        elseif (strpos($chattext_test_pic, 'http://') !== false || strpos($chattext_test_pic, 'https://') !== false  )
        {
            $link = str_replace("\n", " ", $chattext_test_pic);
            $link = explode("/", $link);
            $link = explode(" ", $link[3]);
            $PIC[1] = '<br><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$link[0].'" frameborder="0" allowfullscreen></iframe>';
        }
        else
        {
            $link = str_replace("\n", " ", $chattext_test_pic);
            $link = explode("/", $link);
            $link = explode(" ", $link[1]);
            $PIC[1] = '<br><iframe width="560" height="315" src="https://www.youtube.com/embed/'.$link[0].'" frameborder="0" allowfullscreen></iframe>';
        }
    }

    $Pic = $PIC[1];
    $url = $row->user_id;
    if($Bildernum == 1 || $Bildernum == ""){
        if($Pic!=""){
            foreach($PIC as $label => $ImagesLink){
                if(($label % 2)==0){}else{$styleUngerade = 'float:left;';}
                if($label != 0){
                    if(strpos($ImagesLink, '<iframe') !== false)
                    {
                        $Bild.= $Pic;
                    }
                    elseif (strpos($ImagesLink, 'http://') !== false || strpos($ImagesLink, 'https://') !== false  )
                    {
                        ini_set('default_socket_timeout', 1);
                        $headers = @get_headers($ImagesLink);
                        if(strpos($headers[0],'200')===false){
                            $Bild.= '[Error] Url does not exist!';
                        }else{
                            //$Bild.= '<a href="'.$ImagesLink.'" target="_blank"><img src="'.$ImagesLink.'" alt="Pic is Wrong" width="50%" style="min-width:124px;min-height:124;'.$styleUngerade.'""></a>';
                        }
                        ini_set('default_socket_timeout', 30);
                    }
                    else
                    {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        //$Bild.= finfo_file($finfo, DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink).' '.DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink;
                        $name = basename(DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink);
                        $type = finfo_file($finfo, DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink);
                        $size = filesize(DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink);
                        $fdate = date ("d.m.Y H:i", filemtime(DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink));

                        $Bild.= '<span>';
                        $Bild.= '<a href="'.dol_buildpath('document.php',1).'?modulepart=dolichat&file=uploads/'.$url.'/'.$ImagesLink.'&cache=1" target="_blank">';
                        $Bild.= '<img width="150px" src="/dolibarr/documents/dolichat/uploads/'.$url.'/'.$ImagesLink.'" class="downfile" style="margin:0 auto;border-radius: 8px;">';
                        //$Bild.= '<img width="20%" src="'.DOL_URL_ROOT.'/dolichat/img/text-file-3-xxl.png" class="downfile">';
                        $Bild.= '</a>';
                        $Bild.= '</span>';
                        $Bild.= '<span style="/*position: absolute;*/ margin-left: 20px;">';
                        $Bild.= '<table class="filedetail">';
                        $Bild.= '<tr>';
                        $Bild.= '<td>';
                        $Bild.= 'Filename: ';
                        $Bild.= '</td>';
                        $Bild.= '<td>';
                        $Bild.= $name;
                        $Bild.= '</td>';
                        $Bild.= '</tr>';
                        $Bild.= '<tr>';
                        $Bild.= '<td>';
                        $Bild.= 'Filetype: ';
                        $Bild.= '</td>';
                        $Bild.= '<td>';
                        $Bild.= $type;
                        $Bild.= '</td>';
                        $Bild.= '</tr>';
                        $Bild.= '<tr>';
                        $Bild.= '<td>';
                        $Bild.= 'Size: ';
                        $Bild.= '</td>';
                        $Bild.= '<td>';
                        $Bild.= $size;
                        $Bild.= '</td>';
                        $Bild.= '</tr>';
                        $Bild.= '<tr>';
                        $Bild.= '<td>';
                        $Bild.= 'Date: ';
                        $Bild.= '</td>';
                        $Bild.= '<td>';
                        $Bild.= $fdate;
                        $Bild.= '</td>';
                        $Bild.= '</tr>';
                        $Bild.= '<tr>';
                        $Bild.= '<td>';
                        $Bild.= 'Download: ';
                        $Bild.= '</td>';
                        $Bild.= '<td>';
                        $Bild.= '<a href="'.dol_buildpath('document.php',1).'?modulepart=dolichat&file=uploads/'.$url.'/'.$ImagesLink.'&cache=1'.'">Link</a>';
                        $Bild.= '</td>';
                        $Bild.= '</tr>';
                        $Bild.= '</table>';
                        $Bild.= '</span>';

                        if ( strpos(strtolower($ImagesLink), '.png') !== false || strpos(strtolower($ImagesLink), '.jpg') !== false || strpos(strtolower($ImagesLink), '.gif') !== false  || strpos(strtolower($ImagesLink), '.bmp') !== false)
                        {
                            $Bild.= '<br><a href="'.dol_buildpath('document.php',1).'?modulepart=dolichat&file=uploads/'.$url.'/'.$ImagesLink.'&cache=1" target="_blank">
                                            <!--<img src="'.dol_buildpath('document.php',1).'?modulepart=dolichat&file=uploads/'.$url.'/t_'.$ImagesLink.'&cache=1" alt="Pic is Wrong" width="50%" style="min-width:124px;min-height:124;'.$styleUngerade.'"">-->
                                        </a>';
                        }
                        elseif ( strpos(strtolower($ImagesLink), '.mp3') !== false || strpos(strtolower($ImagesLink), '.ogg') !== false || strpos(strtolower($ImagesLink), '.wav') !== false)
                        {

                            $Bild.= '<br>';
                            $Bild.= '<audio  controls preload="metadata">';
                            $Bild.= '<source src="'.dol_buildpath('document.php',1).'?modulepart=dolichat&file=uploads/'.$url.'/'.$ImagesLink.'&cache=1'.'" type="'.finfo_file($finfo, DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink).'">';
                            $Bild.= 'Your browser does not support the audio tag.';
                            $Bild.= '</audio>';
                        }
                        elseif ( strpos(strtolower($ImagesLink), '.ogg') !== false || strpos(strtolower($ImagesLink), '.webm') !== false || strpos(strtolower($ImagesLink), '.mp4') !== false)
                        {
                            $Bild.= '<br>';
                            $Bild.= '<video width="405px" height="240" controls preload="none" class="html5videoplayer">'; //  poster="'.DOL_URL_ROOT.'/dolichat/img/black_blank.png"
                            $Bild.= '<source src="'.dol_buildpath('document.php',1).'?modulepart=dolichat&file=uploads/'.$url.'/'.$ImagesLink.'&cache=1'.'" type="'.finfo_file($finfo, DOL_DATA_ROOT.'/dolichat/uploads/'.$url.'/'.$ImagesLink).'">';
                            $Bild.= 'Your browser does not support the video tag.';
                            $Bild.= '</video>';
                        }
                    }
                }
                $styleUngerade = '';
            }
            //$Bild = '<a href="../document.php?modulepart=dolichat&file=uploads/'.$url.'/'.$PIC.'&cache=1" target="_blank">
            //            <img src="../document.php?modulepart=dolichat&file=uploads/'.$url.'/t_'.$PIC.'&cache=1" alt="Pic is Wrong" width="100%">
            //         </a>';
            $chattext = $PIC[0].'<br>';

        }
    }


    if (strpos($chattext_test_pic, '%youtube=https://www.youtube.com') !== false)
    {
        // <iframe width="560" height="315" src="https://www.youtube.com/embed/kfvxmEuC7bU" frameborder="0" allowfullscreen></iframe>
        $var_e = explode('%youtube=https://www.youtube.com', $chattext_test_pic);
        $e_ifr = $var_e[1];
        $Bild.= '<iframe width="560" height="315" src="https://www.youtube.com'.$e_ifr.'"  frameborder="0" allowfullscreen>Iframe is Deaktive on the ext. Server</iframe>';
        // if($var_e_n[1]) $eintrag.= htmlspecialchars($var_e_n[1]);
        $chattext = $var_e[0].'<br>';
    }

    if(count($PIC)>2){
        $minBildwiht = '248px';
    }else{
        $minBildwiht = '1px';
    }

    if(!empty($row->chattextblob)) $chattext_l = base64_decode($row->chattextblob);
    else $chattext_l = $row->chattext;
    $chattext_l = strlen( $chattext_l);
    if($chattext_l < 10){
        $chattext_l = $chattext_l + 20;
    }elseif($chattext_l < 200){
        $chattext_l = $chattext_l + 100;
    }elseif($chattext_l < 500){
        $chattext_l = $chattext_l + 345;
    }elseif($chattext_l > 1000){
        $chattext_l = $chattext_l + 645;
    }
    if($Pic!="lib/"){
        //$chattext_l = 1000;
    }

    if($row->user_id==$user->id || $row->gesehen == 1){
        $to_you_time='id="to_you_time"';
        $to_you_who='id="to_you_who"';
        $to_you_text='id="to_you_text"';
        $to_you_text2='id="to_you_text2"';
        $to_trenstrich='id="to_trenstrich"';
        $scriptforone = 'ondblclick="Nachgsehen(\''.$row->rowid.'\', 0)"';
        $scriptforone3 = 'ondblclick="Nachgsehen(\''.$row->rowid.'\', 1)"';
    }elseif($row->gesehen == 0){
        $scriptforone = 'ondblclick="Nachgsehen(\''.$row->rowid.'\', 0)"';
        $scriptforone3 = 'ondblclick="Nachgsehen(\''.$row->rowid.'\', 1)"';
        $to_you_time='id="to_you_time_nicht_gesehen"';
        $to_you_who='id="to_you_who_nicht_gesehen"';
        $to_you_text='id="to_you_text_nicht_gesehen"';
        $to_you_text2='id="to_you_text_nicht_gesehen2"';
        $to_trenstrich='id="to_trenstrich_nicht_gesehen"';
    }
    if($row->gesehen == 1){
        $gesehenimg='<img src="images/checkmark.png" alt="Gesehen" width="13px" height="12px">';
        if($user->rights->dolichat->Admin){
            $pxwherimg = "90px";
        }else{
            $pxwherimg = "70px";
        }
    }else{
        $gesehenimg="";
        if($user->rights->dolichat->Admin){
            $pxwherimg = "70px";
        }else{
            $pxwherimg = "70px";
        }

    }

    //$var=!$var;
    $tester2 = $tester;
    // Wenn die nachricht von einem selber stammt
    //$form->textwithtooltip('','$loginhtmltext',2,1,'$logintext');
    if($user->id == $row->user_id){
        $tester = 1;
    }
    // Wenn die nachricht privat an dich geschickt wurde
    if($user->id == $row->privat) {
        $tester = 2;
    }
    // Wenn Broadcast
    if($row->privat == 0) {
        if($user->id != $row->user_id){
            $tester = 3;
        }
    }
    if($chattext_test){
        $neu.='<div>';
        $delimg="";
        if($user->rights->dolichat->Admin){
            $scriptforone2=$scriptforone;
            $scriptfullsize = 'ondblclick="fullsizechattext(this)"'; // Debug
            $scriptforoneimg = 'onclick="loschenimg(\''.$row->rowid.'\')"'; // Debug
            $delimg='<img src="images/del.png" alt="Del" width="13px" height="12px" '.$scriptforoneimg.'>'; //    padding-right: 4px;
        }
        if($tester == 1){
            // ToDo Day als center
            $neu.='<div id="msg-line" class="msg-lineR">';
            $neu.='<div id="chattabel" '.$scriptfullsize.' class="chatdiv2You" style="border-radius: 13px 0px 40px 13px;">';
            $neu.='<div class="chatdiv2You2" style="">';
            $neu.='	<span '.$scriptforone3.' id="your_tr" class="your_tr_cl" style="color:'.$farbe.';">
                      						<span id="your_chattext" name="your_chattext" class="shadow2" width="'.$chattext_l.'" style="min-width:'.$minBildwiht.';">'.
                $chattext.' '.$Bild.
                '</span>
                      					</span>'; // <span id="your_text_to" class="shadow2" width="'.$pxoffilter.'%" style="'.$zeigwho.'">'.$privatnachricht2.'</span>
            $neu.='	<span class="your_tr_span" >'.
                $time.' '.$gesehenimg.' '.$delimg.
                ' </span>';
            $neu.='</div>';
        }
        if($tester == 2){
            $neu.='<div id="msg-line" class="msg-lineL">';
            $neu.='<div id="chattabel" '.$scriptfullsize.' class="chatdiv2To" style="word-wrap: break-word; font-size:14px; max-width: 90%; max-height: 450px;border-radius:0px 13px 13px 40px;padding-left:28px">';
            $neu.='<div style="width:100%;max-height:460px; overflow:hidden; padding-right: 60px;">';
            $neu.='	<span '.$scriptforone2.' id="to_you_tr" style="height: 30px;color:'.$farbe.';">
                      						<span '.$to_you_text.' class="shadow2" width="'.$chattext_l.'">'.$chattext.' '.$Bild.'</span>
                      					</span>';
            $neu.='	<span style="width: 70px;z-index: 1;float: right; position: absolute; bottom: 10px; right: 10px; text-align: right; padding-right: 4px; font-size: 12px;color: #838383;padding-left: 30px;">'
                .$time.' '.$delimg.
                '</span>';
            $neu.='</div>';
        }
        if($tester == 3){
            $neu.='<div id="msg-line" class="msg-lineL">';
            $neu.='<div id="chattabel" '.$scriptfullsize.' class="chatdiv2Br" style="font-size:14px;">';
            $neu.='	<span '.$scriptforone2.' id="Broadcast_tr" style="height: 30px;color:'.$farbe.';">
                     	 						<span id="Broadcast_text" class="shadow2" width="'.$chattext_l.'" style="padding-left: 4px;">'
                .$chattext.' '.$Bild.
                '		</span>
                     	 					</span>';
            $neu.='	<span style="text-align: right; padding-right: 4px; font-size: 12px;color: #474747;padding-left: 30px;">'
                .$time.' '.$delimg.
                '	</span>';
        }
        $neu.='</div>';
        $neu.='</div>';
        $neu.='</div>';
        //$neu.='<span id="test" style="clear: both; float: left; display: block;"></span>';
        if($testFgroup == "G"){
            $sql2 = "SELECT * FROM " . MAIN_DB_PREFIX . "chattext where privat LIKE '%G%' ORDER BY `" . MAIN_DB_PREFIX . "chattext`.`timestamp` ASC";
            //print $sql;
            $i=0;
            $ergebnis2 = $db->query($sql2);
            while($row2 = $db->fetch_object($ergebnis2))
            {
                if($row2->gesehen_Broadcast != ''){
                    $ids = $row2->gesehen_Broadcast;    // z.b: "11, 2, 4, 6, 15, 22" wehr hat bereits den Cast gesehen als user id
                    $ids_ex = explode(", ", $ids);      // $ids_ex[0] == 11 ... $ids_ex[1] == 2
                    if (in_array($user->id, $ids_ex)) {
                    }else{
                        $nicht_gesehener_Cast = $row2->rowid;

                        $sql='UPDATE " . MAIN_DB_PREFIX . "chattext';
                        $sql.= ' SET gesehen_Broadcast =  "'.$row2->gesehen_Broadcast.', '.$user->id.'"';
                        $sql.= ' WHERE rowid = '.$row2->rowid.' ;';
                        //print $sql;
                        $up_gesehen = $db->query($sql);
                    }
                }else{
                    $sql='UPDATE " . MAIN_DB_PREFIX . "chattext';
                    $sql.= ' SET gesehen_Broadcast =  "'.$user->id.'"';
                    $sql.= ' WHERE rowid = '.$row2->rowid.' ;';
                    //print $sql;
                    $up_gesehen = $db->query($sql);
                }
            }
            $firstoff = 1;
        }else{
            $sql2 = "SELECT * FROM " . MAIN_DB_PREFIX . "chattext where privat = '0' ORDER BY `" . MAIN_DB_PREFIX . "chattext`.`timestamp` ASC";
            //print $sql;
            $i=0;
            $ergebnis2 = $db->query($sql2);
            while($row2 = $db->fetch_object($ergebnis2))
            {
                if($row2->gesehen_Broadcast != ''){
                    $ids = $row2->gesehen_Broadcast;    // z.b: "11, 2, 4, 6, 15, 22" wehr hat bereits den Cast gesehen als user id
                    $ids_ex = explode(", ", $ids);      // $ids_ex[0] == 11 ... $ids_ex[1] == 2
                    if (in_array($user->id, $ids_ex)) {
                    }else{
                        $nicht_gesehener_Cast = $row2->rowid;

                        $sql ="UPDATE " . MAIN_DB_PREFIX . "chattext";
                        $sql.= ' SET gesehen_Broadcast =  "'.$row2->gesehen_Broadcast.', '.$user->id.'"';
                        $sql.= ' WHERE rowid = '.$row2->rowid.' ;';
                        //print $sql;
                        $up_gesehen = $db->query($sql);
                    }
                }else{
                    $sql="UPDATE " . MAIN_DB_PREFIX . "chattext";
                    $sql.= ' SET gesehen_Broadcast =  "'.$user->id.'"';
                    $sql.= ' WHERE rowid = '.$row2->rowid.' ;';
                    //print $sql;
                    $up_gesehen = $db->query($sql);
                }
            }
            $firstoff = 1;
        }
    }
}
$neu.='<div id="chattabel">';
$neu.='<span><span colspan="8" height="15px"></span></span>';
$neu.='</div>';


echo $neu;

function formatFileSize($bytes) {
    if ($bytes >= 1000000000) {
        return round($bytes / 1000000000, 2).' GB';
    }

    if ($bytes >= 1000000) {
        return round($bytes / 1000000, 2).' MB';
    }

    return round($bytes / 1000, 2).' KB';
}