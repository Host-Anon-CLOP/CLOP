<?php
include("backend/backend_myalliance.php");
$extratitle = "My Alliance - ";
include("header.php");
echo <<<EOFORM
<h1>test {$allianceinfo['alliance_id']}</h1>
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
        $regiontypes = array(0 => "The Heavily Fortified Island of Admin", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
        $icontypes = array(0 => "Drugs", 1 => "Oil", 2 => "Copper", 3 => "Apples", 4 => "Machinery Parts");
        $displaynations = array();
        foreach ($nations[$member['user_id']] as $nation) {
            $displaynations[] =<<<EOFORM
<a href="viewnation.php?nation_id={$nation['nation_id']}">{$nation['name']} (<img src="images/icons/{$icontypes[$nation['region']]}.png"/>{$regiontypes[$nation['region']]})</a>
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

# Alliance Resources
echo <<<EOFORM
</tbody>
</table>
</div>
</div>
    <div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">Alliance Resources</div>
        <table class="table">
        <thead>
        <tr>
EOFORM;
        if (!$nationinfo['hideicons']) {
        echo <<<EOFORM
            <td></td>
EOFORM;
        }
        echo <<<EOFORM
            <td style="text-align: right;">Resource</td>
            <td>Generated</td>
            <td>Used</td>
            <td>Net</td>
        </tr>
        </thead>
        <tbody>
EOFORM;
foreach($allianceaffectedresources as $name => $amount) {
if (!$allianceresources[$name]) $allianceresources[$name] = 0;
}
foreach($alliancerequiredresources as $name => $amount) {
if (!$allianceresources[$name]) $allianceresources[$name] = 0;
}
ksort($allianceresources);
foreach($allianceresources as $name => $amount) {
    if (!$allianceaffectedresources[$name]) {
    $allianceaffectedresources[$name] = 0;
    }
    if (!$alliancerequiredresources[$name]) {
    $alliancerequiredresources[$name] = 0;
    }
    $amountNet = ($allianceaffectedresources[$name] - $alliancerequiredresources[$name]);

    if($amountNet > 0) $amountNetClass = "text-success";
    elseif($amountNet == 0) $amountNetClass = "text-warning";
    else $amountNetClass = "text-danger";
    $displayaffected = commas($allianceaffectedresources[$name]);
    $displayrequired = commas($alliancerequiredresources[$name]);
    if ($amountNet < 0) {
    $displaynet = "-" . commas(abs($amountNet));
    } else {
    $displaynet = commas($amountNet);
    }

    echo <<<EOFORM
    <tr>
EOFORM;
    if (!$nationinfo['hideicons']) {
    echo <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$name}.png"/></td>
EOFORM;
    }
    echo <<<EOFORM
    <td style="text-align: right;">{$name}</td>
    <td style="text-align: center;"><span class="text-success">{$displayaffected}</span></td>
    <td style="text-align: center;"><span class="text-danger">{$displayrequired}</span></td>
    <td style="text-align: center;"><span class="{$amountNetClass}">{$displaynet}</span></td>
    </tr>
EOFORM;
}
    
?>