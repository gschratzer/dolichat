<?php
ob_start();
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
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('status' => 'error', 'message' => 'Include of main fails'));
    exit;
}

dol_include_once('/dolichat/class/dolichat.class.php');

header('Content-Type: application/json; charset=UTF-8');

function dolichat_send_json_and_exit($response)
{
    $buffer = '';
    if (ob_get_level() > 0) {
        $buffer = trim((string) ob_get_clean());
    }
    if ($buffer !== '') {
        if (empty($response['debug'])) {
            $response['debug'] = $buffer;
        }
        if (empty($response['message'])) {
            $response['message'] = $buffer;
        }
    }
    echo json_encode($response);
    exit;
}

$dolichat = new dolichat($db);
$langs->load('dolichat@dolichat');

$staticuser = new User($db);
$staticuser->fetch($user->id);

$staticuser2 = new User($db);

$cuser = GETPOST('cuser', 'alphanohtml');
$picSavedId = GETPOSTINT('PicSavedID');
$eintrag = GETPOST('eintrag', 'restricthtml');
$nick = GETPOST('nick', 'alphanohtml');

$response = array(
    'status' => 'error',
    'message' => '',
    'messageId' => 0,
);

if (empty($nick) || trim((string) $eintrag) === '') {
    $response['message'] = $langs->trans('BitteNachrichteingeben');
    dolichat_send_json_and_exit($response);
}

if (empty($cuser) || $cuser === '0' || $cuser === '-1') {
    $response['message'] = 'Please select a chat or contact first.';
    dolichat_send_json_and_exit($response);
}

$picText = '';
if ($picSavedId > 0) {
    foreach ($dolichat->getPicturesByMessageId($picSavedId) as $row) {
        $picText .= ' %picto=' . $picSavedId . '/' . $row->PicName;
    }
}

$testFgroup = substr((string) $cuser, 0, 1);
$write = 1;
$rightsfail = '';
$rightsfail_c = 0;

if ($testFgroup === 'G') {
    $rowidG = (int) substr((string) $cuser, 1);
    $sql = 'SELECT G_write FROM ' . MAIN_DB_PREFIX . 'chatgroup WHERE rowid = ' . $rowidG;
    $resql = $db->query($sql);
    if ($resql) {
        $obj = $db->fetch_object($resql);
        if ($obj) {
            $write = (int) $obj->G_write;
        }
    }
}

if ((int) $write !== 1) {
    $response['message'] = 'Writing to this chat is currently disabled.';
    dolichat_send_json_and_exit($response);
}

$privatuser = $cuser;
if ((is_numeric($privatuser) && (int) $privatuser > 0) || $testFgroup === 'G') {
    // keep selected receiver
} else {
    if (empty($user->rights->dolichat->UseBroadcast)) {
        $privatuser = (string) $user->id;
        $rightsfail = ' No Permison to make a Broadcast ';
        $rightsfail_c = 1;
    } else {
        $privatuser = '0';
    }
}

$alles = '';
if ($eintrag !== '%picto=notext') {
    $alles = htmlspecialchars($eintrag, ENT_QUOTES, 'UTF-8');
}
if ($rightsfail_c === 1) {
    $alles = $rightsfail;
}
$alles = str_replace('&lt;br&gt;', '<br>', $alles);
$alles .= $picText;

if ((is_numeric($privatuser) && (int) $privatuser > 0) && $testFgroup !== 'G') {
    $staticuser2->fetch((int) $privatuser);
}

if ($alles === '/clear' && !empty($user->admin)) {
    $db->query('DELETE FROM ' . MAIN_DB_PREFIX . 'chattext');
    $response['status'] = 'success';
    $response['message'] = 'Chat history cleared.';
    dolichat_send_json_and_exit($response);
}

$messageId = $dolichat->createMessage($staticuser, $privatuser, $alles, $picSavedId);
if ($messageId <= 0) {
    $response['message'] = !empty($dolichat->error) ? $dolichat->error : 'Message could not be saved.';
    dolichat_send_json_and_exit($response);
}

$savedMessage = $dolichat->getMessageById($messageId);
if (!is_object($savedMessage) || (int) $savedMessage->rowid !== (int) $messageId) {
    $response['message'] = 'Message insert returned an ID, but the saved message could not be verified.';
    dolichat_send_json_and_exit($response);
}
if ((int) $savedMessage->user_id !== (int) $user->id) {
    $response['message'] = 'Message save verification failed for the sender.';
    dolichat_send_json_and_exit($response);
}
if ((string) $savedMessage->privat !== (string) $privatuser) {
    $response['message'] = 'Message save verification failed for the receiver.';
    dolichat_send_json_and_exit($response);
}
if (trim((string) $savedMessage->chattextblob) === '') {
    $response['message'] = 'Message save verification failed because the stored payload is empty.';
    dolichat_send_json_and_exit($response);
}

$decodedSavedMessage = base64_decode((string) $savedMessage->chattextblob, true);
if ($decodedSavedMessage === false) {
    $response['message'] = 'Message save verification failed because the stored payload could not be decoded.';
    dolichat_send_json_and_exit($response);
}

$dolichat->markMessagesSeenForReceiver($user->id, $user->id);

$response['status'] = 'success';
$response['message'] = 'Message saved and sent.';
$response['messageId'] = (int) $messageId;
dolichat_send_json_and_exit($response);
