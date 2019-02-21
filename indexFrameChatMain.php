<?php  
//Config auslesen 
$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");         // to work if your module directory is into dolibarr root htdocs directory
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");       // to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");     // to work if your module directory is into a subdir of root htdocs directory
if (! $res && file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");       // to work if your module directory is into a subdir of root htdocs directory
if (! $res) die("Include of main fails");
require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
dol_include_once('/dolichat/class/dolichat.class.php');
require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
//require_once DOL_DOCUMENT_ROOT.'/dolichat/css/dolichat.css.php';
//require_once DOL_DOCUMENT_ROOT.'/dolichat/css/header.php';
$staticuser=new User($db);
$staticuser->fetch($user->id);
$staticuser->getrights();


if (!$staticuser->rights->dolichat->UseChat) accessforbidden();
if(!$conf->global->MAIN_MODULE_DOLICHAT){ accessforbidden();}

$langs->load("dolichat@dolichat");
/*
Admin
    /clear = jeden eintrag in der Datenbank über den chat löschen.

    $conf->global->dolichat_USE_DEL_TIME -----------Die nach zeit bestimmte löschnung aktivirt?
    $conf->global->dolichat_DEL_TIME ---------------nach wie viel Tagen löschen?
*/
$form=new Form($db);

$dolichat=new dolichat($db);

$staticuser=new User($db);
$staticuser->fetch($user->id);

//print_r($staticuser2);



$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_STANDART_AUSWAHL';";
$resqlP=$db->query($sqlP);

$objP = $db->fetch_object($resqlP);
$Sauswahl = $objP->value;
$cuser = $Sauswahl;

$sqlRel="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_RELOAD_INTERVAL';";
$resqlRel= $db->query($sqlRel);
$objRel = $db->fetch_object($resqlRel);
$reloadnum = $objRel->value;

$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_IDLEFT_AUSBLEND';";
$resqlP= $db->query($sqlP);
$objP = $db->fetch_object($resqlP);
$ausblendnum = $objP->value;


if($reloadnum == ""){
    $reloadnum = 5;
}
if($objRel->entity == 0 || $objRel->entity == ""){
    $reloadnum = 5;
}

$cuser = $_GET['cuser'];
$cusernB = $_GET['cusernB'];
$PicSavedID = $_GET['PicSavedID'];

if(($cusernB > 0 || $cusernB != "") && empty($cuser)){
    $sqlBox="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_BOX_CUSER';";
    $resqlBox= $db->query($sqlBox);
    $objBox = $db->fetch_object($resqlBox);
    $BoxCuserConf = $objBox->value;
    if($objBox->entity > 0){
        $BoxCuser = explode(", ", $BoxCuserConf); // zb: 26, G5, G6 = $BoxCuser[0] = 26, $BoxCuser[1] = G5, ...
    }
    $cuser = $BoxCuser[$cusernB];
}

//llxHeader_dolichat('',$langs->trans("Agenda"),$help_url);
//llxHeader('',$langs->trans("Chat"),$help_url);
$form=new Form($db);

?>
<head>
<meta name="robots" content="noindex,nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="author" content="Dolibarr Development Team">
<link rel="shortcut icon" type="image/x-icon" href="/theme/eldy/img/favicon.ico">
<link rel="top" title="Start" href="/">
<link rel="copyright" title="GNU General Public License" href="http://www.gnu.org/copyleft/gpl.html#SEC1">
<link rel="author" title="Dolibarr Development Team" href="http://www.dolibarr.org">
<title>Finanzkonten</title>
<!-- Includes CSS for JQuery (Ajax library) -->
<link rel="stylesheet" type="text/css" href="<?php print DOL_URL_ROOT ?>/includes/jquery/css/smoothness/jquery-ui.css?version=3.9.0">
<link rel="stylesheet" type="text/css" href="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/tiptip/tipTip.css?version=3.9.0">
<link rel="stylesheet" type="text/css" href="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/jnotify/jquery.jnotify-alt.min.css?version=3.9.0">
<link rel="stylesheet" type="text/css" href="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/select2/select2.css?version=3.9.0">
<!-- Includes CSS for Dolibarr theme -->
<link rel="stylesheet" type="text/css" href="<?php print DOL_URL_ROOT ?>/theme/eldy/style.css.php?lang=de_DE&amp;theme=eldy&amp;userid=3&amp;entity=1&amp;version=3.9.0">
<!-- Includes CSS added by module dolichat -->
<link rel="stylesheet" type="text/css" href="<?php print dol_buildpath('/dolichat',1); ?>/css/dolichat.css.php?lang=de_DE&amp;theme=eldy&amp;userid=3&amp;entity=1&amp;version=3.9.0">
<link href="<?php print dol_buildpath('/dolichat',1); ?>/css/dolichat.css" rel="stylesheet" />
<!-- Includes JS for JQuery -->
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/js/jquery.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/js/jquery-ui.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/tablednd/jquery.tablednd.0.6.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/tiptip/jquery.tipTip.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/jnotify/jquery.jnotify.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/core/js/jnotify.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/flot/jquery.flot.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/flot/jquery.flot.pie.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/flot/jquery.flot.stack.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/includes/jquery/plugins/select2/select2.min.js?version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/core/js/select2_locale.js.php?version=3.9.0"></script>
<!-- Includes JS of Dolibarr -->
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/core/js/lib_head.js.php?version=3.9.0&amp;version=3.9.0"></script>
<script type="text/javascript" src="<?php print DOL_URL_ROOT ?>/core/js/datepicker.js.php?lang=de_DE&amp;version=3.9.0"></script>
</head>
<body style="background:none;">
<?php   
print $youbrousermakestroopel;
print '<div id="show"></div>';
print '<div id="Gesended"></div>';
print '<div id="Reload4" style="width: 10px; height: 10px; background-color: red; position: fixed; border-radius: 30px;  box-shadow: 0px 0px 9px red;top: 6px; left: 6px;">';
    print '&nbsp;&nbsp;&nbsp;&nbsp;Live';
print '</div>';
print '<div id="fullsizechatdiv"></div>';
print '<div id="gesehenscript" style="display:none;"></div>';
print '<div id="gesehenscriptcuser" style="display:none;"></div>';
			
			print '<div class="loadAll msg-lineR" id="newload" name="newload" style="display:none;">';
			//print $sql; // DEBUG
			print $langs->trans('Load').' '.$langs->trans('All');
			print '</div>';
			print '<div style="height: 80px;display:none;" name="newload"></div>';
			
print'<div id="ajax_old_chat" class="webback" style="display:none;"></div>';
print'<div id="ajax_chat" name="ajax_chat"  class="webback">'; 
print'  <table border="0px">';
print'      <tr>
                <td id="loding">'.$langs->trans("loadingwithdots").'</td>
            </tr>
        </table>';
print '</div>';
print ' 
 <form action="index.php" method="post" style="display:none;"> 
    <table border="0" id="tabelofdoom" width="100%"> 
        <tr> 
            <td width="6%"></td>
            <td width="25%">';
        print '</td>
            <td width="10%" style="color:#D5D5D5;">
                '.$langs->trans("Sendenan").' ';
        print $dolichat->select($cuser,'cuser', '1','Broadcast','shadow', 'onChange="saverofcuser()"');         
        print  '<p><a href="group/userconf.php" style="color:#D5D5D5;">'.$langs->trans("Einstellungen").'</a>';
        print ' <input type="hidden" value="-1" name="cuser_s" id="cuser_s">';
 
print'      </td>
            <td>';

        print  '<textarea name="eintrag" value="'.htmlspecialchars($_GET['eintrag']).'" id="textbox" width="350px" onChange="reloaduploadXMLDoc()">'.htmlspecialchars($_GET['eintrag']).'</textarea>
            </td>
            <td> 
                <input type="button" class="shadow" style="width:76px;height:76px;" name="eintragen" value="'.$langs->trans("Senden").'" id="button" onClick="saveXMLDoc()">
                <input type="hidden" id="nick" name="nick" value="'.$staticuser->lastname.' '.$staticuser->firstname.'">
                <input type="hidden" name="nick_n_tmp" value="'.$_POST['nick'].'">
                <input type="hidden" name="PicSavedID" id="PicSavedID" value="'.$PicSavedID.'">';

   print   '</td>
        </tr>';
print '</table>';
print '</form>';
print '<input type="hidden" id="noscro" value="0">';

?>
<script type="text/javascript"> 
    /*----------------------------------------------------Start--------------------------------------------------------*/  
            var viewportWidth;
            var viewportHeight;
            var mydate = new Date()
            var downtime;
            var isackick = false;            
            var active;
            $( document ).ready(function() {  
                $('#Gesended').fadeOut('fast');  
                saverofcuser()            
                document.body.style.overflow = "hidden";
                viewportWidth = $(window).width();
                viewportHeight = $(window).height();
                document.body.style.overflow = "";

                    $('#fullsizechatdiv')
                        .css('position', 'fixed')
                        .css('top', '10px')
                        .css('left', '10px')
                        .css('z-index', '99')
                        .css('background-color', '#D4FFCF')
                        .css('border-radius', '4px')
                        .css('padding', '10px')
                        .css('width', '95%')
                        .css('height', (viewportHeight - 50) + 'px')
                        .css('border', '1px solid black')
                        .css('box-shadow', '0px 0px 130px 20px black')
                        .css('display', 'none');

                document.getElementById("textbox").value="";
                document.getElementById("textbox").focus();

                loadXMLDoc();

                <?php if($staticuser->rights->dolichat->UseUpload){
                    print 'reloaduploadXMLDoc();';
                } ?>          
								
				$( ".loadAll" ).click(function() {
	  				loadNextDay();
	  				$( '[name="newload"]' ).hide( "slow" );
				});					
            }); 
    /*--------------------------------------------------- CSS - Morph ---------------------------------------------------*/

            var tmp_chatbox;
            function fullsizechattext(chatbox){
                if($('#fullsizechatdiv').html() == '' && is_deleting == false)
                {
                    // chatdiv2To from the other side
                    // chatdiv2You from my side
                    if($(chatbox).attr('class') == 'chatdiv2To') $('#fullsizechatdiv').css('background-color', '#FFFFF2');
                    if($(chatbox).attr('class') == 'chatdiv2You') $('#fullsizechatdiv').css('background-color', '#D4FFCF');
                    img_s_url = "images/close.png";

                    $(chatbox).clone().appendTo('#fullsizechatdiv');
                    $('#fullsizechatdiv').fadeIn('fast');

                    textH = $('#fullsizechatdiv #chattabel div span span').html();
                    $('#fullsizechatdiv #chattabel').remove();

                    $('#fullsizechatdiv').html('<div id="chatcontend"></div>');
                    
                    $('#fullsizechatdiv #chatcontend')
                        .css('height','100%')
                        .css('width', '95%')
                        .css('overflow','auto')
                        .css('font-size', '14px');
                    $('#fullsizechatdiv #chatcontend')
                        .html(textH + '<div onclick="fullsizechattext(this)" style="float: right; position: absolute; top: 18px; right: 20px;"><?php print img_picto("", "close" ); ?></div>');

                    tmp_chatbox = chatbox;
                    $(chatbox).fadeOut('fast');
                }
                else if(is_deleting == false)
                {
                    $(tmp_chatbox).fadeIn('fast');
                    $('#fullsizechatdiv').fadeOut('fast');
                    delete(tmp_chatbox);
                    //$('#chatanchore').after(chatbox);
                    //$('#chatanchore').remove();
                    $('#fullsizechatdiv').html('');
                }
            }

    /*----------------------------------------------------Loading--------------------------------------------------------*/  
            function saverofcuser(){
                var cuser = '';
                var name = '';
                cuser = document.getElementById("cuser").options[document.getElementById("cuser").selectedIndex].value;
                document.getElementById("cuser_s").value=cuser;

                name = document.getElementById("cuser").options[document.getElementById("cuser").selectedIndex].text;
               
                <?php
                    if($user->rights->dolichat->UseUpload){
                        print 'reloaduploadXMLDoc();';
                    }
                ?>
            }

                setInterval(function(){loadXMLDoc();}, <?php print $reloadnum; ?>000);           

            var loading = false;
            function loadXMLDoc() 
            {   
                var elemt = '';
                var text = '';
                var rowid = '';
                var cuser = '';
                var neu = '1';
                cuser = '<?php print $cuser; ?>';

                cusers = document.getElementById("gesehenscriptcuser").innerHTML;
                rowid = document.getElementById("gesehenscript").innerHTML;
                loading = true;
                
                //----------------------------------------------------------------------------------------
                    $.ajax({
                        method: "GET",
                        url: "core/ajax/ajax_proc_status.php",
                        data: { 
                            pruf: 1,
                            chat_stat: window.parent.$('#active_stat').val()
                        }
                    })
                    .done(function( msg1 ) {
                        schowReload4();
                        stat_arr = msg1.split('%<|>%'); // array value

                        $.each(stat_arr, function(index, value){
                            if(index > 0)
                            {
                                user_stats = value.split('%<>%'); // array value
                                if(user_stats[1] == 2)
                                {
                                    sttrans = window.parent.$('#OnlineStatus2').val();
                                    window.parent.$('.online_stats_' + user_stats[0]).attr('title', sttrans);
                                    window.parent.$('.online_stats_' + user_stats[0]).css('background-color', 'orange');
                                    //console.log('user: '+user_stats[0]+' is_online: '+user_stats[1])
                                }
                                else if(user_stats[1] == 1)
                                {
                                    sttrans = window.parent.$('#OnlineStatus1').val();
                                    window.parent.$('.online_stats_' + user_stats[0]).attr('title', sttrans);
                                    window.parent.$('.online_stats_' + user_stats[0]).css('background-color', 'green');
                                    //console.log('user: '+user_stats[0]+' is_online: '+user_stats[1])
                                }
                                else
                                {
                                    sttrans = window.parent.$('#OnlineStatus0').val();
                                    window.parent.$('.online_stats_' + user_stats[0]).attr('title', sttrans);
                                    window.parent.$('.online_stats_' + user_stats[0]).css('background-color', 'grey');
                                }
                                
                            }
                        });

                        msg1 = stat_arr[0];
                        if(rowid == msg1 && cuser == cusers){
                            //Nicht neu                               alert(neu);                                    alert(elemt);                                    alert(rowid);                                     
                            neu = '0';
                            loading = false;
                        }else{
                            // NEU                                    alert(neu);                                    alert(elemt);                                    alert(rowid);
                            neu = '1';
                            loading = false;
                            rowidg = 0;
                            if(onlyonetime == 1)
                            {
                                rowidg = rowid;
                            }
                            $.ajax({
                                method: "GET",
                                url: "core/ajax/ajax_loaderMain.php",
                                data: { cuser: cuser, getnew: rowidg}
                            })
                            .done(function( msg ) {
                                schowReload4();
                                if(neu == 1){text = msg}
                                if(text == ""){
    
                                }else{
                                    if(onlyonetime == 1){
                                        $('#ajax_chat').append(text);
                                    }else{
                                        document.getElementById('ajax_chat').innerHTML=text;                                                   
                                    }
                                    $("html, body").animate({ scrollTop: $(document).height() }, 500, function() {
                                        // Animation complete.
                                        if(onlyonetime == 0){
                                            $('[name="newload"]').show();
                                        }
                                    });
                                    
                                }
                            });
                        }
                        document.getElementById("gesehenscriptcuser").innerHTML = cuser;
                        document.getElementById("gesehenscript").innerHTML=msg1;
                    });
                    /*
                    if (window.XMLHttpRequest) 
                          {// code for IE7+, Firefox, Chrome, Opera, Safari 
                          xmlhttp2=new XMLHttpRequest(); 
                          } 
                        xmlhttp2.onreadystatechange=function() 
                          { 
                          if (xmlhttp2.readyState==4 && xmlhttp2.status==200) 
                            { 
                                elemt = xmlhttp2.responseText;
                                if(rowid == elemt && cuser == cusers){
                                    //Nicht neu                               alert(neu);                                    alert(elemt);                                    alert(rowid);                                     
                                    neu = '0';
                                    loading = false;
                                }else{
                                    // NEU                                    alert(neu);                                    alert(elemt);                                    alert(rowid);
                                    neu = '1';
                                    loading = false;
                                    if (window.XMLHttpRequest){
                                        // code for IE7+, Firefox, Chrome, Opera, Safari 
                                        xmlhttp=new XMLHttpRequest();     
                                    } 
                                    xmlhttp.onreadystatechange=function(){                                       
                                        if (xmlhttp.readyState==4 && xmlhttp.status==200){
                                            if(neu == 1){text = xmlhttp.responseText;}
                                            if(text == ""){
                
                                            }else{
                                            		if(onlyonetime == 1){
                                            			$('#ajax_chat').append(text);
                                            		}else{
                                                	document.getElementById('ajax_chat').innerHTML=text;                                                   
                                              	}
                                                $("html, body").animate({ scrollTop: $(document).height() }, 500, function() {
  																								  // Animation complete.
  																								  if(onlyonetime == 0){
  																								  	$('[name="newload"]').show();
  																									}
  																							});
                                                
                                            }
                                        }                     
                                    } 
                                    //clearInterval(interval);
                                    //while (xmlhttp.responseText == ""){} 
                                    //setInterval(function(){loadXMLDoc();},000);
                
									if(onlyonetime == 1){
										xmlhttp.open("GET","core/ajax/ajax_loaderMain.php?cuser="+cuser+'&getnew='+rowid,true); // +"&elemt"+elemt
									}else{	
                                    	xmlhttp.open("GET","core/ajax/ajax_loaderMain.php?cuser="+cuser,true); // +"&elemt"+elemt
                                  	}
                                    xmlhttp.send();           
                                    
                                }
                                document.getElementById("gesehenscriptcuser").innerHTML = cuser;
                                document.getElementById("gesehenscript").innerHTML=xmlhttp2.responseText;
                                schowReload2();
                            } 
                          } 
                        
                        xmlhttp2.open("GET","core/ajax/ajax_proc_status.php?pruf=1",true);
                        xmlhttp2.send();
                        */
                //----------------------------------------------------------------------------------------
                if(rowid == elemt){
                            
                }else{
                       
                }   

                if(document.getElementById("noscro").value == 0)
                {
                    $("html, body").animate({ scrollTop: $(document).height() }, 500);     
                    document.getElementById("noscro").value = 1;
                }
            } 
    /*----------------------------------------------------loadNextDay----------------------------------------------------*/
    				var onlyonetime = 0;
    				function loadNextDay() 
            {   
            		if(onlyonetime == 0){
                var elemt = '';
                var text = '';
                var rowid = '';
                var cuser = '';
                var neu = '1';
                var lastheight = $(document).height();
                
                cuser = '<?php print $cuser; ?>';

                cusers = document.getElementById("gesehenscriptcuser").innerHTML;
                rowid = document.getElementById("gesehenscript").innerHTML;
               
                if(cuser > 0) schowReload4();
                //----------------------------------------------------------------------------------------

                if (window.XMLHttpRequest){
                    // code for IE7+, Firefox, Chrome, Opera, Safari 
                    xmlhttp=new XMLHttpRequest();     
                } 
                xmlhttp.onreadystatechange=function(){                                       
                    if (xmlhttp.readyState==4 && xmlhttp.status==200){
                        if(neu == 1){text = xmlhttp.responseText;}
                        if(text == ""){
                
                        }else{
                            //document.getElementById('ajax_chat').innerHTML=text;    
                           
                            
                            $( "#ajax_old_chat" ).html( text );   
                            $( '#ajax_old_chat' ).fadeIn("slow");                         

                            //$("html, body").animate({ scrollTop: $('#today').height() }, 500);
                            if($( '[name="notlostT"]').text() != ""){
                            		$( '[name="notlost1t"]').hide();
                            }
                            $("html, body").delay(500).scrollTop($('#today').offset().top );
                           // $("html, body").animate({ scrollTop: lastheight }, 1);       
         
                        }
                    }                     
                } 
                
                xmlhttp.open("GET","core/ajax/ajax_loaderMain.php?cuser="+cuser+"&alldays=1",true); // +"&elemt"+elemt
                xmlhttp.send();
                onlyonetime = 1;
              	}  
            } 
            
    /*----------------------------------------------------Saveing--------------------------------------------------------*/  
            function nl2br(s,m)
            {
              var p  = document.createElement('pre');
              if(m)
                {
                  var t  = document.createTextNode(s);
                  p.appendChild(t);
                }
              else
                {
                  p.innerHTML=s
                }
              return String(p.innerHTML).replace(/\n/g,'<br>');
            }
 
            function saveXMLDoc() 
            {   
                var eintrag = '';
                var nick = '';
                var cuser = '';
                var PicSavedID = document.getElementById('PicSavedID').value;
                schowReload3();
                eintrag = document.getElementById('textbox').value;
                nick = document.getElementById('nick').value;
                cuser = '<?php print $cuser; ?>';
                eintrag = nl2br(eintrag, 0);
                //alert(cuser);
                if(eintrag == ''){
                    schowReload();
                }else{
                    if (window.XMLHttpRequest) 
                      {// code for IE7+, Firefox, Chrome, Opera, Safari 
                      xmlhttp=new XMLHttpRequest(); 
                      } 
                    xmlhttp.onreadystatechange=function() 
                      { 
                      if (xmlhttp.readyState==4 && xmlhttp.status==200) 
                        { 
                            document.getElementById("show").innerHTML=xmlhttp.responseText; 
                        } 
                      } 
                    
                    xmlhttp.open("POST","core/ajax/ajax_loaderMain.php",true);
                    xmlhttp.send("eintrag="+eintrag+"&nick="+nick+"&cuser="+cuser+"&PicSavedID="+PicSavedID);
    
                    document.getElementById("textbox").value="";
                    document.getElementById("textbox").focus();
                    $('#Gesended').fadeIn('slow').delay(2000).fadeOut('slow');                
                    loadXMLDoc();      
                }      
            }   
        /*----------------------------------------------------Uploading--------------------------------------------------------*/  
            function reloaduploadXMLDoc() 
            {   
                var eintrag = '';
                var nick = '';
                var cuser = '';
                schowReload3();
                eintrag = document.getElementById('textbox').value;
                eintrag = nl2br(eintrag, 0);
                nick = document.getElementById('nick').value;
                cuser = document.getElementById("cuser").options[document.getElementById("cuser").selectedIndex].value;               
                
                //document.getElementById('box').src = "lib/index.html.php?eintrag="+eintrag+"&nick="+nick+"&cuser="+cuser;                
            }   
    /*----------------------------------------------------Scrolling--------------------------------------------------------*/  
            function scrollToText(){
                $('html, body').animate({
                    scrollTop: $("#textbox").offset().top
                }, 1000);
                
                $('#runter').fadeOut('slow');
            }
            function scrollToBegin(){
                $('html, body').animate({
                    scrollTop: $("#mainmenutd_home").offset().top
                }, 1000);
                
                $('#hoch').fadeOut('slow');
            }
            function schowReload(){
                $('#Reload').fadeIn('slow').delay(5000).fadeOut('slow');     /* Missing Text  */
            }
            function schowReload2(){
                $('#Reload2').fadeIn('fast').delay(500).fadeOut('fast');    /* Reloading      */
            }
            function schowReload3(){
                $('#Reload3').fadeIn('slow').delay(5000).fadeOut('slow');    /* Sending       */
            }
            function schowReload4(){
                $('#Reload4').fadeIn('slow').delay(500).fadeOut('slow');    /* Filter         */
            }
            function schowReload5(){
                $('#Reload5').fadeIn('slow').delay(500).fadeOut('slow');    /* Filter         */
            }
            function schowReload_td(){
                document.getElementById("Reload_text").style.color="#000000";   /* Help           */
                document.getElementById("Reload_text2").style.color="#000000";
                document.getElementById("Reload_text4").style.color="#000000";
                document.getElementById("Reload_text3").style.color="#000000";
                document.getElementById("Reload_text").style.zIndex="100";
                document.getElementById("Reload_text2").style.zIndex="100";
                document.getElementById("Reload_text4").style.zIndex="100";
                document.getElementById("Reload_text3").style.zIndex="100";
                document.getElementById("Reload_text5").style.zIndex="100";
                document.getElementById("Reload").style.zIndex="100";
                document.getElementById("Reload2").style.zIndex="100";
                document.getElementById("Reload4").style.zIndex="100";
                document.getElementById("Reload3").style.zIndex="100";
                document.getElementById("Reload5").style.zIndex="100";
                $('#Reload_td').fadeIn('slow').delay(10000).fadeOut('slow');
                
                setTimeout(function(){ 
                    document.getElementById("Reload_text").style.zIndex="";
                    document.getElementById("Reload_text2").style.zIndex="";
                    document.getElementById("Reload_text4").style.zIndex="";
                    document.getElementById("Reload_text5").style.zIndex="";
                    document.getElementById("Reload_text3").style.zIndex="";
                    document.getElementById("Reload").style.zIndex="";
                    document.getElementById("Reload2").style.zIndex="";
                    document.getElementById("Reload4").style.zIndex="";
                    document.getElementById("Reload5").style.zIndex="";
                    document.getElementById("Reload3").style.zIndex="";
                    document.getElementById("Reload_text").style.color="#D5D5D5";
                    $('#Reload_text').fadeIn('slow')
                    document.getElementById("Reload_text2").style.color="#D5D5D5";
                    $('#Reload_text2').fadeIn('slow')
                    document.getElementById("Reload_text4").style.color="#D5D5D5";
                    $('#Reload_text4').fadeIn('slow')
                    document.getElementById("Reload_text3").style.color="#D5D5D5";  
                    $('#Reload_text3').fadeIn('slow')
                    document.getElementById("Reload_text5").style.color="#D5D5D5";
                    $('#Reload_text5').fadeIn('slow')
                }, 11000);
            }

            $( window ).scroll(function() {
              $('#hoch').fadeIn('slow');
              $('#runter').fadeIn('slow');
            });
            function Nachgsehen(idrowid, self){

                if(self == 1 && del == 0){

                }else{

                    if (window.XMLHttpRequest) 
                          {// code for IE7+, Firefox, Chrome, Opera, Safari 
                          xmlhttp=new XMLHttpRequest(); 
                          } 
                        xmlhttp.onreadystatechange=function() 
                          { 
                          if (xmlhttp.readyState==4 && xmlhttp.status==200) 
                            { 
                                document.getElementById("gesehenscript").innerHTML=xmlhttp.responseText; 
                            } 
                          } 
                        
                        xmlhttp.open("GET","core/ajax/ajax_proc_status.php?id="+idrowid+"&del=0",true);
                        xmlhttp.send();
                }
            }

            var is_deleting = false;
            function loschenimg(idrowid){
                is_deleting = true;
                if (window.XMLHttpRequest) 
                      {// code for IE7+, Firefox, Chrome, Opera, Safari 
                      xmlhttp=new XMLHttpRequest(); 
                      } 
                    xmlhttp.onreadystatechange=function() 
                      { 
                      if (xmlhttp.readyState==4 && xmlhttp.status==200) 
                        { 
                            document.getElementById("gesehenscript").innerHTML=xmlhttp.responseText; 
                            is_deleting = false;
                        } 
                      } 
                    
                    xmlhttp.open("GET","core/ajax/ajax_proc_status.php?id="+idrowid+"&del=1",true);
                    xmlhttp.send();
                
            }
            function saverofadmin(){
                var cuser = '';
                cuser = document.getElementById("admin").options[document.getElementById("admin").selectedIndex].value;
                document.getElementById("admin_s").value=cuser;

                if(cuser == 1){
                    $('#Reload5').fadeIn('slow');
                }else{
                    $('#Reload5').fadeOut('slow');
                }
            }

            <?php if($_GET['eintrag'] != ""){ ?>
                saveXMLDoc();
            <?php } ?>
            

</script>
</body>
</html>