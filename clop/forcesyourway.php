<?php
include("backend/backend_forcesyourway.php");
$extratitle = "Incoming Forces - ";
include("header.php");
$tempid = "";
if ($attackers) {
echo <<<EOFORM
<center><h4>Attackers</h4></center>
<div id="container1" class="js-masonry" data-masonry-options='{ "itemSelector": ".masonryitem", "isFitWidth": true }'>
EOFORM;
foreach ($attackers as $attacker) {
    if ($tempid != $attacker['forcegroup_id']) {
    if ($tempid != "") echo "</table></div></div>"; 
	echo <<<EOFORM
<div class="masonryitem" style="padding: 10px; width: 300px">
	<div class="panel panel-default">
	    <div class="panel-heading">{$attacker['groupname']}<br/>
Owned by <a href="viewnation.php?nation_id={$attacker['ownernation_id']}">{$attacker['ownername']}</a> ({$attacker['ownerregionname']})<br/>
Coming from <a href="viewnation.php?nation_id={$attacker['location_id']}">{$attacker['locationname']}</a> ({$attacker['locationregionname']})<br/>
Left on {$attacker['departuredate']}<br/>
Arriving at {$attacker['arrivaldate']}</div>
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
<div class="row"><center><h5>No one is coming to attack you.</h5></center></div>
EOFORM;
}
$tempid = "";
if ($defenders) {
echo <<<EOFORM
<center><h4>Defenders</h4></center>
<div id="container2" class="js-masonry" data-masonry-options='{ "itemSelector": ".masonryitem", "isFitWidth": true }'>
EOFORM;
foreach ($defenders as $defender) {
    if ($tempid != $defender['forcegroup_id']) {
    if ($tempid != "") echo "</table></div></div>"; 
    echo <<<EOFORM
<div class="masonryitem" style="padding: 10px; width: 300px">
	<div class="panel panel-default">
	    <div class="panel-heading">{$defender['groupname']}<br/>
Owned by <a href="viewnation.php?nation_id={$defender['ownernation_id']}">{$defender['ownername']}</a> ({$defender['ownerregionname']})<br/>
Coming from <a href="viewnation.php?nation_id={$defender['location_id']}">{$defender['locationname']}</a> ({$defender['locationregionname']})<br/>
Left on {$defender['departuredate']}<br/>
Arriving at {$defender['arrivaldate']}</div>
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
<div class="row"><center><h5>No one is coming to protect you.</h5></center></div>
EOFORM;
}
include("footer.php");
?>