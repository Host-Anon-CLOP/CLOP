/* clop uses jQuery so no need to defie "poor man's selectors" here */

function submitForm(e) {
	//e.preventDefault();
	
	var switchState = $('input[name="funstatus"]', e.target.parentNode)[0].checked;
	var token = $('input[name="async_token"]', e.target.parentNode)[0].value;
	var body = ['async_token='+token, 'funstatus='+switchState, 'funtoggle=funtoggle'].join('&');
	
	AJAXForm(body);
	return false;
}

function AJAXForm(data) {
	var xhr = new XMLHttpRequest();
	xhr.open('post', 'async_userinfo.php', true);
	xhr.withCredentials = true;
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send(encodeURI(data));
	xhr.onreadystatechange = function (e) {
		if (xhr.readyState != 4) return;
			else return xhr.responseText;
	}
}
