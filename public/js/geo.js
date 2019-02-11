

var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];




var map, infoWindow, mark, pos, positionfrombase, latLng;

function initMap() 
    {


    map = new google.maps.Map(document.getElementById('map'), 
    {
        center: {lat: -7.460, lng: -139.062},
        zoom: 6
    });



      xhr = new XMLHttpRequest();
      xhr.open('POST', baseUrl + '/user/profile', true);
      var formdata = new FormData();
      formdata.append("method", "getPosition");
      xhr.send(formdata); 
      xhr.onloadend = function()
      {   
        if (xhr.readyState == 4)
        {
          positionfrombase = JSON.parse(xhr.responseText);
         
          infoWindow = new google.maps.InfoWindow;
          if (navigator.geolocation)
          {
              navigator.geolocation.getCurrentPosition(function(position)
              {
                      pos = {
                          lat: position.coords.latitude,
                          lng: position.coords.longitude
                      };
                latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                if(positionfrombase.manual == 1)
                {
                    latLng = new google.maps.LatLng(positionfrombase.lat, positionfrombase.lng);

                    mark = new google.maps.Marker({
                          position: latLng,
                          map: map
                        });
                     map.setCenter(latLng);
                }
                else 
                {
                   latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    infoWindow.setPosition(latLng);
                    infoWindow.setContent('You are here');
                    infoWindow.open(map);
                    map.setCenter(latLng);
                    sendCoords(position.coords.latitude, position.coords.longitude);  
                }
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
       



          //create a marker that user will set on the map (the red one)
          //if the marker exists, it will be replaced by new
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
          });

         
        }
        }
    }
//in case of error, just show a message

    function handleLocationError(browserHasGeolocation, infoWindow, pos) 
    {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
                              'You must allow a geolocation to be located automatically. ' :
                              'Error: Your browser doesn\'t support geolocation.');
        infoWindow.open(map);
        sendCoords("", "");
        document.getElementById('autoset_location').style.display = "none";

    }

$(document).ready(function() {
 enableSetAuto();
});


function enableSetAuto(){
      document.getElementById("autoset_location").disabled = true;
      setTimeout(function(){document.getElementById("autoset_location").disabled = false;},4000);
  }



var manset_location = document.getElementById('manset_location');
if( manset_location )
{

  manset_location.onclick = function(e)
  {
     e.preventDefault();
        if(mark)
        {
          var xhr = new XMLHttpRequest;
          var formdata = new FormData();
          formdata.append("method", "position");
          formdata.append("submethod", "manual");
          formdata.append("posLat", mark.getPosition().lat());
          formdata.append("posLong", mark.getPosition().lng());
          xhr.open('POST', baseUrl + '/user/profile', true);
          xhr.send(formdata);
          xhr.onreadystatechange = function()
          {   
            if (xhr.readyState == 4)
            {
              document.getElementById('map_message').innerHTML = "Your location was updated manually";
              document.getElementById('lat_lng').innerHTML = xhr.responseText;
              infoWindow.setMap(null);
            }
          }
        }
        else
        {
          document.getElementById('map_message').innerHTML = "No marker has been added";
        }
  }
}



var autoset_location = document.getElementById('autoset_location');

if( autoset_location)
{
     autoset_location.onclick = function(e)
      {
          e.preventDefault();
          var xhr = new XMLHttpRequest;
          var formdata = new FormData();
          formdata.append("method", "position");
          formdata.append("submethod", "auto");
          formdata.append("posLat", pos.lat);
          formdata.append("posLong", pos.lng);
          xhr.open('POST', baseUrl + '/user/profile', true);
          xhr.send(formdata);
          xhr.onreadystatechange = function()
          {  
            if (xhr.readyState == 4)
            {
              document.getElementById('map_message').innerHTML = "Your location was autoupdated";
               document.getElementById('lat_lng').innerHTML = "";
              infoWindow.setPosition(pos);
              infoWindow.setContent('You are here');
              infoWindow.open(map);
              map.setCenter(pos);
              if(mark)
              {
                mark.setMap(null);
                mark = null;
              }
              map.setZoom(6);
            }
          }
      }
}

function sendCoords(lat, lng)
{
   var xhr = new XMLHttpRequest;
          var formdata = new FormData();
          formdata.append("method", "position");
          formdata.append("posLat", lat);
          formdata.append("posLong", lng);
          xhr.open('POST', baseUrl + '/user/profile', true);
          xhr.send(formdata);
          xhr.onreadystatechange = function()
          {   
            if(xhr.readyState == 4)
            {
               // document.getElementById('lat_lng').innerHTML = xhr.responseText;
             ;

            }
          }
}