
var perPage = 16;
var i = 0;
var $notifications = $('#notification').children();


$(document).ready(function()
 {
 	add_ivents();
	var win = $(window);
	show_notifications();

	//load more notifications
	win.scroll(function() {
		if ($(document).height() - win.height() == win.scrollTop())
	    {
	    	load_new();

	        
	    } 
	});
    

    //button to top
    $(function() {
       $(window).scroll(function()
        {
           if($(this).scrollTop() != 0)
            {

                $('#toTop').fadeIn();
           
           } 
        	else
				$('#toTop').fadeOut();

          });
           
           $('#toTop').click(function() {

           $('body,html').animate({scrollTop:0},800);

           });
           
      });
});	          

function load_new(){
    i = i + perPage;
    show_notifications();
}

function show_notifications() {
	for(let j = i; j < i + perPage; j++){
		if($notifications.eq(j))
			$notifications.eq(j).show();
	}
}

function show_more(){
	$showed = $('#notification > div:visible');
	if($showed.length < 16)
		load_new();
}

function add_ivents(){
    //mark notification as viewed
	$('.viewed_button').on('click', function(){
		var data = {
			'id': $(this).parent().parent().attr('data-id')
		};
		
		$(this).remove();
		
		$.ajax({
			url: 'user/notifications/viewed',
			method: "POST", 
			data: data,
			success: function(data) 
			{
				//$("[data-id=" + data + "]").find('div').eq(1).find('button').remove();
				$("#notifications_count").text($("#notifications_count").text() - 1);
			}
		});
	})

	//delete notification
	$('.delete_notification').on('click', function(){
		let data = {
			'id': $(this).parent().attr('data-id')
		};

		if($(this).parent().find('span').eq(0).hasClass('not_viewed'))
			$("#notifications_count").text($("#notifications_count").text() - 1);

		show_more();
		$.ajax({
			url: 'user/notifications/delete',
			method: "POST", 
			data: data,
			success: function(data) 
			{
			
				$("[data-id=" + data + "]").toggle('slow');
			}
		});
	})

	//delete all notifications
	$('#delete_all_notifications').on('click', function(){
		let data = {
			'id': 'all'
		};

		$.ajax({
			url: 'user/notifications/delete',
			method: "POST", 
			data: data,
			success: function(data) 
			{
				$("#notifications_count").text(0);
				$("#notification").children().remove();
			}
		});
		$('#delete_all_notifications').remove();
	})
}

function render(i, pageCount)
{
	
	/*var response = document.getElementById('notification');
   var xhr_not = new XMLHttpRequest;
	    var formdata = new FormData();
	          formdata.append("method", "load");
	          xhr_not.open('POST', baseUrl + '/user/notifications/load', true);
	          xhr_not.send(formdata);
	            xhr_not.onloadend = function()
	            {
	         
	              if (xhr_not.readyState == 4)
	                {
	                 res = JSON.parse(xhr_not.responseText);
	          
	                  var len = Object.keys(res).length;

	                  if (perPage > len)
	                  {
	                    perPage = len;
	                  }
	                  if(i > 0)
	                    i += 1;
	                  for (var j = i; j < perPage; j++)
	                  {

	                  		
	                		var block = document.createElement('div');
	                		var block_status = document.createElement('div');
	                		var link_wrapper = document.createElement('div');
	                		var link = document.createElement('a');
	                		link.setAttribute("href",  baseUrl + "/show?profile=" + res[j].uid);
	                		link.innerHTML = (res[j].firstName + " " + res[j].lastName);

	                		block.setAttribute("class", "box each_notification");
	                		block.setAttribute("data-id", res[j].id);
	                		if(res[j].viewed == 0)
	                		{
	                			block_status.setAttribute("class", "not_viewed");
	                			var button_mark = document.createElement('button');
	                			button_mark.setAttribute("class", "button is-light is-small viewed_button");
	                			button_mark.innerHTML = "Mark as viewed";
	                			block_status.append(button_mark);

	                		}
	                		else
	                		{
	                			block_status.setAttribute("class", "viewed");
	                		}
	                		var block_text = document.createElement('div');
	                		var block_time = document.createElement('div');
	                		var button_del_each = document.createElement('button');
	                		button_del_each.setAttribute("class", "delete_notification delete is-medium");
	                		button_del_each.innerHTML = "Delete";
	                		block_text.innerHTML = res[j].text;
	                		block_time.innerHTML = res[j].time;


	                		block.append(block_status);
	                		link_wrapper.append(link);
	                		block.append(link_wrapper);
	                		block.append(block_text);
	                		block.append(block_time);
	                		block.append(button_del_each);
	                		response.append(block);
	                  }
	                  add_ivents();
	                }
	              }*/
}