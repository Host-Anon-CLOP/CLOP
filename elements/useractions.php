<?php
include("backend/backend_useractions.php");
$extratitle = "User Actions - ";
include("header.php");
$token = $_SESSION["token_useractions"];
echo <<<EOFORM
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Own Satisfaction by 10</div>
Cost: {$constants['happinessrequired']} Happiness
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-6">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-6">
<input type="submit" name="increaseownsatisfaction" value="Increase" class="btn btn-success"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Alliance Member's Satisfaction by 10</div>
Cost: {$constants['camaraderierequired']} Camaraderie<div class="row input-group">
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<div class="col-sm-4">
<select name="user_id" class="form-control">
EOFORM;
if ($alliancemembers) {
foreach ($alliancemembers as $member_id => $name) {
echo <<<EOFORM
<option value="{$member_id}">{$name}</option>
EOFORM;
}
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="increasemembersatisfaction" value="Increase" class="btn btn-success"/>
</div>
</form>
</div></div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Unallied Player's Satisfaction by 10</div>
Cost: {$constants['cheerrequired']} Cheer
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-4">
<input name="username" placeholder="Player" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="increasenonmembersatisfaction" value="Increase" class="btn btn-success"/>
</div></div>
</form>
</div></div>
</div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Own Production by 1</div>
Cost: {$productioncost} {$requiredname}
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<center><input type="submit" name="increaseproduction" value="Increase" class="btn btn-success"/></center>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Focus Production</div>
EOFORM;
if (!$userinfo['focusamount']) {
echo <<<EOFORM
Cost to Focus: {$focuscost} Devotion
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
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
} else if ($userinfo['focusamount'] == 1) {
echo <<<EOFORM
Cost to Further Focus: {$focuscost} Devotion<br/>
Current focus: {$focusarray[$userinfo['focus']]}
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-4">
<input type="submit" name="focus" value="Focus Further" class="btn btn-success"/>
</div>
<div class="col-sm-2">
</div>
<div class="col-sm-4">
<input type="submit" onclick="return confirm('Really unfocus your production? You don\'t get your Devotion back!')" name="unfocus" value="Unfocus" class="btn btn-danger"/>
</div>
</div>
</form>
EOFORM;
} else {
echo <<<EOFORM
You cannot focus your production any further.
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<center><input type="submit" onclick="return confirm('Really unfocus your production? You don\'t get your Devotion back!')" name="unfocus" value="Unfocus" class="btn btn-danger"/></center>
</form>
EOFORM;
}
echo <<<EOFORM
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Embezzle From Alliance</div>
Cost: {$constants['embezzlementrequired']} Embezzlement
<div class="row input-group">
<form name="embezzleform" action="useractions.php" method="post">
<input type="hidden" name="token_useractions" value="{$token}"/>
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
<input type="submit" name="embezzle" value="Embezzle" class="btn btn-warning"/>
</div></div>
</div></div>
</div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Purchase Ability</div>
Cost: {$constants['beneficenceforabilities']} Beneficence per tick
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-6">
<select name="abilityname" class="form-control">
<option value=""></option>
EOFORM;
foreach ($userabilities as $abilityname => $friendlyname) {
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
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Change Top Message</div>
Cost: {$constants['humornecessary']} Humor
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<div class="input-group">
<input name="message" placeholder="Message" type="text" class="form-control" maxlength="128"/>
<span class="input-group-btn">
<input type="submit" name="postmessage" value="Change" class="btn btn-success"/>
</span>
</div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Encourage Alliance Member</div>
Cost: {$constants['encouragementrequired']} Encouragement<div class="row input-group">
<form action="useractions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_useractions" value="{$token}"/>
<div class="col-sm-4">
<select name="user_id" class="form-control">
EOFORM;
if ($alliancemembers) {
foreach ($alliancemembers as $member_id => $name) {
echo <<<EOFORM
<option value="{$member_id}">{$name}</option>
EOFORM;
}
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-4">
<input name="turns" placeholder="Ticks" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="encouragemember" value="Encourage" class="btn btn-success"/>
</div>
</form>
</div></div></div>
</div></div>
EOFORM;
include("footer.php");
?>