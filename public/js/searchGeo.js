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
          console.log(positionfrombase);
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
        sendCoords("", "");
    }



var perPage = 20;
var i = 0;

if(document.getElementById('submit_search'))
{
  
  var submit_search = new Vue({
    el: '#submit_search',
    methods:
    {
      sendFormdata: function(event)
      {
            var country = document.getElementById('chosen_country').innerHTML;
            var state = document.getElementById('chosen_state').innerHTML;
            var city = document.getElementById('chosen_city').innerHTML;
            xhr = new XMLHttpRequest;
            formdata = new FormData(document.forms.search_form);
            formdata.append("method", "sendData");
            formdata.append("country", country);
            formdata.append("state", state);
            formdata.append("city", city);
            xhr.open('POST', baseUrl + '/search', true);
            xhr.send(formdata);
            xhr.onreadystatechange = function()
            {
              if (xhr.readyState == 4)
                {
    
                  document.getElementById('form_status').innerHTML = xhr.responseText; ///delete this before finish!!!
                  document.getElementById('gallery_response').innerHTML = "";
                  i = 0;
                  render(i, perPage);
                }
            }   
      }
    } 
  })
}


$(document).ready(function() {
  var win = $(window);

  win.scroll(function() {
    if ($(document).height() - win.height() == win.scrollTop())
    {
        i = perPage;
        perPage += 5;
        render(i, perPage);
    }
  });
});


function render(i, pageCount)
{
   var xhr1 = new XMLHttpRequest;
            formdata = new FormData();
            formdata.append("method", "showGallery");
            xhr1.open('POST', baseUrl + '/search', true);
            xhr1.send(formdata);
            xhr1.onloadend = function()
            {
              if (xhr1.readyState == 4)
                {
                 gallery_array = JSON.parse(xhr1.responseText);
                 console.log(gallery_array);
                  var len = Object.keys(gallery_array).length;

                  console.log(len);
                  console.log(perPage);
                  if (perPage > len)
                  {
                    perPage = len;
                  }
                  for (var j = i + 1; j <= perPage; j++)
                  {
          
                    var section = document.createElement('section');
                    section.setAttribute("class", "user_card_wrapper animated fadeIn");

                    var block_img =  document.createElement('div');
                    block_img.setAttribute("class", "user_card_img_wrapper");

                    var img_link =  document.createElement('a');
                    img_link.setAttribute("class", "user_card_img_link");
                    img_link.setAttribute("href", baseUrl +"/show?profile=" + gallery_array[j].id);

                    var img =  document.createElement('img');
                    img.setAttribute("src", gallery_array[j].mainPhoto);
                    img_link.append(img);

                    var block_name =  document.createElement('div');
                    block_img.append(img_link);
                    block_name.innerHTML = gallery_array[j].firstName;

                    var block_age =  document.createElement('div');
                    block_age.setAttribute("class", "user_card_age");
                    block_age.innerHTML = gallery_array[j].age;

                    var block_status =  document.createElement('div');
                    block_status.setAttribute("class", "user_card_status");
                    block_status.innerHTML = gallery_array[j].status;

                    var block_gender =  document.createElement('div');
                    block_gender.setAttribute("class", "user_card_gender");
                    block_gender.innerHTML = gallery_array[j].gender;

                     var block_country =  document.createElement('div');
                    block_country.setAttribute("class", "user_card_country");
                    block_country.innerHTML = gallery_array[j].country;

                     var block_state =  document.createElement('div');
                    block_state.setAttribute("class", "user_card_state");
                    block_state.innerHTML = gallery_array[j].state;

                     var block_city =  document.createElement('div');
                    block_city.setAttribute("class", "user_card_city");
                    block_city.innerHTML = gallery_array[j].city;

                     var block_prefer =  document.createElement('div');
                    block_prefer.setAttribute("class", "user_card_city");
                    block_prefer.innerHTML = gallery_array[j].sexualPreferences;

                     var block_fame =  document.createElement('div');
                    block_fame.setAttribute("class", "user_card_city");
                    block_fame.innerHTML = gallery_array[j].rating;

                    var block_tags =  document.createElement('div');
                    block_tags.setAttribute("class", "user_card_city");
                    block_tags.innerHTML = gallery_array[j].tags;

                    section.append(block_img);
                    section.append(block_name);
                    section.append(block_age);
                    section.append(block_status);
                    section.append(block_gender);
                    section.append(block_country);
                    section.append(block_state);
                    section.append(block_city);
                    section.append(block_prefer);
                    section.append(block_fame);
                    section.append(block_tags);
                    gallery_response.append(section);
                
                  }
                }
              }
}

window.onload = function()
{

    var tags = [];
    var win = $(window);
    document.getElementById('age_gap').value = "none";
    document.getElementById('fame_gap').value = "none";

     var gallery_response = document.getElementById('gallery_response');
     var gallery_array;
       if(gallery_response)
       {
            render(i, perPage);
        }
     
}



if(document.getElementById('show_options'))
{
  
  var submit_search = new Vue({
    el: '#show_options',
    methods:
    {
      showDetailed: function(event)
      {
        var filter_age_select = document.getElementById('filter_age_select');
        var filter_fame_select = document.getElementById('filter_fame_select');

          document.getElementById('hide_options').style.display = "block";
          document.getElementById('show_options').style.display = "none";
          document.getElementById('hidden_fields').style.display = "block";
          document.getElementById('filter_age').style.display = "none";
          document.getElementById('filter_fame').style.display = "none";
          document.getElementById('age_gap').value = "18 - 42";
          document.getElementById('fame_gap').value = "24 - 42";
          

        filter_age_select.value = 'none';
        filter_fame_select.value = 'none';
      }
    } 
  })
}

if(document.getElementById('hide_options'))
{
  
  var submit_search = new Vue({
    el: '#hide_options',
    methods:
    {
      showDetailed: function(event)
      {
          document.getElementById('show_options').style.display = "block";
          document.getElementById('hide_options').style.display = "none";
           document.getElementById('hidden_fields').style.display = "none";
           document.getElementById('filter_age').style.display = "block";
           document.getElementById('filter_fame').style.display = "block";
           document.getElementById('age_gap').value = "none";
           document.getElementById('fame_gap').value = "none";
      }
    } 
  })
}

