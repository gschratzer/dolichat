<?php  
//Config auslesen 
require '../../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/dolichat/class/dolichat.class.php';
$dolichat=new dolichat($db);
$langs->load("dolichat@dolichat");
$staticuser=new User($db);
$staticuser->fetch($user->id);

$staticuser2=new User($db);

    $cuser = $_POST['cuser'];
    $PicSavedID = $_POST['PicSavedID'];
    $Pic_text = '';
    if($PicSavedID)
    {
        $sql ="SELECT * FROM  llx_chatpic where MsgID = '".$PicSavedID."' ORDER BY  llx_chatpic.rowid DESC ";
        $ergebnis = $db->query($sql);      
        while($row = $db->fetch_object($resql)) 
        { 
            $Pic = $row->PicName;  
            $Picuser = $row->UserID;  

            $Pic_text.= ' %picto='.$PicSavedID.'/'.$Pic;
        }
    }   

    $testFgroup = $cuser[0];
    $write = 1;
    if($testFgroup == "G")
    {
        $rowidG = substr($cuser,1);
        $sql ="SELECT * FROM  llx_chatgroup where rowid = '".$rowidG."'";
        $resql=$db->query($sql);
        $obj = $db->fetch_object($resql);
        $write = $obj->G_write;        
    }
    if($write == 1)
    {
        if(isset($_POST['eintrag']))
        { 
    
        if(empty($_POST['nick']) || empty($_POST['eintrag']))
        { 
            echo '<script>alert("'.$langs->trans("BitteNachrichteingeben").'")</script>'; 
        }
        else
        {
            $rightsfail = "";
            $privatuser = $_POST['cuser'];
            if($privatuser > 0 || $testFgroup == "G")
            {

            }
            else
            {
                if(!$user->rights->dolichat->UseBroadcast)
                {
                    $privatuser = $user->id;
                    $rightsfail = ' No Permison to make a Broadcast ';
                    $rightsfail_c = 1;
                }
                else
                {
                    $privatuser = 0;
                }
            }
            //Variablen definieren und mit "POST" Daten füllen (Mit htmlspecialchars filtern..) 

            $nick = htmlspecialchars($_POST['nick']); 
            $eintrag = htmlspecialchars(($_POST['eintrag'])); 

            //if(empty($eintrag)) $eintrag = ($_POST['eintrag']);
            //$eintrag = nl2br($eintrag);
            //Die 2 oben definierten Variablen zusammensetzen 
            if($eintrag != "%picto=notext")
            {
                $alles = $eintrag; 
            }

            if($rightsfail_c == 1) $alles = $rightsfail;
            $alles = ($alles);  
            //$alles = str_replace('<','&lt;',$alles);
            //$alles = str_replace('>','&gt;',$alles);
            $alles = str_replace('&lt;br&gt;','<br>',$alles);
            $alles = $alles.$Pic_text;

            if($privatuser>0)
            {
                $staticuser2->fetch($privatuser); 
                $privatnachricht = ' sagt zu '.$staticuser2->lastname.' '.$staticuser2->firstname;
            }

            if($alles == "/clear" && $user->admin)
            {
                $sql ="DELETE FROM `llx_chattext` WHERE 1";
            }
            else
            {


                $base_alles = base64_encode($alles);
                if(!empty($base_alles))
                {
                    //Nick + Eintrag in die Datenbank schreiben 
                    //".MAIN_DB_PREFIX."
                    $sql ="INSERT INTO llx_chattext (";
                    $sql.="rowid ,";
                    $sql.="chattextblob ,";
                    $sql.="user ,";
                    $sql.="user_id ,";        
                    $sql.="privat ,";
                    $sql.="privat_name ,";        
                    $sql.="timestamp";
                    $sql.=") ";
                    $sql.="VALUES (";
                    $sql.="'', '".($base_alles)."', '".$staticuser->lastname.' '.$staticuser->firstname."', '".$user->id."','".$privatuser."', '".$privatnachricht."', NOW( )";
                    $sql.=");";
                }
            }
            //print $sql;
            //exit;
            $res = $db->query($sql);
    
            $sql='UPDATE llx_chattext';
            $sql.= ' SET gesehen =  "1"';
            $sql.= ' WHERE privat = '.$user->id.' ;'; 
            //print $sql;
            $up_gesehen = $db->query($sql);
            //header("Location: ".DOL_URL_ROOT.'/dolichat/index.php?cuser='.$privatuser);
            //exit;
            }   
    }                                 
} 
