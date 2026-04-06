<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       dev/skeletons/skeleton_class.class.php
 *  \ingroup    mymodule othermodule1 othermodule2
 *  \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *				Put here some comments
 */

// Put here all includes required by your class file
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';

$langs->load("dolichat@dolichat");

//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *	Put here description of your class
 */
class dolichat extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)

		/**
		 *  Constructor
		 *
		 *  @param	DoliDb		$db      Database handler
		 */
		function __construct($db)
		{
			$this->db = $db;
			return 1;
		}

		/**
		 * Backward-compatible constructor wrapper.
		 *
		 * @param DoliDB $db Database handler
		 * @return void
		 */
		function dolichat($db)
		{
			self::__construct($db);
		}


		/**
		 *  Create object into database
		 *
		 *  @param	User	$user        User that creates
		 *  @param  int		$notrigger   0=launch triggers after, 1=disable triggers
		 *  @return int      		   	 <0 if KO, Id of created object if OK
		 */

	function selectGuser($option='', $userid=''){
		// On recherche les utilisateurs
		$sql = "SELECT DISTINCT u.rowid, u.lastname as lastname, u.firstname, u.statut, u.login, u.admin, u.entity";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= ", e.label";
		}
		$sql.= " FROM ".MAIN_DB_PREFIX ."user as u";

		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."entity as e ON e.rowid=u.entity";            
			if ($force_entity) $sql.= " WHERE u.entity IN (0,".$force_entity.")";
			else $sql.= " WHERE u.entity IS NOT NULL";
		}
		else
		{
			if (! empty($conf->multicompany->transverse_mode))
			{
				$sql.= ", ".MAIN_DB_PREFIX."usergroup_user as ug";
				$sql.= " WHERE ug.fk_user = u.rowid";
				$sql.= " AND ug.entity = ".$conf->entity;
			}
			else
			{
				//$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
			}
		}
		if (! empty($user->societe_id)) $sql.= " AND u.fk_societe = ".$user->societe_id;

		if (!empty($includeUsers)) {
            if (is_array($includeUsers)) {
                $sql .= " AND u.rowid IN ('".implode("','", $includeUsers)."')";
            } else {
                $sql .= " AND u.rowid IN ('".$includeUsers."')";
            }
        }
		$sql.= " Where u.statut<>0 ";
		$sql.= " AND u.rowid>1 ";
		$sql.= " AND u.rowid <> '".$userid."'";
		$sql.= " AND u.rowid!='".$user->id."' ";

		$sql.= " ORDER BY u.lastname ASC";
		//print $sql;
		dol_syslog(get_class($this)."::select_dolusers sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);

			$i = 0;

			$out.= '<select class="flat '.$class.'" '.$option.' id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').'>';
			//$out.= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected="selected"':'').'>'.$langs->trans('ALLE').'</option>'."\n";

			$out.= '<option value="0"></option>';
			while ($i < $num)
			{
				
				$obj = $this->db->fetch_object($resql);
				$staticuser=new User($this->db);
				$staticuser->fetch($obj->rowid);
				$staticuser->getrights('dolichat');
				if($staticuser->rights->dolichat->UseChat){
				//$userstatic->id=$obj->rowid;
				//$userstatic->lastname=$obj->lastname;
				//$userstatic->firstname=$obj->firstname;

					$out.= '<option value="'.$obj->rowid.'">';                    
					$out.= $obj->lastname.' '.$obj->firstname;
					$out.= '</option>';
				}
				$i++;
			}

			$out.= '</select>';

		}
		else
		{
			dol_print_error($this->db);
		}

		print $out;
	}

	function get_Dolichat_user()
	{
		global $conf, $user;
		// On recherche les utilisateurs
		$sql = "SELECT DISTINCT u.rowid, u.lastname as lastname, u.firstname, u.statut, u.login, u.admin, u.entity";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= ", e.label";
		}
		$sql.= " FROM ".MAIN_DB_PREFIX ."user as u";        

		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."entity as e ON e.rowid=u.entity";            
			if ($force_entity) $sql.= " WHERE u.entity IN (0,".$force_entity.")";
			else $sql.= " WHERE u.entity IS NOT NULL";
		}
		else
		{
			if (! empty($conf->multicompany->transverse_mode))
			{
				$sql.= ", ".MAIN_DB_PREFIX."usergroup_user as ug";
				$sql.= " WHERE ug.fk_user = u.rowid";
				$sql.= " AND ug.entity = ".$conf->entity;
			}
			else
			{
				$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
			}
		}

		if (!empty($includeUsers)) {
            if (is_array($includeUsers)) {
                $sql .= " AND u.rowid IN ('".implode("','", $includeUsers)."')";
            } else {
                $sql .= " AND u.rowid IN ('".$includeUsers."')";
            }
        }
		$sql.= " AND u.statut <> 0 ";
		$sql.= " AND u.rowid > 0 ";
		$sql.= " AND u.rowid!='".$user->id."' ";        
		$sql.= " ORDER BY u.lastname ASC";

		//echo $sql;
		dol_syslog(get_class($this)."::select_dolusers sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			while($obj = $this->db->fetch_object($resql))
			{
				$obj_arr[] = $obj;
			}
			return $obj_arr;
		}
	}

	function get_last_message($user_id = '', $pr_user = 0)
	{
		$sql = "SELECT * FROM `" . MAIN_DB_PREFIX . "chattext` ";
		$sql.= " Where 1=1";
		$sql.= " And (user_id = '".$user_id."'";
		$sql.= " And privat = '".$pr_user."'";
		$sql.= " or user_id = '".$pr_user."'";
		$sql.= " And privat = '".$user_id."')";
		if($user_id > 0) $sql.= " ORDER BY `" . MAIN_DB_PREFIX . "chattext`.`rowid` DESC";
		
		dol_syslog(get_class($this)."::select_dolusers sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			if($obj)
			{
				return $obj;
			}
		}
	}


    /**
     * Return one user parameter value.
     *
     * @param int $userId User id
     * @param string $param Parameter name
     * @param mixed $default Default value
     * @return mixed
     */
    public function getUserParamValue($userId, $param, $default = '')
    {
        $sql = 'SELECT value FROM ' . MAIN_DB_PREFIX . "user_param WHERE fk_user = " . ((int) $userId) . " AND param = '" . $this->db->escape($param) . "'";
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if (is_object($obj) && isset($obj->value)) {
                return $obj->value;
            }
        }

        return $default;
    }

    /**
     * Return one user parameter row.
     *
     * @param int $userId User id
     * @param string $param Parameter name
     * @return object|null
     */
    public function getUserParamRow($userId, $param)
    {
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . "user_param WHERE fk_user = " . ((int) $userId) . " AND param = '" . $this->db->escape($param) . "'";
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if (is_object($obj)) {
                return $obj;
            }
        }

        return null;
    }

    /**
     * Create one chat message.
     *
     * @param User $author Current user
     * @param string|int $target Target user or special code
     * @param string $message Message text
     * @param int $picSavedId Pending picture message id
     * @return int
     */
    public function createMessage($author, $target, $message, $picSavedId = 0)
    {
        $this->error = '';
        $this->errors = array();

        if (!is_object($author) || empty($author->id)) {
            $this->error = 'Invalid author';
            return -1;
        }

        $message = trim((string) $message);
        $target = (string) $target;
        $picSavedId = (int) $picSavedId;

        $pictureText = '';
        if ($picSavedId > 0) {
            $pics = $this->getPicturesByMessageId($picSavedId);
            foreach ($pics as $pic) {
                $pictureText .= ' %picto=' . $picSavedId . '/' . $pic->PicName;
            }
        }

        if ($message === '%picto=notext') {
            $message = '';
        }

        $fullMessage = $message . $pictureText;
        if ($fullMessage === '') {
            $this->error = 'Empty message';
            return -2;
        }

        $privatUser = $target;
        $privatName = '';
        if (is_numeric($target) && (int) $target > 0) {
            $targetUser = new User($this->db);
            if ($targetUser->fetch((int) $target) > 0) {
                $privatName = ' sagt zu ' . $targetUser->lastname . ' ' . $targetUser->firstname;
            }
        } elseif ((string) $target === '-1') {
            $privatUser = '0';
        }

        $baseMessage = base64_encode($fullMessage);
        $sql = 'INSERT INTO ' . MAIN_DB_PREFIX . 'chattext ('
            . 'chattextblob, user, user_id, privat, privat_name, timestamp'
            . ') VALUES ('
            . "'" . $this->db->escape($baseMessage) . "', "
            . "'" . $this->db->escape(trim($author->lastname . ' ' . $author->firstname)) . "', "
            . ((int) $author->id) . ', '
            . "'" . $this->db->escape($privatUser) . "', "
            . "'" . $this->db->escape($privatName) . "', CURRENT_TIMESTAMP)";

        if (!$this->db->query($sql)) {
            $this->error = $this->db->lasterror();
            return -3;
        }

        $messageId = (int) $this->db->last_insert_id(MAIN_DB_PREFIX . 'chattext');
        if ($messageId <= 0) {
            $messageId = $this->getLatestChatRowId();
        }

        if ($picSavedId > 0 && $messageId > 0) {
            $this->reassignChatPictures($picSavedId, $messageId);
        }

        return $messageId;
    }

    public function getLatestChatRowId()
    {
        $sql = 'SELECT rowid FROM ' . MAIN_DB_PREFIX . 'chattext ORDER BY rowid DESC LIMIT 1';
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if (is_object($obj) && isset($obj->rowid)) {
                return (int) $obj->rowid;
            }
        }

        return 0;
    }

    public function countPrivateConversationMessages($userId, $otherUserId, $days = 0)
    {
        $sql = 'SELECT COUNT(rowid) as total FROM ' . MAIN_DB_PREFIX . 'chattext'
            . ' WHERE ((privat = ' . ((int) $otherUserId) . ' AND user_id = ' . ((int) $userId) . ')'
            . ' OR (privat = ' . ((int) $userId) . ' AND user_id = ' . ((int) $otherUserId) . '))';
        if ((int) $days > 0) {
            $sql .= ' AND timestamp > now() - INTERVAL ' . ((int) $days) . ' DAY';
        }
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if (is_object($obj) && isset($obj->total)) {
                return (int) $obj->total;
            }
        }
        return 0;
    }

    public function fetchMessagesForContext($currentUserId, $cuser, $days = 0, $fromrow = 0, $alldays = 0)
    {
        $rows = array();
        $sql = '';
        $cuser = (string) $cuser;

        if ($fromrow > 0) {
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chattext WHERE (privat IN (0, ' . ((int) $currentUserId) . ') OR user_id = ' . ((int) $currentUserId) . ')'
                . ' AND rowid > ' . ((int) $fromrow) . ' ORDER BY timestamp ASC LIMIT 10';
        } elseif (is_numeric($cuser) && (int) $cuser > 0) {
            $other = (int) $cuser;
            $total = $this->countPrivateConversationMessages($currentUserId, $other, $days);
            if ($total < 10) {
                $total = 10;
            }
            $offset = $alldays ? 0 : max(0, $total - 10);
            $limit = $alldays ? max(0, $total - 10) : 10;
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chattext WHERE ((privat = ' . $other . ' AND user_id = ' . ((int) $currentUserId) . ')'
                . ' OR (privat = ' . ((int) $currentUserId) . ' AND user_id = ' . $other . '))';
            if ((int) $days > 0) {
                $sql .= ' AND timestamp > now() - INTERVAL ' . ((int) $days) . ' DAY';
            }
            $sql .= ' ORDER BY timestamp ASC';
            if ($limit > 0) {
                $sql .= ' LIMIT ' . $offset . ', ' . $limit;
            }
        } elseif ($cuser === '0') {
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chattext WHERE privat = 0';
            if ((int) $days > 0) {
                $sql .= ' AND timestamp > now() - INTERVAL ' . ((int) $days) . ' DAY';
            }
            $sql .= ' ORDER BY timestamp ASC';
        } elseif ($cuser === '-1') {
            $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chattext WHERE ((privat IN (0, ' . ((int) $currentUserId) . ') OR user_id = ' . ((int) $currentUserId) . '))';
            if ((int) $days > 0) {
                $sql .= ' AND timestamp > now() - INTERVAL ' . ((int) $days) . ' DAY';
            }
            if (!$alldays) {
                $sql .= ' AND rowid IN (SELECT foo.rowid FROM (SELECT rowid FROM ' . MAIN_DB_PREFIX . 'chattext ORDER BY rowid DESC LIMIT 10) AS foo)';
            } else {
                $sql .= ' AND rowid NOT IN (SELECT foo.rowid FROM (SELECT rowid FROM ' . MAIN_DB_PREFIX . 'chattext ORDER BY rowid DESC LIMIT 10) AS foo)';
            }
            $sql .= ' ORDER BY timestamp ASC';
            if (!$alldays) {
                $sql .= ' LIMIT 10';
            }
        }

        if ($sql === '') {
            return $rows;
        }

        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $rows[] = $obj;
            }
        }

        return $rows;
    }

    public function getMessageById($messageId)
    {
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chattext WHERE rowid = ' . ((int) $messageId);
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if (is_object($obj)) {
                return $obj;
            }
        }
        return null;
    }

    public function getPicturesByMessageId($messageId)
    {
        $rows = array();
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chatpic WHERE MsgID = ' . ((int) $messageId) . ' ORDER BY rowid DESC';
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $rows[] = $obj;
            }
        }
        return $rows;
    }

    public function createChatPicture($messageId, $picName, $userId)
    {
        $sql = 'INSERT INTO ' . MAIN_DB_PREFIX . "chatpic (PicName, MsgID, UserID) VALUES ('" . $this->db->escape($picName) . "', " . ((int) $messageId) . ', ' . ((int) $userId) . ')';
        if (!$this->db->query($sql)) {
            $this->error = $this->db->lasterror();
            return -1;
        }
        return 1;
    }

    public function reassignChatPictures($oldMessageId, $newMessageId)
    {
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . 'chatpic SET MsgID = ' . ((int) $newMessageId) . ' WHERE MsgID = ' . ((int) $oldMessageId);
        return $this->db->query($sql) ? 1 : -1;
    }

    public function deletePictureByMessageAndName($messageId, $picName)
    {
        $sql = 'DELETE FROM ' . MAIN_DB_PREFIX . 'chatpic WHERE MsgID = ' . ((int) $messageId) . " AND PicName = '" . $this->db->escape($picName) . "'";
        return $this->db->query($sql) ? 1 : -1;
    }

    public function deleteMessageById($messageId)
    {
        $sql = 'DELETE FROM ' . MAIN_DB_PREFIX . 'chattext WHERE rowid = ' . ((int) $messageId);
        return $this->db->query($sql) ? 1 : -1;
    }

    public function markMessageSeen($messageId)
    {
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . 'chattext SET gesehen = 1 WHERE rowid = ' . ((int) $messageId);
        return $this->db->query($sql) ? 1 : -1;
    }

    public function markMessagesSeenForReceiver($fromUserId, $toUserId)
    {
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . 'chattext SET gesehen = 1 WHERE user_id = ' . ((int) $fromUserId) . ' AND privat = ' . ((int) $toUserId) . ' AND gesehen = 0';
        return $this->db->query($sql) ? 1 : -1;
    }

    public function getOrCreateChatStat($userId)
    {
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chatstat WHERE user_id = ' . ((int) $userId);
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if (is_object($obj)) {
                return $obj;
            }
        }
        $sql = 'INSERT INTO ' . MAIN_DB_PREFIX . 'chatstat (user_id, online, last_stat) VALUES (' . ((int) $userId) . ', 1, CURRENT_TIMESTAMP)';
        $this->db->query($sql);
        $resql = $this->db->query('SELECT * FROM ' . MAIN_DB_PREFIX . 'chatstat WHERE user_id = ' . ((int) $userId));
        return $resql ? $this->db->fetch_object($resql) : null;
    }

    public function updateChatStat($userId, $online)
    {
        $stat = $this->getOrCreateChatStat($userId);
        $newChecks = is_object($stat) && isset($stat->checks) ? ((int) $stat->checks + 1) : 1;
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . 'chatstat SET checks = ' . $newChecks . ', online = ' . ((int) $online) . ' WHERE user_id = ' . ((int) $userId);
        return $this->db->query($sql) ? 1 : -1;
    }

    public function listChatStats()
    {
        $rows = array();
        $resql = $this->db->query('SELECT * FROM ' . MAIN_DB_PREFIX . 'chatstat');
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $rows[] = $obj;
            }
        }
        return $rows;
    }

    public function getLatestIncomingRowId($currentUserId, $fromUserId = 0)
    {
        $sql = 'SELECT rowid FROM ' . MAIN_DB_PREFIX . 'chattext';
        if ((int) $fromUserId > 0) {
            $sql .= ' WHERE user_id = ' . ((int) $fromUserId) . ' AND privat = ' . ((int) $currentUserId);
        }
        $sql .= ' ORDER BY rowid DESC LIMIT 1';
        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if (is_object($obj) && isset($obj->rowid)) {
                return (int) $obj->rowid;
            }
        }
        return 0;
    }

    public function getUnreadSenders($currentUserId)
    {
        $rows = array();
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chattext WHERE privat = ' . ((int) $currentUserId) . ' AND gesehen = 0 GROUP BY user_id';
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $rows[] = $obj;
            }
        }
        return $rows;
    }

    public function getUnreadMessagesFromSender($senderId, $currentUserId)
    {
        $rows = array();
        $sql = 'SELECT rowid, user_id, privat, chattext, chattextblob, timestamp FROM ' . MAIN_DB_PREFIX . 'chattext WHERE user_id = ' . ((int) $senderId) . ' AND privat = ' . ((int) $currentUserId) . ' AND gesehen = 0 ORDER BY rowid DESC';
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $rows[] = $obj;
            }
        }
        return $rows;
    }

    public function appendBroadcastSeenUser($messageId, $userId)
    {
        $message = $this->getMessageById($messageId);
        if (!is_object($message)) {
            return -1;
        }
        $existing = trim((string) $message->gesehen_Broadcast);
        if ($existing === '') {
            $newValue = (string) ((int) $userId);
        } else {
            $ids = explode(', ', $existing);
            if (in_array((string) ((int) $userId), $ids, true) || in_array((int) $userId, $ids, true)) {
                return 1;
            }
            $newValue = $existing . ', ' . ((int) $userId);
        }
        $sql = 'UPDATE ' . MAIN_DB_PREFIX . "chattext SET gesehen_Broadcast = '" . $this->db->escape($newValue) . "' WHERE rowid = " . ((int) $messageId);
        return $this->db->query($sql) ? 1 : -1;
    }


    public function getMessagesForBroadcastSeen($groupMode = false)
    {
        $rows = array();
        $sql = 'SELECT * FROM ' . MAIN_DB_PREFIX . 'chattext WHERE ';
        $sql .= $groupMode ? "privat LIKE '%G%'" : 'privat = 0';
        $sql .= ' ORDER BY timestamp ASC';
        $resql = $this->db->query($sql);
        if ($resql) {
            while ($obj = $this->db->fetch_object($resql)) {
                $rows[] = $obj;
            }
        }
        return $rows;
    }

	/**
	 *	Return select list of users
	 *
	 *  @param	string	$selected       User id or user object of user preselected. If -1, we use id of current user.
	 *  @param  string	$htmlname       Field name in form
	 *  @param  int		$show_empty     0=liste sans valeur nulle, 1=ajoute valeur inconnue
	 *  @param  array	$exclude        Array list of users id to exclude
	 * 	@param	int		$disabled		If select list must be disabled
	 *  @param  array	$include        Array list of users id to include
	 * 	@param	array	$enableonly		Array list of users id to be enabled. All other must be disabled
	 *  @param	int		$force_entity	0 or Id of environment to force
	 *  @param	int		$maxlength		Maximum length of string into list (0=no limit)
	 *  @param	int		$showstatus		0=show user status only if status is disabled, 1=always show user status into label, -1=never show user status
	 * 	@return	string					HTML select string
	 */
	function select($selected='', $htmlname='userid', $show_empty=0, $exclude='', $class='', $option='', $onlyarray=0, $mobile=0, $confvari=0, $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0)
	{
		global $conf,$user,$langs;
		// 52901 rights <--- from user to show
		// If no preselected user defined, we take current user

		// Permettre l'exclusion d'utilisateurs

		// Permettre l'inclusion d'utilisateurs
		if (is_array($include))	$includeUsers = implode("','",$include);

		$out='';

		// On recherche les utilisateurs
		$sql = "SELECT DISTINCT u.rowid, u.lastname as lastname, u.firstname, u.statut, u.login, u.admin, u.entity";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= ", e.label";
		}
		$sql.= " FROM ".MAIN_DB_PREFIX ."user as u";        
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."entity as e ON e.rowid=u.entity";            
			if ($force_entity) $sql.= " WHERE u.entity IN (0,".$force_entity.")";
			else $sql.= " WHERE u.entity IS NOT NULL";
		}
		else
		{
			if (! empty($conf->multicompany->transverse_mode))
			{
				$sql.= ", ".MAIN_DB_PREFIX."usergroup_user as ug";
				$sql.= " WHERE ug.fk_user = u.rowid";
				$sql.= " AND ug.entity = ".$conf->entity;
			}
			else
			{
				$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
			}
		}
		if (! empty($user->societe_id)) $sql.= " AND u.fk_societe = ".$user->societe_id;

		if (!empty($includeUsers)) {
            if (is_array($includeUsers)) {
                $sql .= " AND u.rowid IN ('".implode("','", $includeUsers)."')";
            } else {
                $sql .= " AND u.rowid IN ('".$includeUsers."')";
            }
        }
		$sql.= " AND u.statut<>0 ";
		$sql.= " AND u.rowid>1 ";
		$sql.= " AND u.rowid!='".$user->id."' ";        
		$sql.= " ORDER BY u.lastname ASC";

		dol_syslog(get_class($this)."::select_dolusers sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
					
				$out.= '<select class="flat '.$class.'" '.$option.' id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').'>';
				if($confvari != 1){
					if ($show_empty) $out.= '<option value="-1"'.((empty($selected) || $selected==-1)?' selected="selected"':'').'>'.$langs->trans('ALLE').'</option>'."\n";
					$out.='<option value="0">'.$langs->trans('Broadcast').'</option>';
				}else{
					$out.= '<option></option>';
				}
				$userstatic=new User($this->db);

				$sqlG ="SELECT * ";
				$sqlG.="FROM  ".MAIN_DB_PREFIX ."chatgroup ";
				$sqlG.="WHERE G_user_id LIKE  '%".$user->id."%'";
				dol_syslog(get_class($this)."::select_dolG  sql=".$sqlG);
				$resqlG=$this->db->query($sqlG);
				$numG = $this->db->num_rows($resqlG);
				$i = 0;
				if($confvari != 1){
				while ($i < $numG){

					$objG = $this->db->fetch_object($resqlG);
					$checksel = "G".$objG->rowid;
	
					if ((is_object($selected) && $selected->id == $checksel) || (! is_object($selected) && $selected == $checksel))
					{
						$out.= '<option value="G'.$objG->rowid.'"';                        
						$out.= ' selected="selected">';
						$out.= $objG->G_name;
					}
					else
					{
						$out.= '<option value="G'.$objG->rowid.'">';
						$out.= $objG->G_name;
					}


												//$ChatUserArrayBlock[$objG->rowid]=$objG->G_name;
							$ChatUserArray['G'.$objG->rowid]=$objG->G_name;
					
					
					$out.= '</option>';
					$i++;
				}
				}
				$i = 0;

					$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_BLACKLIST';";
					$resqlP= $this->db->query($sqlP);
					$objP = $this->db->fetch_object($resqlP);
					$block = $objP->entity;
					
					$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_WHITELIST';";
					$resqlP= $this->db->query($sqlP);
					$objP = $this->db->fetch_object($resqlP);
					$white = $objP->entity;
					
					//if($block==1) $check1 = 'anzeigen';
					//if($white==1) $check2 = 'anzeigen';

				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);

					$userstatic->id=$obj->rowid;
					$userstatic->lastname=$obj->lastname;
					$userstatic->firstname=$obj->firstname;
					if($white==1){
						$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_WHITELIST';";
						$resqlP= $this->db->query($sqlP);
						$objP = $this->db->fetch_object($resqlP);
						$ids_ex = explode(", ", $objP->value); 
						if(in_array($obj->rowid, $ids_ex)){
							foreach ($ids_ex as &$value) {
								if($value == $obj->rowid){
									$check = '';
								}
							}                    
						}elseif($obj->rowid == $objP->value || $confvari == 1){
							$check = '';
						}elseif($confvari == 0){
							$check = "1";
						}
					}elseif($block==1){
						$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_BLACKLIST';";
						$resqlP= $this->db->query($sqlP);
						$objP = $this->db->fetch_object($resqlP);
						$ids_ex = explode(", ", $objP->value); 
						if(in_array($obj->rowid, $ids_ex)  && $confvari != 1){
							foreach ($ids_ex as &$value) {
								if($value == $obj->rowid){
									$check = '1';
								}
							}                    
						}elseif($obj->rowid == $objP->value && $confvari != 1){
							$check = '1';
						}else{
							$check = "";
						}
					}
					$disableline=0;

					$staticuser=new User($this->db);
					$staticuser->fetch($obj->rowid);
					$staticuser->getrights('dolichat');
					if($staticuser->rights->dolichat->UseChat){

						if($check == "" || $mobile == 1){
							if (is_array($enableonly) && count($enableonly) && ! in_array($obj->rowid,$enableonly)) $disableline=1;
		
							if ((is_object($selected) && $selected->id == $obj->rowid) || (! is_object($selected) && $selected == $obj->rowid))
							{
								$out.= '<option value="'.$obj->rowid.'"';
								if ($check) $out.= ' disabled="disabled"';
								$out.= ' selected="selected">';
							}
							else
							{
								$out.= '<option value="'.$obj->rowid.'"';
								if ($check) $out.= ' disabled="disabled"';
								$out.= '>';
							}
														
														if ($check)
														$ChatUserArrayBlock[$obj->rowid]=$userstatic->getFullName($langs, 0, 0, $maxlength);
														
														if (!$check)
							$ChatUserArray[$obj->rowid]=$userstatic->getFullName($langs, 0, 0, $maxlength);

							$out.= $userstatic->getFullName($langs, 0, 0, $maxlength);
							// Complete name with more info
							$moreinfo=0;
							if (! empty($conf->global->MAIN_SHOW_LOGIN))
							{
								$out.= ($moreinfo?' - ':' (').$obj->login;
								$moreinfo++;
							}
							if ($showstatus >= 0)
							{
								if ($obj->statut == 1 && $showstatus == 1)
								{
									$out.=($moreinfo?' - ':' (').$langs->trans('Enabled');
									$moreinfo++;
								}
							if ($obj->statut == 0)
							{
								$out.=($moreinfo?' - ':' (').$langs->trans('Disabled');
								$moreinfo++;
							}
						  }
							if (! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode) && $conf->entity == 1 && $user->admin && ! $user->entity)
							{
								if ($obj->admin && ! $obj->entity)
								{
									$out.=($moreinfo?' - ':' (').$langs->trans("AllEntities");
									$moreinfo++;
								}
								else
							 {
									$out.=($moreinfo?' - ':' (').$obj->label;
									$moreinfo++;
								}
							}
						  $out.=($moreinfo?')':'');
							$out.= '</option>';
						}
					}
					$i++;
				}
			}
			else
			{
				$out.= '<select class="flat" name="'.$htmlname.'" disabled="disabled">';
				$out.= '<option value="">'.$langs->trans("None").'</option>';
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		if($onlyarray==0){
			return $out;
		}else{
				if($mobile==1)
					return $ChatUserArrayBlock;
				else
				return $ChatUserArray;
		}
		
	}

	/**
	 *  Return select list of users
	 *
	 *  @param  string  $selected       User id or user object of user preselected. If -1, we use id of current user.
	 *  @param  string  $htmlname       Field name in form
	 *  @param  int     $show_empty     0=liste sans valeur nulle, 1=ajoute valeur inconnue
	 *  @param  array   $exclude        Array list of users id to exclude
	 *  @param  int     $disabled       If select list must be disabled
	 *  @param  array   $include        Array list of users id to include
	 *  @param  array   $enableonly     Array list of users id to be enabled. All other must be disabled
	 *  @param  int     $force_entity   0 or Id of environment to force
	 *  @param  int     $maxlength      Maximum length of string into list (0=no limit)
	 *  @param  int     $showstatus     0=show user status only if status is disabled, 1=always show user status into label, -1=never show user status
	 *  @return string                  HTML select string
	 */
	function selectO($selected='', $htmlname='userid', $show_empty=0, $exclude='', $class='', $option='', $enableonly='', $force_entity=0, $maxlength=0, $showstatus=0)
	{
		global $conf,$user,$langs;
		// 52901 rights <--- from user to show
		// If no preselected user defined, we take current user

		// Permettre l'exclusion d'utilisateurs

		// Permettre l'inclusion d'utilisateurs
		if (is_array($include)) $includeUsers = implode("','",$include);

		$out='';

		// On recherche les utilisateurs
		$sql = "SELECT DISTINCT u.rowid, u.lastname as lastname, u.firstname, u.statut, u.login, u.admin, u.entity";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= ", e.label";
		}
		$sql.= " FROM ".MAIN_DB_PREFIX ."user as u";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."entity as e ON e.rowid=u.entity";            
			if ($force_entity) $sql.= " WHERE u.entity IN (0,".$force_entity.")";
			else $sql.= " WHERE u.entity IS NOT NULL";
		}
		else
		{
			if (! empty($conf->multicompany->transverse_mode))
			{
				$sql.= ", ".MAIN_DB_PREFIX."usergroup_user as ug";
				$sql.= " WHERE ug.fk_user = u.rowid";
				$sql.= " AND ug.entity = ".$conf->entity;
			}
			else
			{
				$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
			}
		}
		if (! empty($user->societe_id)) $sql.= " AND u.fk_societe = ".$user->societe_id;

		if (!empty($includeUsers)) {
            if (is_array($includeUsers)) {
                $sql .= " AND u.rowid IN ('".implode("','", $includeUsers)."')";
            } else {
                $sql .= " AND u.rowid IN ('".$includeUsers."')";
            }
        }
		$sql.= " AND u.statut<>0 ";
		$sql.= " AND u.rowid>1 ";
		$sql.= " AND u.rowid!='".$user->id."' ";

		$sql.= " ORDER BY u.lastname ASC";

		dol_syslog(get_class($this)."::select_dolusers sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num)
			{
				$out.= '<select class="flat '.$class.'" '.$option.' id="'.$htmlname.'" name="'.$htmlname.'"'.($disabled?' disabled="disabled"':'').'>';
				$userstatic=new User($this->db);                
				
					$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_BLACKLIST';";
					$resqlP= $this->db->query($sqlP);
					$objP = $this->db->fetch_object($resqlP);
					$block = $objP->entity;
					
					$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_WHITELIST';";
					$resqlP= $this->db->query($sqlP);
					$objP = $this->db->fetch_object($resqlP);
					$white = $objP->entity;
					
				$i = 0;
				while ($i < $num)
				{
					$obj = $this->db->fetch_object($resql);

					$userstatic->id=$obj->rowid;
					$userstatic->lastname=$obj->lastname;
					$userstatic->firstname=$obj->firstname;
					if($white==1){
						$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_WHITELIST';";
						$resqlP= $this->db->query($sqlP);
						$objP = $this->db->fetch_object($resqlP);
						$ids_ex = explode(", ", $objP->value); 
						if(in_array($obj->rowid, $ids_ex)){
							foreach ($ids_ex as &$value) {
								if($value == $obj->rowid){
									$check = '';
								}
							}                    
						}elseif($obj->rowid == $objP->value){
							$check = '';
						}else{
							$check = "1";
						}
					}elseif($block==1){
						$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_BLACKLIST';";
						$resqlP= $this->db->query($sqlP);
						$objP = $this->db->fetch_object($resqlP);
						$ids_ex = explode(", ", $objP->value); 
						if(in_array($obj->rowid, $ids_ex)){
							foreach ($ids_ex as &$value) {
								if($value == $obj->rowid){
									$check = '1';
								}
							}                    
						}elseif($obj->rowid == $objP->value){
							$check = '1';
						}else{
							$check = "";
						}
					}
					$disableline=0;
					$staticuser=new User($this->db);
					$staticuser->fetch($obj->rowid);
					$staticuser->getrights('dolichat');
					if($staticuser->rights->dolichat->UseChat){
						if($check == ""){
							if (is_array($enableonly) && count($enableonly) && ! in_array($obj->rowid,$enableonly)) $disableline=1;
		
							if ((is_object($selected) && $selected->id == $obj->rowid) || (! is_object($selected) && $selected == $obj->rowid))
							{
								$out.= '<option value="'.$obj->rowid.'"';
								if ($disableline) $out.= ' disabled="disabled"';
								$out.= ' selected="selected">';
							}
							else
							{
								$out.= '<option value="'.$obj->rowid.'"';
								if ($disableline) $out.= ' disabled="disabled"';
								$out.= '>';
							}
		
							$out.= $userstatic->getFullName($langs, 0, 0, $maxlength);
							// Complete name with more info
							$moreinfo=0;
							if (! empty($conf->global->MAIN_SHOW_LOGIN))
							{
								$out.= ($moreinfo?' - ':' (').$obj->login;
								$moreinfo++;
							}
							if ($showstatus >= 0)
							{
								if ($obj->statut == 1 && $showstatus == 1)
								{
									$out.=($moreinfo?' - ':' (').$langs->trans('Enabled');
									$moreinfo++;
								}
							if ($obj->statut == 0)
							{
								$out.=($moreinfo?' - ':' (').$langs->trans('Disabled');
								$moreinfo++;
							}
						   }
							if (! empty($conf->multicompany->enabled) && empty($conf->multicompany->transverse_mode) && $conf->entity == 1 && $user->admin && ! $user->entity)
							{
								if ($obj->admin && ! $obj->entity)
								{
									$out.=($moreinfo?' - ':' (').$langs->trans("AllEntities");
									$moreinfo++;
								}
								else
							 {
									$out.=($moreinfo?' - ':' (').$obj->label;
									$moreinfo++;
								}
							}
						   $out.=($moreinfo?')':'');
							$out.= '</option>';
						}
					}
					$i++;
				}
			}
			else
			{
				$out.= '<select class="flat" name="'.$htmlname.'" disabled="disabled">';
				$out.= '<option value="">'.$langs->trans("None").'</option>';
			}
			$out.= '</select>';
		}
		else
		{
			dol_print_error($this->db);
		}

		print $out;
	}
	function showuserBlocklist($blacklist="", $onlyarray=1){
		global $conf,$user,$langs;
		 // On recherche les utilisateurs
		$sql = "SELECT DISTINCT u.rowid, u.lastname as lastname, u.firstname, u.statut, u.login, u.admin, u.entity";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= ", e.label";
		}
		$sql.= " FROM ".MAIN_DB_PREFIX ."user as u";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."entity as e ON e.rowid=u.entity";            
			if ($force_entity) $sql.= " WHERE u.entity IN (0,".$force_entity.")";
			else $sql.= " WHERE u.entity IS NOT NULL";
		}
		else
		{
			if (! empty($conf->multicompany->transverse_mode))
			{
				$sql.= ", ".MAIN_DB_PREFIX."usergroup_user as ug";
				$sql.= " WHERE ug.fk_user = u.rowid";
				$sql.= " AND ug.entity = ".$conf->entity;
			}
			else
			{
				//$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
			}
		}
		if (! empty($user->societe_id)) $sql.= " AND u.fk_societe = ".$user->societe_id;

		if (!empty($includeUsers)) {
            if (is_array($includeUsers)) {
                $sql .= " AND u.rowid IN ('".implode("','", $includeUsers)."')";
            } else {
                $sql .= " AND u.rowid IN ('".$includeUsers."')";
            }
        }
		$sql.= " Where u.statut<>0 ";
		$sql.= " AND u.rowid>1 ";
		$sql.= " AND u.rowid <> '".$userid."'";
		$sql.= " AND u.rowid!='".$user->id."' ";
		
		$sql.= " ORDER BY u.lastname ASC";
		//print $sql;
		$z = 0;
		$i = 0;
		dol_syslog(get_class($this)."::select_dolusers sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			while ($i < $num)
			{   
				$check ="";
				$obj = $this->db->fetch_object($resql);
				$staticuser=new User($this->db);
				$staticuser->fetch($obj->rowid);
				$staticuser->getrights('dolichat');
				if($staticuser->rights->dolichat->UseChat){
					if($blacklist==1){
						$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_BLACKLIST';";
						$resqlP= $this->db->query($sqlP);
						$objP = $this->db->fetch_object($resqlP);
						$ids_ex = explode(", ", $objP->value); 
						if(in_array($obj->rowid, $ids_ex)){
							foreach ($ids_ex as &$value) {
								if($value == $obj->rowid){
									$check = 'checked="checked"';
									$Bloackarray[$obj->rowid]=$obj->lastname.' '.$obj->firstname;
								}
							}                    
						}elseif($obj->rowid == $objP->value){
							$check = 'checked="checked"';
							$Bloackarray[$obj->rowid]=$obj->lastname.' '.$obj->firstname;
						}else{
							$check = "";
						}
					}
					//$out.= '<tr>';
					//    $out.= '<td>';
							$out.= '<input type="checkbox" onChange="saveusertonotshow('."'".$obj->rowid."'".')" id="'.$i.'" name="'.$i.'" value="'.$obj->rowid.'" '.$check.'>';                    
								$out.= '<label for="'.$i.'">'.$obj->lastname.' '.$obj->firstname.'</label>';
								
					if($z == 4){    $out.='<p>'; $z = 0;}
					//    $out.= '</td>';
					//$out.= '</tr>';
				}
					$i++;
					$z++;
			}
		}
			if($onlyarray){
			return $Bloackarray;
		}else{
			print $out;
			}
	}
	function showuserWhitelist($whitelist="", $onlyarray=0){
		global $conf,$user,$langs;
		 // On recherche les utilisateurs
		$sql = "SELECT DISTINCT u.rowid, u.lastname as lastname, u.firstname, u.statut, u.login, u.admin, u.entity";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= ", e.label";
		}
		$sql.= " FROM ".MAIN_DB_PREFIX ."user as u";
		if (! empty($conf->multicompany->enabled) && $conf->entity == 1 && $user->admin && ! $user->entity)
		{
			$sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."entity as e ON e.rowid=u.entity";            
			if ($force_entity) $sql.= " WHERE u.entity IN (0,".$force_entity.")";
			else $sql.= " WHERE u.entity IS NOT NULL";
		}
		else
		{
			if (! empty($conf->multicompany->transverse_mode))
			{
				$sql.= ", ".MAIN_DB_PREFIX."usergroup_user as ug";
				$sql.= " WHERE ug.fk_user = u.rowid";
				$sql.= " AND ug.entity = ".$conf->entity;
			}
			else
			{
				//$sql.= " WHERE u.entity IN (0,".$conf->entity.")";
			}
		}
		if (! empty($user->societe_id)) $sql.= " AND u.fk_societe = ".$user->societe_id;

		if (!empty($includeUsers)) {
            if (is_array($includeUsers)) {
                $sql .= " AND u.rowid IN ('".implode("','", $includeUsers)."')";
            } else {
                $sql .= " AND u.rowid IN ('".$includeUsers."')";
            }
        }
		$sql.= " Where u.statut<>0 ";
		$sql.= " AND u.rowid>1 ";
		$sql.= " AND u.rowid <> '".$userid."'";
		$sql.= " AND u.rowid!='".$user->id."' ";

		$sql.= " ORDER BY u.lastname ASC";
		//print $sql;
		$z = 0;
		$i = 0;
		dol_syslog(get_class($this)."::select_dolusers sql=".$sql);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			while ($i < $num)
			{   
				$check ="";
				$obj = $this->db->fetch_object($resql);
				$staticuser=new User($this->db);
				$staticuser->fetch($obj->rowid);
				$staticuser->getrights('dolichat');
				if($staticuser->rights->dolichat->UseChat){
					if($whitelist==1){
						$sqlP="SELECT * FROM ".MAIN_DB_PREFIX."user_param WHERE fk_user = ".$user->id." AND param = 'CHAT_AUSWAHL_WHITELIST';";
						$resqlP= $this->db->query($sqlP);
						$objP = $this->db->fetch_object($resqlP);
						$ids_ex = explode(", ", $objP->value); 
						if(in_array($obj->rowid, $ids_ex)){
							foreach ($ids_ex as &$value) {
								if($value == $obj->rowid){
									$check = 'checked="checked"';
										$unBloackarray[$obj->rowid]=$obj->lastname.' '.$obj->firstname;
								}
							}                    
						}elseif($obj->rowid == $objP->value){
							$check = 'checked="checked"';
							$unBloackarray[$obj->rowid]=$obj->lastname.' '.$obj->firstname;
						}else{
							$check = "";
						}
					}
					//$out.= '<tr>';
					//    $out.= '<td>';
							$out.= '<input type="checkbox" onChange="saveusertoshow('."'".$obj->rowid."'".')" id="'.$i.'" name="'.$i.'" value="'.$obj->rowid.'" '.$check.'>';                    
								$out.= '<label for="'.$i.'">'.$obj->lastname.' '.$obj->firstname.'</label>';
								
						if($z == 4){    $out.='<p>'; $z = 0;}
					//    $out.= '</td>';
					//$out.= '</tr>';
				}
				$i++;
				$z++;
			}
		}
		if($onlyarray){
			return $unBloackarray;
		}else{
			print $out;
			}
	}
	function getBox(){
		if($conf->global->MAIN_MODULE_DOLICHAT){
			if ($user->rights->dolichat->UseChat){ 
				//  Timestamp bsp.: 2014-08-12 09:35:20
				if($chatbox_age < 0){$chatbox_age = 0;}
				$timestamp = strtotime('- '.$chatbox_age.' day'); 
				$inTagen = date("Y-m-d",$timestamp);
				$inTagen.= ' 00:00:00';
				//  WHERE timestamp < '".$inTagen."'";
			
				$sql = "SELECT * FROM " . MAIN_DB_PREFIX . "chattext where privat IN (0, ".$user->id.") AND timestamp > '".$inTagen."' ORDER BY `" . MAIN_DB_PREFIX . "chattext`.`timestamp` DESC"; 

				$ergebnis = $db->query($sql);
				$i = 1;
				while($row = $db->fetch_object($ergebnis)) 
				{
					// $chatbox_num zb: 3 <-- dann 3 nachrichten anzeigen
					
					$time = dol_print_date($row->timestamp,'%d %H:%M');
					$time = substr($time, 2);

					$user_to_you = $row->user;
					$chattext = $row->chattext;

					// <li><span style="font-size:10px;">12:30</span> <span style="font-size:15px;"><?php print $langs->trans("Anderson"); :</span> <span><?php print 'Halo?'; </span></li>
					if($chatbox_num >= $i){
						$chat_box_nachricht[$i]= '<li><span style="font-size:10px;">'.$time.'</span> <span style="font-size:15px;">'.$user_to_you.' :</span> <span>'.$chattext.'</span></li>';
					}
					$i++;
				}
			}
		}
	}

	function get_hash_r_g_b($farbe){
		$r = $farbe[0] + $farbe[1];
		$r = hexdec($r);

		$g = $farbe[2] + $farbe[3];
		$g = hexdec($g);

		$b = $farbe[4] + $farbe[5];
		$b = hexdec($b);

		$rgb_a[] = $r;
		$rgb_a[] = $g;
		$rgb_a[] = $b;
		return $rgb_a;
	}

	function color_inverse($color){
		$color = str_replace('#', '', $color);
		if (strlen($color) != 6){ return '000000'; }
		$rgb = '';
		for ($x=0;$x<3;$x++){
			$c = 255 - hexdec(substr($color,(2*$x),2));
			$c = ($c < 0) ? 0 : dechex($c);
			$rgb .= (strlen($c) < 2) ? '0'.$c : $c;
		}
		return '#'.$rgb;
	}

	function getContrast50($hexcolor)
	{
		return (hexdec($hexcolor) > 0xffffff/2) ? 'black':'white';
	}


	function write_json_array($var,$param)
	{
		if ($var=='importlog') $this->importlog=json_encode($this->importlog,JSON_FORCE_OBJECT);
		elseif ($var=='original') $this->original=json_encode($this->original,JSON_FORCE_OBJECT);
		else $var=json_encode($var,$param);
		switch(json_last_error())
		{
			  case JSON_ERROR_DEPTH:
				  $error++; 
				  $this->errors[]="Error JSON - Maximum stack depth exceeded"; 
				  break;
			  case JSON_ERROR_CTRL_CHAR:
				  $error++; 
				  $this->errors[]="Error JSON - Unexpected control character found"; 
				  break;
			  case JSON_ERROR_SYNTAX:
				  $error++; 
				  $this->errors[]="Error JSON - Syntax error, malformed JSON"; 
				  break;
			  case JSON_ERROR_UTF8:
				  $error++; 
				  $this->errors[]="Error JSON - Malformed UTF-8 characters, possibly incorrectly encoded"; 
				  break;    
			  case JSON_ERROR_NONE:
			  default:
		}
			
		if ($error) {
			
				foreach($this->errors as $errmsg)
				{
					dol_syslog(get_class($this)."::write_json_array ".$errmsg, LOG_ERR);
					$this->error.=($this->error?', '.$errmsg:$errmsg);
				}
				
		}
		else return 1;
	}
	
}
?>