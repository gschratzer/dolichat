	// noprotect
	var user_chat_active = true;
	var statusWindow = document.getElementById('status');
	var off_notify = false;
	(function (win)
	{
		//Private variables
		var _LOCALSTORAGE_KEY = 'WINDOW_VALIDATION';
		var RECHECK_WINDOW_DELAY_MS = 100;
		var _initialized = false;
		var _isMainWindow = false;
		var _unloaded = false;
		var _windowArray;
		var _windowId;
		var _isNewWindowPromotedToMain = false;
		var _onWindowUpdated;


		function WindowStateManager(isNewWindowPromotedToMain, onWindowUpdated)
		{
			//this.resetWindows();
			_onWindowUpdated = onWindowUpdated;
			_isNewWindowPromotedToMain = isNewWindowPromotedToMain;
			_windowId = Date.now().toString();

			bindUnload();

			determineWindowState.call(this);

			_initialized = true;

			_onWindowUpdated.call(this);
		}

		//Determine the state of the window 
		//If its a main or child window
		function determineWindowState()
		{
			var self = this;
			var _previousState = _isMainWindow;

			_windowArray = localStorage.getItem(_LOCALSTORAGE_KEY);

			if (_windowArray === null || _windowArray === "NaN")
			{
				_windowArray = [];
			}
			else
			{
				_windowArray = JSON.parse(_windowArray);
			}

			if (_initialized)
			{
				//Determine if this window should be promoted
				if (_windowArray.length <= 1 ||
				   (_isNewWindowPromotedToMain ? _windowArray[_windowArray.length - 1] : _windowArray[0]) === _windowId)
				{
					_isMainWindow = true;
				}
				else
				{
					_isMainWindow = false;
				}
			}
			else
			{
				if (_windowArray.length === 0)
				{
					_isMainWindow = true;
					_windowArray[0] = _windowId;
					localStorage.setItem(_LOCALSTORAGE_KEY, JSON.stringify(_windowArray));
				}
				else
				{
					_isMainWindow = false;
					_windowArray.push(_windowId);
					localStorage.setItem(_LOCALSTORAGE_KEY, JSON.stringify(_windowArray));
				}
			}

			//If the window state has been updated invoke callback
			if (_previousState !== _isMainWindow)
			{
				_onWindowUpdated.call(this);
			}

			//Perform a recheck of the window on a delay
			setTimeout(function()
					   {
						 determineWindowState.call(self);
					   }, RECHECK_WINDOW_DELAY_MS);
		}

		//Remove the window from the global count
		function removeWindow()
		{
			var __windowArray = JSON.parse(localStorage.getItem(_LOCALSTORAGE_KEY));
			for (var i = 0, length = __windowArray.length; i < length; i++)
			{
				if (__windowArray[i] === _windowId)
				{
					__windowArray.splice(i, 1);
					break;
				}
			}
			//Update the local storage with the new array
			localStorage.setItem(_LOCALSTORAGE_KEY, JSON.stringify(__windowArray));
		}

		//Bind unloading events  
		function bindUnload()
		{
			win.addEventListener('beforeunload', function ()
			{
				if (!_unloaded)
				{
					removeWindow();
				}
			});
			win.addEventListener('unload', function ()
			{
				if (!_unloaded)
				{
					removeWindow();
				}
			});
		}

		WindowStateManager.prototype.isMainWindow = function ()
		{
			return _isMainWindow;
		};

		WindowStateManager.prototype.resetWindows = function ()
		{
			localStorage.removeItem(_LOCALSTORAGE_KEY);
		};

		win.WindowStateManager = WindowStateManager;
	})(window);

	var WindowStateManager = new WindowStateManager(false, windowUpdated);

	var window_state = '';
	function windowUpdated()
	{
		//"this" is a reference to the WindowStateManager
		//statusWindow.className = (this.isMainWindow() ? 'main' : 'child');
		console.log((this.isMainWindow() ? 'main' : 'child'));
		window_state = (this.isMainWindow() ? 'main' : 'child');

		if(window_state == 'main')
		{
			if(user_chat_active == false)
			{
				console.log('notify user about message!');
				notify = true;
			}
			else
			{
				console.log('show user chat message!');
				notify = true;
			}

			check_chats(1);
		}
		
		check_alert();
	}
	//Resets the count in case something goes wrong in code
	//WindowStateManager.resetWindows()

	$( document ).ready(function() {
		userselfid = $('#userid_input').val();
		org_user = userselfid;
		
		$(window).blur(function(){
			user_chat_active = false;
			if(window_state == 'main')
			{
				console.log('notify user about message!');
				notify = true;
			}
			else
			{
				console.log('do nothing!');
			}
		});
		
		$(window).focus(function(){
			user_chat_active = true;
			if(window_state == 'main')
			{
				console.log('show user chat message!');
				notify = true;
			}
			else
			{
				console.log('do nothing!');
			}
		});	

		$('#dolichat_alert_global').click(function(){
			off_notify = true;
		});
	});

	var check_interval;
	var mute = false;
	var active = false;
	var sendwithenter;
	var org_user;
	var user_id_tmp = 0;

 	var last_rowid = [];
    var last_user = [];
    var cnt_usr = [];
    function check_chats(ignor)
    {
    	$(document).ready(function(){
	    	$.ajax({
			    method: "GET",
			    url: "/dolichat/core/ajax/ajax_proc_status.php",
			    data: { pruf: 2, tmp_user: org_user, get_user_info: 1 }
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
									
									//sort_user_boxes(last_user[stat_arr[1]]);
									last_user_name = stat_arr[7];

									off_notify = false;
									$('#dolichat_alert_global').attr('href', '/dolichat/index.php?user='+stat_arr[5]);
									alert_callsign();
									createCookie('chatmessage_alert', 'true', 1);
									notifyMe(stat_arr[2], stat_arr[3], last_user_name); // last_user

									mesges = 0;
									$.each( cnt_usr, function( key, value ) {
										if(value > 0) mesges = mesges + value;
									});

									//Title_changer_Intervall( mesges + ' New Message!', 2000, false);
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
				});
			});
		});
    }

    var auto_notify = false;
    function check_alert()
    {
    	testv = readCookie('chatmessage_alert');
    	if(testv === null && $('#dolichat_unseen').val() == false)
    	{
	    	off_notify = true;
		}
		else
		{
			off_notify = false;
			auto_notify = true;
			$('#dolichat_alert_global_count').html('!');
			//alert_callsign();
		}
		setTimeout(function(){ 
			if(auto_notify == true) $('#dolichat_alert_global_count').html('');
			setTimeout(function(){ 
    			check_alert();
    		}, 1000);
		}, 1000);
    }

    function alert_callsign()
    {
    	if(off_notify != true)
    	{
	    	$('#dolichat_alert_global_count').html('!');
	    	setTimeout(function(){ 
	    		$('#dolichat_alert_global_count').html('');
	    		setTimeout(function(){ 
	    			alert_callsign();
	    		}, 1000);
	    	}, 1000);
	    }
    }

    alert_support = false;
	function notifyMe(text, time, username) {
		
		var options = {
			body: text,
			icon: 'img/dolichat_note.png'
		}

		if (!("Notification" in window) && alert_support == false) 
		{
			alert_support = true;
			// Let's check if the browser supports notifications
			//alert("This browser does not support system notifications");
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
