<?php
include("backend/allfunctions.php");
$extratitle = "Chat - ";
include("header.php");
$_SESSION["token_chat"] = sha1(rand());
echo <<<EOFORM
<script type="text/javascript" src="js/chat.js"></script>
<div id="messages" style="height: 400px; overflow: auto;"><input type="hidden" id="lastmessage" value="0"/></div>
<form class="form-inline" role="form" action="" onsubmit="addmessage(); return false;">
<input type="hidden" id="token" class="form-control" value="{$_SESSION["token_chat"]}"/>
<div class="input-group">
<input type="text" id="message" class="form-control"/>
<span class="input-group-btn">
<input type="submit" value="Send" class="btn btn-success"/>
</span>
</div>
</form>
<script>getmessages(false); setInterval("getmessages(false);", 5000);</script>
EOFORM;
include("footer.php");
?>