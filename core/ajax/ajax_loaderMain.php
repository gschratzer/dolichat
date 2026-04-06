<?php
/* Copyright (C) 2016 Guido Schratzer
 * Copyright (C) 2016 Niklas Spanring
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

$res = 0;
if (!$res && file_exists(__DIR__ . '/../../main.inc.php')) {
    $res = @include __DIR__ . '/../../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../../main.inc.php')) {
    $res = @include __DIR__ . '/../../../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../../../main.inc.php')) {
    $res = @include __DIR__ . '/../../../../main.inc.php';
}
if (!$res && file_exists(__DIR__ . '/../../../../../main.inc.php')) {
    $res = @include __DIR__ . '/../../../../../main.inc.php';
}
if (!$res) {
    die('Include of main fails');
}

require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
dol_include_once('/dolichat/class/dolichat.class.php');

$langs->load('dolichat@dolichat');

$dolichat = new dolichat($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eintrag = GETPOST('eintrag', 'restricthtml');
    $nick = GETPOST('nick', 'alpha');
    $cuser = GETPOST('cuser', 'alphanohtml');
    $picSavedId = GETPOSTINT('PicSavedID');

    if ($eintrag === '' && $picSavedId <= 0) {
        setEventMessages($langs->trans('BitteNachrichteingeben'), null, 'errors');
        print '<div class="error">' . dol_escape_htmltag($langs->trans('BitteNachrichteingeben')) . '</div>';
        exit;
    }

    $messageId = $dolichat->createMessage($user, $cuser, $eintrag, $picSavedId);
    if ($messageId <= 0) {
        $errorText = $dolichat->error ? $dolichat->error : $langs->trans('Error');
        dol_syslog('dolichat save failed: ' . $errorText, LOG_ERR);
        setEventMessages($errorText, null, 'errors');
        print '<div class="error">' . dol_escape_htmltag($errorText) . '</div>';
        exit;
    }

    print '<div class="ok">OK #' . ((int) $messageId) . '</div>';
    exit;
}

$bildernum = $dolichat->getUserParamValue($user->id, 'CHAT_SHOW_PIC', '');
$cuser = GETPOST('cuser', 'alphanohtml');
$alldays = GETPOSTINT('alldays');
$fromrow = GETPOSTINT('getnew');

$neu = '';
$testFgroup = (!empty($cuser) && is_string($cuser)) ? substr($cuser, 0, 1) : '';
$tage = 0;
if (!empty($conf->global->dolichat_USE_DEL_TIME) && !empty($conf->global->dolichat_DEL_TIME)) {
    $tage = (int) $conf->global->dolichat_DEL_TIME;
}
$days = array();
$name = '';
$tester = 0;
$firstoff = 0;
$chattext_user_css_text_shadow = '';
$rows = $dolichat->fetchMessagesForContext($user->id, $cuser, $tage, $fromrow, $alldays);
if (!$fromrow && !$alldays) {
    $neu .= '<div class="dateshower" style="display:none;">' . $langs->trans('Today') . '</div>';
    $neu .= '<div name="notlost1t"><div id="today"></div></div>';
}

$neu .= '<div>';
foreach ($rows as $row) {
    $rowDate = substr((string) $row->timestamp, 0, 10);
    $name = '';

    if (empty($days) || !in_array($rowDate, $days)) {
        $neu .= '</div>';
        $neu .= '<div id="' . dol_escape_htmltag(date('d.m.Y', strtotime($rowDate))) . '">';
        if (!$fromrow && !$alldays) {
            $name = 'lost';
        }
        $days[] = $rowDate;
        end($days);
        $dayKey = (string) key($days);

        if (strtotime($rowDate) >= strtotime('today')) {
            $neu .= '<div class="date" id="' . $dayKey . $name . 'datekeydiv"><div class="dolichat-date-label" name="notlost" id="today">' . $langs->trans('Today') . '</div></div>';
        } elseif (strtotime($rowDate) >= strtotime('yesterday')) {
            $neu .= '<div class="date" id="' . $dayKey . $name . 'datekeydiv"><div class="dolichat-date-label" name="notlost" id="' . $dayKey . 'datekey">' . $langs->trans('Yesterday') . '</div></div>';
        } else {
            $neu .= '<div class="date" id="' . $dayKey . $name . 'datekeydiv"><div class="dolichat-date-label" name="notlost" id="' . $dayKey . 'datekey">' . date('d.m.Y', strtotime($rowDate)) . '</div></div>';
        }

        $neu .= '<script>
            $(window).scroll(function () {
                var input = $("#' . $dayKey . 'datekey").text();
                var position = $("#' . $dayKey . 'datekey").position();
                if (position && (position.top - $(window).scrollTop()) < 30) {
                    $(".dateshower").text(input);
                    $(".dateshower").show();
                }
            });
        </script>';
    }

    if ((int) $row->privat === 0) {
        $farbe = '#2A0000';
        $privatnachricht = ' sagt';
    } else {
        $farbe = '#0000DD';
        $privatnachricht = (string) $row->privat_name;
    }

    $tageKurz = array($langs->trans('So'), $langs->trans('Mo'), $langs->trans('Di'), $langs->trans('Mi'), $langs->trans('Do'), $langs->trans('Fr'), $langs->trans('Sa'));
    $time = date('H:i', strtotime((string) $row->timestamp));
    $day = (int) date('w', strtotime((string) $row->timestamp));
    $day = $tageKurz[$day];

    $privatnachricht3 = '';
    $privatnachricht2 = substr($privatnachricht, 8);
    if ($privatnachricht2 === '') {
        $privatnachricht2 = 'Broadcast';
        $privatnachricht3 = $langs->trans('zum') . ' Broadcast';
    }
    $privatnachricht2 = 'an ' . $privatnachricht2;
    $user_to_you = (string) $row->user;

    $chattext = !empty($row->chattextblob) ? base64_decode($row->chattextblob) : $row->chattext;
    $chattext = str_replace('<', '&lt;', (string) $chattext);
    $chattext = str_replace('>', '&gt;', (string) $chattext);
    $chattext = str_replace("\n", '<br>', (string) $chattext);

    $chattext_test = !empty($row->chattextblob) ? base64_decode($row->chattextblob) : $row->chattext;

    if (is_numeric($cuser) && (int) $cuser > 0) {
        $privatnachricht2 = '';
        $user_to_you = '';
        $pxoffilter = 4;
        $zeigwho = 'display:none;';
        $to_you_who_zeig = 'display:none;';
    } elseif ((string) $cuser === '0') {
        $privatnachricht2 = '';
        $privatnachricht3 = '';
        $pxoffilter = 4;
    } else {
        $chattext_user = new User($db);
        $chattext_user->fetch((int) $row->user_id);
        $hex = str_replace('#', '', (string) $chattext_user->color);

        if (strlen($hex) === 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $r_alt = 255 - $r;
        $g_alt = 255 - $g;
        $b_alt = 255 - $b;
        $chattext_user_css = 'font-size: 10px; border-radius: 30px; ' . $chattext_user_css_text_shadow . ' padding: 3px; background-color: rgba(' . $r_alt . ', ' . $g_alt . ', ' . $b_alt . ', 0.38)';
        if ((int) $user->id !== (int) $row->user_id) {
            $chattext_user_name = ' <span style="color:rgb(' . $r . ', ' . $g . ', ' . $b . ');' . $chattext_user_css . '"> ' . $row->user . '</span>';
        } else {
            $chattext_user_name = '';
        }
        $pxoffilter = 4;
        $chattext .= $chattext_user_name;
        unset($chattext_user_name);
    }

    $bild = '';

    $chattext_test_pic = !empty($row->chattextblob) ? base64_decode($row->chattextblob) : $row->chattext;
    $pic = substr(strrchr((string) $chattext_test_pic, '%picto='), 7);
    $picParts = explode('%picto=', (string) $chattext_test_pic);

    if (strpos(strtolower((string) $chattext_test_pic), 'youtube.com') !== false || strpos(strtolower((string) $chattext_test_pic), 'youtu.be') !== false) {
        $link = explode('=', (string) $chattext_test_pic);
        if (strpos((string) $chattext_test_pic, '=') !== false) {
            $link = str_replace("\n", ' ', $link[1]);
            $link = explode(' ', $link);
            $picParts[1] = '<br><iframe width="560" height="315" src="https://www.youtube.com/embed/' . $link[0] . '" frameborder="0" allowfullscreen></iframe>';
        } elseif (strpos((string) $chattext_test_pic, 'http://') !== false || strpos((string) $chattext_test_pic, 'https://') !== false) {
            $link = str_replace("\n", ' ', (string) $chattext_test_pic);
            $link = explode('/', $link);
            $link = explode(' ', $link[3]);
            $picParts[1] = '<br><iframe width="560" height="315" src="https://www.youtube.com/embed/' . $link[0] . '" frameborder="0" allowfullscreen></iframe>';
        } else {
            $link = str_replace("\n", ' ', (string) $chattext_test_pic);
            $link = explode('/', $link);
            $link = explode(' ', $link[1]);
            $picParts[1] = '<br><iframe width="560" height="315" src="https://www.youtube.com/embed/' . $link[0] . '" frameborder="0" allowfullscreen></iframe>';
        }
    }

    $picHtml = isset($picParts[1]) ? $picParts[1] : '';
    $url = (int) $row->user_id;
    if ($bildernum == 1 || $bildernum === '') {
        if ($picHtml !== '') {
            foreach ($picParts as $label => $imagesLink) {
                if (($label % 2) == 0) {
                    $styleUngerade = '';
                } else {
                    $styleUngerade = 'float:left;';
                }
                if ($label != 0) {
                    if (strpos($imagesLink, '<iframe') !== false) {
                        $bild .= $picHtml;
                    } elseif (strpos(strtolower($imagesLink), 'http://') !== false || strpos(strtolower($imagesLink), 'https://') !== false) {
                        ini_set('default_socket_timeout', '1');
                        $headers = @get_headers($imagesLink);
                        if (empty($headers) || strpos($headers[0], '200') === false) {
                            $bild .= '[Error] Url does not exist!';
                        } else {
                            $bild .= '<a href="' . $imagesLink . '" target="_blank"><img src="' . $imagesLink . '" alt="Pic is Wrong" width="50%" style="min-width:124px;min-height:124;' . $styleUngerade . '"></a>';
                        }
                        ini_set('default_socket_timeout', '30');
                    } else {
                        $fullpath = DOL_DATA_ROOT . '/dolichat/uploads/' . $url . '/' . $imagesLink;
                        if (is_file($fullpath)) {
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $nameFile = basename($fullpath);
                            $type = finfo_file($finfo, $fullpath);
                            $size = filesize($fullpath);
                            $size = formatFileSizeDolichat($size);
                            $fdate = date('d.m.Y H:i', filemtime($fullpath));

                            $bild .= '<span><a href="' . dol_buildpath('document.php', 1) . '?modulepart=dolichat&file=uploads/' . $url . '/' . rawurlencode($imagesLink) . '&cache=1" target="_blank"><img width="20%" src="' . DOL_URL_ROOT . '/dolichat/img/text-file-3-xxl.png" class="downfile"></a></span>';
                            $bild .= '<span style="position: absolute; margin-left: 20px;"><table class="filedetail">';
                            $bild .= '<tr><td>Filename: </td><td>' . dol_escape_htmltag($nameFile) . '</td></tr>';
                            $bild .= '<tr><td>Filetype: </td><td>' . dol_escape_htmltag($type) . '</td></tr>';
                            $bild .= '<tr><td>Size: </td><td>' . dol_escape_htmltag($size) . '</td></tr>';
                            $bild .= '<tr><td>Date: </td><td>' . dol_escape_htmltag($fdate) . '</td></tr>';
                            $bild .= '<tr><td>Download: </td><td><a href="' . dol_buildpath('document.php', 1) . '?modulepart=dolichat&file=uploads/' . $url . '/' . rawurlencode($imagesLink) . '&cache=1">Link</a></td></tr>';
                            $bild .= '</table></span>';

                            if (preg_match('/\.(png|jpg|gif|bmp)$/i', $imagesLink)) {
                                $bild .= '<br><a href="' . dol_buildpath('document.php', 1) . '?modulepart=dolichat&file=uploads/' . $url . '/' . rawurlencode($imagesLink) . '&cache=1" target="_blank"><img src="' . dol_buildpath('document.php', 1) . '?modulepart=dolichat&file=uploads/' . $url . '/t_' . rawurlencode($imagesLink) . '&cache=1" alt="Pic is Wrong" width="50%" style="min-width:124px;min-height:124;' . $styleUngerade . '"></a>';
                            } elseif (preg_match('/\.(mp3|ogg|wav)$/i', $imagesLink)) {
                                $bild .= '<br><audio controls preload="metadata">';
                                $bild .= '<source src="' . dol_buildpath('document.php', 1) . '?modulepart=dolichat&file=uploads/' . $url . '/' . rawurlencode($imagesLink) . '&cache=1" type="' . dol_escape_htmltag($type) . '">';
                                $bild .= 'Your browser does not support the audio tag.';
                                $bild .= '</audio>';
                            } elseif (preg_match('/\.(ogg|webm|mp4)$/i', $imagesLink)) {
                                $bild .= '<br><video width="405px" height="240" controls preload="none" class="html5videoplayer">';
                                $bild .= '<source src="' . dol_buildpath('document.php', 1) . '?modulepart=dolichat&file=uploads/' . $url . '/' . rawurlencode($imagesLink) . '&cache=1" type="' . dol_escape_htmltag($type) . '">';
                                $bild .= 'Your browser does not support the video tag.';
                                $bild .= '</video>';
                            }
                        }
                    }
                }
                $styleUngerade = '';
            }
            $chattext = (isset($picParts[0]) ? $picParts[0] : '') . '<br>';
        }
    }

    if (strpos((string) $chattext_test_pic, '%youtube=https://www.youtube.com') !== false) {
        $var_e = explode('%youtube=https://www.youtube.com', (string) $chattext_test_pic);
        $e_ifr = isset($var_e[1]) ? $var_e[1] : '';
        $bild .= '<iframe width="560" height="315" src="https://www.youtube.com' . $e_ifr . '" frameborder="0" allowfullscreen>Iframe is Deaktive on the ext. Server</iframe>';
        $chattext = (isset($var_e[0]) ? $var_e[0] : '') . '<br>';
    }

    if (count($picParts) > 2) {
        $minBildwiht = '248px';
    } else {
        $minBildwiht = '1px';
    }

    $chattext_l = !empty($row->chattextblob) ? base64_decode($row->chattextblob) : $row->chattext;
    $chattext_l = strlen((string) $chattext_l);
    if ($chattext_l < 10) {
        $chattext_l += 20;
    } elseif ($chattext_l < 200) {
        $chattext_l += 100;
    } elseif ($chattext_l < 500) {
        $chattext_l += 345;
    } elseif ($chattext_l > 1000) {
        $chattext_l += 645;
    }

    if ((int) $row->user_id === (int) $user->id || (int) $row->gesehen === 1) {
        $scriptforone = 'ondblclick="Nachgsehen(\'' . $row->rowid . '\', 0)"';
        $scriptforone3 = 'ondblclick="Nachgsehen(\'' . $row->rowid . '\', 1)"';
    } else {
        $scriptforone = 'ondblclick="Nachgsehen(\'' . $row->rowid . '\', 0)"';
        $scriptforone3 = 'ondblclick="Nachgsehen(\'' . $row->rowid . '\', 1)"';
    }

    if ((int) $row->gesehen === 1) {
        $gesehenimg = '<img src="images/checkmark.png" alt="Gesehen" width="13px" height="12px">';
    } else {
        $gesehenimg = '';
    }

    $tester2 = $tester;
    if ((int) $user->id === (int) $row->user_id) {
        $tester = 1;
    }
    if ((int) $user->id === (int) $row->privat) {
        $tester = 2;
    }
    if ((int) $row->privat === 0 && (int) $user->id !== (int) $row->user_id) {
        $tester = 3;
    }

    if ($chattext_test) {
        $neu .= '<div>';
        $delimg = '';
        $scriptfullsize = '';
        $scriptforone2 = $scriptforone;
        if (!empty($user->rights->dolichat) && !empty($user->rights->dolichat->Admin)) {
            $scriptfullsize = 'ondblclick="fullsizechattext(this)"';
            $scriptforoneimg = 'onclick="loschenimg(\'' . $row->rowid . '\')"';
            $delimg = '<img src="images/del.png" alt="Del" width="13px" height="12px" ' . $scriptforoneimg . '>';
        }

        if ($tester == 1) {
            $neu .= '<div id="msg-line" class="msg-lineR">';
            $neu .= '<div id="chattabel" ' . $scriptfullsize . ' class="chatdiv2You">';
            $neu .= '<div class="chatdiv2You2">';
            $neu .= '<span ' . $scriptforone3 . ' id="your_tr" class="your_tr_cl" style="color:' . $farbe . ';"><span id="your_chattext" name="your_chattext" class="shadow2" width="' . $chattext_l . '" style="min-width:' . $minBildwiht . ';">' . $chattext . ' ' . $bild . '</span></span>';
            $neu .= '<span class="your_tr_span">' . $time . ' ' . $gesehenimg . ' ' . $delimg . '</span>';
            $neu .= '</div>';
        }
        if ($tester == 2) {
            $neu .= '<div id="msg-line" class="msg-lineL">';
            $neu .= '<div id="chattabel" ' . $scriptfullsize . ' class="chatdiv2To dolichat-chat-bubble-left" style="font-size:14px;">';
            $neu .= '<div class="dolichat-chat-bubble-inner-left">';
            $neu .= '<span ' . $scriptforone2 . ' id="to_you_tr" style="height: 30px;color:' . $farbe . ';"><span id="to_you_text" class="shadow2" width="' . $chattext_l . '">' . $chattext . ' ' . $bild . '</span></span>';
            $neu .= '<span class="dolichat-chat-time-left">' . $time . ' ' . $delimg . '</span>';
            $neu .= '</div>';
        }
        if ($tester == 3) {
            $neu .= '<div id="msg-line" class="msg-lineL">';
            $neu .= '<div id="chattabel" ' . $scriptfullsize . ' class="chatdiv2Br" style="font-size:14px;">';
            $neu .= '<span ' . $scriptforone2 . ' id="Broadcast_tr" style="height: 30px;color:' . $farbe . ';"><span id="Broadcast_text" class="shadow2" width="' . $chattext_l . '" style="padding-left: 4px;">' . $chattext . ' ' . $bild . '</span></span>';
            $neu .= '<span style="text-align: right; padding-right: 4px; font-size: 12px;color: #474747;padding-left: 30px;">' . $time . ' ' . $delimg . '</span>';
        }
        $neu .= '</div></div></div>';

        foreach ($dolichat->getMessagesForBroadcastSeen($testFgroup === 'G') as $row2) {
            $dolichat->appendBroadcastSeenUser((int) $row2->rowid, (int) $user->id);
        }
        $firstoff = 1;
    }
}

$neu .= '<div id="chattabel"><span><span colspan="8" height="15px"></span></span></div>';

echo $neu;

function formatFileSizeDolichat($bytes)
{
    if ($bytes >= 1000000000) {
        return round($bytes / 1000000000, 2) . ' GB';
    }

    if ($bytes >= 1000000) {
        return round($bytes / 1000000, 2) . ' MB';
    }

    return round($bytes / 1000, 2) . ' KB';
}

