<?php
include("backend/backend_allianceactions.php");
$extratitle = "Alliance Actions - ";
include("header.php");
$token = $_SESSION["token_allianceactions"];
echo <<<EOFORM
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Invite User</div>
Cost: {$costtoinvite} Love
<form name="inviteform" action="allianceactions.php" method="post">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-8">
<input name="username" type="text" placeholder="User" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="inviteuser" value="Invite User" class="btn btn-info"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Current Invitations</div>
<table>
EOFORM;
if ($invitations) {
foreach ($invitations as $user_id => $username) {
echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$user_id}">{$username}</a></td><td>
<form name="rescindform{$user_id}" action="allianceactions.php" method="post">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<input type="hidden" name="user_id" value="{$user_id}"/>
<input type="submit" onclick="return confirm('Really rescind your invitation to {$username}?')" name="rescindinvitation" value="Rescind Invitation" class="btn btn-danger"/>
</form>
</td></tr>
EOFORM;
}
}
echo <<<EOFORM
</table>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Grant Ability</div>
Cost: {$constants['favorforabilities']} Favor per tick
<form name="abilityform" action="allianceactions.php" method="post">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-6">
<select name="abilityname" class="form-control">
<option value=""></option>
EOFORM;
foreach ($allianceabilities as $abilityname => $friendlyname) {
echo <<<EOFORM
<option value="{$abilityname}">{$friendlyname}</option>
EOFORM;
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-6">
<input name="turns" placeholder="Ticks" type="text" class="form-control"/>
</div></div>
<div class="row input-group">
<div class="col-sm-8">
<select name="user_id" class="form-control">
<option value=""></option>
EOFORM;
foreach ($memberarray as $member_id => $name) {
echo <<<EOFORM
<option value="{$member_id}">{$name}</option>
EOFORM;
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input type="submit" name="grantability" value="Grant" class="btn btn-success"/>
</div></div></div>
</form></div></div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Give Resource to Alliance</div>
<div class="row input-group">
<form name="giveform" action="allianceactions.php" method="post">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="col-sm-4">
<select name="resource_id" class="form-control">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Amount" type="text" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="giveresource" value="Give" class="btn btn-success"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Take Resource from Alliance</div>
<div class="row input-group">
<form name="takeform" action="allianceactions.php" method="post">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="col-sm-4">
<select name="resource_id" class="form-control">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Amount" type="text" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="takeresource" value="Take" class="btn btn-success"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Focus Production</div>
EOFORM;
if (!$allianceinfo['alliancefocusamount']) {
echo <<<EOFORM
Cost to Focus: {$focuscost} Faith
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-4">
<select name="focuson" class="form-control">
<option value=""/>
<option value="1">Magic</option>
<option value="2">Loyalty</option>
<option value="4">Laughter</option>
<option value="8">Kindness</option>
<option value="16">Honesty</option>
<option value="32">Generosity</option>
</select>
</div>
<div class="col-sm-2">
</div>
<div class="col-sm-4">
<input type="submit" name="focus" value="Focus" class="btn btn-success"/>
</div>
</div>
</form>
EOFORM;
} else if ($allianceinfo['alliancefocusamount'] == 1) {
echo <<<EOFORM
Cost to Further Focus: {$focuscost} Faith<br/>
Current focus: {$focusarray[$allianceinfo['alliancefocus']]}
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-4">
<input type="submit" name="focus" value="Focus Further" class="btn btn-success"/>
</div>
<div class="col-sm-2">
</div>
<div class="col-sm-4">
<input type="submit" onclick="return confirm('Really unfocus your alliance\'s production? You don\'t get your Faith back!')" name="unfocus" value="Unfocus" class="btn btn-danger"/>
</div>
</div>
</form>
EOFORM;
} else {
echo <<<EOFORM
You cannot focus your alliance's production any further.
Current focus: {$focusarray[$allianceinfo['alliancefocus']]}
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<center><input type="submit" onclick="return confirm('Really unfocus your alliance\'s production? You don\'t get your Faith back!')" name="unfocus" value="Unfocus" class="btn btn-danger"/></center>
</form>
EOFORM;
}
echo <<<EOFORM
</div></div>
</div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Alliance Satisfaction by 10</div>
Cost: {$constants['joyrequired']} Joy
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-6">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-6">
<input type="submit" name="increasesatisfaction" value="Increase" class="btn btn-success"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Another Alliance's Satisfaction by 10</div>
Cost: {$constants['hoperequired']} Hope
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-4">
<input name="alliancename" placeholder="Alliance" type="text" class="form-control"/>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="increaseothersatisfaction" value="Increase" class="btn btn-success"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Purchase Ability</div>
Cost: {$constants['benevolenceforabilities']} Benevolence per tick
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-6">
<select name="abilityname" class="form-control">
<option value=""></option>
EOFORM;
foreach ($groupabilities as $abilityname => $friendlyname) {
echo <<<EOFORM
<option value="{$abilityname}">{$friendlyname}</option>
EOFORM;
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-6">
<div class="input-group">
<input name="turns" placeholder="Ticks" type="text" class="form-control"/>
<span class="input-group-btn">
<input type="submit" name="purchaseability" value="Purchase" class="btn btn-success"/>
</span>
</div></div></div>
</form>
</div>
</div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Kick Alliance Member</div>
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row">
<div class="col-sm-6">
<select name="user_id" class="form-control">
<option value=""></option>
EOFORM;
foreach ($memberarraynoself as $member_id => $name) {
echo <<<EOFORM
<option value="{$member_id}">{$name}</option>
EOFORM;
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input type="submit" onclick="return confirm('Really kick this player from the alliance?')" name="kick" value="Kick" class="btn btn-danger"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Uplift Alliance Member</div>
Cost: {$constants['magnanimitytouplift']} Magnanimity
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row">
<div class="col-sm-6">
<select name="user_id" class="form-control">
<option value=""></option>
EOFORM;
foreach ($memberarraynoself as $member_id => $name) {
echo <<<EOFORM
<option value="{$member_id}">{$name}</option>
EOFORM;
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input type="submit" name="uplift" value="Uplift" class="btn btn-success"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Transfer Control</div>
Cost: {$constants['nobilitytotransfer']} Nobility
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<div class="row">
<div class="col-sm-6">
<select name="user_id" class="form-control">
<option value=""></option>
EOFORM;
foreach ($memberarraynoself as $member_id => $name) {
echo <<<EOFORM
<option value="{$member_id}">{$name}</option>
EOFORM;
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input type="submit" onclick="return confirm('Really transfer control of the alliance?')" name="transfercontrol" value="Transfer" class="btn btn-danger"/>
</div></div>
</form>
</div></div>
</div>
<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Disband Alliance</div>
<form action="allianceactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_allianceactions" value="{$token}"/>
<center><input type="submit" onclick="return confirm('Really disband your entire alliance?')" name="disband" value="Disband" class="btn btn-danger"/></center>
</form>
</div></div>
<div class="col-md-4"></div>
</div>
EOFORM;
include("footer.php");
?>