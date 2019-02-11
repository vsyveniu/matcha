var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];

getValues();


function getValues()
{
  var age;
  var fame;
   var xhr_age_fame_values = new XMLHttpRequest;
     formdata = new FormData(document.forms.search_form);
          formdata.append("method", "getMaxValues");
            xhr_age_fame_values.open('POST', baseUrl + '/search', true);
            xhr_age_fame_values.send(formdata);
           xhr_age_fame_values.onloadend = function()
            {
              if (xhr_age_fame_values.readyState == 4)
                {
                  max_values = JSON.parse(xhr_age_fame_values.responseText);
                  age = parseInt(max_values[0]);
                  fame = parseInt(max_values[1]);
                  age_fun(age);
                  fame_rate(fame);
                }
                
            }
  }




function age_fun(age) {

     $( "#slider-range_age" ).slider({
     range: true,
     min: 18,
     max: age,
     values: [ 18, age],
     slide: function( event, ui ) {
     $( "#age_gap" ).val(ui.values[ 0 ] + " - " + ui.values[ 1 ] );
     }
     });
     $( "#age_gap" ).val($( "#slider-range_age" ).slider( "values", 0 ) +
     " - " + $( "#slider-range_age" ).slider( "values", 1 ) );
}

function fame_rate(fame) {
   $( "#slider-range_fame" ).slider({
   range: true,
   min: 1,
   max: fame,
   values: [ 1, fame],
   slide: function( ewvent, ui ) {
   $( "#fame_gap" ).val(ui.values[ 0 ] + " - " + ui.values[ 1 ] );
   }
   });
   $( "#fame_gap" ).val($( "#slider-range_fame" ).slider( "values", 0 ) +
   " - " + $( "#slider-range_fame" ).slider( "values", 1 ) );
}
