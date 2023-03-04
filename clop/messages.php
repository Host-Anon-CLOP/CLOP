<?php
include("backend/backend_messages.php");
$extratitle = "Messages - ";
include("header.php");
echo <<<EOFORM
<center>New Message</center>
<form name="newmessage" method="post" action="messages.php">
<input type="hidden" name="token_messages" value="{$_SESSION['token_messages']}"/>
<center><div class="input-group">To: <input name="sendto" type="text" class="form-control" style="width:200px;"/></div></center>
<center><textarea name="message" class="form-control" style="width:300px;">{$display['message']}</textarea></center>
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
<tr><th>Message</th><th>From</th><th>Sent</th><th></th></tr>
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
<tr><td>{$messageinfo['message']}</td><td>{$displayuser}</td><td>{$messageinfo['sent']}</td>
<td><form name="deletemessage" method="post" action="messages.php"><input type="hidden" name="messagetype" value="inbox"/>
<input type="hidden" name="token_messages" value="{$_SESSION['token_messages']}"/>
<input type="hidden" name="message_id" value="{$messageinfo['message_id']}"/>
EOFORM;
if (!$messageinfo['is_read']) {
    echo <<<EOFORM
<input type="submit" name="markread" value="Mark as Read" class="btn btn-primary"/>
EOFORM;
} else {
    echo <<<EOFORM
<input type="submit" name="markunread" value="Mark as Unread" class="btn btn-warning"/>
EOFORM;
}
    echo <<<EOFORM
<input type="submit" name="action" value="Delete Message" class="btn btn-danger"/>
</form></tr>
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
<tr><th>Message</th><th>To</th><th>Sent</th><th></th></tr>
EOFORM;
foreach ($sentbox as $messageinfo) {
    echo <<<EOFORM
<tr><td>{$messageinfo['message']}</td><td><a href="viewuser.php?user_id={$messageinfo['user_id']}">{$messageinfo['username']}</a></td><td>{$messageinfo['sent']}</td>
<td><form name="deletemessage" method="post" action="messages.php"><input type="hidden" name="messagetype" value="sentbox"/>
<input type="hidden" name="token_messages" value="{$_SESSION['token_messages']}"/>
<input type="hidden" name="message_id" value="{$messageinfo['message_id']}"/>
<input type="submit" name="action" value="Delete Message" class="btn btn-danger"/>
</form></tr>
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
?>