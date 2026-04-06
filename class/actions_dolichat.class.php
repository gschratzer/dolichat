<?php

dol_include_once('/dolichat/class/dolichat.class.php');

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
        	if($user->rights->dolichat->UseChat && $_SERVER['PHP_SELF'] != dol_buildpath('/dolichat',1)."/index.php" && $_SERVER['PHP_SELF'] != "htdocs/dolichat/index.php" && $_SERVER['PHP_SELF'] != DOL_URL_ROOT."/dolichat/index.php")
        	{
				$dolichat = new dolichat($db);
				$unreadSenders = $dolichat->getUnreadSenders($user->id);
				$gese = !empty($unreadSenders) ? $unreadSenders[0] : null;
				if(is_object($gese) && !empty($gese->rowid))
				{
					$unseen = true;
					$callsign = '!';
					$ch_user = '?user='.$gese->user_id;
				}
				else
				{
					$unseen = false;
				}

				$hookmanager->resPrint .= '<div class="inline-block"><a href="'.dol_buildpath('/dolichat',1).'/index.php'.$ch_user.'" id="dolichat_alert_global" target="_blank">';
					$hookmanager->resPrint .= '<img src="'.dol_buildpath('/dolichat',1).'/img/dolichat.png" style="width:20px;">';
					$hookmanager->resPrint .= '<span style="color: rgba(255, 255, 255, 0.75); font-size: 20px;" id="dolichat_alert_global_count">'.$callsign.'</span>';
				$hookmanager->resPrint .= '</a></div>';
				$hookmanager->resPrint .= '<input type="hidden" id="userid_input" value="'.$user->id.'">';
				$hookmanager->resPrint .= '<input type="hidden" id="dolichat_unseen" value="'.$unseen.'">';
				$hookmanager->resPrint .= '<script type="text/javascript" src="'.dol_buildpath('/dolichat',1).'/js/global_chat.js"></script>';
				return 1;
        	}
        	else
        	{
        		$hookmanager->resPrint .= '<input type="hidden" id="dolichat_path" value="'.dol_buildpath('/dolichat',1).'">';
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
?>