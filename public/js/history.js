var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

var vis_resp = document.getElementById('visit_response');
var perPage = 20;
var i = 0;



$(document).ready(function()
 {

	 var win = $(window);


	  win.scroll(function() {
	    if ($(document).height() - win.height() == win.scrollTop())
	    {
	        i = perPage;
	        perPage += 16;
	        render(i, perPage);

	    } 
	  });
	    i = 0;
         perPage = 20;
        render(i, perPage);


       $(window).scroll(function()
        {

           if($(this).scrollTop() != 0)
            {

                $('#toTop').fadeIn();
           
           } 
           else
            {

                  $('#toTop').fadeOut();

           }

          });
           
           $('#toTop').click(function() {

           $('body,html').animate({scrollTop:0},800);

           });
          


	   
});	          


function render(i, pageCount)
{
	var response = document.getElementById('visits');
   var xhr_visit = new XMLHttpRequest;
	    var formdata = new FormData();
	          formdata.append("method", "load");
	          xhr_visit.open('POST', baseUrl + '/user/browsing_history', true);
	          xhr_visit.send(formdata);
	            xhr_visit.onloadend = function()
	            {
	            	var res = JSON.parse(xhr_visit.responseText);
	            
	              if (xhr_visit.readyState == 4)
	                {
	                 res = JSON.parse(xhr_visit.responseText);
	            
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
	                		block.setAttribute("class", "box history_block");
	                		var block_text = document.createElement('div');
	                		block_text.innerHTML = "You watched ";
	                		block.append(block_text);

	                		var link = document.createElement('a');
	                		link.setAttribute("href", baseUrl + "/show?profile=" + res[j].visited_id);
	                		link.innerHTML = res[j].firstName + " " + res[j].lastName;
	                		block.append(link);
	                		var block_time = document.createElement('div');
	                		block_time.innerHTML = " profile " + res[j].time;
	                		block.append(block_time);
	                		response.append(block); 
						 	
	                  }
	                  
	                }
	              }
}