<?php
include("backend/backend_viewalliance.php");
$extratitle = "View Alliance - ";
include("header.php");
if ($thisallianceinfo['name']) {
echo <<<EOFORM
<center><h3>{$thisallianceinfo['name']}</h3></center>
<center>{$displaypubdescription}</center>
<center><table class="table table-striped table-bordered">
<thead><tr><td>Member</td><td>Production</td><td>Tier</td></tr></thead><tbody>
EOFORM;
foreach ($alliancemembers as $member) {
echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$member['user_id']}">{$member['username']}</a></td><td>{$member['production']}</td><td>{$member['tier']}</td></tr>
EOFORM;
}
echo <<<EOFORM
</tbody></table></center>
EOFORM;
} else {
    echo <<<EOFORM
<center>Alliance not found.</center> 
EOFORM;
}
if (!$resourcelist && $userinfo['alliance_id'] && $thisallianceinfo['alliance_id'] && $thisallianceinfo['alliance_id'] != $userinfo['alliance_id']) {
	echo <<<EOFORM
	<center>Cost to Spy: {$constants['allianceequalitytospy']} Equality</center>
	<center>
<form name="spy" method="post" action="viewalliance.php">
<input type="hidden" name="token_viewalliance" value="{$_SESSION['token_viewalliance']}"/>
<input type="hidden" name="alliance_id" value="{$mysql['alliance_id']}"/>
<input type="submit" name="spy" value="Spy" class="btn btn-warning"/>
</form>
</center>
EOFORM;
} else if ($resourcelist) {
echo <<<EOFORM
<div class="row">
<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Take a screenshot</div>
	If you leave the page, you lose the information.
    </div>
    </div>
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading"></div>
<center>Alliance Satisfaction: {$thisallianceinfo['alliancesatisfaction']}<br/>
EOFORM;
if ($thisallianceinfo['alliancefocus']) {
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
</div></div></div>
EOFORM;
EOFORM;
echo displayallianceresources($resourcelist, $userinfo['hideicons']);
}
?>