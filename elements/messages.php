<?php
include("backend/backend_messages.php");
$extratitle = "Messages - ";
include("header.php");
echo <<<EOFORM
<center>New Message</center>
<form name="newmessage" method="post" action="messages.php">
<input type="hidden" name="token_messages" value="{$_SESSION['token_messages']}"/>
<center><div class="input-group">To: <input name="sendto" type="text" class="form-control" style="width:200px;"/></div></center>
<center><textarea name="message" class="form-control" style="width:75%">{$display['message']}</textarea></center>
<center><input type="submit" name="action" value="Send Message" class="btn btn-info"/></center>
</form>
<form name="bulkdelete" method="post" action="messages.php">
<input type="hidden" name="token_messages" value="{$_SESSION['token_messages']}"/>
<center><div class="input-group">Delete all messages more than <input name="deletedays" type="text" style="width:25px;" maxlength="2"/> days old
from the <select name="bulkdeletebox">
<option value="inbox">Inbox</option>
<option value="sentbox">Sentbox</option>
</select>
<input type="submit" name="bulkdelete" value="Bulk Delete" class="btn btn-sm btn-danger" onclick="return confirm('Really bulk delete messages?')"/></div></center>
</form>
EOFORM;
if (!empty($inbox)) {
    echo <<<EOFORM
<center>Inbox</center>
<center><table class="table table-striped table-bordered">
<tr><td><div class="row"><div class="col-md-6">Message</div><div class="col-md-2">From</div><div class="col-md-2">Sent</div><div class="col-md-2"></div></div></td></tr>
EOFORM;
foreach ($inbox as $messageinfo) {
    if ($messageinfo['user_id']) {
        $displayuser =<<<EOFORM
        <a href="viewuser.php?user_id={$messageinfo['user_id']}">{$messageinfo['username']}</a>
EOFORM;
    } else {
        $displayuser = $messageinfo['username'];
    }
    echo <<<EOFORM
<tr><td><div class="row"><div class="col-md-6">{$messageinfo['message']}</div><div class="col-md-2">{$displayuser}</div><div class="col-md-2">{$messageinfo['sent']}</div>
<div class="col-md-2"><form name="deletemessage" method="post" action="messages.php"><input type="hidden" name="messagetype" value="inbox"/>
<input type="hidden" name="token_messages" value="{$_SESSION['token_messages']}"/>
<input type="hidden" name="message_id" value="{$messageinfo['message_id']}"/>
EOFORM;
if (!$messageinfo['is_read']) {
    echo <<<EOFORM
<input type="submit" name="markread" value="Mark as Read" class="btn btn-sm btn-primary"/>
EOFORM;
} else {
    echo <<<EOFORM
<input type="submit" name="markunread" value="Mark as Unread" class="btn btn-warning"/>
EOFORM;
}
    echo <<<EOFORM
<input type="submit" name="action" value="Delete Message" class="btn btn-danger"/>
</form></div></div></td></tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
EOFORM;
} else {
    echo <<<EOFORM
<center><h4>No messages in inbox</h4></center>
EOFORM;
}
if (!empty($sentbox)) {
echo <<<EOFORM
<center>Sentbox</center>
<center><table class="table table-striped table-bordered">
<tr><td><div class="row"><div class="col-md-6">Message</div><div class="col-md-2">To</div><div class="col-md-2">Sent</div><div class="col-md-2"></div><div class="col-md-2"></div></div></td></tr>
EOFORM;
foreach ($sentbox as $messageinfo) {
    echo <<<EOFORM
<tr><td><div class="row"><div class="col-md-6">{$messageinfo['message']}</div><div class="col-md-2"><a href="viewuser.php?user_id={$messageinfo['user_id']}">{$messageinfo['username']}</a></div>
<div class="col-md-2">{$messageinfo['sent']}</div>
<div class="col-md-2"><form name="deletemessage" method="post" action="messages.php"><input type="hidden" name="messagetype" value="sentbox"/>
<input type="hidden" name="token_messages" value="{$_SESSION['token_messages']}"/>
<input type="hidden" name="message_id" value="{$messageinfo['message_id']}"/>
<input type="submit" name="action" value="Delete Message" class="btn btn-danger"/>
</form></div></div></td></tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
EOFORM;
} else {
    echo <<<EOFORM
<center><h4>No messages in sentbox</h4></center>
EOFORM;
}
include("footer.php");
?>