var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
var i;
var list;
var limit;
var button = null;

var chat_resp = document.getElementById('chat_response');
var crutch = document.getElementById('crutch');

var page = window.location.pathname.split('/').pop();
var id = $('.navbar .button.is-info span').eq(0).attr('data-id');

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

function get_arr()
{

  	 var xhr_chat = new XMLHttpRequest;
          var formdata = new FormData();
          formdata.append("method", "chat");
          formdata.append("channel", page);
          xhr_chat.open('POST', baseUrl + '/chats/load', true);
          xhr_chat.send(formdata);
          xhr_chat.onloadend = function()
          {   
            if(xhr_chat.readyState == 4)
            {
            	chat_resp.innerHTML = "";


            	var res = JSON.parse(xhr_chat.responseText);


                 var len = Object.keys(res).length;
                  
             
				
				if(len > 42 && !button)
				{
				
					button = document.createElement('button');
					button.setAttribute("class", "button is-info show_all_chats");
					button.setAttribute("id", "button_show_all_chat_history");
					button.setAttribute("onclick", "show_all_shit()");
					button.innerHTML = "Show all";
					document.getElementById('chat_button').append(button);

					limit = len - 42;
				}
				else if(len > 42)
				{
					limit = len - 42;
				}
				else
				{
					limit = 0;
				}
			 

				if(len)
				{		
				   for (var j = len - 1; j >= limit; j--)
				    {
				        var block = document.createElement('div');
				        var mess_wrap = document.createElement('div');
				        var time = document.createElement('div');

				       
				        mess_wrap.setAttribute("class", "mess_wrap");
				        time.setAttribute("class", "mess_time");
				        if(res[j].sender_id == id)
				        	block.setAttribute("class", "each_message box left_mess");
				         else
				        {
				        	 var photo_container = document.createElement('div');
				        	var photo = document.createElement('img');
				        	photo_container.setAttribute("class", "chat_photo_container");
				       		 photo.setAttribute("src", baseUrl + "/app/users_photos/" + res[j].photo);
				        	block.setAttribute("class", "each_message box right_mess");
				        	photo_container.append(photo);
							block.append(photo_container);
				        }	
			
							
						mess_wrap.innerHTML = res[j].text;
						time.innerHTML = res[j].time;
						
						block.append(mess_wrap);
						block.append(time);
						$(chat_resp).prepend(block);
		
				    }
			    }
			    chat_resp.scrollTop = chat_resp.scrollHeight - chat_resp.clientHeight           
							           
            }
          }  
}


$('#messages').on("DOMSubtreeModified",function()
{	

  	get_arr();
  	chat_resp.scrollTop = chat_resp.scrollHeight - chat_resp.clientHeight;	
});

$(document).ready(function()
{

    get_arr();
    chat_resp.scrollTop = chat_resp.scrollHeight - chat_resp.clientHeight;	



});

function show_all_shit()
	{



	  	 var xhr_chat_1 = new XMLHttpRequest;
	          var formdata = new FormData();
	          formdata.append("method", "chat");
	          formdata.append("channel", page);
	          xhr_chat_1.open('POST', baseUrl + '/chats/load', true);
	          xhr_chat_1.send(formdata);
	          xhr_chat_1.onloadend = function()
	          {   
	            if(xhr_chat_1.readyState == 4)
	            {
	            	chat_resp.innerHTML = "";
	            	var res = JSON.parse(xhr_chat_1.responseText);
        
                

                 var len = Object.keys(res).length;
	                
	                 var len = Object.keys(res).length;
	                  
	              
					   for (var j = len - 1; j >= 0; j--)
					    {
					     var block = document.createElement('div');
				        var mess_wrap = document.createElement('div');
				        var time = document.createElement('div');

				       
				        mess_wrap.setAttribute("class", "mess_wrap");
				        time.setAttribute("class", "mess_time");
				        if(res[j].sender_id == id)
				        	block.setAttribute("class", "each_message box left_mess");
				         else
				        {
				        	 var photo_container = document.createElement('div');
				        	var photo = document.createElement('img');
				        	photo_container.setAttribute("class", "chat_photo_container");
				       		 photo.setAttribute("src", baseUrl + "/app/users_photos/" + res[j].photo);
				        	block.setAttribute("class", "each_message box right_mess");
				        	photo_container.append(photo);
							block.append(photo_container);
				        }	
					
							
						mess_wrap.innerHTML = res[j].text;
						time.innerHTML = res[j].time;
						
						block.append(mess_wrap);
						block.append(time);
						$(chat_resp).prepend(block);
		
					    }
				    }         
								           
	            }
	   }  





