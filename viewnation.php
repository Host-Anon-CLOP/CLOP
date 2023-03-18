<?php
include("backend/backend_viewnation.php");
$extratitle = "View Nation - ";
include("header.php");
if ($nationinfo) {
echo <<<EOFORM
<center><h3>{$nationinfo['name']}</h3></center>
<center><h4>{$nationinfo['subregionname']}{$nationinfo['regionname']}</h4></center>
<center><h4>Government: {$nationinfo['government']}</h4></center>
<center><h4>Economy: {$nationinfo['economy']}</h4></center>
<center><h4>Leader: <a href="viewuser.php?user_id={$nationinfo['user_id']}">{$nationinfo['username']}</a></h4></center>
<center><h4>Created: {$nationinfo['creationdate']}</h4></center>
<center>{$display['description']}</center><br/>
<center>GDP: {$displaygdp} bits every 2 hours</center>
<center><b>Buildings</b></center>
<center><table id="buildings">
EOFORM;
foreach ($buildings as $name => $amount) {
echo <<<EOFORM
<tr><td>{$name}</td><td>{$amount}</td></tr>
EOFORM;
}
echo "</table></center>";
$tempid = "";
if ($attackers) {
echo <<<EOFORM
<div class="row"><center><h4>Attackers</h4></center>
EOFORM;
foreach ($attackers as $attacker) {
    if ($tempid != $attacker['forcegroup_id']) {
    if ($attacker['ownernation_id'] > 0) {
        $nationlink = <<<EOFORM
<a href="viewnation.php?nation_id={$attacker['ownernation_id']}">{$attacker['ownername']}</a> ({$attacker['ownerregionname']})
EOFORM;
    } else {
        $nationlink = $attacker['ownername'];
    }
    if ($tempid != "") echo "</table></div></div>"; 
	echo <<<EOFORM
<div class="col-md-4">
	<div class="panel panel-default">
	    <div class="panel-heading">{$attacker['groupname']}<br/>
Owned by {$nationlink}</div>
<table class="table table-striped table-bordered">
EOFORM;
    $tempid = $attacker['forcegroup_id'];
    }
    echo <<<EOFORM
<tr><td>
<center><img src="images/{$attacker['lowertype']}/w-{$attacker['armor_id']}-{$attacker['weapon_id']}.png" width="80" height="50"/></center>
{$attacker['name']}<br/>
{$forcetypes[$attacker['type']]}<br/>
EOFORM;
if ($attacker['type'] != 6) {
echo <<<EOFORM
{$attacker['weaponname']}<br/>
{$attacker['armorname']}<br/>
EOFORM;
}
echo <<<EOFORM
Size: {$attacker['size']}<br/>
Training: {$attacker['training']}</td></tr>
EOFORM;
}
echo <<<EOFORM
</table></div></div></div>
EOFORM;
} else {
echo <<<EOFORM
<div class="row"><center><h5>No attackers are in this nation.</h5></center></div>
EOFORM;
}
$tempid = "";
if ($defenders) {
echo <<<EOFORM
<div class="row"><center><h4>Defenders</h4></center>
EOFORM;
foreach ($defenders as $defender) {
    if ($tempid != $defender['forcegroup_id']) {
    if ($tempid != "") echo "</table></div></div>"; 
    echo <<<EOFORM
<div class="col-md-4">
	<div class="panel panel-default">
	    <div class="panel-heading">{$defender['groupname']}<br/>
Owned by <a href="viewnation.php?nation_id={$defender['ownernation_id']}">{$defender['ownername']}</a> ({$defender['ownerregionname']})</div>
<table class="table table-striped table-bordered">
EOFORM;
    $tempid = $defender['forcegroup_id'];
    }
    echo <<<EOFORM
<tr><td>
<center><img src="images/{$defender['lowertype']}/w-{$defender['armor_id']}-{$defender['weapon_id']}.png" width="80" height="50"/></center>
{$defender['name']}<br/>
{$forcetypes[$defender['type']]}<br/>
EOFORM;
if ($defender['type'] != 6) {
echo <<<EOFORM
{$defender['weaponname']}<br/>
{$defender['armorname']}<br/>
EOFORM;
}
echo <<<EOFORM
Size: {$defender['size']}<br/>
Training: {$defender['training']}</td></tr>
EOFORM;
}
echo <<<EOFORM
</table></div></div></div>
EOFORM;
} else {
echo <<<EOFORM
<div class="row"><center><h5>No defenders are in this nation.</h5></center></div>
EOFORM;
}
} else {
echo <<<EOFORM
<center><h3>Nation not found!</h3></center>
EOFORM;
}
include("footer.php");
?>