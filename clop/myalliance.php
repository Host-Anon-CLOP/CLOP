<?php
include("backend/backend_myalliance.php");
$extratitle = "My Alliance - ";
include("header.php");
echo <<<EOFORM
<center><h3>{$allianceinfo['name']}</h3></center><br/>
<center>{$displaydescription}</center>
EOFORM;
if ($owner) {
    echo <<<EOFORM
    <center><h5>Public Description</h5></center>
    <center><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <textarea class="form-control" name="alliancepubdescription" style="width:300px;">{$displayeditpubdescription}</textarea><br/>
    <input type="submit" name="updatepubdescription" value="Update Public Description" class="btn btn-info"/></form></center>
    <center><h5>Private Description</h5></center>
    <center><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <textarea class="form-control" name="alliancedescription" style="width:300px;">{$displayeditdescription}</textarea><br/>
    <input type="submit" name="updatedescription" value="Update Private Description" class="btn btn-info"/></form></center>
EOFORM;
}
if (!empty($requestingmembers)) {
    echo <<<EOFORM
    <center><h4>Requesting Members</h4></center>
    <center><table class="table table-striped table-bordered">
EOFORM;
    foreach ($requestingmembers as $rmember) {
        echo <<<EOFORM
    <tr><td><a href="viewuser.php?user_id={$rmember['user_id']}">{$rmember['username']}</a></td>
EOFORM;
    if ($owner) {
        echo <<<EOFORM
    <td><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <input type="hidden" name="user_id" value="{$rmember['user_id']}"/>
    <input type="submit" name="action" value="Accept User" class="btn btn-success btn-sm"/><br/>
    <input type="submit" name="action" value="Reject User" class="btn btn-danger btn-sm"/></form></td>
EOFORM;
    }
        echo "</tr>";
    }
echo <<<EOFORM
</table></center>
EOFORM;
}
echo <<<EOFORM
<center><h6>New Alliance Message</h6></center>
<form name="newmessage" method="post" action="myalliance.php">
<input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
<center><textarea name="message" class="form-control" style="width:300px;">{$display['message']}</textarea></center>
<center><input type="submit" name="sendmessage" value="Post Message" class="btn btn-info"/></center>
</form>
EOFORM;
if ($owner) {
    echo <<<EOFORM
<form name="bulkdelete" method="post" action="myalliance.php">
<input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
<center><div class="input-group">Delete all messages more than <input name="deletedays" type="text" style="width:25px;" maxlength="2"//> days old
<input type="submit" name="bulkdelete" value="Bulk Delete" class="btn btn-sm btn-danger" onclick="return confirm('Really bulk delete messages?')"/></div></center>
</form>
EOFORM;
}
if (!empty($messages)) {
    echo <<<EOFORM
    <center><h4>Messages</h4></center>
    <center><table class="table table-striped table-bordered">
EOFORM;
    foreach ($messages as $messageinfo) {
    echo <<<EOFORM
    <tr><td>{$messageinfo['displaymessage']}</td>
    <td><a href="viewuser.php?user_id={$messageinfo['user_id']}">{$messageinfo['username']}</a></td>
    <td>{$messageinfo['posted']}</td>
EOFORM;
    if ($owner || $_SESSION['user_id'] == $messageinfo['user_id']) {
        echo <<<EOFORM
        <td><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <input type="hidden" name="message_id" value="{$messageinfo['message_id']}"/>
    <input type="submit" name="deletemessage" value="Delete Message" class="btn btn-danger btn-sm"/></form></td>
EOFORM;
    }
    echo "</tr>";
    }
    echo <<<EOFORM
</table></center>
EOFORM;
}
    echo <<<EOFORM
    <center><h4>Current Members</h4></center>
    <center><table class="table table-striped table-bordered">
    <tr><th>Member</th><th>Stasis</th><th>Nations</th>
EOFORM;
    if ($owner) {
        echo "<th></th>";
    }
    echo "</tr>";
foreach ($alliancemembers as $member) {
        if ($member['stasismode']) {
        $stasis = "<center>X</center>";
        } else {
        $stasis = "";
        }
echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$member['user_id']}">{$member['username']}</a></td>
<td>{$stasis}</td><td>
EOFORM;
    if ($nations[$member['user_id']]) {
        $displaynations = array();
        foreach ($nations[$member['user_id']] as $nation) {
            $displaynations[] =<<<EOFORM
<a href="viewnation.php?nation_id={$nation['nation_id']}">{$nation['name']}</a>
EOFORM;
        }
        echo implode(", ", $displaynations);
    } else {
        echo "Nationless";
    }
echo <<<EOFORM
</td>
EOFORM;
if ($owner && $_SESSION['user_id'] != $member['user_id']) {
        echo <<<EOFORM
    <td><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <input type="hidden" name="user_id" value="{$member['user_id']}"/>
    <input type="submit" name="action" value="Eject User" class="btn btn-danger btn-sm"/></form></td>
EOFORM;
} else if ($owner) {
    echo "<td></td>";
}
echo <<<EOFORM
</tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
<br/>
<br/>
<br/>
EOFORM;
if ($owner) {
    echo <<<EOFORM
    <center><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <center>Give Alliance To: <input name="giveto" type="text" class="form-control" style="width:200px;"/></center>
    <input type="submit" name="givealliance" value="Give Away Alliance" class="btn btn-danger btn-sm" onclick="return confirm('Really give away your alliance?')"/></form></center>
    <br/>
    <center><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <input type="submit" name="action" value="Disband Alliance" class="btn btn-danger btn-sm" onclick="return confirm('Really disband your alliance?')"/></form></center>
EOFORM;
} else {
    echo <<<EOFORM
    <center><form method="post" action="myalliance.php"><input type="hidden" name="token_myalliance" value="{$_SESSION['token_myalliance']}"/>
    <input type="submit" name="action" value="Leave Alliance" class="btn btn-danger btn-sm" onclick="return confirm('Really leave your alliance?')"/></form></center>
EOFORM;
}
?>