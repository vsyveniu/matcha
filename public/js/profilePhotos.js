$(document).ready(function(){
  var photos = $("#hidden_photos").children("span").toArray();
	var a = [];
	var counter = $("#counter").text();
	var getUrl = window.location;
	var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

	for (var i = 0; i < photos.length; i++) {
  	a.push(baseUrl + "/app/users_photos/" + photos[i].innerHTML);
  }
	$("#main_photo").attr("src", a[0]);

	if(photos.length > 1){
		$("#pag").css("display", "block");
	}

	$("#next").click(changePhoto);
	$("#prev").click(changePhoto);

	function changePhoto(event){
		if(this.id == "next") {
			counter = parseInt(counter) + 1;
			$("#change_main_photo").css("display", "block");
		}
		else {
			counter = parseInt(counter) - 1;
		}
		$("#counter").text(counter);

		if(counter == photos.length - 1){
			$("#next").prop( "disabled", true);
		}
		if(counter > 0){
			$("#prev").prop( "disabled", false);
		}
		if(counter == 0){
			$("#prev").prop( "disabled", true);
			$("#change_main_photo").css("display", "none");
		}
		if(counter < photos.length - 1){
			$("#next").prop( "disabled", false);
		}

		$("#main_photo").attr("src", a[counter]);

		$("#change_main_photo").children("a").attr("href", baseUrl + "/public/user/profile?photo=" + counter);
	}

});

