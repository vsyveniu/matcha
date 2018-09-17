$(document).ready(function(){
  $('#country').change(function(){
    loadState($(this).find(':selected').val())
  })

  $('#state').change(function(){
    loadCity($(this).find(':selected').val())
  })

})

var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

function loadCountry(){
    $.ajax({
        type: "POST",
        url: baseUrl + '/getCountry',
        data: "get=country"
        }).done(function(result) {
            result = JSON.parse(result);
            $(result).each(function(){
                $("#country").append($('<option>', {
                    value: this.id + ':' + this.name,
                    text: this.name,
                }));
            })
        });
}

function loadState(countryId){
    $("#state").children().remove()
    $("#city").children().remove()
    $("#state").append($('<option selected value>'))
    $.ajax({
        type: "POST",
        url: baseUrl + '//getCountry',
        data: "get=state&countryId=" + countryId
        }).done(function(result) {
            result = JSON.parse(result);
            $(result).each(function(){
                $("#state").append($('<option>', {
                    value: this.id + ':' + this.name,
                    text: this.name,
                }));
            })
        });
}

function loadCity(stateId){
    $("#city").children().remove()
    $("#city").append($('<option selected value>'))
    $.ajax({
        type: "POST",
        url: baseUrl + '/getCountry',
        data: "get=city&stateId=" + stateId
        }).done(function(result) {
            result = JSON.parse(result);
            $(result).each(function(){
                $("#city").append($('<option>', {
                    value: this.id + ':' + this.name,
                    text: this.name,
                }));
            })
        });
}

loadCountry();