<?php  
//Config auslesen 
require '../../../main.inc.php';

    $ges = $_GET['ges_id'];
    $id = $_GET['id'];
    $del = $_GET['del'];
    $pruf = $_GET['pruf'];
    $userid = $_GET['userid'];
    $tmp_userid = $_GET['tmp_user'];
    $chat_stat = $_GET['chat_stat'];
    $get_user_info = $_GET['get_user_info'];

    if(isset($id) && $del == 0){ 
    
        $sql="UPDATE " . MAIN_DB_PREFIX . "chattext";
        $sql.= ' SET gesehen =  "1"';
        $sql.= ' WHERE rowid = "'.$id.'"'; 

        $up_gesehen = $db->query($sql);
        
    }elseif(isset($ges)){ 
    
        $sql="UPDATE " . MAIN_DB_PREFIX . "chattext";
        $sql.= ' SET gesehen =  "1"';
        $sql.= ' WHERE user_id = "'.$ges.'"'; 
        $sql.= ' AND privat = "'.$user->id.'"'; 
        $sql.= ' AND gesehen = 0';
        //echo $sql;
        $up_gesehen = $db->query($sql);
        
    }elseif(isset($id) && $del == 1){

        $sql = "SELECT * FROM  `" . MAIN_DB_PREFIX . "chattext` WHERE rowid = ".$id;
        $res = $db->query($sql);
        $row = $db->fetch_object($res);

        if(!empty($row->chattextblob)) $chattext = base64_decode($row->chattextblob);
        else $chattext = $row->chattext;

        $PIC = substr (strrchr ($chattext, "%picto="), 7);
        $Pic = $PIC; 
        $url = $row->user_id;
        if($Bildernum == 1 || $Bildernum == ""){
            if($Pic!=""){
                $path = DOL_DATA_ROOT.'/dolichat/uploads/'.$user->id.'/'.$PIC;
                unlink($path);
            
                $path2 = DOL_DATA_ROOT.'/dolichat/uploads/'.$user->id.'/t_'.$PIC;
                unlink($path2);
                
            } 
        }




        $sql="DELETE FROM " . MAIN_DB_PREFIX . "chattext WHERE rowid = ".$id;
        $up_gelöscht = $db->query($sql);
    }elseif($pruf == 1){

        $sql0 = "SELECT * FROM " . MAIN_DB_PREFIX . "chatstat where user_id = ".$user->id;
        $res0 = $db->query($sql0);
        $stat = $db->fetch_object($res0);

        if(empty($stat))
        {
            $sql = "INSERT INTO " . MAIN_DB_PREFIX . "chatstat (`rowid`, `user_id`, `online`, `last_stat`) VALUES (NULL, '".$user->id."', '1', CURRENT_TIMESTAMP);";
            $db->query($sql);
        }

        $sql0 = "SELECT * FROM " . MAIN_DB_PREFIX . "chatstat";
        $res0 = $db->query($sql0);
        while($stat = $db->fetch_object($res0))
        {
            if($stat->user_id == $user->id)
            {
                if(empty($chat_stat)) $chat_stat = 1;
                $sql = "UPDATE " . MAIN_DB_PREFIX . "chatstat SET `checks` = '".($stat->checks + 1)."', online = '".$chat_stat."' WHERE `" . MAIN_DB_PREFIX . "chatstat`.`user_id` = ".$user->id.";";
                $db->query($sql);
            }
            else
            {
                if(strtotime($stat->last_stat) > strtotime('- 10 sec.')) $chat_users_stats[$stat->user_id] = $stat->online;
                if(strtotime($stat->last_stat) < strtotime('- 10 sec.')) $chat_users_stats[$stat->user_id] = 0;
            }
        }


        $sql ="SELECT rowid FROM " . MAIN_DB_PREFIX . "chattext ";
        if($userid > 0) $sql.= " Where user_id = ".$userid." and privat = ".$user->id;
        $sql.=" ORDER BY rowid  DESC ";
        $sql.=" LIMIT 0 , 1";
        $res = $db->query($sql);
        $row = $db->fetch_object($res);

        $rowid = $row->rowid;
		
        echo $rowid;
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
        $sql1 = "SELECT * FROM `" . MAIN_DB_PREFIX . "chattext`";
        if($user->id > 0) $sql1.= " Where privat = ".$user->id;
        $sql1.= " and gesehen = 0 Group by user_id";
        $res1 = $db->query($sql1);
        //echo $sql;

        $first = true;
        while($gese = $db->fetch_object($res1)) 
        {
            $sql ="SELECT rowid, user_id, privat, chattext, chattextblob, timestamp FROM " . MAIN_DB_PREFIX . "chattext ";
            if($user->id > 0) $sql.= " Where user_id = ".$gese->user_id." and privat = ".$user->id.' and gesehen = 0';
            $sql.=" ORDER BY rowid DESC ";
            //$sql.=" LIMIT 0 , 1";
            $res = $db->query($sql);
            //$row = $db->fetch_object($res);

            $cn_gesehen = 0;
            while($row_cm = $db->fetch_object($res))
            {
                if($cn_gesehen == 0) $row = $row_cm;
                $cn_gesehen++;
            }

            $rowid = $row->rowid;
            if($user->id != $row->user_id) $ruser = $row->user_id;
            if($user->id != $row->privat) $ruser = $row->privat;
            
            if($first == false) echo '%<|>%';         
            if($row->chattextblob) echo $rowid.'%<>%'.$ruser.'%<>%'.base64_decode($row->chattextblob).'%<>%'.date('H:i', strtotime($row->timestamp)).'%<>%'.$user->id.'%<>%'.$row->user_id; // .'-'.$sql
            elseif($row->chattext) echo $rowid.'%<>%'.$ruser.'%<>%'.$row->chattext.'%<>%'.date('H:i', strtotime($row->timestamp)).'%<>%'.$user->id.'%<>%'.$row->user_id; // .'-'.$sql
            echo '%<>%'.$cn_gesehen;
            //echo '%<>%'.$sql;

            $staticuser=new User($db);
            $staticuser->fetch($row->user_id);

            echo '%<>%'.($staticuser->lastname).' '.($staticuser->firstname);

            $first = false;
        }
    }

