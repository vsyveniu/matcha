//Connect to Pusher
Pusher.logToConsole = false;
var pusher = new Pusher('fd0440e60019404539bf', {
	cluster: 'eu',
	forceTLS: true
});
var $user_id = $('.navbar .button.is-info span').eq(0).attr('data-id');
var page = window.location.pathname.split('/').pop();
var profile = getUrlParameter('profile');

$(document).ready(function() {
	$.ajax({
		url: 'is_auth',
		method: "GET",
		success: function(data) 
		{
			if(data){
				//Get count of unreaded notifications and unreand chats messages and insert to the header
				count_unread();

				//connect to all avaliabel chats
				connect_to_notifications_chanel();
				connect_to_chats_chanels();
			}
		}
	});

	/********* Browsing history page ***********/

	//delete all notifications
	$('#delete_all_visits').on('click', function(){
		$.ajax({
			url: 'user/browsing_history/clear_history',
			method: "GET", 
			success: function(data) 
			{
				$("#visits").children().toggle('slow');
			}
		});
	})


	/***********  show_profile page **************/

	//likes on click
	$('#like').on("click",function(){
	    if ($(this).attr('data-type') == 'like'){
	        $(this).text('dislike');
	        $(this).attr('data-type', 'dislike');
	    }
	        
	    else{
	        $(this).text('like');
	        $(this).attr('data-type', 'like');
	    }
	    
	    let data = {
	        'profile': profile
	    }
	    $.ajax({
	        url: 'user/like',
	        method: "POST",
	        data: data,
	        success: function(data) 
	        {
	        	if(data.chanel)
	        		connect_to_chat_chanel(data.chanel);
	        }
	    });
	});

	$('#fake').on('click', function(){
		let data = {
	        'id': $(this).attr('data-id')
		}
		$.ajax({
	        url: 'user/report_as_fake',
	        method: "POST",
	        data: data,
	        success: function(data) 
	        {
				$('#block_response').text('Reported');
	        	/*if(data.chanel)
	        		connect_to_chat_chanel(data.chanel);*/
	        }
	    });
	});

	/********* Chat page ***********/
	$('#send_message').on('click', function(){
		let $message = $('#message').val().trim();

		if($message){
			send_chat_message($message);
			$('#message').val('');
			//save_chat_message($message);
		}
	});
});


/********* Functions ***********/

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function connect_to_notifications_chanel(){
	let channel = pusher.subscribe('notification-message-' + $user_id);

	channel.bind('notification', function(data) {
		$("#notifications_count").text(Number($("#notifications_count").text()) + 1);

		if(data.chanel){
			connect_to_chat_chanel(data.chanel);
		}
			
		//if user watching notification page - add new notification message to page
		if(page === 'notifications')
			setTimeout(function(){ location.reload(); }, 800);
	});
}

function connect_to_chats_chanels(){
	$.ajax({
		url: 'chats/get_chats',
		method: "GET",
		success: function(data) 
		{
			if(data){
				data.forEach(function(chat_id){
					connect_to_chat_chanel(chat_id);
				})
			}
		}
	});
}

function connect_to_chat_chanel(chat_id){
	let channel = pusher.subscribe('chat-' + chat_id);
	channel.bind('message', function(data) {
		$('#messages').append('<p>' + data.message + '</p>');
	});
}

function count_unread() {
	$.ajax({
		url: 'user/notifications/count',
		method: "POST",
		success: function(data) 
		{
			$("#notifications_count").text(Number(data));
		}
	});
}

function send_chat_message($message) {
	let data = {
		'message': $message,
		'chat_chanel': page
	}

	$.ajax({
		url: 'chats/send_message',
		method: "POST",
		data: data,
		success: function(data) 
		{
			if(data === 'not-active')
				location.reload();
		}
	});
}
