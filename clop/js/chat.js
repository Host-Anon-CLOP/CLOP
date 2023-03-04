var maxID = 0;
var messagesDiv;
var lastmessage;
var hasFocus = true;
var oldTitle = document.title;
var newMsg = "/ People talked!";
var msgAux = "\\ People talked!";
var timeoutID;
var first = true;
var bbcodes = {
bold: /\[b\](.*?)\[\/b\]/g,
em: /\[i\](.*?)\[\/i\]/g,
strikethrough: /\[s\](.*?)\[\/s\]/g,
spoiler: /\[spoiler\](.*?)\[\/spoiler\]/g,
color: /\[c=#(.{6})\](.*?)\[\/c\]/g,
me: /<\/a>: \/me/g
};
window.onfocus = function onfoci() {
hasFocus = true;
}

window.onblur = function onlost() {
hasFocus = false;
}

function blink() {
if(hasFocus) {
document.title = oldTitle;
clearTimeout(timeoutID);
return;
} else {
document.title = (document.title == newMsg) ? msgAux : newMsg;
timeoutID = setTimeout("blink()", 1000);
}
}

if(!window.XMLHttpRequest)
{
  var XMLHttpRequest = function()
  {
    try{ return new ActiveXObject("MSXML3.XMLHTTP") } catch(e) {}
    try{ return new ActiveXObject("MSXML2.XMLHTTP.3.0") } catch(e) {}
    try{ return new ActiveXObject("MSXML2.XMLHTTP") } catch(e) {}
    try{ return new ActiveXObject("Microsoft.XMLHTTP") } catch(e) {}
  }
}
var chat_XMLHttp_add = new XMLHttpRequest();
var chat_XMLHttp_get = new XMLHttpRequest();
function addmessage()
{
  if (!document.getElementById('message').value || chat_XMLHttp_add.readyState % 4) return;
  chat_XMLHttp_add.open("get", "addmessage.php?message="+encodeURIComponent(document.getElementById('message').value)+"&token="+encodeURIComponent(document.getElementById('token').value));
  chat_XMLHttp_add.send(null);
  chat_XMLHttp_add.onreadystatechange = function()
  {
    if(chat_XMLHttp_add.readyState == 4 && chat_XMLHttp_add.status == 200)
    {
      getmessages(true);
    }
  }
  document.getElementById('message').value = '';
  document.getElementById('message').focus();
}

function getmessages(fromadd)
{
  if (chat_XMLHttp_get.readyState % 4) return;
  chat_XMLHttp_get.open("get", "getmessages.php?token="+encodeURIComponent(document.getElementById('token').value)+"&lastmessage="+maxID);
  chat_XMLHttp_get.send(null);
  chat_XMLHttp_get.onreadystatechange = function()
  {
    if(chat_XMLHttp_get.readyState == 4 && chat_XMLHttp_get.status == 200)
    {
		if(hasFocus == false) {
		  blink();
		}
	lastmessage = document.getElementById('lastmessage');
	lastmessage.remove();
	var newNode = document.createElement('span');
newNode.innerHTML = chat_XMLHttp_get.responseText;
newNode.innerHTML = newNode.innerHTML.replace(bbcodes["bold"], "<strong>$1</strong>");
newNode.innerHTML = newNode.innerHTML.replace(bbcodes["em"], "<em>$1</em>");
newNode.innerHTML = newNode.innerHTML.replace(bbcodes["strikethrough"], "<del>$1</del>");
newNode.innerHTML = newNode.innerHTML.replace(bbcodes["spoiler"], '<span class="spoiler">$1</span>');
newNode.innerHTML = newNode.innerHTML.replace(bbcodes["color"], '<span style="color: #$1">$2</span>');
newNode.innerHTML = newNode.innerHTML.replace(bbcodes["me"], '</a>');
	messagesDiv = document.getElementById('messages');
	if((messagesDiv.scrollHeight - messagesDiv.scrollTop === messagesDiv.clientHeight) || first || fromadd) {
        messagesDiv.appendChild(newNode);
		messagesDiv.scrollTop = messagesDiv.scrollHeight;
	} else {
        messagesDiv.appendChild(newNode);
    }
	first = false;
    	maxID = document.getElementById('lastmessage').value;
    }
  }
}