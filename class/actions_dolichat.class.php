<?php

class Actionsdolichat {

	var $db;

	var $mesg;
	var $error;
	var $errors= array();
	//! Numero de l'erreur
	var $errno = 0;
	var $options=array();

	/**
	 * 	Constructor
	 *
	 * 	@param	DoliDB	$db		Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
	}

    /** Overloading the doActions function : replacing the parent's function with the one below 
     *  @param      parameters  meta datas of the hook (context, etc...) 
     *  @param      object             the object you want to process (an invoice if you are in invoice module, a propale in propale's module, etc...) 
     *  @param      action             current action (if set). Generally create or edit or null 
     *  @return       void 
     */
    
    /**
		 *  Show a search area
		 *
		 *  @param  string	$urlaction          Url post
		 *  @param  string	$urlobject          Url of the link under the search box
		 *  @param  string	$title              Title search area
		 *  @param  string	$htmlmodesearch     Value to set into parameter "mode_search" ('soc','contact','products','member',...)
		 *  @param  string	$htmlinputname      Field Name input form
		 *  @return	void
		 */


      function printTopRightMenu() {
        global $langs, $user, $db, $conf, $hookmanager;
       		//print '<div class="login"><a href="test">test</a></div>';
				
			$temp_user_id = $user->id;
        	if($user->rights->dolichat->UseChat && $_SERVER['PHP_SELF'] != "/dolichat/index.php" && $_SERVER['PHP_SELF'] != "htdocs/dolichat/index.php" && $_SERVER['PHP_SELF'] != DOL_URL_ROOT."/dolichat/index.php")
        	{
				$sql1 = "SELECT * FROM `" . MAIN_DB_PREFIX . "chattext`";
				$sql1.= " Where privat = ".$user->id;
				$sql1.= " and gesehen = 0 Group by user_id";
				$res1 = $db->query($sql1);
				$gese = $db->fetch_object($res1);
				if($gese->rowid > 0)
				{
					$unseen = true;
					$callsign = '!';
					$ch_user = '?user='.$gese->user_id;
				}
				else
				{
					$unseen = false;
				}

				$hookmanager->resPrint .= '<div class="inline-block"><a href="'.DOL_URL_ROOT.'/dolichat/index.php'.$ch_user.'" id="dolichat_alert_global" target="_blank">';
					$hookmanager->resPrint .= '<img src="'.DOL_URL_ROOT.'/dolichat/img/dolichat.png" style="width:20px;">';
					$hookmanager->resPrint .= '<span style="color: rgba(255, 255, 255, 0.75); font-size: 20px;" id="dolichat_alert_global_count">'.$callsign.'</span>';
				$hookmanager->resPrint .= '</a></div>';
				$hookmanager->resPrint .= '<input type="hidden" id="userid_input" value="'.$user->id.'">';
				$hookmanager->resPrint .= '<input type="hidden" id="dolichat_unseen" value="'.$unseen.'">';
				$hookmanager->resPrint .= '<script type="text/javascript" src="'.DOL_URL_ROOT.'/dolichat/js/global_chat.js"></script>';
				return 1;
        	}
			
      	return 1;

      }
      
      function ChatBox()
      {
      	// First Box
	        $n = 1;
	        $userSelectBox = $dolichat->select($BoxCuser[0],'cuser'.$n, '1','Broadcast','shadow', 'onChange="saverofcuser('.$n.')"');
	            
	        if(!empty($userpic[$BoxCuser[$nB]])){
	                $Boxusrpic=  '<img id="'.$n.'usrpic" src="'.$userpic[$BoxCuser[$nB]].'" style="width: 26px; padding-right: 4px;">'; // user pic            
	        }else{
	            if($userisadmin[$BoxCuser[$nB]] == 1){
	                $Boxusrpic=  '<img id="'.$n.'usrpic" src="images/nouserpicadmin.png" style="width: 26px; padding-right: 4px;">'; // user pic
	            }else{
	                $Boxusrpic=  '<img id="'.$n.'usrpic" src="images/nouserpic.png" style="width: 26px; padding-right: 4px;">'; // user pic
	            }
	        }
	
	        $Box = '<table class="noborder draggable" width="100%" id="'.$n.'t" style="border: 1px solid black;">';
	        $Box.= '<input type="hidden" value="0" id="'.$n.'d">'; // fullsceen or not ?
	        $Box.= '<input type="hidden" value="0" id="'.$n.'m">'; // Minimize or not ?
	        $Box.= '<input type="hidden" value="'.$BoxCuser[$nB].'" id="'.$n.'cusernB">';
	        $Box.=   '<tr class="liste_titre" id="'.$n.'tr0" style="white-space: normal; background-image: none; background-color: #CBCBCB;">';
	        $Box.=   '<th class="liste_titre nodrag1" style="width: 30%;padding-right: 4px;">';
	        $Box.=      $Boxusrpic;
	        $Box.=      $userSelectBox.'';   
	        $Box.=   '</th><th class="liste_titre" style="width: 30%;padding-right: 4px;min-width: 116px;"  colspan="2">';    
	        $Box.=      '<img id="'.$n.'ent" onClick="entf_Box('.$n.')" src="images/Schliessen.png" style="width: 26px;float: right;padding-right: 0px;padding-left: 0px;margin-left: 0px;" alt="Schließen">';
	        $Box.=      '<img id="'.$n.'img" onClick="animate_Fullscreen('.$n.', \'left\')" src="images/fullscreen.png" style="width: 26px;float: right;padding-right: 4px;padding-left: 0px;margin-left: 0px;" alt="fullscreen">';
	        $Box.=      '<img id="'.$n.'min" onClick="Min_Box('.$n.')" src="images/Minus.png" style="width: 26px;float: right;padding-right: 4px;padding-left: 0px;margin-left: 0px;" alt="Minus">';
	        $Box.=      '<img id="'.$n.'mov" class="moveICO" src="images/move.png" style="width: 26px;float: right;padding-right: 4px;padding-left: 0px;margin-left: 0px;" alt="Move">';
	        $Box.=      '<img src="images/conf.png" id="Conf'.$n.'" onClick="conf('.$n.')" style="width: 26px;float: right;padding-right: 4px;padding-left: 0px;margin-left: 0px;" alt="Conf">';
	        $Box.=   '</th></tr>';
	        $Box.=   '<tr '.$bc[true].' id="'.$n.'tr1">';
	        $Box.=      '<td class="nowrap webback2" colspan="3" style="padding: 0px;"><iframe frameBorder="0" width="100%" src="indexFrame2.php?cuser='.$BoxCuser[$nB].'&cusernB='.$nB.'" id="'.$n.'"></iframe></td></tr>';
	   		  $Box.=   '<tr '.$bc[false].' id="'.$n.'tr2" style="height: 35px;">';
	   		  $Box.=       '<td colspan="3" class="nowrap screentd">';
	   		 	$Box.= 			 '<div class="textareadiv">';
	   		  $Box.= 			 		'<textarea class="animated nodrag1" onfocus="InFocus('.$n.')" onfocusout="InFocus('.$n.')" type="text" name="eintrag'.$n.'" value="" id="textbox'.$n.'" style="width: 90%; margin-left: 1.5%; box-shadow: none; border: none;outline: 0;max-height:80px;height:48px;"></textarea>';
	    		
	    		//$Box.=       '</td><td class="nowrap" style="max-width:50px;">';
	    		//$Box.=       '<td style="width:1%;min-width:25px;" id="upload'.$nB.'">';
	    		$Box.=       '<img onclick="SendText('.$n.', event)" id="'.$n.'snd" src="images/Send.png" style="width: 26px; padding-top: 2.5%;padding-left: 19px;display:none;">'; // SEND
	    		$Box.=       '<span id="'.$n.'spanUP">';
	    		$Box.=			 '<img class="UploadPicImg'.$n.'" id="'.$n.'upl" src="images/Upload.png" style="width: 26px; padding-top: 2.5%;padding-left: 19px;">';
	    		$Box.=			 '</span>';
	    		$Box.= 		 	 '</div>';
	    		//$Box.=       '<input  type="button" value="'.$langs->trans("Senden").'">';
	    		$Box.=       '<input type="hidden" id="SavedPicID'.$n.'">';
	   	   	// $Box.=       '</td><td style="width: 1%;min-width: 38px;">';
	    		$Box.= 			 		'<iframe src="lib/indexAt.php?cuser='.$BoxCuser[$nB].'&boxid='.$n.'" style="display:none;" id="uploadF'.$n.'"></iframe>'; // indexAT Trigers on class class="UploadPicImg BOXID"
	    		$box.= 			 '</td></tr>'; // </td> 
	        $Box.=   '<tr '.$bc[true].' id="'.$n.'tr3" style="display:none;">';
	        $Box.=       '<td class="wrap" style="width: 37%;" colspan="3"><div id="'.$n.'span3"></div></input></td>';
	        $Box.=  "</tr>\n";
	        $Box.="</table>\n";
					
					return $Box;
      }
      
      function Chat_style()
      {
      	?>
      	<style>
				/* Spanring Niklas */
			
			    textarea {
			        width: 830px;
			        height: 60px;
			        border: 3px solid #cccccc;
			        padding: 5px;
			        font-family: Tahoma, sans-serif;
			        background-position: bottom right;
			        background-repeat: no-repeat;
			    }
			    #loding{
			        font-size:24px;
			        text-align: center;
			    }
			    
			/* --------------------------------------------------------------------------------    
			    Textarea 02*/
			    
			    .textareadiv:before { 
							/* EFFFED */
						float: right;
			  		content: "\A";
			 			border-style: solid;
			  		border-width: 25px 55px 20px 49px;
			  		border-color: transparent transparent transparent #FFFFFF;
			  		margin-top: 10px;
					}
					
					/* Der Lade Balcken */
					.loadAll{
						background-color: rgba(74, 74, 74, 0.33);
			  		position: absolute;
			  		width: 100%;
			  		height: 30px;
			 		 	left: 0px;
			 		 	top: 0px;
			  		text-align: center;
			 		 	padding-top: 15px;
			 		 	border-bottom: 2px solid rgb(255, 194, 0);
					}
					.loadAll:after{
							content: 	'';
							display: 	table;
							clear: 		both;
					}
					
					.dateshower{
						position: fixed;
			  		top: 0px;
			  		left: 0px;
			  		width: 100%;
			  		/* height: 25px; */
			  		/* border-radius: 20px; */
			  		background-color: rgba(108, 108, 108, 0.26);
			  		padding: 10px 0px 10px 0px;
			  		border-bottom: 2px solid rgb(255, 194, 0);
					}
					
					.date{
						border-radius: 20px;
			  		padding: 10px 0px 10px 0px;
			  		background-color: #E7C74C;
			 	 		margin: 10px 26% 20px 26%;
			 	 		box-shadow: 0px 5px 8px rgba(0, 0, 0, 0.44);
					}
			/* --------------------------------------------------------------------------------    
			    Tabel einstellungen o2*/
					.webback{
						background: rgba(0, 0, 0, 0);
						margin: 20px;			
					}
					.webback2{
						/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#e5e5e5+0,becdb1+100 */
						/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#e5e5e5+0,d6d6ba+100 */
						background: rgb(229,229,229); /* Old browsers */
						background: -moz-linear-gradient(top,  rgba(229,229,229,1) 0%, rgba(214,214,186,1) 100%); /* FF3.6+ */
						background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(229,229,229,1)), color-stop(100%,rgba(214,214,186,1))); /* Chrome,Safari4+ */
						background: -webkit-linear-gradient(top,  rgba(229,229,229,1) 0%,rgba(214,214,186,1) 100%); /* Chrome10+,Safari5.1+ */
						background: -o-linear-gradient(top,  rgba(229,229,229,1) 0%,rgba(214,214,186,1) 100%); /* Opera 11.10+ */
						background: -ms-linear-gradient(top,  rgba(229,229,229,1) 0%,rgba(214,214,186,1) 100%); /* IE10+ */
						background: linear-gradient(to bottom,  rgba(229,229,229,1) 0%,rgba(214,214,186,1) 100%); /* W3C */
						filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e5e5e5', endColorstr='#d6d6ba',GradientType=0 ); /* IE6-9 */
			
					}
					
					/*
					.msg-lineR{
							display: inline-block;
							float:	 right;
							
					}	
					*/
					.msg-lineR:after{
							content: 	'';
							display: 	table;
							clear: 		both;
					}	
					
					/*
					.msg-lineL{
							display: inline-block;
							
					}	
					*/
					.msg-lineL:after{
							content: 	'';
							display: 	table;
							clear: 		both;
					}	
					
					/*
					chatdiv2You -> From You Chat
					chatdiv2To  -> To You Chat
					chatdiv2Br  -> Brodcast
			
					*/
					
					
					.chatdiv2You{
							/* #EFFFED */
							float: right;
							margin-bottom: 20px;
			  			padding: 10px 0px 10px 10px;
			  			display: inline-block;
			  			background-color: #D4FFCF; 
			  			border-radius: 13px;
			  			box-shadow: -2px 2px 3px rgba(0, 0, 0, 0.37);
					}
					
					.chatdiv2You:before {  
							/* EFFFED */
							float: right;
			    		content:"\A";
			    		border-style: solid;
			    		border-width: 10px 6px 4px 15px;
			    		border-color: transparent transparent transparent #D4FFCF;
			    		position: absolute;
			    		right: 0px;
					}
					
					.chatdiv2To{
							/* #EFFFED */
							margin-bottom: 20px;
			  			padding: 10px 10px 10px 10px;
			  			display: inline-block;
			  			background-color: #FFFFF2; 
			  			border-radius: 13px;
			  			box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.37);
					}
					
					.chatdiv2To:before {
							/* EFFFED */
			    		content:"\A";
			    		border-style: solid;
			    		border-width: 10px 15px 4px 0;
			    		border-color: transparent #FFFFF2 transparent transparent; 
			    		position: absolute;
			    		left: 6px;
					}
					
					.chatdiv2Br{
							/* #EFFFED */
							margin-bottom: 20px;
			  			padding: 10px 10px 10px 10px;
			  			display: inline-block;  			
			  			background-color: #FF9F9F; 
			  			border-radius: 13px;
			  			box-shadow: 2px 2px 3px rgba(0, 0, 0, 0.37);
					}
					
					.chatdiv2Br:before {
							/* EFFFED */
			    		content:"\A";
			    		border-style: solid;
			    		border-width: 10px 15px 4px 0;
			    		border-color: transparent #FF9F9F transparent transparent; 
			    		position: absolute;
			    		left: 6px;
					}
			/* --------------------------------------------------------------------------------
			    Tabel einstellungen */
			    #chattabel{
			        border-collapse: collapse;
			        
			    }
			    #your_tr{
			        
			        /*background-image: url(../images/metall-iphone5s-hd-wallpaper.jpg);*/
			    }
			    #to_you_tr{
			        
			    }
			    #Broadcast_tr{
			       
			    }
			/* --------------------------------------------------------------------------------
			    Text der rechts steht (der von einen selbst kommt) */
			    #your_chattext{
			        color:#000000;
			
			        background-color:#D4FFCF;
			        border-style:hidden;
			        /*background-image: url(../images/Unbenannt.png);
			        /*border-bottom: 1px dashed black;*/
			    }
			    #your_chattext2{
			        background-color:#D4FFCF;
			    }
			    #your_text_to{
			        color:#000000;
			        font-size:10px;
			        text-align: left;
			        vertical-align: bottom;
			        background-color:#D4FFCF;      
			        /*border-bottom: 1px dashed black;*/
			    }
			    #your_time{
			        color:#000000;
			        font-size:8px;        
			        vertical-align: bottom;
			        background-color:#D4FFCF;   
			        /*border-bottom: 1px dashed black;*/
			    }
			/* --------------------------------------------------------------------------------
			    Text der lenks steht (der von einen anderen als man selber kommt) */
			    #to_you_time{
			        color:#000000;
			        font-size:8px;
			        vertical-align: bottom;
			        background-color:#FFFFF2;     
			        /*border-bottom: 1px dashed black;*/
			    }
			    #to_you_who{
			        color:#000000;
			        vertical-align: bottom;
			        background-color:#FFFFF2;
			
			        /*border-bottom: 1px dashed black;*/
			    }
			    #to_you_text{
			        color:#000000;
			
			        background-color:#FFFFF2;        
			        /*border-bottom: 1px dashed black;*/
			    }
			    #to_you_text2{
			        color:#000000;
			
			        background-color:#FFFFF2;        
			    }
			    #to_you_time_nicht_gesehen{
			        color:#000000;
			        font-size:8px;
			        vertical-align: bottom;
			        background-color:#BBB5FF;
			        /*border-bottom: 1px dashed black;*/
			    }
			    #to_you_who_nicht_gesehen{
			        color:#000000;
			        vertical-align: bottom;
			        background-color:#BBB5FF;
			        width: 15%;
			        /*border-bottom: 1px dashed black;*/
			    }
			    #to_you_text_nicht_gesehen{
			        color:#000000;
			        text-align: left;
			        background-color:#BBB5FF;        
			        /*border-bottom: 1px dashed black;*/
			    }    
			    #to_you_text_nicht_gesehen2{
			        color:#000000;
			        text-align: left;
			        background-color:#BBB5FF;        
			    }
			/* --------------------------------------------------------------------------------
			    Broadcast der lenks steht (nicht von einem selbst)*/
			    #Broadcast_time{
			        font-size:8px;
			        background-color:#FF9F9F;
			        vertical-align: bottom;
			        /*border-bottom: 1px dashed black;*/
			    }
			    #Broadcast_who{
			        background-color:#FF9F9F;
			        vertical-align: bottom;
			        font-size: 10px;
			        /*border-bottom: 1px dashed black;*/
			    }
			    #Broadcast_text{
			
			        background-color:#FF9F9F;
			        /*border-bottom: 1px dashed black;*/
			    }
			    #Broadcast_text2{
			
			        background-color:#FF9F9F;
			    }
			/* --------------------------------------------------------------------------------
			    CSS für den geraden durchgehenden Trenn strich*/
			    #trenstrich{
			        /*border-right:medium solid black;*/
			    }
			    #to_trenstrich{
			        background-color:#A1FFA5
			    }
			    #to_trenstrich_nicht_gesehen{
			        background-color:#FFFF98
			    }
			    #Bro_trenstrich{
			        background-color:#FF9F9F;
			    }    
			    #to_col{
			        border-style:hidden;
			    }
			    #to_col2{
			        border-style:hidden;
			    }
			    #Bro_col{
			        border-style:hidden;
			    }
			
			/* --------------------------------------------------------------------------------
			    CSS für den Boden abstand*/
			    #bodenabstand{
			        height: 50px;
			        text-align: right;
			    }
			
			    #ist_gesehen{
			        background-image: url(../images/gesehen.png);
			        background-size: 100%;
			        background-repeat:no-repeat;
			    }
			/* --------------------------------------------------------------------------------
			    CSS für die hoch runter Button's
			    .f1 { top:10%; background-image: url(../images/Hoch.jpg);}
			    .f2 { top:90%; background-image: url(../images/Runter.jpg);}
			    .back{
			        position:fixed; 
			        left:97%;
			        opacity: 0.5;
			        width:50px;
			        height:50px;
			        background-size: 100%;
			        background-repeat:no-repeat;
			    }
			    .dreh{
			        -webkit-transform: rotate(180deg);
			        -moz-transform: rotate(180deg);
			        -ms-transform: rotate(180deg);
			        -o-transform: rotate(180deg);
			        transform: rotate(180deg);
			    }
			*/
			
			#cuser{
			
			    color:#D5D5D5;
			}
			#cuser:hover{
			    background-color:#EDEDED;
			    color:#909090;
			}
			#admin{
			
			    color:#D5D5D5;
			}
			#admin:hover{
			    background-color:#EDEDED;
			    color:#909090;
			}
			#button{
			
			    color:#D5D5D5;
			}
			#button:hover{
			    background-color:#EDEDED;  
			    color:#909090;
			}
			#textbox{
			
			    color:#FCFCFC;
			    -webkit-box-shadow: 5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			    -moz-box-shadow:    5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			    box-shadow:         5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			}
			#textbox:hover{
			    background-color:#EDEDED; 
			    color:#000000;
			    -webkit-box-shadow: 5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			    -moz-box-shadow:    5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			    box-shadow:         5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			}
			#textbox:active{
			    background-color:#EDEDED; 
			    color:#000000;
			    -webkit-box-shadow: 5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			    -moz-box-shadow:    5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			    box-shadow:         5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			}
			
			
			#Gesended{
			    position:fixed;
			    top:30%;
			    left:45%;
			    width:60px;
			    height:60px;
			    background-image: url(../images/loading.gif);
			}
			/*--------------------------------------RELOAD---------------------------------------------*/
			#Reload_td{  
			    position:fixed;
			    width:95px;
			    height:75px;
			    top:75px;     
			    left:107px;  
			    background-color:#FFFFFF;
			    z-index:1;
			}
			#Chat_name{
			    position:fixed;
			    top:55px;     
			    left:104px;
			    text-align: right;
			    color:#FFFF00;
			    z-index:1;
			}
			#Reload_text{
			    position:fixed;
			    top:75px;     
			    left:109px;
			    text-align: right;
			    color:#D5D5D5;
			    z-index:1;
			}
			#Reload_text2{
			    position:fixed;
			    top:95px;     
			    left:124px;
			    text-align: right;
			    color:#D5D5D5;
			    z-index:1;
			}
			#Reload_text3{
			    position:fixed;
			    top:135px;  
			    left:132px;
			    text-align: right;
			    color:#D5D5D5;
			    z-index:1;
			}
			#Reload_text4{
			    position:fixed;
			    top:115px;
			    left:150px;
			    text-align: right;
			    color:#D5D5D5;
			    z-index:1;
			}
			#Reload_text5{
			    position:fixed;
			    top:155px;
			    left:142px;
			    text-align: right;
			    color:#D5D5D5;
			    z-index:1;
			}
			#Reload{
			    position:fixed;
			    top:78px;     
			    left:190px;
			    width:10px;
			    height:10px;    
			    background-color:#DC0000;
			    border-radius: 10px;
			    -webkit-box-shadow: 2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    -moz-box-shadow:    2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    box-shadow:         2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			}
			#Reload2{
			    position:fixed;
			    top:98px;     
			    left:190px;
			    width:10px;
			    height:10px;    
			    background-color:#E9AE00;
			    border-radius: 10px;
			    -webkit-box-shadow: 2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    -moz-box-shadow:    2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    box-shadow:         2px 1px 14px 0px rgba(50, 50, 50, 0.98);  
			}
			#Reload3{
			    position:fixed;
			    top:138px;
			    left:190px;
			    width:10px;
			    height:10px;    
			    background-color:#04E900;
			    border-radius: 10px;
			    -webkit-box-shadow: 2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    -moz-box-shadow:    2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    box-shadow:         2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			}
			#Reload4{
			    position:fixed;
			    top:118px;    
			    left:190px;
			    width:10px;
			    height:10px;    
			    background-color:#FFFC00;
			    border-radius: 10px;
			    -webkit-box-shadow: 2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    -moz-box-shadow:    2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    box-shadow:         2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			}
			#Reload5{
			    position:fixed;
			    top:158px;    
			    left:190px;
			    width:10px;
			    height:10px;    
			    background-color:#0000FF;
			    border-radius: 10px;
			    -webkit-box-shadow: 2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    -moz-box-shadow:    2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			    box-shadow:         2px 1px 14px 0px rgba(50, 50, 50, 0.98);
			}
			
			/*--------------------------------------RELOAD---------------------------------------------*/
			
			.shadow{
			    box-shadow:         5px 3px 10px 0px rgba(50, 50, 50, 0.75);
			}
			.shadow2{
			    
			}
			.shadow3{
			    box-shadow:         22px 12px 14px 0px rgba(50, 50, 50, 0.75);
			}
			.f1 { top:5%; background-image: url(images/Hoch.png);border-radius: 39px;}
			.f2 { top:92.5%; background-image: url(images/Runter.png);border-radius: 39px;}
	.back{
	    position:fixed; 
	    left:97%;
	    opacity: 0.5;
	    width:50px;
	    height:50px;
	    background-size: 100%;
	    background-repeat:no-repeat;
	}
	.back:hover{
	    position:fixed; 
	    left:97%;
	    opacity: 1.0;
	    width:50px;
	    height:50px;
	    background-size: 100%;
	    background-repeat:no-repeat;
	}
	.dreh{
	    -webkit-transform: rotate(180deg);
	    -moz-transform: rotate(180deg);
	    -ms-transform: rotate(180deg);
	    -o-transform: rotate(180deg);
	    transform: rotate(180deg);
	}
      	</style>
      	<?php
    	}
}
/* FaceBook style chat
				if($user->id == 19)
				{
					require_once DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php';
					require_once DOL_DOCUMENT_ROOT.'/dolichat/class/dolichat.class.php';
					require_once DOL_DOCUMENT_ROOT.'/user/class/usergroup.class.php';
					require_once DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php';
					
					$dolichat=new dolichat($db);
					$staticuser=new User($db);
					$form = new Form($db);
					
					$staticuser->fetch($user->id);
					$staticuser->getrights();
					
						$tmp_dol_hide_topmenu = $conf->dol_hide_topmenu;
						$tmp_dol_hide_leftmenu = $conf->dol_hide_leftmenu;
						$conf->dol_hide_topmenu = 1;
						$conf->dol_hide_leftmenu = 1;
					
					//$this->Chat_style();
					$head_obj = '<style>
											div.fiche {
												margin: 0px !important;
											}
											.ClassUserBox:hover{
												background-color: rgba(255, 255, 255, 0.48) !important;
											}
											</style>';
					
					$head_obj.= '<link href="'.DOL_URL_ROOT.'/dolichat/css/dolichat2.css" rel="stylesheet" />';
					
						//llxHeader('',$langs->trans("Chat"));
						$style_chat_line_list = 'pointer-events:all; width:100%; position: absolute; bottom: 0%; height:25px; text-align:right;'.$off_st;
						
						$head_obj.= '<div id="chat_line_list" class="liste_titre" style="'.$style_chat_line_list.'">';
							$head_obj.= '<input id="click_switch" value="false" type="hidden">';
							$head_obj.= '<table id="chat_line_list_table" style="width: 100%; text-align:right;">';
								$head_obj.= '<tr>';
									$head_obj.= '<td style="width: 210px;">';
									$head_obj.= '</td>';			
								$head_obj.= '</tr>';
							$head_obj.= '</table>';
							
							$head_obj.= '<img id="CloseAll" onclick="CloseAllUserBoxes()" src="'.DOL_URL_ROOT.'/dolichat/images/A-down.png" style="cursor: pointer; width: 30px; position: relative; right: 10px; bottom: 8px;" alt="Schließen">';
							$head_obj.= $dolichat->select('','addUser', '1', 'Broadcast', 'shadow', 'style="height: 22px; bottom: 18px; right: 5px; position: relative;" onChange="addUser()"', 0);
							
						$head_obj.= '</div>';
					
						$conf->dol_hide_topmenu = $tmp_dol_hide_topmenu;
						$conf->dol_hide_leftmenu = $tmp_dol_hide_leftmenu;
	
					$css = ' position: fixed; height: 30px; width: 100%; bottom: 0%; right: 0%; z-index: 99; pointer-events:none;';
					
						$head_obj.= '<div id="gesehenscript" style="display:none;"></div>';
						$head_obj.= '<div id="gesehenscriptcuser" style="display:none;"></div>';
					
					$resPrint_res = '<div id="dolichat_main_frame_div" style="'.$css.'">'.$head_obj.'</div>';

        	?>
        	<script>
							
						$( document ).ready(function() {
							OpenAllSession();
						});
						
						var userid_overwrite = 0;
						var username_overwrite = "";
						var userInterval = {} ;
						var NewMassageCounter = {} ;
						var max_width = 0;
						var iframeheight = "400";
						var i = 0;
						var tmp_rowid = {};
						var tmp_mode;
						
						function OpenUserChat(userid, username)
						{
							chat_activ_switch = '<input type="hidden" id="active_box_'+userid+'" value="1">';
							
							chat_tool_box_css = 'width: 295px; height: 22px; background-color: #CCCCCC; font-size: 12px; text-align: left; padding-top: 8px; padding-left: 5px;color: black;';
							
							chat_tool_box_delte = '<img id="1ent" onclick="DelteUserBox('+userid+')" src="' + <?php print "'".DOL_URL_ROOT."/dolichat/'"; ?> + 'images/Schliessen.png" style="cursor: pointer; width: 30px; float: right; position: relative; top: -9px; right: 0px;" alt="Schließen">';
							chat_tool_box_close = '<img id="1ent" onclick="CloseUserBox('+userid+')" src="' + <?php print "'".DOL_URL_ROOT."/dolichat/'"; ?> + 'images/Minus.png" style="cursor: pointer; width: 30px; float: right; position: relative; top: -9px; right: 0px;" alt="Schließen">';
							
							chat_tool_box = '<div name="toolbox" id="tool_box_'+userid+'" style="'+chat_tool_box_css+'"> '+username+chat_tool_box_delte+chat_tool_box_close+'</div>';
							
							chat_frame_obj_css = 'position: absolute; bottom: 30px; height: 335px; left: 0px;'
							chat_frame_obj = '<iframe style="'+chat_frame_obj_css+'" frameborder="0" width="300px" src="' + <?php print "'".DOL_URL_ROOT."/dolichat/'"; ?> + 'indexFrameChatMain.php?cuser='+userid+'" height="'+iframeheight+'px"></iframe>';
							
							chat_text_box_css = 'width: 295px; height: 28px; background-color: #CCCCCC; font-size: 12px; text-align: left; padding-top: 2px; padding-left: 5px;color: black;';
							chat_text_box = '<div style="'+chat_text_box_css+'position:absolute;bottom: 0px;"><input onkeyup="WhenEnter('+userid+', event)" style="width:263px;" type="text" value="" placeholder="Text" id="user_text_box_'+userid+'" name="Chat_Text_Input"><input onclick="SendText('+userid+')" type="button" value=">"></div>';
							
							obj_position = $('#box_' + userid).position();
							css = 'position: absolute; background-color: #FFFFFF; border: 1px solid black; width: 300px; height: '+(iframeheight - 5)+'px; bottom: 0px; z-index: 99;';
							$( '#chat_line_list_table' ).before('<div name="userBox" id="ChatBoxOf_'+userid+'" style="'+css+'">'+chat_activ_switch+chat_tool_box+chat_frame_obj+chat_text_box+'</div>');
							
							$('#dolichat_main_frame_div').css('height', iframeheight + 'px');
							$('#click_switch').val('true');
							CalculatePosition();
							NewMassageCounter[userid] = 0;
							$('#NewMassageFor_' + userid).html( '' );
							if(tmp_mode == 0) SessionChange(userid, 3);
						}
						
						function WhenEnter(userid, e)
						{
						    var code = e.which; // recommended to use e.which, it's normalized across browsers
						    if(code==13)e.preventDefault();
						    if(code==13)
						    {
						      SendText(userid);
						    } // missing closing if brace
						}
							
						function CloseAllUserBoxes()
						{
							$("div[name='userBox']").remove();
							CalculatePosition();
						}
						
						function CloseUserBox(userid)
						{
							$("#ChatBoxOf_"+userid).remove();
							CalculatePosition();
							if(tmp_mode == 0) SessionChange(userid, 4);
						}
						
						function DelteUserBox(userid)
						{
							$("#ChatBoxOf_"+userid).remove();
							$("#box_"+userid).remove();
							CalculatePosition();
							clearInterval(userInterval[userid]);
							if(tmp_mode == 0) SessionChange(userid, 2);
						}
						
						function CalculatePosition()
						{
							// !!! From right to left Box !!! 
							$( ".ClassUserBox" ).each(function( index ) 
							{
								right_css = (300 * (index + 1));
								//box_ and userid
								//ChatBoxOf_ and userid
								$('#box_'+$(this).find('input').val()).css('right', right_css);
								$('#ChatBoxOf_'+$(this).find('input').val()).css('right', right_css);
								i  = index + 1;
							});
						}
						
						function addUser()
						{
							if( $('#box_' + $('#addUser').val()).text() == "" || userid_overwrite > 0)
							{
								i++;
								css = "width: 300px; position: absolute; right: "+(300 * i)+"px;";
								css+= "cursor: pointer; bottom: 0px; border: 1px solid black; height: 20px; text-align: center; padding-top: 8px; color: black; background-color: rgba(193, 193, 193, 0.48);";
								
								userid = $('#addUser').val();
								if(userid_overwrite > 0) userid = userid_overwrite;
								
								username = $("#addUser  option[value='" + $('#addUser').val() + "']").text();
								if(username_overwrite != "") username = username_overwrite;
								
								username_text= username+'<input type="hidden" name="userid" id="userid_'+userid+'" value="'+userid+'">';
								
								userNewMassage = '<span id="NewMassageFor_'+userid+'"></span>';
								
								$('#chat_line_list_table').find('tr').children('td:first').before( '<div style="'+css+'" name="box_'+i+'" id="box_' + userid + '" class="ClassUserBox" onclick="OpenUserChat(' + userid + ', \''+username+'\')">' + username_text + userNewMassage + '</div>');
								
								if(tmp_mode != 2) OpenUserChat(userid, username);
								if(tmp_mode != 2) $('#dolichat_main_frame_div').css('height', iframeheight + 'px');			
								if(tmp_mode != 2) $('#click_switch').val('true');
							}
							NewMassageCounter[userid] = 0;
							userInterval[userid] = setInterval(TestClosedChat, 2000, userid, username);
							if(tmp_mode == 0) SessionChange(userid, 1);
						}
						
						function SendText(cuser) 
					  {   
					  		eintrag = $('#user_text_box_' + cuser).val();
					      nick = '<?php print $staticuser->lastname.' '.$staticuser->firstname; ?>';
					      
					      $('#user_text_box_' + cuser).val('');
					      $('#user_text_box_' + cuser).focus();
					      //alert(cuser);
					      if(eintrag == ''){
					
					      }else{
					          if (window.XMLHttpRequest) 
					            {// code for IE7+, Firefox, Chrome, Opera, Safari 
					            xmlhttp=new XMLHttpRequest(); 
					            } 
					          xmlhttp.onreadystatechange=function() 
					            { 
					            if (xmlhttp.readyState==4 && xmlhttp.status==200) 
					              { 
					                  //document.getElementById("show").innerHTML=xmlhttp.responseText; 
					              } 
					            } 
					          
					          xmlhttp.open("GET", <?php print "'".DOL_URL_ROOT."/dolichat/'"; ?> + "core/ajax/ajax_loaderMain.php?eintrag="+eintrag+"&nick="+nick+"&cuser="+cuser,true);
					          xmlhttp.send();    
					      }	 	  
					  }
					  
					  function SessionChange(userid, change_no)
					  {   
							
							switch(change_no)
							{
								case 1:
		 							action_var = 'ADD_SessionMainChatBox';
		 						break;
		 						case 2:
									action_var = 'DEL_SessionMainChatBox';
								break;
								case 3:
									action_var = 'OPEN_SessionMainChatBox';
								break;
								case 4:
									action_var = 'CLOSE_SessionMainChatBox';
								break;
							}
		          if (window.XMLHttpRequest) 
		          {
		          	// code for IE7+, Firefox, Chrome, Opera, Safari 
		            xmlhttp=new XMLHttpRequest(); 
		          } 
		          xmlhttp.onreadystatechange=function() 
		            { 
		            if (xmlhttp.readyState==4 && xmlhttp.status==200) 
		              { 
		                  //document.getElementById("show").innerHTML=xmlhttp.responseText; 
		              } 
		            } 

		          xmlhttp.open("GET", <?php print "'".DOL_URL_ROOT."/dolichat/'"; ?> + "ajax_conf_saverMain.php?action="+action_var+"&userid="+userid, true);
		          xmlhttp.send();    
					       	  
					  }
					         
					  function TestClosedChat(userid, username)
					  {
							var elemt = '';
							var text = '';
							var rowid = '';
							var cuser = '';
							var neu = '1';
							cuser = userid;
							
							cusers = document.getElementById("gesehenscriptcuser").innerHTML;
							rowid = document.getElementById("gesehenscript").innerHTML;
                               

						  if (window.XMLHttpRequest) 
	            {
	            	// code for IE7+, Firefox, Chrome, Opera, Safari 
	            	xmlhttp2=new XMLHttpRequest(); 
	            } 
	            
	         		xmlhttp2.onreadystatechange=function() 
	            { 
	            	if (xmlhttp2.readyState==4 && xmlhttp2.status==200) 
	              { 
	                  elemt = xmlhttp2.responseText;
	                  if(tmp_rowid[userid] == elemt)
	                  {
	                      //Nicht neu	                      alert(neu);alert(elemt);alert(rowid);                                     
	                      neu = '0';
	                  }
	                  else
	                  {
	                      // NEU 														alert(neu);alert(elemt);alert(rowid);
	                      neu = '1';
	                      if($('#active_box_' + userid).val() != 1)
	                      {
	                      	// OpenUserChat(userid, username); // To open user box by incoming massage
	                      	NewMassageCounter[userid]++;
	                      	NewMassageForCounter(userid);
	                      }
	                  }
	             		//document.getElementById("gesehenscriptcuser").innerHTML = cuser;
               		//document.getElementById("gesehenscript").innerHTML=xmlhttp2.responseText;
               		tmp_rowid[userid] = xmlhttp2.responseText;
	              } 
	            } 
	          
	          	xmlhttp2.open("GET","/dolichat/core/ajax/ajax_gsehen.php?pruf=1&userid="+userid,true);
	         		xmlhttp2.send();
            } 
						
						function NewMassageForCounter(userid)
						{
	          	nummber = '<span style="font-size: 17px; background-color: rgba(0, 255, 31, 0.42); border-radius: 30px; padding: 0px 5px 0px 6px; margin-left: 10px;">' + NewMassageCounter[userid] + '</span>';
	           	$('#NewMassageFor_' + userid).html( nummber );
						}
					
						function OpenAllSession()
						{
							<?php
								//  [MainChatBox] => Array ( [42] => 1 ) )
								if(is_array($_SESSION['MainChatBox']))
								{
									foreach($_SESSION['MainChatBox'] as $userid => $mode)
									{
										$sql ="SELECT rowid FROM " . MAIN_DB_PREFIX . "chattext ";
				            if($userid > 0) $sql.= " Where user_id = ".$userid." and privat = ".$temp_user_id;
				            $sql.=" ORDER BY rowid  DESC ";
				            $sql.=" LIMIT 0 , 1";
				            $res = $db->query($sql);
				            $row = $db->fetch_object($res);
										
				            $rowid = $row->rowid;
										print 'tmp_rowid['.$userid.'] = '.$rowid.';';
										
										$user->fetch($userid);
										if($mode == 1) // Chat Open
										{
											print 'tmp_mode=1;';
											print 'userid_overwrite ='.$userid.'; ';
											print 'username_overwrite = \''.$user->firstname.' '.$user->lastname.'\' ;';
											print 'addUser(); ';
											print 'userid_overwrite = 0; ';
											print 'username_overwrite = ""; ';
											print 'tmp_mode=0;';
										}
										if($mode == 2) // Chat Closed
										{
											print 'tmp_mode=2;';
											print 'userid_overwrite ='.$userid.'; ';
											print 'username_overwrite = \''.$user->firstname.' '.$user->lastname.'\' ;';
											print 'addUser(); ';
											print 'CloseUserBox('.$userid.');';
											print 'userid_overwrite = 0; ';
											print 'username_overwrite = ""; ';
											print 'tmp_mode=0;';
										}
									}
								}
							?>
						}
					</script>
					<?php
        	$hookmanager->resPrint.=  $resPrint_res;
      	}
*/
?>