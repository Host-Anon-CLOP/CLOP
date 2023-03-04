<?php
include("backend/backend_allianceoverview.php");
$extratitle = "Alliance Overview - ";
include("header.php");
$token = $_SESSION["token_allianceoverview"];
echo <<<EOFORM
<div class="row">
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Abilities</div>
      <table class="table">
		<thead>
		<tr><td style="text-align: right;">Ability</td><td>Ticks</td></tr>
		</thead>
        <tbody>
EOFORM;
if ($abilities) {
foreach ($abilities AS $abilityname => $ticks) {
	echo <<<EOFORM
	<tr><td style="text-align: right;">{$abilityname}</td><td>{$ticks}</td></tr>
EOFORM;
}
}
		echo <<<EOFORM
        </tbody>
      </table>
    </div>
  </div>
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"></div>
<center>Alliance Satisfaction: {$allianceinfo['alliancesatisfaction']}<br/>
EOFORM;
if ($allianceinfo['alliancefocus']) {
echo "Focus:<br/>";
} else {
echo "No focus.<br/>";
}
echo <<<EOFORM
{$production[1]}<br/>
<img src="images/icons/{$positions[1]}.png"/><br/>
{$production[6]}<img src="images/icons/{$positions[6]}.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/{$positions[2]}.png"/>{$production[2]}<br/>
{$production[5]}<img src="images/icons/{$positions[5]}.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/{$positions[3]}.png"/>{$production[3]}<br/>
<img src="images/icons/{$positions[4]}.png"/><br/>
{$production[4]}</center>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Peace Treaties</div>
<table class="table">
<thead>
<tr><td style="text-align: right;">Alliance</td><td>Ticks</td></tr>
</thead>
<tbody>
EOFORM;
if ($treaties) {
foreach ($treaties as $treaty) {
echo <<<EOFORM
<tr><td style="text-align: right;"><a href="viewalliance.php?alliance_id={$treaty['alliance_id']}">{$treaty['name']}</a></td><td>{$treaty['turns']}</td></tr>
EOFORM;
}
}
echo <<<EOFORM
</tbody></table>
</div>
</div>
</div>
EOFORM;
echo displayallianceresources($resourcelist, $userinfo['hideicons']);
echo <<<EOFORM
   <div class="panel panel-default">
     <div class="panel-heading">Members</div>
     <table class="table">
	<thead>
	<tr><td>Member</td><td>Abilities</td><td>Production</td><td>Satisfaction</td><td>Tier</td></tr>
	</thead><tbody>
EOFORM;
foreach ($members as $member) {
	if ($member['user_id'] == $allianceinfo['owner_id']) {
		$member['abilities']["owner"] = "&#8734;";
	}
    if (!$member['abilities']) {
        $showabilities = "None";
    } else {
        $showabilities =<<<EOFORM
<table class="table-striped"><thead><tr><td>Ability</td><td>Ticks</td></tr></thead><tbody>
EOFORM;
		foreach ($member['abilities'] as $ability => $ticks) {
			$showabilities .=<<<EOFORM
<tr><td>{$allianceabilities[$ability]}</td><td style="text-align: right;">{$ticks}</td></tr>
EOFORM;
		}
		$showabilities .=<<<EOFORM
</tbody></table>
EOFORM;
    }
	echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$member['user_id']}">{$member['username']}</a></td><td>{$showabilities}</td><td>{$member['production']}</td><td>{$member['satisfaction']}</td><td>{$member['tier']}</td></tr>
EOFORM;
}
echo <<<EOFORM
</tbody></table>
EOFORM;
include("footer.php");
?>