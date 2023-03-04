<?php
include("backend/backend_alliancemessages.php");
$extratitle = "Alliance Messages - ";
include("header.php");
echo <<<EOFORM
<center><h3>{$allianceinfo['name']}</h3></center>
EOFORM;
if ($owner || $messagingpowers) {
    echo <<<EOFORM
    <center><h5>Description</h5></center>
    <center><form method="post" action="alliancemessages.php"><input type="hidden" name="token_alliancemessages" value="{$_SESSION['token_alliancemessages']}"/>
    <textarea class="form-control" name="alliancedescription" style="width:75%">{$displayeditdescription}</textarea><br/>
    <input type="submit" name="updatedescription" value="Update Description" class="btn btn-info"/></form></center>
EOFORM;
}
echo <<<EOFORM
<center><h6>New Alliance Message</h6></center>
<center>Cost: {$constants['fealtyrequired']} Fealty</center>
<form name="newmessage" method="post" action="alliancemessages.php">
<input type="hidden" name="token_alliancemessages" value="{$_SESSION['token_alliancemessages']}"/>
<center><textarea name="message" class="form-control" style="width:75%">{$display['message']}</textarea></center>
<center><input type="submit" name="sendmessage" value="Post Message" class="btn btn-info"/></center>
</form>
<form name="mark" method="post" action="alliancemessages.php">
<input type="hidden" name="token_alliancemessages" value="{$_SESSION['token_alliancemessages']}"/>
<center><input type="submit" name="markall" value="Mark All as Read" class="btn btn-sm btn-info"/></center>
</form>
EOFORM;
if ($owner || $messagingpowers) {
    echo <<<EOFORM
<form name="bulkdelete" method="post" action="alliancemessages.php">
<input type="hidden" name="token_alliancemessages" value="{$_SESSION['token_alliancemessages']}"/>
<center><div class="input-group">Delete all messages more than <input name="deletedays" type="text" style="width:25px;" maxlength="2"//> days old
<input type="submit" name="bulkdelete" value="Bulk Delete" class="btn btn-sm btn-danger" onclick="return confirm('Really bulk delete messages?')"/></div></center>
</form>
EOFORM;
}
if (!empty($messages)) {
    echo <<<EOFORM
    <center><table class="table table-striped table-bordered">
EOFORM;
    foreach ($messages as $messageinfo) {
    echo <<<EOFORM
    <tr><td>{$messageinfo['displaymessage']}</td>
EOFORM;
    if ($messageinfo['user_id']) {
        $displayuser =<<<EOFORM
        <a href="viewuser.php?user_id={$messageinfo['user_id']}">{$messageinfo['username']}</a>
EOFORM;
    } else {
        $displayuser = $messageinfo['username'];
    }
    echo <<<EOFORM
    <td>{$displayuser}</td>
    <td>{$messageinfo['posted']}</td>
    <td><form method="post" action="alliancemessages.php"><input type="hidden" name="token_alliancemessages" value="{$_SESSION['token_alliancemessages']}"/>
    <input type="hidden" name="message_id" value="{$messageinfo['message_id']}"/>
EOFORM;
	if ($messageinfo['isread']) {
		echo <<<EOFORM
    <input type="submit" name="markasunread" value="Mark as Unread" class="btn btn-warning btn-sm"/>
EOFORM;
	} else {
		echo <<<EOFORM
    <input type="submit" name="markasread" value="Mark as Read" class="btn btn-info btn-sm"/>
EOFORM;
	}
    if ($owner || $messagingpowers || $_SESSION['user_id'] == $messageinfo['user_id']) {
        echo <<<EOFORM
    <input type="submit" name="deletemessage" value="Delete Message" class="btn btn-danger btn-sm"/>
EOFORM;
    }
    echo <<<EOFORM
	</form></td></tr>
EOFORM;
    }
    echo <<<EOFORM
</table></center>
EOFORM;
}
include("footer.php");
?>