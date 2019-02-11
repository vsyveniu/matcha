var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];


var perPage = 20;
var i = 0;





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
    i = 0;
    perPage = 20;
      document.getElementById('gallery_response').innerHTML = "";
    render(i, perPage);
});



function render(i, pageCount)
{
   var xhr_gal = new XMLHttpRequest;

            xhr_gal = new XMLHttpRequest;
            formdata = new FormData(document.forms.search_form);
            formdata.append("method", "showGalleryBasic");
            xhr_gal.open('POST', baseUrl + '/search', true);
            xhr_gal.send(formdata);

            xhr_gal.onloadend = function()
            {

             
              if (xhr_gal.readyState == 4)
                {
                 gallery_array = JSON.parse(xhr_gal.responseText);
               


                  if(gallery_array == "error")
                  {
                    alert("I don't even want to know what you entered to get this place, if you do this accidantelly, otherwise it means you trying to illegally sneak into system and do some bad things. In that case i wish you can't plug a flashdrive in roght position at first attempt till the end of your days... ");
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
                    section.setAttribute("class", "user_card_wrapper animated fadeIn");
                    section_location.setAttribute("class", "user_card_wrapper_location animated fadeIn");
          
                    
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
                      img_link.style.cssText = "pointer-events: none; cursor: default";
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




