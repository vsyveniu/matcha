$(document).ready(function() {
	setInterval(function(){ 
		$.ajax({
			type: "POST",
			url: baseUrl + "/user/live_note",
			data: {
				now: Math.floor(Date.now() * 0.001)
			},
			success: function(response){
				//console.log(response);
			}
		}); 
	}, 3000);

	$('#tags-select').multiselect({
		nonSelectedText: 'Select tags',
		buttonWidth: '100%',
		maxHeight: 200

	});

	$('#search_form').on('submit', function(event){
		var form_data = $(this).serialize();
		$.ajax({
			url: $('head base').attr('href') + 'search',
			method: "POST",
			data: form_data,
			success: function(data) 
			{
				$('#tags-select option:selected').each(function(){
					$(this).prop('selected', false);
				});
				$('#tags-select').multiselect('refresh');
			}
		});
	});
});