<?php  
// ini_set('display_errors', '1');

global $user;
if (! defined('REQUIRE_JQUERY_LAYOUT'))  define('REQUIRE_JQUERY_LAYOUT','1');
if (! defined('REQUIRE_JQUERY_BLOCKUI')) define('REQUIRE_JQUERY_BLOCKUI', 1);

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

    $selectedUserId = GETPOSTINT('user');
    if ($selectedUserId > 0) {
        setcookie('chat_user'.$user->id, (string) $selectedUserId);
    }

    $chat_user_arr = array();
    $no_chat_user_arr = array();

    $chat_user = $dolichat->get_Dolichat_user();
    if (is_array($chat_user)) {
        foreach ($chat_user as $key => $cuser) {
            if ((int) $cuser->rowid !== (int) $user->id) {
                $lmassage = $dolichat->get_last_message($cuser->rowid, $user->id);
                if (is_object($lmassage) && !empty($lmassage->timestamp)) {
                    $chat_user_arr[strtotime($lmassage->timestamp)] = $cuser;
                } else {
                    $no_chat_user_arr[] = $cuser;
                }
            }
        }
    }

    if (is_array($chat_user_arr) && !empty($chat_user_arr)) {
        krsort($chat_user_arr);
    }

    if (is_array($no_chat_user_arr) && !empty($no_chat_user_arr)) {
        foreach ($no_chat_user_arr as $key => $cval) {
            $chat_user_arr['nochat_'.$key] = $cval;
        }
    }
	//print_r($chat_user_arr);
$standard_user = 0;
if (!empty($chat_user_arr) && is_array($chat_user_arr)) {
    foreach ($chat_user_arr as $tmpuser) {
        if (is_object($tmpuser) && !empty($tmpuser->rowid) && (int) $tmpuser->rowid !== (int) $user->id) {
            $standard_user = (int) $tmpuser->rowid;
            break;
        }
    }
}
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

	$moreheadcss2 = '<link href="'.dol_buildpath('/dolichat/css/dolichat.css', 1).'" rel="stylesheet">';
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
	        ,   west__size:         450
	        ,   west__minSize:      420
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

	llxHeader($moreheadcss.$moreheadjs.$moreheadcss2,$langs->trans("Dolichat"),'','','','',$morejs,'',0,0);


	$classviewhide='';


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
print '<audio id="note_sound" class="dolichat-hidden-audio">';
	print '<source src="sound/newmessage.wav" type="audio/wav">';
print '</audio>';

print '<input type="hidden" id="OnlineStatus0" value="'.$langs->trans("OnlineStatusGrey").'">';
print '<input type="hidden" id="OnlineStatus1" value="'.$langs->trans("OnlineStatusGreen").'">';
print '<input type="hidden" id="OnlineStatus2" value="'.$langs->trans("OnlineStatusOrange").'">';
print '<input type="hidden" id="dolichat_path" value="'.dol_buildpath('/dolichat', 1).'">';

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
	print '<div id="ecm-layout-west" class="'.$classviewhide.'">';
		print '<div class="LeftBox">';

			// Chat Tool Box
			print '<div class="dolichat-left-tabs">';
				print '<div class="dolichat-left-tabs-row">';
					print '<div class="dolichat-left-tab-col dolichat-left-tab-col-first">';
						print '<input class="UserViewTool UserViewToolActive" type="button" name="chats" value="'.$langs->trans("Chats").'" onclick="switch_user_chat()">';
					print '</div>';
					print '<div class="dolichat-left-tab-col">';
						print '<input class="UserViewTool" type="button" name="contacts" value="'.$langs->trans("Contacts").'" onclick="switch_user_contatct()">';
					print '</div>';
				print '</div>';
			print '</div>';

			print '<div class="dolichat-left-toolbar">';
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
							$user_box_visible = "";
							//$user_box_visible = "display:none;";
						}

						if(strtotime($lmassage->timestamp) < strtotime("now - ".$conf->global->dolichat_DEL_TIME." days"))
						{
							$user_name_div = "user_box_kontakt";
							$user_box_visible = "";
						//	$user_box_visible = "display:none;";
						}

						print '<div id="user_detail_box_'.$object->id.'" class="UserKontakttBox'.(($user_name_div == 'user_box_chat') ? ' user-box-chat' : ' user-box-contact').'" name="'.$user_name_div.'" style="'.$user_box_visible.'" onclick="change_chat_user('.$object->id.')" lastname="'.strtolower($cuser->lastname).'" firstname="'.strtolower($cuser->firstname).'" >';

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
							print '<div title="'.$langs->trans("OnlineStatusUnknow").'" class="dolichat-online-dot online_stats_'.$object->id.'"></div>';
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
			// UserChatDiv End

		print '</div>';
	print '</div>';
	// # End left panel ########################################################################################

	// # Rechtes Fenster  ######################################################################################
	print '<div id="ecm-layout-center" class="'.$classviewhide.'">';

		// Rechts Oben ---------------------------------------------------------------------------------------
		print '<div class="pane-in ecm-in-layout-center">';
			print '<div id="ecmfileview" class="ecmfileview dolichat-right-panel">'; //overflow: hidden;

				// Chat Frame
				print '<div id="chat_detail">';
					print '';
				print '</div>';

				print '<div class="dolichat-mainframe-wrap">';
					$frameSrc = '';
					if (!empty($standard_user)) {
					    $frameSrc = dol_buildpath('/dolichat', 1).'/indexFrameChatMain.php?cuser='.(int) $standard_user;
					}

					print '<iframe id="mainframe" class="dolichat-mainframe" frameborder="0" src="'.$frameSrc.'"></iframe>';

	//				print '<iframe id="mainframe" frameborder="0" src="" style="width: 100%; height: 100%;"></iframe>'; // dol_buildpath('/dolichat',1)./indexFrameChatMain.php?cuser='.$standard_user.'
				print '</div>';

			print '</div>';
		print '</div>';
		// End Rechts Oben  ----------------------------------------------------------------------------------

		// Rechts Unten --------------------------------------------------------------------------------------
		print '<div class="pane-in ecm-in-layout-south layout-padding valignmiddle">';
			
			// TOOL
			print '<div class="dolichat-compose-toolbar">';
				
				// IMG UPL
				print '<input type="hidden" id="SavedPicID'.$standard_user.'">';
				print '<iframe src="'.dol_buildpath('/dolichat',1).'/lib/indexN.php?cuser='.$standard_user.'" class="dolichat-upload-frame" frameborder="0" scrolling="no" id="uploadF"></iframe><div id="progressbar"></div>'; // title="'.$langs->trans('UploadImg').'"
				
				// IMG URL
				//print '<img title="'.$langs->trans('UploadImgWithURL').'" src="'.DOL_URL_ROOT.dol_buildpath('/dolichat',1).'/images/upload_url.png" style="width:26px;" onclick="$( \'#dialog\' ).dialog( \'open\');">';

				print '<div class="entertosend">';
					print '<span class="dolichat-enter-send-label">'.$langs->trans("PressEnterToSend").' </span>';
					print '<input type="checkbox" value="0" id="EnterSenden" >';
				print '</div>';

			print '</div>';

			// Message
			print '<div id="dolichat_submit_status" class="dolichat-submit-status" style="display:none;"></div>';
			print '<div class="dolichat-compose-body">';
				print '<table class="dolichat-compose-table">';
					print '<tr>';	
						print '<td class="dolichat-compose-message-cell">';
							print '<textarea id="message" class="textmessage"></textarea>';
						print '</td>';
						print '<td class="dolichat-compose-send-cell">';
							print '<input type="hidden" value="'.$user->id.'" id="userid_input">';
							print '<input type="hidden" value="'.$user->lastname.' '.$user->firstname.'" id="nameforsend">';
							print '<input class="button dolichat-send-button" type="button" id="sendmail" name="sendmail" value="'.$langs->trans("Send").'" onclick="SendText(\''.$user->lastname.' '.$user->firstname.'\')">';
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
