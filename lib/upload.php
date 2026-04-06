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
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(array('status' => 'error', 'message' => 'Include of main fails'));
    exit;
}
require_once DOL_DOCUMENT_ROOT . '/core/lib/files.lib.php';
dol_include_once('/dolichat/class/dolichat.class.php');

header('Content-Type: application/json; charset=UTF-8');

global $db;
$dolichat = new dolichat($db);

$eintrag = GETPOST('eintrag', 'restricthtml');
$eintrag = addslashes($eintrag);
$eintrag = str_replace('&lt;br&gt;', '<br>', $eintrag);

$cuser = GETPOST('cuser', 'alphanohtml');
if ($cuser === '-1') {
    $cuser = '0';
}
$testFgroup = substr((string) $cuser, 0, 1);

$upload = 1;
if ($testFgroup === 'G') {
    $rowidG = (int) substr((string) $cuser, 1);
    $sql = 'SELECT G_upload FROM ' . MAIN_DB_PREFIX . 'chatgroup WHERE rowid = ' . $rowidG;
    $resql = $db->query($sql);
    if ($resql) {
        $obj = $db->fetch_object($resql);
        if ($obj) {
            $upload = (int) $obj->G_upload;
        }
    }
}
if ((int) $upload !== 1) {
    echo json_encode(array('status' => 'error', 'message' => 'Uploading to this chat is currently disabled.'));
    exit;
}

if (empty($cuser) || $cuser === '0') {
    echo json_encode(array('status' => 'error', 'message' => 'Please select a chat or contact first.'));
    exit;
}

if (empty($_FILES['upl'])) {
    echo json_encode(array('status' => 'error', 'message' => 'No file was uploaded.'));
    exit;
}

if ((int) $_FILES['upl']['error'] !== 0) {
    echo json_encode(array('status' => 'error', 'message' => 'Upload failed with error code ' . (int) $_FILES['upl']['error']));
    exit;
}

$not_allowed = array('exe', 'php', 'html', 'js', 'py');
$allowedImageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg');
$extension = strtolower(pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION));

if (in_array($extension, $not_allowed, true)) {
    echo json_encode(array('status' => 'error', 'message' => 'This file type is not allowed.'));
    exit;
}

if (empty($_FILES['upl']['tmp_name']) || !is_uploaded_file($_FILES['upl']['tmp_name'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Temporary upload file is missing.'));
    exit;
}

$filesize = filesize($_FILES['upl']['tmp_name']);
if ($filesize === false) {
    echo json_encode(array('status' => 'error', 'message' => 'Could not read file size.'));
    exit;
}
if ($filesize >= 10000000) {
    echo json_encode(array('status' => 'error', 'message' => 'File size is too large.'));
    exit;
}

$rowid = $dolichat->getLatestChatRowId() + 1;
if (GETPOSTINT('rowid') > 0) {
    $rowid = GETPOSTINT('rowid');
}

$cleanFileName = dol_sanitizeFileName($_FILES['upl']['name']);
$baseDir = DOL_DATA_ROOT . '/dolichat/uploads/' . $user->id;
$pathDir = $baseDir . '/' . $rowid;
$thumbDir = $baseDir . '/t_' . $rowid;
dol_mkdir($baseDir);
dol_mkdir($pathDir);
dol_mkdir($thumbDir);

$path = $pathDir . '/' . $cleanFileName;
$t_path = $thumbDir . '/' . $cleanFileName;

if (!move_uploaded_file($_FILES['upl']['tmp_name'], $path)) {
    echo json_encode(array('status' => 'error', 'message' => 'The uploaded file could not be stored.'));
    exit;
}

$dolichat->createChatPicture($rowid, $cleanFileName, $user->id);

if (GETPOSTINT('N') === 1) {
    $messageId = $dolichat->createMessage($user, $cuser, '%picto=notext', $rowid);
    if ($messageId <= 0) {
        echo json_encode(array('status' => 'error', 'message' => !empty($dolichat->error) ? $dolichat->error : 'The upload was stored, but the chat message could not be created.'));
        exit;
    }
}

if (in_array($extension, $allowedImageExtensions, true) && function_exists('getimagesize')) {
    $imgsz = @getimagesize($path);
    if (is_array($imgsz) && !empty($imgsz[0]) && !empty($imgsz[1])) {
        $maxHeight = 473;
        $maxWidth = 498;
        $ratio = min($maxWidth / $imgsz[0], $maxHeight / $imgsz[1], 1);
        $newWidth = max(1, (int) round($imgsz[0] * $ratio));
        $newHeight = max(1, (int) round($imgsz[1] * $ratio));
        $imgh = imagecreatetruecolor($newWidth, $newHeight);
        $imgh2 = null;

        if ($extension === 'jpg' || $extension === 'jpeg') {
            $imgh2 = @imagecreatefromjpeg($path);
        } elseif ($extension === 'png') {
            $imgh2 = @imagecreatefrompng($path);
        } elseif ($extension === 'gif') {
            copy($path, $t_path);
        }

        if ((is_resource($imgh2)) || (PHP_VERSION_ID >= 80000 && $imgh2 instanceof \GdImage)) {
            imagecopyresampled($imgh, $imgh2, 0, 0, 0, 0, $newWidth, $newHeight, $imgsz[0], $imgsz[1]);
            imagejpeg($imgh, $t_path, 60);
            imagedestroy($imgh2);
            imagedestroy($imgh);
        }
    }
}

echo json_encode(array(
    'status' => 'success',
    'message' => 'Upload completed.',
    'rowid' => (int) $rowid,
    'filename' => $cleanFileName,
));
exit;
