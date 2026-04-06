<?php
/*
 * Refactored for Dolibarr style / PSR-12 while preserving Dolichat functionality.
 */

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

require_once DOL_DOCUMENT_ROOT . '/comm/action/class/actioncomm.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/html.form.class.php';
require_once DOL_DOCUMENT_ROOT . '/user/class/usergroup.class.php';
dol_include_once('/dolichat/class/dolichat.class.php');

/**
 * Read a user_param value safely.
 *
 * @param DoliDB $db
 * @param int    $userId
 * @param string $paramName
 *
 * @return array{value:string,entity:int}
 */
function dolichatGetUserParam($db, $userId, $paramName)
{
    $result = array('value' => '', 'entity' => 0);

    $sql = 'SELECT value, entity';
    $sql .= ' FROM ' . MAIN_DB_PREFIX . 'user_param';
    $sql .= ' WHERE fk_user = ' . ((int) $userId);
    $sql .= " AND param = '" . $db->escape($paramName) . "'";

    $resql = $db->query($sql);
    if ($resql) {
        $obj = $db->fetch_object($resql);
        if (is_object($obj)) {
            $result['value'] = isset($obj->value) ? (string) $obj->value : '';
            $result['entity'] = isset($obj->entity) ? (int) $obj->entity : 0;
        }
    }

    return $result;
}

$langs->load('dolichat@dolichat');

$form = new Form($db);
$dolichat = new dolichat($db);
$staticuser = new User($db);
$staticuser->fetch($user->id);
$staticuser->getrights();

if (empty($staticuser->rights->dolichat) || empty($staticuser->rights->dolichat->UseChat)) {
    accessforbidden();
}
if (empty($conf->global->MAIN_MODULE_DOLICHAT)) {
    accessforbidden();
}

$userParamDefaultSelection = dolichatGetUserParam($db, $user->id, 'CHAT_STANDART_AUSWAHL');
$userParamReload = dolichatGetUserParam($db, $user->id, 'CHAT_RELOAD_INTERVAL');
$userParamHideLeft = dolichatGetUserParam($db, $user->id, 'CHAT_IDLEFT_AUSBLEND');

$Sauswahl = $userParamDefaultSelection['value'];
$reloadnum = (int) $userParamReload['value'];
$ausblendnum = $userParamHideLeft['value'];

if (empty($reloadnum) || empty($userParamReload['entity'])) {
    $reloadnum = 3;
}

$cuser = GETPOSTINT('cuser');
$cusernB = GETPOSTINT('cusernB');
$PicSavedID = GETPOSTINT('PicSavedID');
$eintrag = GETPOST('eintrag', 'restricthtml');
$nickPost = GETPOST('nick', 'alphanohtml');
$youbrousermakestroopel = '';

if (empty($cuser)) {
    $cuser = (int) $Sauswahl;
}

if (($cusernB > 0) && empty($cuser)) {
    $userParamBox = dolichatGetUserParam($db, $user->id, 'CHAT_BOX_CUSER');
    $boxCuserConf = $userParamBox['value'];
    $boxCuser = array();

    if (!empty($userParamBox['entity']) && !empty($boxCuserConf)) {
        $boxCuser = explode(', ', $boxCuserConf);
    }

    if (isset($boxCuser[$cusernB])) {
        $cuser = (int) $boxCuser[$cusernB];
    }
}

$useUpload = !empty($staticuser->rights->dolichat) && !empty($staticuser->rights->dolichat->UseUpload);
$helpUrl = '';

$head = array();
$headjs = array(
    dol_buildpath('/dolichat/js/main.js', 1),
);

llxHeader('<link href="'.dol_buildpath('/dolichat/css/dolichat.css', 1).'" rel="stylesheet">', $langs->trans('Chat'), $helpUrl, '', 0, 0, $head, $headjs, '', 'mod-dolichat-frame');

print $youbrousermakestroopel;
print '<div id="show"></div>';
print '<div id="Gesended"></div>';
print '<div id="Reload4" class="dolichat-live-indicator">';
print '&nbsp;&nbsp;&nbsp;&nbsp;Live';
print '</div>';
print '<div id="fullsizechatdiv"></div>';
print '<div id="gesehenscript" class="dolichat-hidden"></div>';
print '<div id="gesehenscriptcuser" class="dolichat-hidden"></div>';
print '<div class="loadAll msg-lineR" id="newload" name="newload" style="display:none;">';
print $langs->trans('Load') . ' ' . $langs->trans('All');
print '</div>';
print '<div class="dolichat-hidden-loadspacer" name="newload"></div>';
print '<div id="ajax_old_chat" class="webback dolichat-hidden"></div>';
print '<div id="ajax_chat" name="ajax_chat" class="webback">';
print '<table border="0">';
print '<tr><td id="loding">' . $langs->trans('loadingwithdots') . '</td></tr>';
print '</table>';
print '</div>';

print '<form action="index.php" method="post" class="dolichat-hidden">';
print '<table border="0" id="tabelofdoom" width="100%">';
print '<tr>';
print '<td width="6%"></td>';
print '<td width="25%"></td>';
print '<td width="10%" class="dolichat-legacy-label-cell">';
print $langs->trans('Sendenan') . ' ';
print $dolichat->select($cuser, 'cuser', '1', 'Broadcast', 'shadow', 'onChange="saverofcuser()"');
print '<p><a href="group/userconf.php" class="dolichat-legacy-link">' . $langs->trans('Einstellungen') . '</a>';
print '<input type="hidden" value="-1" name="cuser_s" id="cuser_s">';
print '</td>';
print '<td>';
print '<textarea name="eintrag" id="textbox" width="350px" onChange="reloaduploadXMLDoc()">' . dol_escape_htmltag($eintrag) . '</textarea>';
print '</td>';
print '<td>';
print '<input type="button" class="shadow dolichat-legacy-button" name="eintragen" value="' . dol_escape_htmltag($langs->trans('Senden')) . '" id="button" onClick="saveXMLDoc()">';
print '<input type="hidden" id="nick" name="nick" value="' . dol_escape_htmltag(trim($staticuser->lastname . ' ' . $staticuser->firstname)) . '">';
print '<input type="hidden" name="nick_n_tmp" value="' . dol_escape_htmltag($nickPost) . '">';
print '<input type="hidden" name="PicSavedID" id="PicSavedID" value="' . ((int) $PicSavedID) . '">';
print '</td>';
print '</tr>';
print '</table>';
print '</form>';
print '<input type="hidden" id="noscro" value="0">';
?>
<script type="text/javascript">
    var viewportWidth;
    var viewportHeight;
    var mydate = new Date();
    var downtime;
    var isackick = false;
    var active;

    $(document).ready(function () {
        $('#Gesended').fadeOut('fast');
        saverofcuser();
        document.body.style.overflow = 'hidden';
        viewportWidth = $(window).width();
        viewportHeight = $(window).height();
        document.body.style.overflow = '';

        $('#fullsizechatdiv')
            .addClass('dolichat-fullsize-overlay')
            .css('height', (viewportHeight - 50) + 'px')
            .hide();

        document.getElementById('textbox').value = '';
        document.getElementById('textbox').focus();

        loadXMLDoc();

        <?php if ($useUpload) { ?>
        reloaduploadXMLDoc();
        <?php } ?>

        $('.loadAll').click(function () {
            loadNextDay();
            $('[name="newload"]').hide('slow');
        });
    });

    var dolichatCurrentCuser = '<?php print ((int) $cuser); ?>';

    function buildCurrentChatUrl(extraParams) {
        var baseUrl = 'indexFrameChatMain.php?cuser=' + encodeURIComponent(dolichatCurrentCuser);
        if (extraParams) {
            baseUrl += '&' + extraParams;
        }
        baseUrl += '&_=' + new Date().getTime();
        return baseUrl;
    }

    function reloadCurrentChatView(extraParams) {
        window.location.href = buildCurrentChatUrl(extraParams || 'refresh=1');
    }

    var tmp_chatbox;
    function fullsizechattext(chatbox) {
        if ($('#fullsizechatdiv').html() === '' && is_deleting === false) {
            if ($(chatbox).attr('class') === 'chatdiv2To') {
                $('#fullsizechatdiv').css('background-color', '#FFFFF2');
            }
            if ($(chatbox).attr('class') === 'chatdiv2You') {
                $('#fullsizechatdiv').css('background-color', '#D4FFCF');
            }

            $(chatbox).clone().appendTo('#fullsizechatdiv');
            $('#fullsizechatdiv').fadeIn('fast');

            var textH = $('#fullsizechatdiv #chattabel div span span').html();
            $('#fullsizechatdiv #chattabel').remove();
            $('#fullsizechatdiv').html('<div id="chatcontend"></div>');

            $('#fullsizechatdiv #chatcontend')
                .css('height', '100%')
                .css('width', '95%')
                .css('overflow', 'auto')
                .css('font-size', '14px');
            $('#fullsizechatdiv #chatcontend').html(
                textH + '<div onclick="fullsizechattext(this)" class="dolichat-fullsize-close"><?php print img_picto('', 'close'); ?></div>'
            );

            tmp_chatbox = chatbox;
            $(chatbox).fadeOut('fast');
        } else if (is_deleting === false) {
            $(tmp_chatbox).fadeIn('fast');
            $('#fullsizechatdiv').fadeOut('fast');
            $('#fullsizechatdiv').html('');
        }
    }

    function saverofcuser() {
        var cuser = '';
        cuser = document.getElementById('cuser').options[document.getElementById('cuser').selectedIndex].value;
        document.getElementById('cuser_s').value = cuser;

        <?php if ($useUpload) { ?>
        reloaduploadXMLDoc();
        <?php } ?>
    }

    setInterval(function () {
        loadXMLDoc();
    }, <?php print ((int) $reloadnum); ?>000);

    var loading = false;
    function loadXMLDoc() {
        var elemt = '';
        var text = '';
        var rowid = '';
        var cuser = '<?php print ((int) $cuser); ?>';
        var neu = '1';
        var cusers = document.getElementById('gesehenscriptcuser').innerHTML;

        rowid = document.getElementById('gesehenscript').innerHTML;
        loading = true;

        $.ajax({
            method: 'GET',
            url: 'core/ajax/ajax_proc_status.php',
            data: {
                pruf: 1,
                chat_stat: window.parent.$('#active_stat').val()
            }
        }).done(function (msg1) {
            schowReload4();
            var stat_arr = msg1.split('%<|>%');

            $.each(stat_arr, function (index, value) {
                if (index > 0) {
                    var user_stats = value.split('%<>%');
                    var sttrans = '';

                    if (user_stats[1] == 2) {
                        sttrans = window.parent.$('#OnlineStatus2').val();
                        window.parent.$('.online_stats_' + user_stats[0]).attr('title', sttrans);
                        window.parent.$('.online_stats_' + user_stats[0]).css('background-color', 'orange');
                    } else if (user_stats[1] == 1) {
                        sttrans = window.parent.$('#OnlineStatus1').val();
                        window.parent.$('.online_stats_' + user_stats[0]).attr('title', sttrans);
                        window.parent.$('.online_stats_' + user_stats[0]).css('background-color', 'green');
                    } else {
                        sttrans = window.parent.$('#OnlineStatus0').val();
                        window.parent.$('.online_stats_' + user_stats[0]).attr('title', sttrans);
                        window.parent.$('.online_stats_' + user_stats[0]).css('background-color', 'grey');
                    }
                }
            });

            msg1 = stat_arr[0];
            if (rowid === msg1 && cuser === cusers) {
                neu = '0';
                loading = false;
            } else {
                neu = '1';
                loading = false;

                var rowidg = 0;
                if (onlyonetime === 1) {
                    rowidg = rowid;
                }

                $.ajax({
                    method: 'GET',
                    url: 'core/ajax/ajax_loaderMain.php',
                    data: {cuser: cuser, getnew: rowidg}
                }).done(function (msg) {
                    schowReload4();
                    if (neu === '1') {
                        text = msg;
                    }
                    if (text !== '') {
                        if (onlyonetime === 1) {
                            $('#ajax_chat').append(text);
                        } else {
                            document.getElementById('ajax_chat').innerHTML = text;
                        }
                        $('html, body').animate({scrollTop: $(document).height()}, 500, function () {
                            if (onlyonetime === 0) {
                                $('[name="newload"]').show();
                            }
                        });
                    }
                });
            }

            document.getElementById('gesehenscriptcuser').innerHTML = cuser;
            document.getElementById('gesehenscript').innerHTML = msg1;
        });

        if (document.getElementById('noscro').value == 0) {
            $('html, body').animate({scrollTop: $(document).height()}, 500);
            document.getElementById('noscro').value = 1;
        }
    }

    var onlyonetime = 0;
    function loadNextDay() {
        if (onlyonetime === 0) {
            var text = '';
            var cuser = '<?php print ((int) $cuser); ?>';

            if (cuser > 0) {
                schowReload4();
            }

            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            }
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    text = xmlhttp.responseText;
                    if (text !== '') {
                        $('#ajax_old_chat').html(text);
                        $('#ajax_old_chat').fadeIn('slow');
                        if ($('[name="notlostT"]').text() !== '') {
                            $('[name="notlost1t"]').hide();
                        }
                        $('html, body').delay(500).scrollTop($('#today').offset().top);
                    }
                }
            };

            xmlhttp.open('GET', 'core/ajax/ajax_loaderMain.php?cuser=' + cuser + '&alldays=1', true);
            xmlhttp.send();
            onlyonetime = 1;
        }
    }

    function nl2br(s, m) {
        var p = document.createElement('pre');
        if (m) {
            var t = document.createTextNode(s);
            p.appendChild(t);
        } else {
            p.innerHTML = s;
        }
        return String(p.innerHTML).replace(/\n/g, '<br>');
    }

    function saveXMLDoc() {
        var eintrag = document.getElementById('textbox').value;
        var nick = document.getElementById('nick').value;
        var cuser = '<?php print ((int) $cuser); ?>';
        var PicSavedID = document.getElementById('PicSavedID').value;

        schowReload3();
        eintrag = nl2br(eintrag, 0);

        if (eintrag === '') {
            schowReload();
        } else {
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            }
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById('show').innerHTML = xmlhttp.responseText;
                }
            };

            xmlhttp.open('POST', 'core/ajax/ajax_loaderMain.php', true);
            xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(
                'eintrag=' + encodeURIComponent(eintrag)
                + '&nick=' + encodeURIComponent(nick)
                + '&cuser=' + encodeURIComponent(cuser)
                + '&PicSavedID=' + encodeURIComponent(PicSavedID)
            );

            document.getElementById('textbox').value = '';
            document.getElementById('textbox').focus();
            $('#Gesended').fadeIn('slow').delay(2000).fadeOut('slow');
            loadXMLDoc();
        }
    }

    function reloaduploadXMLDoc() {
        var eintrag = document.getElementById('textbox').value;
        var cuser = document.getElementById('cuser').options[document.getElementById('cuser').selectedIndex].value;
        schowReload3();
        eintrag = nl2br(eintrag, 0);
    }

    function scrollToText() {
        $('html, body').animate({scrollTop: $('#textbox').offset().top}, 1000);
        $('#runter').fadeOut('slow');
    }

    function scrollToBegin() {
        $('html, body').animate({scrollTop: $('#mainmenutd_home').offset().top}, 1000);
        $('#hoch').fadeOut('slow');
    }

    function schowReload() {
        $('#Reload').fadeIn('slow').delay(5000).fadeOut('slow');
    }

    function schowReload2() {
        $('#Reload2').fadeIn('fast').delay(500).fadeOut('fast');
    }

    function schowReload3() {
        $('#Reload3').fadeIn('slow').delay(5000).fadeOut('slow');
    }

    function schowReload4() {
        $('#Reload4').fadeIn('slow').delay(500).fadeOut('slow');
    }

    function schowReload5() {
        $('#Reload5').fadeIn('slow').delay(500).fadeOut('slow');
    }

    function schowReload_td() {
        document.getElementById('Reload_text').style.color = '#000000';
        document.getElementById('Reload_text2').style.color = '#000000';
        document.getElementById('Reload_text4').style.color = '#000000';
        document.getElementById('Reload_text3').style.color = '#000000';
        document.getElementById('Reload_text').style.zIndex = '100';
        document.getElementById('Reload_text2').style.zIndex = '100';
        document.getElementById('Reload_text4').style.zIndex = '100';
        document.getElementById('Reload_text3').style.zIndex = '100';
        document.getElementById('Reload_text5').style.zIndex = '100';
        document.getElementById('Reload').style.zIndex = '100';
        document.getElementById('Reload2').style.zIndex = '100';
        document.getElementById('Reload4').style.zIndex = '100';
        document.getElementById('Reload3').style.zIndex = '100';
        document.getElementById('Reload5').style.zIndex = '100';
        $('#Reload_td').fadeIn('slow').delay(10000).fadeOut('slow');

        setTimeout(function () {
            document.getElementById('Reload_text').style.zIndex = '';
            document.getElementById('Reload_text2').style.zIndex = '';
            document.getElementById('Reload_text4').style.zIndex = '';
            document.getElementById('Reload_text5').style.zIndex = '';
            document.getElementById('Reload_text3').style.zIndex = '';
            document.getElementById('Reload').style.zIndex = '';
            document.getElementById('Reload2').style.zIndex = '';
            document.getElementById('Reload4').style.zIndex = '';
            document.getElementById('Reload5').style.zIndex = '';
            document.getElementById('Reload3').style.zIndex = '';
            document.getElementById('Reload_text').style.color = '#D5D5D5';
            $('#Reload_text').fadeIn('slow');
            document.getElementById('Reload_text2').style.color = '#D5D5D5';
            $('#Reload_text2').fadeIn('slow');
            document.getElementById('Reload_text4').style.color = '#D5D5D5';
            $('#Reload_text4').fadeIn('slow');
            document.getElementById('Reload_text3').style.color = '#D5D5D5';
            $('#Reload_text3').fadeIn('slow');
            document.getElementById('Reload_text5').style.color = '#D5D5D5';
            $('#Reload_text5').fadeIn('slow');
        }, 11000);
    }

    $(window).scroll(function () {
        $('#hoch').fadeIn('slow');
        $('#runter').fadeIn('slow');
    });

    function Nachgsehen(idrowid, self) {
        if (!(self == 1 && del == 0)) {
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            }
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById('gesehenscript').innerHTML = xmlhttp.responseText;
                }
            };

            xmlhttp.open('GET', 'core/ajax/ajax_proc_status.php?id=' + idrowid + '&del=0', true);
            xmlhttp.send();
        }
    }

    var is_deleting = false;

    function parseDeleteResponse(rawResponse) {
        if (typeof rawResponse !== 'string') {
            return null;
        }

        var trimmed = rawResponse.trim();
        if (trimmed === '') {
            return null;
        }

        try {
            return JSON.parse(trimmed);
        } catch (e) {
            return null;
        }
    }

    function loschenimg(idrowid) {
        if (!idrowid) {
            return false;
        }

        is_deleting = true;

        $.ajax({
            method: 'GET',
            url: 'core/ajax/ajax_proc_status.php',
            dataType: 'text',
            cache: false,
            data: {
                id: idrowid,
                del: 1
            }
        }).done(function (rawResponse) {
            var response = parseDeleteResponse(rawResponse);

            if (response && response.status === 'success') {
                reloadCurrentChatView('deleted=' + encodeURIComponent(idrowid));
                return;
            }

            if (rawResponse.trim() === '') {
                reloadCurrentChatView('deleted=' + encodeURIComponent(idrowid));
                return;
            }

            alert((response && response.message) ? response.message : 'Message could not be deleted.');
        }).fail(function (xhr) {
            var response = parseDeleteResponse(xhr && xhr.responseText ? xhr.responseText : '');
            alert((response && response.message) ? response.message : 'Message could not be deleted.');
        }).always(function () {
            is_deleting = false;
        });

        return false;
    }

    function saverofadmin() {
        var cuser = '';
        cuser = document.getElementById('admin').options[document.getElementById('admin').selectedIndex].value;
        document.getElementById('admin_s').value = cuser;

        if (cuser == 1) {
            $('#Reload5').fadeIn('slow');
        } else {
            $('#Reload5').fadeOut('slow');
        }
    }

    <?php if ($eintrag !== '') { ?>
    saveXMLDoc();
    <?php } ?>
</script>
<?php
llxFooter('', 'public');
$db->close();
