var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
var map, infoWindow, mark, pos, postofillForm;


function initMapSearch() 
    {


    map = new google.maps.Map(document.getElementById('map_search'), 
    {
        center: {lat: -7.460, lng: -139.062},
        zoom: 6
    });



      xhr = new XMLHttpRequest();
      xhr.open('POST', baseUrl + '/search', true);
      var formdata = new FormData();
      formdata.append("method", "setAutoPosition");
      xhr.send(formdata); 
      xhr.onloadend = function()
      {   
        if (xhr.readyState == 4)
        {
          positionfrombase = JSON.parse(xhr.responseText);
          document.getElementById('chosen_country').innerHTML = positionfrombase.country;
          document.getElementById('chosen_state').innerHTML = positionfrombase.state;
          document.getElementById('chosen_city').innerHTML = positionfrombase.city;
          infoWindow = new google.maps.InfoWindow;
           if (navigator.geolocation)
          {
              navigator.geolocation.getCurrentPosition(function(position)
              {
                pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

          if(positionfrombase.manual == 1)
          {
             pos = {
              lat: positionfrombase.lat,
              lng: positionfrombase.lng
             };

             var latLng = new google.maps.LatLng(positionfrombase.lat, positionfrombase.lng);

              mark = new google.maps.Marker({
                    position: latLng,
                    map: map
                  });
               map.setCenter(latLng);
          }
          else 
          {
              infoWindow.setPosition(pos);
              infoWindow.setContent('You are here');
              infoWindow.open(map);
              map.setCenter(pos);

          }

              return(pos);  
              }, 
              function() 
              {

                if(positionfrombase.manual == 1)
                {
                   pos = {
                    lat: positionfrombase.lat,
                    lng: positionfrombase.lng
                   };

                   var latLng = new google.maps.LatLng(positionfrombase.lat, positionfrombase.lng);

                    mark = new google.maps.Marker({
                          position: latLng,
                          map: map
                        });
                     map.setCenter(latLng);
                }
                handleLocationError(true, infoWindow, map.getCenter());
              });

            } 
          else 
          {
              handleLocationError(false, infoWindow, map.getCenter());
          }

          google.maps.event.addListener(map, 'click', function(event) 
          {
              if(!mark)
              {
                mark = new google.maps.Marker({
                  position: event.latLng,
                  map: map
                });
              }
              else if(mark)
              {
                 mark.setMap(null);
                 mark = new google.maps.Marker({
                 position: event.latLng,
                 map: map
                });
              }

             xhr = new XMLHttpRequest;
             formdata = new FormData();
             formdata.append("method", "getPosition");
             formdata.append("posLat", mark.getPosition().lat());
             formdata.append("posLong", mark.getPosition().lng());
             xhr.open('POST', baseUrl + '/search', true);
             xhr.send(formdata);
             xhr.onreadystatechange = function()
             {
              if (xhr.readyState == 4)
                {
                  postofillForm = JSON.parse(xhr.responseText);
                 document.getElementById('chosen_country').innerHTML = postofillForm.country;
                 document.getElementById('chosen_state').innerHTML = postofillForm.state;
                 document.getElementById('chosen_city').innerHTML = postofillForm.city;

                }
              }   
             }); 
          }
      }

    }


    function handleLocationError(browserHasGeolocation, infoWindow, pos) 
    {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'You must allow a geolocation to be located automatically. ' :
                              'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
        //sendCoords("", "");
    }



var perPage = 20;
var i = 0;




var submit_search = document.getElementById('submit_search');


if(submit_search)
{
  

  submit_search.onclick = function(e)
  
  {
    e.preventDefault();
      var xhr_submit = new XMLHttpRequest;
              formdata = new FormData(document.forms.search_form);
          formdata.append("method", "fuck");
            xhr_submit.open('POST', baseUrl + '/search', true);
            xhr_submit.send(formdata);
            xhr_submit.onloadend = function()
            {
              if (xhr_submit.readyState == 4)
                {
                    document.getElementById('gallery_response').innerHTML = "";
                    document.getElementById('gallery_location_response').innerHTML = "";
                    document.getElementById('gallery_location_city_response').innerHTML = "";
                    document.getElementById('gallery_location_state_response').innerHTML = "";
                
                  i = 0;
                  perPage = 20;
                  render(i, perPage);
                }
            }   
  }
}


$(document).ready(function() {
  var win = $(window);


  win.scroll(function() {
    if ($(document).height() - win.height() == win.scrollTop())
    {
        i = perPage;
        perPage += 16;
        render(i, perPage);
    } 
  });

  enableSubmit();


  $(function() {

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

});

function enableSubmit(){
      document.getElementById("submit_search").disabled = true;
      setTimeout(function(){document.getElementById("submit_search").disabled = false;},3000);
  }

function render(i, pageCount)
{
   var xhr_gal = new XMLHttpRequest;

            var country = document.getElementById('chosen_country').innerHTML;
            var state = document.getElementById('chosen_state').innerHTML;
            var city = document.getElementById('chosen_city').innerHTML;
            xhr_gal = new XMLHttpRequest;
            formdata = new FormData(document.forms.search_form);
            formdata.append("method", "showGallery");
            formdata.append("country", country);
            formdata.append("state", state);
            formdata.append("city", city);
            xhr_gal.open('POST', baseUrl + '/search', true);
            xhr_gal.send(formdata);

            xhr_gal.onloadend = function()
            {

              if (xhr_gal.readyState == 4)
                {
                 gallery_array = JSON.parse(xhr_gal.responseText);

                  if(gallery_array == "error")
                  {
                    alert("I don't even want to know what you entered to get this place, if you do this accidantelly, otherwise it means you trying to illegally sneak into system and do some bad things. In that case i wish you can't plug a flashdrive in right position at first attempt till the end of your days... ");
                    return ;
                  }
                  var len = Object.keys(gallery_array).length;

    
                  if (perPage > len)
                  {
                    perPage = len;
                  }
                  if(i > 0)
                    i += 1;
       
                  for (var j = i; j < perPage; j++)
                  {
                    var section = document.createElement('section');
                    var section_location = document.createElement('div');
                    var section_location_state = document.createElement('div');
                    var section_location_city = document.createElement('div');
                    section.setAttribute("class", "user_card_wrapper animated fadeIn");
                    section_location.setAttribute("class", "user_card_wrapper_location animated fadeIn");
                    section_location_city.setAttribute("class", "user_card_wrapper_location animated fadeIn");
                    section_location_state.setAttribute("class", "user_card_wrapper_location animated fadeIn");
          
                    
                    var block_img =  document.createElement('div');
                    block_img.setAttribute("class", "user_card_img_wrapper");

                    var img_link =  document.createElement('a');
                    img_link.setAttribute("class", "user_card_img_link");
                     img_link.setAttribute("class", "image");
                    img_link.setAttribute("href", baseUrl +"/show?profile=" + gallery_array[j].id);


                    var img =  document.createElement('img');
                    img.setAttribute("class", "is-rounded");
                    if(gallery_array[j].id <= 1000)
                       img.setAttribute("src", gallery_array[j].mainPhoto);
                     else
                      img.setAttribute("src", baseUrl +"/app/users_photos/"+ gallery_array[j].mainPhoto);
                    img_link.append(img);
                     block_img.append(img_link);

                    var block_name =  document.createElement('div');
                    block_name.setAttribute("class", "user_card_name");
                    block_name.setAttribute("class", "tag is-rounded");
                
                    block_name.innerHTML = gallery_array[j].firstName;

                    var block_age =  document.createElement('div');
                    var block_age_label =  document.createElement('div');
                    block_age.setAttribute("class", "user_card_age");
                    block_age.setAttribute("class", "tag is-rounded");
                    block_age.innerHTML = "Age: " + gallery_array[j].age;


                     var block_gender =  document.createElement('div');
                    var block_gender =  document.createElement('div');
                    block_gender.setAttribute("class", "user_card_gender");
                    block_gender.setAttribute("class", "tag is-rounded");
                    block_gender.innerHTML = gallery_array[j].gender;


                    var block_status =  document.createElement('div');
                    block_status.setAttribute("class", "user_card_status");
                     block_status.setAttribute("class", "tag is-rounded");
                    block_status.innerHTML = gallery_array[j].status;


                     var block_country =  document.createElement('div');
                    block_country.setAttribute("class", "user_card_country");
                      block_country.setAttribute("class", "tag is-rounded");
                    block_country.innerHTML = gallery_array[j].country;

                     var block_fame =  document.createElement('div');
                    block_fame.setAttribute("class", "user_card_fame");
                     block_fame.setAttribute("class", "tag is-rounded");
                    block_fame.innerHTML = "Fame: " + gallery_array[j].rating;

                     var block_common_tags =  document.createElement('div');
                    block_common_tags.setAttribute("class", "user_card_common_tags");
                     block_common_tags.setAttribute("class", "tag is-rounded");
                    block_common_tags.innerHTML = "Tags: " + gallery_array[j].count;
                    
                    if(gallery_array[j].city == city && city != "")
                    {
                      section_location_city.append(block_img);
                      section_location_city.append(block_name);
                      section_location_city.append(block_age);
                      section_location_city.append(block_gender);
                      section_location_city.append(block_status);
                      section_location_city.append(block_country);
                      section_location_city.append(block_fame);
                      section_location_city.append(block_common_tags);
                      gallery_location_city_response.append(section_location_city);
                    }
                     else if(gallery_array[j].state == state && state != "")
                    {
                      section_location_state.append(block_img);
                      section_location_state.append(block_name);
                      section_location_state.append(block_age);
                      section_location_state.append(block_gender);
                      section_location_state.append(block_status);
                      section_location_state.append(block_country);
                      section_location_state.append(block_fame);
                      section_location_state.append(block_common_tags);
                      gallery_location_state_response.append(section_location_state);
                    }
                     else if(gallery_array[j].country == country)
                    {
                      section_location.append(block_img);
                      section_location.append(block_name);
                      section_location.append(block_age);
                      section_location.append(block_gender);
                      section_location.append(block_status);
                      section_location.append(block_country);
                      section_location.append(block_fame);
                      section_location.append(block_common_tags);
                      gallery_location_response.append(section_location);
                    }
                    else
                    {
                      section.append(block_img);
                      section.append(block_name);
                      section.append(block_age);
                      section.append(block_gender);
                      section.append(block_status);
                      section.append(block_country);
                      section.append(block_fame);
                      section.append(block_common_tags);
                      gallery_response.append(section);
                     } 
                
                  }
                }
              }
}

window.onload = function()
{
  $('#tags-select').multiSelect();
     var gallery_response = document.getElementById('gallery_response');
     
     if(gallery_response)
     {
         var xhr_onwindow = new XMLHttpRequest;
          formdata = new FormData(document.forms.search_form);
          formdata.append("method", "fuck");
            xhr_onwindow.open('POST', baseUrl + '/search', true);
            xhr_onwindow.send(formdata);
            xhr_onwindow.onloadend = function()
            {
              i = 0;
              perPage = 20;
              document.getElementById('gallery_response').innerHTML = "";
              document.getElementById('gallery_location_response').innerHTML = "";
              document.getElementById('gallery_location_city_response').innerHTML = "";
              document.getElementById('gallery_location_state_response').innerHTML = "";
              render(i, perPage);
            }

     }


}

  
var show_options = document.getElementById('show_options');

if(show_options)
{
  

  show_options.onclick = function(e)
  {
    e.preventDefault();


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

             var filter_age_select = document.getElementById('filter_age_select');
                var filter_fame_select = document.getElementById('filter_fame_select');

                  document.getElementById('hide_options').style.display = "block";
                  document.getElementById('show_options').style.display = "none";
                  document.getElementById('hidden_fields').style.display = "block";
                  document.getElementById('filter_age').style.display = "none";
                  document.getElementById('filter_fame').style.display = "none";
                  document.getElementById('age_gap').value = "18 - " + age;
                  document.getElementById('fame_gap').value = "1 - " + fame;
                   document.getElementById('tags-select').disabled = false;


                filter_age_select.value = 'none';
                filter_fame_select.value = 'none';

                }
                
            }
    }








  }

}



var hide_options = document.getElementById('hide_options');


if(hide_options)
{
  
  hide_options.onclick = function(e)
  {
    e.preventDefault();
      document.getElementById('show_options').style.display = "block";
      document.getElementById('hide_options').style.display = "none";
      document.getElementById('hidden_fields').style.display = "none";
      document.getElementById('filter_age').style.display = "block";
      document.getElementById('filter_fame').style.display = "block";
      document.getElementById('age_gap').value = "none";
      document.getElementById('fame_gap').value = "none";
      document.getElementById('tags-select').disabled = true;
  }


}
