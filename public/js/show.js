var getUrl = window.location;
var baseUrl = getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
var block = document.getElementById('block');

if (block)
{
	block.onclick = function()
	{
    var target = event.target;
		var xhr_block = new XMLHttpRequest;
          formdata = new FormData();
          formdata.append("method", "block");
          formdata.append("id", target.name);
            xhr_block.open('POST', baseUrl + '/show', true);
            xhr_block.send(formdata);
            xhr_block.onloadend = function()
            {
              if (xhr_block.readyState == 4)
                {
                    var res = xhr_block.responseText;
                    console.log(res);
                    if (res == 1 ){
                      document.getElementById('block_response').innerHTML = "You have succesfully blocked this user";
                    }
                    else{
                       document.getElementById('block_response').innerHTML = "You have already blocked this user";
                     }
                }
            }   
	}
}