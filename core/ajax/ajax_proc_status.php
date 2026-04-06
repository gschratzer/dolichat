<?php  
//Config auslesen 
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
dol_include_once('/dolichat/class/dolichat.class.php');

$dolichat = new dolichat($db);

function dolichatStatusJsonResponse($status, $message, $extra = array())
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
    }
    $payload = array_merge(
        array(
            'status' => $status,
            'message' => $message,
        ),
        is_array($extra) ? $extra : array()
    );
    echo json_encode($payload);
    exit;
}

    $ges = GETPOSTINT('ges_id');
    $id = GETPOSTINT('id');
    $del = GETPOSTINT('del');
    $pruf = GETPOSTINT('pruf');
    $userid = GETPOSTINT('userid');
    $tmp_userid = GETPOSTINT('tmp_user');
    $chat_stat = GETPOSTINT('chat_stat');
    $get_user_info = GETPOST('get_user_info', 'alpha');
    dol_syslog("DOLICHAT status hit: uri=" . $_SERVER['REQUEST_URI'], LOG_WARNING);
    dol_syslog(
    "DOLICHAT params: id=" . $id .
    " del=" . $del .
    " ges_id=" . $ges .
    " pruf=" . $pruf .
    " userid=" . $userid .
    " tmp_user=" . $tmp_userid .
    " chat_stat=" . $chat_stat,
    LOG_WARNING
    );

    /**
    * DELETE FIRST
    * Must run before any polling/status logic.
    */
    if ((int) $id > 0 && (int) $del === 1) {
    dol_syslog("DOLICHAT delete branch entered for message id=" . $id, LOG_WARNING);

    if (empty($user->id)) {
        dolichatStatusJsonResponse('error', 'User not authenticated.', array('messageId' => (int) $id));
    }

    $row = $dolichat->getMessageById((int) $id);
    if (!is_object($row)) {
        dolichatStatusJsonResponse('error', 'Message not found.', array('messageId' => (int) $id));
    }

    // Prefer a class method if you have one:
    // if (!$dolichat->canDeleteMessage($row, $user)) { ... }

    $ownerId = 0;
    if (isset($row->user_id) && (int) $row->user_id > 0) {
        $ownerId = (int) $row->user_id;
    }

    if ($ownerId !== (int) $user->id && empty($user->admin)) {
        dolichatStatusJsonResponse('error', 'You can only delete your own messages.', array('messageId' => (int) $id));
    }

    /**
     * Best solution:
     * Move picture/file cleanup into dolichat.class.php and call one method here:
     *
     * $deleteResult = $dolichat->deleteMessageWithAssets((int) $id, $user);
     *
     * For now, if deleteMessageById() in your class already handles cleanup,
     * this controller stays clean.
     */
    $deleteResult = $dolichat->deleteMessageById((int) $id);

    if ((int) $deleteResult <= 0) {
        $errorMessage = !empty($dolichat->error) ? $dolichat->error : 'Message could not be deleted.';
        dol_syslog("DOLICHAT delete failed for id=" . $id . " error=" . $errorMessage, LOG_WARNING);
        dolichatStatusJsonResponse('error', $errorMessage, array('messageId' => (int) $id));
    }

    $verifyDeleted = $dolichat->getMessageById((int) $id);
    if (is_object($verifyDeleted)) {
        dol_syslog("DOLICHAT delete verify failed for id=" . $id, LOG_WARNING);
        dolichatStatusJsonResponse('error', 'Delete could not be verified.', array('messageId' => (int) $id));
    }

    dol_syslog("DOLICHAT delete success for id=" . $id, LOG_WARNING);
    dolichatStatusJsonResponse('success', 'Message deleted.', array('messageId' => (int) $id));
}

/**
 * MARK ONE MESSAGE AS SEEN
 */
if ((int) $id > 0 && (int) $del === 0) {
    $dolichat->markMessageSeen((int) $id);
    exit;
}

/**
 * MARK RECEIVED MESSAGES AS SEEN
 */
if ((int) $ges > 0) {
    $dolichat->markMessagesSeenForReceiver((int) $ges, (int) $user->id);
    exit;
}

/**
 * STATUS / ONLINE USERS
 */
if ((int) $pruf === 1) {
    if (empty($chat_stat)) {
        $chat_stat = 1;
    }

    $dolichat->updateChatStat((int) $user->id, (int) $chat_stat);

    $chat_users_stats = array();
    foreach ($dolichat->listChatStats() as $stat) {
        if ((int) $stat->user_id !== (int) $user->id) {
            $chat_users_stats[$stat->user_id] = (strtotime($stat->last_stat) > strtotime('-10 sec')) ? $stat->online : 0;
        }
    }

        $rowid = $dolichat->getLatestIncomingRowId((int) $user->id, (int) $userid);
        if(is_array($chat_users_stats))
	        foreach($chat_users_stats as $key => $chat_user)
	        {
	            echo '%<|>%';
        	    echo $key;
	            echo '%<>%';
	            echo $chat_user;
	        }
	    }
	    elseif($pruf == 2)
	    {
	        $first = true;
	        foreach ($dolichat->getUnreadSenders($user->id) as $gese) {
        	    $rowsUnread = $dolichat->getUnreadMessagesFromSender($gese->user_id, $user->id);
	            $cn_gesehen = count($rowsUnread);
	            $row = $cn_gesehen > 0 ? $rowsUnread[0] : null;
	            if (!$row) {
        	        continue;
	            }

        	    $rowid = $row->rowid;
	            if($user->id != $row->user_id) $ruser = $row->user_id;
        	    if($user->id != $row->privat) $ruser = $row->privat;

            if($first == false) echo '%<|>%';
            if($row->chattextblob) echo $rowid.'%<>%'.$ruser.'%<>%'.base64_decode($row->chattextblob).'%<>%'.date('H:i', strtotime($row->timestamp)).'%<>%'.$user->id.'%<>%'.$row->user_id; // .'-'.$sql
            elseif($row->chattext) echo $rowid.'%<>%'.$ruser.'%<>%'.$row->chattext.'%<>%'.date('H:i', strtotime($row->timestamp)).'%<>%'.$user->id.'%<>%'.$row->user_id; // .'-'.$sql
            echo '%<>%'.$cn_gesehen;

            $staticuser=new User($db);
            $staticuser->fetch($row->user_id);

            echo '%<>%'.($staticuser->lastname).' '.($staticuser->firstname);

            $first = false;
        }
    }

