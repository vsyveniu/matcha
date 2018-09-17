var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

$(document).ready(function() {
    $.ajax({
	  	url: baseUrl + '/user/get_tags',
	  	type: 'POST',
	  	success: function(result){
	  		result = JSON.parse(result);
	  		$(result).each(function(){
                $("#show_tags ul").append('<li>' + '#' + this.tag + '</li>');
            })
    		console.log(result);
  		}
	});
});

$("#addTag").click(function(){
 	var tag = $("#tag").val();
 	$("#tag").val('');

	if(tag){
		var exist = false;
		$("#show_tags ul li").each(function(){
			if($(this).text() == '#' + tag) {
				$(this).css('color', 'red');
				exist = true;
			}
		});

		if (!exist){
  			$("#show_tags ul").append('<li>' + '#' + tag + '</li>');
			$.ajax({
			  	url: baseUrl + '/user/save_tag',
			  	type: 'POST',
			  	data: {
			  		tag: tag,
			  	},
			  	success: function(result){
		    		console.log(result);
		  		}
			});
		}
  	}	
});
