<?php
ini_set('display_errors', '1');

global $user;
if (! defined('REQUIRE_JQUERY_LAYOUT'))  define('REQUIRE_JQUERY_LAYOUT','1');
if (! defined('REQUIRE_JQUERY_BLOCKUI')) define('REQUIRE_JQUERY_BLOCKUI', 1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");			// to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");		// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");		// to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");		// to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
dol_include_once('/dolichat/class/dolichat.class.php');
require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
// require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';

$dolichat=new dolichat($db);
$staticuser=new User($db);
$object = new User($db);
$form = new Form($db);

$staticuser->fetch($user->id);
$staticuser->getrights();

if (!$staticuser->rights->dolichat->UseChat) accessforbidden();
if(!$conf->global->MAIN_MODULE_DOLICHAT){ accessforbidden();}

/**
### Dolichat Aktion and Processing ###
 */

if($_GET['user'] > 0)
{
    setcookie('chat_user'.$user->id, $_GET['user']);
}

$chat_user = $dolichat->get_Dolichat_user();
if(is_array($chat_user))
    foreach($chat_user as $key => $cuser)
    {
        if($cuser->rowid != $user->id)
        {
            $lmassage = $dolichat->get_last_message($cuser->rowid, $user->id);
            if($lmassage) $chat_user_arr[strtotime($lmassage->timestamp)] = $cuser;
            else $no_chat_user_arr[] = $cuser;
        }
    }

if(is_array($chat_user_arr))
    $ksort_res = krsort($chat_user_arr);

if(is_array($no_chat_user_arr))
    foreach($no_chat_user_arr as $key => $cval)
    {
        $chat_user_arr[$key] = $cval;
    }
//print_r($chat_user_arr);
$standard_user = 0;

/**
### Dolichat Aktion and Processing ###
 */

/**
### Desing Processing ###
 */
// Define height of file area (depends on $_SESSION["dol_screenheight"])
//print $_SESSION["dol_screenheight"];
$maxheightwin=(isset($_SESSION["dol_screenheight"]) && $_SESSION["dol_screenheight"] > 466)?($_SESSION["dol_screenheight"]-186):660;	// Also into index_auto.php file

$morejs=array();
if (empty($conf->global->MAIN_ECM_DISABLE_JS)) $morejs=array("/includes/jquery/plugins/jqueryFileTree/jqueryFileTree.js");
$moreheadcss="
	<!-- dol_screenheight=".$_SESSION["dol_screenheight"]." -->
	<style type=\"text/css\">
	    #containerlayout {
	        height:     ".$maxheightwin."px;
	        margin:     0 auto;
	        width:      100%;
	        min-width:  700px;
	        _width:     700px; /* min-width for IE6 */
	    }
	    .pane-in {
	   		overflow-y: hidden !important;
	    }
	</style>";
$moreheadjs=empty($conf->use_javascript_ajax)?"":"
	<script type=\"text/javascript\">
	    jQuery(document).ready(function () {
	        jQuery('#containerlayout').layout({
	        	name: \"ecmlayout\"
	        ,   paneClass:    \"ecm-layout-pane\"
	        ,   resizerClass: \"ecm-layout-resizer\"
	        ,   togglerClass: \"ecm-layout-toggler\"
	        ,   center__paneSelector:   \"#ecm-layout-center\"
	        ,   north__paneSelector:    \"#ecm-layout-north\"
	        ,   west__paneSelector:     \"#ecm-layout-west\"
	        ,   resizable: true
	        ,   north__size:        32
	        ,   north__resizable:   false
	        ,   north__closable:    false
	        ,   west__size:         340
	        ,   west__minSize:      280
	        ,   west__slidable:     true
	        ,   west__resizable:    true
	        ,   west__togglerLength_closed: '100%'
	        ,   useStateCookie:     true
	            });

	        jQuery('#ecm-layout-center').layout({
	            center__paneSelector:   \".ecm-in-layout-center\"
	        ,   south__paneSelector:    \".ecm-in-layout-south\"
	        ,   resizable: false
	        ,   south__minSize:      32
	        ,   south__resizable:   false
	        ,   south__closable:    false
	            });
	    });
	</script>";

llxHeader($moreheadcss.$moreheadjs,$langs->trans("Dolichat"),'','','','',$morejs,'',0,0);


if (! empty($conf->use_javascript_ajax)) $classviewhide='hidden';
else $classviewhide='visible';


if (empty($conf->dol_use_jmobile))
{
    //$head = ecm_prepare_dasboard_head('');
    $head="";
    dol_fiche_head($head, 'index', '', 1, '');
}

/**
### Desing Processing END ###
 */

/**
### VIEW ###
 */
// Status Processing
print '<input type="hidden" id="active_stat" value="1">';
// Audio Processing
print '<audio id="note_sound" controls style="display:none;">';
print '<source src="sound/newmessage.wav" type="audio/wav">';
print '</audio>';

print '<input type="hidden" id="OnlineStatus0" value="'.$langs->trans("OnlineStatusGrey").'">';
print '<input type="hidden" id="OnlineStatus1" value="'.$langs->trans("OnlineStatusGreen").'">';
print '<input type="hidden" id="OnlineStatus2" value="'.$langs->trans("OnlineStatusOrange").'">';

print '<input type="hidden" id="DOL_URL_ROOT" value="'.DOL_URL_ROOT.'">';

print '<div id="containerlayout"> <!-- begin div id="containerlayout" -->';
/* TOOL Bar Global Chat

print '<div id="ecm-layout-north" class="toolbar largebutton">';

    // Start top panel, toolbar
    print '<div class="toolbarbutton">';
        print 'Tool';
    print '</div>';

// End top panel, toolbar
print '</div>';
*/


// Linkes Fenster ##########################################################################################

$classviewhide="visible";
//print '<div id="ecm-layout-west" class="'.$classviewhide.'">';
//		print '<div class="LeftBox">';
//
//			// Chat Tool Box
//			print '<div style="height: 30px; border-bottom: 1px solid #BBBBBB;">';
//				print '<div style="clear:both;">';
//					print '<div style="float: left; width: 50%;height: 30px;">';
//						print '<input class="UserViewTool" type="button" name="chats" value="'.$langs->trans("Chats").'" style="border-right: 1px solid #BBBBBB;" onclick="switch_user_chat()">';
//					print '</div>';
//					print '<div style="float: right; width: 50%;">';
//						print '<input class="UserViewTool" type="button" name="contacts" value="'.$langs->trans("Contacts").'" onclick="switch_user_contatct()">';
//					print '</div>';
//				print '</div>';
//			print '</div>';
//			print '<div style="height:31px;">';
//				print '<input type="text" value="" id="user_search" class="UserSearch" placeholder="'.$langs->trans("UserSearchDotDotDot").'">';
//				//print '<div class="UserSearchButton">';
//				print '<img id="mute" src="img/mute.png" class="muteclass" title="'.$langs->trans("Mute").'">';
//				//print '</div>';
//			print '</div>';
//
//			// UserChatDiv
//			print '<div class="UserChatDivOut">';
//				print '<div class="UserChatDivIn">';
//
//				if(is_array($chat_user_arr))
//				foreach($chat_user_arr as $key => $cuser)
//				{
//					if($cuser->rowid != $user->id)
//					{
//						print '<input type="hidden" value="'.$cuser->firstname.' '.$cuser->lastname.'" id="user_name_of_'.$cuser->rowid.'">';
//						// User Box
//						$object->fetch($cuser->rowid);
//
//						$MessageCNT = "";
//						$lmassage = $dolichat->get_last_message($cuser->rowid, $user->id);
//						if(!empty($lmassage->timestamp))
//						{
//							$user_name_div = "user_box_chat";
//							$user_box_visible = "";
//							if($lmassage->user_id != $user->id && $lmassage->gesehen == 0) $MessageCNT = "new";
//						}
//						else
//						{
//							$user_name_div = "user_box_kontakt";
//							$user_box_visible = "display:none;";
//						}
//
//						if(strtotime($lmassage->timestamp) < strtotime("now - ".$conf->global->dolichat_DEL_TIME." days"))
//						{
//							$user_name_div = "user_box_kontakt";
//							$user_box_visible = "display:none;";
//						}
//
//						print '<div id="user_detail_box_'.$object->id.'" class="UserKontakttBox" name="'.$user_name_div.'" style="'.$user_box_visible.'" onclick="change_chat_user('.$object->id.')" lastname="'.strtolower($cuser->lastname).'" firstname="'.strtolower($cuser->firstname).'" >';
//
//							print '<div class="UserImg">';
//								if($object->photo != "")
//								{
//									print $form->showphoto('userphoto', $object, '','','','user_img');
//								}
//								else
//								{
//									if($object->color == "")
//									{
//										$object->color = "CCCCCC";
//										$hex = 'white';
//									}
//									else
//									{
//										$hex = $dolichat->getContrast50($object->color);
//									}
//
//									print '<div class="user_img" style="background-color:#'.$object->color.';"><div class="UserBuchstabe" style="color: '.$hex.';">'.$object->lastname[0].'</div></div>';
//								}
//							print '</div>';
//
//							print '<div class="UserDetail">'; // Left Row Userbox
//								print '<div class="UserDetailName">';
//								// Online Stat
//							print '<div title="'.$langs->trans("OnlineStatusUnknow").'" class="online_stats_'.$object->id.'" style="width: 10px; height: 10px; background-color: grey; border-radius: 90px; position: relative; bottom: -14px; left: 55px; opacity: 0.7;"></div>';
//									print $cuser->firstname;
//									print ' ';
//									print $cuser->lastname;
//									if(empty($MessageCNT)) print '<div name="UserDetailLastMessageCNT_'.$object->id.'" class="UserDetailCNTclass">'.$MessageCNT.'</div>';
//									else print '<div name="UserDetailLastMessageCNT_'.$object->id.'" class="UserDetailCNTclass" style="display: block !important;">'.$MessageCNT.'</div>';
//								print '</div>';
//								print '<div class="UserDetailLastMessage">';
//									print '<div class="UserDetailLastMessageText" name="UserDetailLastMessageText_'.$object->id.'">';
//										if($lmassage->chattextblob) if(!empty($lmassage->timestamp)) print base64_decode($lmassage->chattextblob);
//										else if(!empty($lmassage->timestamp)) print $lmassage->chattext;
//									print '</div>';
//									print '<div class="UserDetailLastMessageTime" name="UserDetailLastMessageTime_'.$object->id.'">';
//										if(!empty($lmassage->timestamp)){
//											if(strtotime($lmassage->timestamp) > strtotime("now - 24 hours")){
//												print date('H:i', strtotime($lmassage->timestamp)).' ';
//											}else{
//												print date('d.m.y', strtotime($lmassage->timestamp)).' ';
//											}
//										}
//									print '</div>';
//								print '</div>';
//							print '</div>';
//
//
//
//						print '</div>';
//						// Userbox End
//					}
//				}
//
//				print '</div>';
//			print '</div>';
//			// UserChatDiv End
//
//		print '</div>';
//	print '</div>';
//	// # End left panel ########################################################################################

// # Rechtes Fenster  ######################################################################################
print '<div id="ecm-layout-center" class="'.$classviewhide.'" style="border:1px solid #ccc; width:100%;border-radius: 6px;display: flex;height: 85%;">';

print '<div id="pre_LeftBox" style=" width: 20%; height: 100%; max-height:100%;float: left; bottom: 0; background-color: rgba(182,232,224,0.28); position: inherit; border-right: 1px solid #ccc;">';

print '<div class="LeftBox">';

// Chat Tool Box
print '<div style="height: 30px; border-bottom: 1px solid #BBBBBB;">';
print '<div style="clear:both;">';
print '<div style="float: left; width: 50%;height: 30px;">';
print '<input class="UserViewTool" type="button" name="chats" value="'.$langs->trans("Chats").'" style="border-right: 1px solid #BBBBBB;" onclick="switch_user_chat()">';
print '</div>';
print '<div style="float: right; width: 50%;">';
print '<input class="UserViewTool" type="button" name="contacts" value="'.$langs->trans("Contacts").'" onclick="switch_user_contatct()">';
print '</div>';
print '</div>';
print '</div>';
print '<div style="height:31px;">';
print '<input type="text" value="" id="user_search" class="UserSearch" placeholder="'.$langs->trans("UserSearchDotDotDot").'">';
//print '<div class="UserSearchButton">';
print '<img id="mute" src="img/mute.png" class="muteclass" title="'.$langs->trans("Mute").'">';
//print '</div>';
print '</div>';

// UserChatDiv
print '<div class="UserChatDivOut">';
print '<div class="UserChatDivIn">';

if(is_array($chat_user_arr))
    foreach($chat_user_arr as $key => $cuser)
    {
        if($cuser->rowid != $user->id)
        {
            print '<input type="hidden" value="'.$cuser->firstname.' '.$cuser->lastname.'" id="user_name_of_'.$cuser->rowid.'">';
            // User Box
            $object->fetch($cuser->rowid);

            $MessageCNT = "";
            $lmassage = $dolichat->get_last_message($cuser->rowid, $user->id);
            if(!empty($lmassage->timestamp))
            {
                $user_name_div = "user_box_chat";
                $user_box_visible = "";
                if($lmassage->user_id != $user->id && $lmassage->gesehen == 0) $MessageCNT = "new";
            }
            else
            {
                $user_name_div = "user_box_kontakt";
                $user_box_visible = "display:none;";
            }

            if(strtotime($lmassage->timestamp) < strtotime("now - ".$conf->global->dolichat_DEL_TIME." days"))
            {
                $user_name_div = "user_box_kontakt";
                $user_box_visible = "display:none;";
            }

            print '<div id="user_detail_box_'.$object->id.'" class="UserKontakttBox" name="'.$user_name_div.'" style="'.$user_box_visible.'" onclick="change_chat_user('.$object->id.')" lastname="'.strtolower($cuser->lastname).'" firstname="'.strtolower($cuser->firstname).'" >';

            print '<div class="UserImg">';
            if($object->photo != "")
            {
                print $form->showphoto('userphoto', $object, '','','','user_img');
            }
            else
            {
                if($object->color == "")
                {
                    $object->color = "CCCCCC";
                    $hex = 'white';
                }
                else
                {
                    $hex = $dolichat->getContrast50($object->color);
                }

                print '<div class="user_img" style="background-color:#'.$object->color.';"><div class="UserBuchstabe" style="color: '.$hex.';">'.$object->lastname[0].'</div></div>';
            }
            print '</div>';

            print '<div class="UserDetail">'; // Left Row Userbox
            print '<div class="UserDetailName">';
            // Online Stat
            print '<div title="'.$langs->trans("OnlineStatusUnknow").'" class="online_stats_'.$object->id.'" style="width: 10px; height: 10px; background-color: grey; border-radius: 90px; position: relative; bottom: -14px; left: 55px; opacity: 0.7;"></div>';
            print $cuser->firstname;
            print ' ';
            print $cuser->lastname;
            if(empty($MessageCNT)) print '<div name="UserDetailLastMessageCNT_'.$object->id.'" class="UserDetailCNTclass">'.$MessageCNT.'</div>';
            else print '<div name="UserDetailLastMessageCNT_'.$object->id.'" class="UserDetailCNTclass" style="display: block !important;">'.$MessageCNT.'</div>';
            print '</div>';
            print '<div class="UserDetailLastMessage">';
            print '<div class="UserDetailLastMessageText" name="UserDetailLastMessageText_'.$object->id.'">';
            if($lmassage->chattextblob) if(!empty($lmassage->timestamp)) print base64_decode($lmassage->chattextblob);
            else if(!empty($lmassage->timestamp)) print $lmassage->chattext;
            print '</div>';
            print '<div class="UserDetailLastMessageTime" name="UserDetailLastMessageTime_'.$object->id.'">';
            if(!empty($lmassage->timestamp)){
                if(strtotime($lmassage->timestamp) > strtotime("now - 24 hours")){
                    print date('H:i', strtotime($lmassage->timestamp)).' ';
                }else{
                    print date('d.m.y', strtotime($lmassage->timestamp)).' ';
                }
            }
            print '</div>';
            print '</div>';
            print '</div>';



            print '</div>';
            // Userbox End
        }
    }

print '</div>';

print '</div>';

print '<a href="javascript:void(0)" class="btn_boots_hide" id="toggle_chat_up" style="position:absolute;margin-left: 233px;margin-top: -32px;"></a>';

// UserChatDiv End

print '</div>';
print '<a href="javascript:void(0)" class="b_hide btn_boots_show" id="toggle_chat_down" style="position: absolute;margin-left: 6px;top: 71px"></a>';

print '</div>';


// Rechts Unten --------------------------------------------------------------------------------------
print '<div class="pane-in ecm-in-layout-south layout-padding valignmiddle" style="width: 100%">';
// Rechts Oben ---------------------------------------------------------------------------------------
print '<div class="pane-in ecm-in-layout-center">';
print '<div id="ecmfileview" class="ecmfileview" style="height:100%;">'; //overflow: hidden;

// Chat Frame
print '<div id="chat_detail">';
print '';
print '</div>';

print '<div style="height:91%;">';
print '<iframe id="mainframe" frameborder="0" src="" style="width: 100%; height: 100%;"></iframe>'; // dol_buildpath('/dolichat',1)./indexFrameChatMain.php?cuser='.$standard_user.'
print '</div>';

print '</div>';
print '</div>';
// End Rechts Oben  ----------------------------------------------------------------------------------
// TOOL
print '<div style="border-bottom: 1px solid #BBBBBB;padding: 5px 5px 5px 5px;">';

// IMG UPL
print '<input type="hidden" id="SavedPicID'.$standard_user.'">';
print '<iframe src="'.dol_buildpath('/dolichat',1).'/lib/indexN.php?cuser='.$standard_user.'" style="width: 30px;height: 30px;margin-bottom: -4px;" frameborder="0" scrolling="no" id="uploadF"></iframe><div id="progressbar"></div>'; // title="'.$langs->trans('UploadImg').'"

// IMG URL
//print '<img title="'.$langs->trans('UploadImgWithURL').'" src="'.DOL_URL_ROOT.dol_buildpath('/dolichat',1).'/images/upload_url.png" style="width:26px;" onclick="$( \'#dialog\' ).dialog( \'open\');">';

print '<div class="entertosend">';
print '<span style="position: relative; bottom: 3px;">'.$langs->trans("PressEnterToSend").' </span>';
print '<input type="checkbox" value="0" id="EnterSenden" >';
print '</div>';

print '</div>';

// Message
print '<div style="padding: 5px 5px 5px 5px; clear: both;">';
print '<table style="width: 100%;">';
print '<tr>';
print '<td style="width: 90%;">';
print '<textarea id="message" style="float: left;resize:vertical; max-height:67px; min-height:25px; " class="textmessage"></textarea>';
print '</td>';
print '<td style="width: 50%;">';
print '<input type="hidden" value="'.$user->id.'" id="userid_input">';
print '<input type="hidden" value="'.$user->lastname.' '.$user->firstname.'" id="nameforsend">';
print '<input style="float: right;" class="button btn-danger" type="submit" id="sendmail" name="sendmail" value="'.$langs->trans("Send").'" onclick="SendText(\''.$user->lastname.' '.$user->firstname.'\')">';
print '</td>';
print '</tr>';
print '</table>';
print '</div>';

print '</div>';
// End Rechts Unten ----------------------------------------------------------------------------------

print '</div>';
// End Rechtes Fenster #####################################################################################

print '</div> <!-- end div id="containerlayout" -->';

print ' <div id="dialog" title="'.$langs->trans("ImagefromaURL").'" style="display: none;">
			'.$langs->trans("ShowaImagefromaotherServerdot").'
			<p>
			<form id="pic_url_form">
				<span id="url_info"></span><br>
				<input id="pic_url" type="url" value="" name="pic_url" placeholder="'.$langs->trans("UrlfromanImage").'">
				<input type="submit" value="'.$langs->trans("OK").'">
			</form>
		</div>';
// End of page


?>
    <script>
        $(document).ready(function () {
            $("#toggle_chat_up").on('click', function () {

                $(".LeftBox").fadeOut(800);
                setTimeout(function() {
                    $("#toggle_chat_up").fadeOut(500);
                    var elem = document.getElementById("pre_LeftBox");
                    var width = 336;
                    var id = setInterval(frame, 20);
                    function frame() {
                        if (width === 46) {
                            clearInterval(id);
                        } else {
                            width-=10;
                            elem.style.width = width + 'px';
                        }
                    }
                }, 100);
                setTimeout(function() {
                    $("#toggle_chat_down").fadeIn(1000)
                }, 500);
            });
            $("#toggle_chat_down").on('click', function () {
                $(".LeftBox").fadeIn(800);
                setTimeout(function() {
                    $("#toggle_chat_down").fadeOut(500);
                    var elem = document.getElementById("pre_LeftBox");
                    var width = 46;
                    var id = setInterval(frame, 20);
                    function frame() {
                        if (width === 336) {
                            clearInterval(id);
                        } else {
                            width+=10;
                            elem.style.width = width + 'px';
                        }
                    }
                }, 100);
                setTimeout(function() {
                    $("#toggle_chat_up").fadeIn(1000)
                }, 500);
            });

        });




        $( document ).ready(function(){
            $(function() {
                $( "#dialog" ).dialog({
                    autoOpen: false,
                    resizable: false,
                    modal: true,
                    show: {
                        effect: "blind",
                        duration: 100
                    },
                    hide: {
                        effect: "blind",
                        duration: 100
                    },
                    open: function(event, ui) { $('.ui-widget-overlay').bind('click', function () { $(this).siblings('.ui-dialog').find('.ui-dialog-content').dialog('close'); }); }
                });
            });
        });

        var url_ok = false;
        $( document ).ready(function(){
            $('#pic_url').on('change', function(){
                $.ajax({
                    type: 'HEAD',
                    url: $('#pic_url').val(),
                    success: function(){
                        //callback(true);
                        $('#url_info').html('URL: OK!');
                        url_ok = true;
                    },
                    error: function() {
                        //callback(false);
                        $('#url_info').html('URL: WRONG!');
                        url_ok = false;
                    }
                });
            });
            $('#pic_url_form').on('submit', function(event){
                event.preventDefault();
                if(url_ok == true)
                {
                    $('#message').val('%picto=' + $('#pic_url').val());
                    SendText(<?php print "'".$user->lastname." ".$user->firstname."'"; ?>);
                    $( '#dialog' ).dialog( 'close' );
                }
            });
        });
    </script>
    <script type="text/javascript" src="js/main.js"></script>
    <style>
        #mainframe{
            /*background-image: url("img/dolichat.png"); /* 381549.jpg dolichat.png*/
            background-color: rgb(241, 241, 241);
            height: 400px !important;
        }
        .btn_boots_show{
            background-image: url("../dolichat/images/img_hidden_menu.png");
            background-repeat: no-repeat;
            background-size: contain;
            height: 30px;
            width: 30px;
        }
        .btn_boots_hide{
            background-image: url("../dolichat/images/img_hide.png");
            background-repeat: no-repeat;
            background-size: contain;
            height: 30px;
            width: 25px;
        }
        .UserViewTool{
            width: 100%;
            height: 30px;
            border-radius: 0px;
            border: 0px solid #BBBBBB;
            border-bottom: 1px solid #BBBBBB;
            cursor: pointer;
        }
        .UserViewTool:hover{
            background-color: #D6D6D6;
            border-top: 1px solid #BBBBBB;
        }
        .UserViewTool[name="contacts"]{
            background-color: #F1F1F1;
        }
        .LeftBox{
            overflow: hidden;
            height: 100%;
        }
        .b_hide{
            display: none;
        }
        .UserChatDivOut {
            width: 100%;
            height: 537px !important;
            overflow: auto;
        }
        .UserKontakttBox{
            height: 55px;
            border-bottom: 1px solid #BBBBBB;
            padding: 5px 5px 5px 5px;
            cursor: pointer;
        }
        .UserKontakttBox:hover{
            background-color: #FFF8E6;
        }
        .UserImg{
            float: left;
            width: 50px;
            height: 50px;
            margin-right: 20px;
        }
        .UserDetail{
            margin-top: 4px;
        }
        .UserDetailName{
            line-height: 8px;
        }
        .UserDetailLastMessage{
            margin-top: 5px;
            color: #8E8E8E;
        }
        .UserDetailLastMessageText{
            float: left;
            width: 60%;
            height: 15px;
            overflow: hidden;
        }
        .UserDetailLastMessageTime{
            float: right;
        }
        .user_img{
            width: 50px;
            border-radius: 30px;
            line-height: 45px;
        }
        .UserBuchstabe
        {
            height: 50px;
            font-size: 45px;
            padding-top: 0px;
            text-align: center;
        }
        .date {
            border-bottom: 3px dotted #b3b3b3 !important;

        }
        #chat_detail{
            height: 51px;
            padding: 5px 5px 5px 5px;
            border-bottom: 1px solid #BBBBBB;
        }
        .textmessage{
            width: 94%;
            height: 56px;
            height: 36px;
            border-radius: 5px 5px 5px;
        }
        .UserDetailCNTclass
        {
            padding: 2px;
            background-color: #1F9E2C;
            color: white;
            border-radius: 6px;
            float: right;
            display: none;
        }
        .btn-danger
        {
            background: antiquewhite;
            height: 33px;
            width: 100%;
        }
        .UserSearch{
            height: 23px;
            padding: 4px;
            width: 100%;
            margin-top: -2px;
            margin-left: -2px;
            border-radius: 0px;
        }
        .muteclass{
            position: relative;
            top: -33px;
            left: 90%;
            width: 30px;
        }
        .entertosend{
            float: right;
            position: relative;
            bottom: -5px;
        }
    </style>

    <!-- DEBUG -->
    <script>
        $(document).ready(function(){
            $('#chat_line_list').hide();
        });
    </script>
<?php
llxFooter();

$db->close();
?>