
	var check_interval;
	var mute = false;
	var active = false;
	var sendwithenter;
	var org_user;
	// DOCUMENT READY FUNCTION
	$(document).ready(function(){
	    $( "#progressbar" ).progressbar({
	    		value: false
	    });

	    progressbar = $( "#progressbar" ),
	   	progressbar.css({
			"position": "absolute",
        	"width": "500px",
        	"height": "10px",
        	"top": "15px",
        	"left": "42px",
        	"display": "none"
        });
        progressbarValue = progressbar.find( ".ui-progressbar-value" );
	    progressbarValue.css({
          "background": '#2dce23'
        });

		eraseCookie('chatmessage_alert');
		
		if (("Notification" in window))
		{
			Notification.requestPermission();
		}

		check_chats(1);
		userselfid = $('#userid_input').val();
		org_user = userselfid;
		$('#user_search').on('keyup', function(){

			$('div[name="user_box_kontakt"]').hide();
			$('div[name="user_box_chat"]').hide();

			search = $(this).val();
			search = search.toLowerCase();
			if(search == ""){
				if(switch_tmp == true)
				{
					$('div[name="user_box_chat"]').show();
					switch_user_chat();
				}
				else
				{
					$('div[name="user_box_chat"]').show();
					switch_user_contatct();
				}
			}
			else
			{
				$( "div[lastname*='"+search+"']" ).show();
				$( "div[firstname*='"+search+"']" ).show();
			}
		});

		if(readCookie('mute' + userselfid)) mute = readCookie('mute' + userselfid);
		if(mute == true)
		{
			$('#mute').attr('src', 'img/mute_off.png');
		}

		$('#mute').on('click', function(){
			if(mute == false)
			{
				createCookie('mute' + userselfid, '1', 365);
				$('#mute').attr('src', 'img/mute_off.png');
				mute = true;
			}
			else
			{
				eraseCookie('mute' + userselfid) ;
				$('#mute').attr('src', 'img/mute.png');
				mute = false;
			}
		});

		sendwithenter = readCookie('sendwithenter' + userselfid);
		if(sendwithenter == 1)
		{
			$('#EnterSenden').prop( "checked", true );
		}

		$('#EnterSenden').on('click', function(){
			Eval = $('#EnterSenden').prop( "checked" );
			if(Eval == true)
			{
				createCookie('sendwithenter' + userselfid, '1', 365);
				sendwithenter = true;
			}
			else
			{
				eraseCookie('sendwithenter' + userselfid) ;
				sendwithenter = false;
			}
		});

		$('#message').on('keydown', function(e){
			if ( e.which == 13 && e.shiftKey == false) 
			{
				if(sendwithenter == true)
				{
					SendText($('#nameforsend').val());
				}
			}
		});

		active = true;
		$(window).blur(function(){
		   active = false;
		   $('#active_stat').val(2);
		});

		$(window).focus(function(){
		  Title_changer_Intervall('Inactive', 2000, true);
		  active = true;
		  setread();
		  $('#active_stat').val(1);
		});

		chat_user = readCookie('chat_user' + userselfid);
		if(chat_user > 0)
		{
			change_chat_user(chat_user);
		}

	}); // READY DOCUMENT FUNCTION


	function setread()
	{
		$.ajax({
		    method: "GET",
		    url: "core/ajax/ajax_proc_status.php",
		    data: { ges_id: user_id_tmp }
		});
	}

	var title_intervall;
	function Title_changer_Intervall(title, freq, off)
	{
		if( off == false)
		{
			clearInterval(title_intervall);
			title_intervall = setInterval(Title_changer, freq, title, freq);	
		}
		else
		{
			clearInterval(title_intervall);
		}
	}

	function Title_changer(title, freq)
	{
		old = document.title;
		document.title = title;
		setTimeout(function(){ document.title = old; }, (freq / 2));
	}

	var switch_tmp = true;
	function switch_user_chat()
	{
		switch_tmp = true;
		$('input[name="chats"]').css('background-color', '#FFFFFF');
		$('input[name="contacts"]').css('background-color', '#F1F1F1');
		$('div[name="user_box_kontakt"]').hide();
	}

	function switch_user_contatct()
	{
		switch_tmp = false;
		$('input[name="contacts"]').css('background-color', '#FFFFFF');
		$('input[name="chats"]').css('background-color', '#F1F1F1');
		$('div[name="user_box_kontakt"]').show();
	}

	var user_id_tmp = 0;
	function change_chat_user(userid)
	{
		eraseCookie('chat_user' + userselfid);
		createCookie('chat_user' + userselfid, userid, 365);
		$('div[name="user_box_chat"]').css('background-color', '');
		$('div[name="user_box_kontakt"]').css('background-color', '');
		$('#user_detail_box_'+userid).css('background-color', '#DDFFE0');

		user_id_tmp = userid;
		$('#uploadF').attr('src', $('#dolichat_path').val()+'/lib/indexN.php?cuser='+userid); // for User Pic Upload
		$('#mainframe').attr('src', $('#dolichat_path').val()+'/indexFrameChatMain.php?cuser='+userid); // for User Chat

		tmp_html = $('#user_detail_box_' + userid).html();
		$('#chat_detail').html(tmp_html);

		$('#chat_detail').css('background-color', '#DDFFE0');

		cnt_usr[userid] = 1;
		$('div[name="UserDetailLastMessageCNT_' + userid + '"]').fadeOut();
		$('div[name="UserDetailLastMessageCNT_' + userid + '"]').text('');

		$.ajax({
		    method: "GET",
		    url: "core/ajax/ajax_proc_status.php",
		    data: { ges_id: userid }
		});
	}

	function SendText(nick) 
    {   
    	//var eintrag = CKEDITOR.instances['message'].getData();
		var eintrag = ($('#message').val()).trim();
        //var nick = '';
        var cuser = '';
        var PicSavedID = 0;

       	cuser = user_id_tmp;

        $('#message').val('');    
        $('#message').focus();
        //CKEDITOR.instances['message'].setData('');

        if(eintrag == ''){

        }else{
        	$( "#load" ).fadeIn("slow");
        	console.log(eintrag);
			//eintrag = btoa(eintrag);
			//console.log(eintrag);
			$.ajax({
				method: "POST",
				url: "core/ajax/ajax_proc_dbase.php",
				data: { 
					eintrag: eintrag, 
					nick: nick, 
					cuser: cuser, 
					PicSavedID: PicSavedID 
				}
			})
			.done(function( msg ) {
				$('#load').delay(1000).fadeOut('slow');
			});

        }   

       	$('#SavedPicID').val('');  	  
    }

    var last_rowid = [];
    var last_user = [];
    var cnt_usr = [];
    function check_chats(ignor)
    {
    	$(document).ready(function(){
	    	$.ajax({
			    method: "GET",
			    url: "core/ajax/ajax_proc_status.php",
			    data: { pruf: 2, tmp_user: org_user }
			})
			.done(function( msg1 ) {
				var stat_full_arr = msg1.split('%<|>%'); // new array
				$.each(stat_full_arr, function(index, stat_arr){
					var stat_arr = stat_arr.split('%<>%'); // array value
				
					if(ignor == 0)
					{
						if(stat_arr.length > 1 && org_user == stat_arr[4]) // neu
						{
							if(last_rowid[stat_arr[1]] != stat_arr[0])
							{
								if(last_rowid[stat_arr[1]] != stat_arr[0])
								{
									last_rowid[stat_arr[1]] = stat_arr[0];
								}
								if(last_user[stat_arr[1]] != stat_arr[1])
								{
									last_user[stat_arr[1]] = stat_arr[1];
								}

								if(typeof cnt_usr[last_user[stat_arr[1]]] == "undefined")
								{
									if(user_id_tmp != last_user[stat_arr[1]]) cnt_usr[last_user[stat_arr[1]]] = 1;
								}
								else
								{
									if(user_id_tmp != last_user[stat_arr[1]]) cnt_usr[last_user[stat_arr[1]]] = cnt_usr[last_user[stat_arr[1]]] + 1;
								}
								//$('div[name="UserDetailLastMessageText_' + last_user + '"]').text(last_rowid[stat_arr[1]]);
								if(user_id_tmp != last_user[stat_arr[1]] || (active == false &&  last_user[stat_arr[1]] == stat_arr[5] ))
								{
									$('div[name="UserDetailLastMessageCNT_' + last_user[stat_arr[1]] + '"]').text('+'+stat_arr[6]);
									$('div[name="UserDetailLastMessageCNT_' + last_user[stat_arr[1]] + '"]').fadeIn();
									if(mute == false)
									{
										document.getElementById('note_sound').play();
									}
									sort_user_boxes(last_user[stat_arr[1]]);
									last_user_name = $('#user_name_of_' + last_user[stat_arr[1]]).val();
									notifyMe(stat_arr[2], stat_arr[3], last_user_name); // last_user

									mesges = 0;
									$.each( cnt_usr, function( key, value ) {
										if(value > 0) mesges = mesges + value;
									});

									Title_changer_Intervall( mesges + ' New Message!', 2000, false);
								}

								$('div[name="UserDetailLastMessageText_'+last_user[stat_arr[1]]+'"]').text(stat_arr[2]);
								$('div[name="UserDetailLastMessageTime_'+last_user[stat_arr[1]]+'"]').text(stat_arr[3]);
							}
							console.log('org_user: '+org_user+' user_tmp: ' + user_id_tmp + ' rowid:' + last_rowid[stat_arr[1]] + ' user:' + last_user[stat_arr[1]]);
							console.log(stat_arr);
						}
					}
					else
					{
							last_rowid[stat_arr[1]] = stat_arr[0];
							last_user[stat_arr[1]] = stat_arr[1];
							check_interval = setInterval(check_chats, 3000, 0);
					}
					//
				});
			});
		});
    }
	
    function sort_user_boxes(userid)
    {
    	$('#user_detail_box_'+userid).hide("slide", function() 
    	{ 
	    	ushtml = $('#user_detail_box_'+userid).clone();
	    	$('#user_detail_box_'+userid).remove();
	    	$('.UserChatDivIn').prepend(ushtml);
	    	$('#user_detail_box_'+userid).show("slide");
	    });
    }

    function addimgstart()
    {
   		progressbar = $( "#progressbar" ),
        progressbarValue = progressbar.find( ".ui-progressbar-value" );

		progressbar.css({
			"display": ""
		});
    }

	function imgload(proc)
	{
		$( "#load_text" ).text(proc + '%');
		$( "#load" ).fadeIn("slow");
		if(proc == 100)
		{
			$( "#load_text" ).text('Sended');
			$( "#load" ).delay(1000).fadeOut('slow', function(){
				$( "#load_text" ).text('Send');
			});
		}

		progressbar = $( "#progressbar" ),
        progressbarValue = progressbar.find( ".ui-progressbar-value" );

		progressbar.progressbar( "option", {
          value: proc
        });

        if(proc == 100){
			progressbar.fadeOut();
        }
	}

	function imgload_faild()
	{
		progressbar = $( "#progressbar" ),
        progressbarValue = progressbar.find( ".ui-progressbar-value" );
		progressbar.css({
			"background": "red"
		});
	}

	function createCookie(name, value, days) 
	{
		var expires;
	
		if (days) 
		{
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toGMTString();
		} 
		else 
		{
			expires = "";
		}
		document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
	}

	function readCookie(name) 
	{
		var nameEQ = encodeURIComponent(name) + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) 
		{
			var c = ca[i];
			while (c.charAt(0) === ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
		}
		return null;
	}

	function eraseCookie(name) 
	{
		createCookie(name, "", -1);
	}

	function notifyMe(text, time, username) {
		
		var options = {
			body: text,
			icon: 'img/dolichat_note.png'
		}

		if (!("Notification" in window)) 
		{
			// Let's check if the browser supports notifications
			// alert("This browser does not support system notifications");
		} 
		else if (Notification.permission === "granted") 
		{
			// Let's check whether notification permissions have already been granted
			// If it's okay let's create a notification
			var notification = new Notification(username + ' ' + time , options);
		}
		else if (Notification.permission !== 'denied') 
		{ 
			// Otherwise, we need to ask the user for permission
			Notification.requestPermission(function (permission) 
			{
				// If the user accepts, let's create a notification
				if (permission === "granted") 
				{
					var notification = new Notification(username + ' ' + time , options);
				}
			});
		}
		
	  // Finally, if the user has denied notifications and you 
	  // want to be respectful there is no need to bother them any more.
	}