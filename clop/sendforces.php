<?php
include("backend/backend_sendforces.php");
$extratitle = "Send Forces - ";
include("header.php");
$tempid = "";
echo <<<EOFORM
<center>
EOFORM;
if ($forces) {
echo <<<EOFORM
<div id="container" class="js-masonry" data-masonry-options='{ "itemSelector": ".masonryitem", "isFitWidth": true }'>
EOFORM;
foreach ($forces AS $force) {
    if ($tempid != $force['forcegroup_id']) {
		if (!$force['departuredate']) {
            if ($force['attack_mission']) {
                $mission = "Attacking";
            } else {
                $mission = "Defending";
            }
			if ($tempid != "") echo "</table></div></div>"; 
			echo <<<EOFORM
<div class="masonryitem" style="padding: 10px; width: 300px">
	<div class="panel panel-default">
	    <div class="panel-heading">
	    	{$force['groupname']}<br/>
			{$mission} at <a href="viewnation.php?nation_id={$force['location_id']}">{$force['locationname']}</a> ({$force['locationregionname']})<br/>
			<form action="sendforces.php" method="post">
				<input type="hidden" name="token_sendforces" value="{$_SESSION['token_sendforces']}"/>
				<input type="hidden" name="forcegroup_id" value="{$force['forcegroup_id']}"/>
				<input name="name" class="form-control" placeholder="Target Nation" value="" style="width:233px;"/>
				<input type="submit" onclick="return confirm('Really attack this nation?')" name="attack" value="Attack Nation" class="btn btn-danger"/>
				<input type="submit" onclick="return confirm('Really defend this nation?')" name="defend" value="Defend Nation" class="btn btn-success"/>
EOFORM;
                if ($force['locationowner'] == $_SESSION['user_id'] && $force['location_id'] != $_SESSION['nation_id']) {
                echo <<<EOFORM
				<input type="submit" name="transfer" onclick="return confirm('Really transfer ownership of this group to {$force['locationname']}?')" value="Transfer Group" class="btn btn-warning"/>
EOFORM;
                }
                echo <<<EOFORM
			</form>
		</div>
		<table class="table table-striped table-bordered">
EOFORM;
		} else {
            if ($tempid != "") echo "</table></div></div>";
			echo <<<EOFORM
<div class="masonryitem" style="padding: 10px; width: 300px">
	<div class="panel panel-default">
	    <div class="panel-heading">
{$force['groupname']}<br/>
Coming from <a href="viewnation.php?nation_id={$force['location_id']}">{$force['locationname']}</a> ({$force['locationregionname']})<br/>
Going to <a href="viewnation.php?nation_id={$force['destination_id']}">{$force['destinationname']}</a> ({$force['destinationregionname']}) to {$force['missiondescription']}<br/>
Left on {$force['departuredate']}<br/>
Arrives at {$force['arrivaldate']}<br/>
<form action="sendforces.php" method="post">
<input type="hidden" name="token_sendforces" value="{$_SESSION['token_sendforces']}"/>
<input type="hidden" name="forcegroup_id" value="{$force['forcegroup_id']}"/>
EOFORM;
if ($force['oldmission'] && ($force['destination_id'] != $_SESSION['nation_id'])) {
echo <<<EOFORM
<input type="submit" onclick="return confirm('Really send your forces home?')" name="recall" value="Go Home" class="btn btn-info"/>
EOFORM;
} else if (!$force['oldmission']) {
echo <<<EOFORM
<input type="submit" onclick="return confirm('Really recall this force?')" name="recall" value="Recall Force" class="btn btn-info"/>
EOFORM;
}
echo <<<EOFORM
</form></div>
<table class="table table-striped table-bordered">
EOFORM;
		}
        $tempid = $force['forcegroup_id'];
    }
    echo <<<EOFORM
<tr><td>
<center><img src="images/{$force['lowertype']}/w-{$force['armor_id']}-{$force['weapon_id']}.png"/></center>
{$force['name']}<br/>
{$forcetypes[$force['type']]}<br/>
EOFORM;
if ($force['type'] != 6) {
    echo <<<EOFORM
{$force['weaponname']}<br/>
{$force['armorname']}<br/>
EOFORM;
}
echo <<<EOFORM
Size: {$force['size']}<br/>
Training: {$force['training']}</td></tr>
EOFORM;
}
echo <<<EOFORM
</table></div></div></div>
EOFORM;
} else {
echo <<<EOFORM
You have no forces to send!
EOFORM;
}
echo "</center>";
include("footer.php");
?>