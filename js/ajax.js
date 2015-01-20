var HTTP_PATH;
function ajax(file,form)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById(form).innerHTML=xmlhttp.responseText;
		}
	  }
	xmlhttp.open("GET",file,true);
	xmlhttp.send();
}

function submitVote(url)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		 if(xmlhttp.responseText!='oks') 
		    jAlert(xmlhttp.responseText,'POSM Alert');
		 else
		    window.location.href=HTTP_PATH + "gallery/thanks.html";
		}
	  }
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}

function ajax2(file,form)
{
	var xmlhttp2;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp2=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp2.onreadystatechange=function()
	  {
	  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
		{
	 
		 	document.getElementById(form).innerHTML=xmlhttp2.responseText;
		}
	  }
	xmlhttp2.open("GET",file,true);
	xmlhttp2.send();
}

function ajax3(file,form)
{
	var xmlhttp2;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp2=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp2.onreadystatechange=function()
	  {
	  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
		{
	 
		 	;//document.getElementById(form).innerHTML=xmlhttp2.responseText;
		}
	  }
	xmlhttp2.open("GET",file,true);
	xmlhttp2.send();
}

function ajaxTest(file)
{
	var xmlhttp2;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp2=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
	  xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp2.onreadystatechange=function()
	{
	  if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
	  {
		 	return xmlhttp2.responseText;
	  }
	}
	xmlhttp2.open("GET",file,true);
	xmlhttp2.send();
}


function ajaxAppend(file,form)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			var x = document.getElementById(form);
			x.insertAdjacentHTML('beforeend',xmlhttp.responseText)
		}
	  }
	xmlhttp.open("GET",file,true);
	xmlhttp.send();
}


