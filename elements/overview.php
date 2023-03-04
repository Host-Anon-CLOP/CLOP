<?php
include("backend/backend_overview.php");
$extratitle = "Overview - ";
include("header.php");
$token = $_SESSION["token_overview"];
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
if ($abilities['Encouraged']) {
    $extra = "+5";
}
		echo <<<EOFORM
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Status</div>
      <table class="table">
        <tbody>
          <tr><td style="text-align: right; width: 50%;">Satisfaction</td><td><span class="text-success">{$userinfo['satisfaction']}</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Alliance Satisfaction</td><td><span class="text-success">{$allianceinfo['alliancesatisfaction']}</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Production</td><td><span class="text-success">{$userinfo['production']} {$extra}</span></td></tr>
		  <tr><td style="text-align: right; width: 50%;">Tier</td><td><span class="text-success">{$userinfo['tier']}</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Need complements at</td><td><span class="text-success">{$threshold}</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Current Element Positions and Production</div>
      <div class="panel-body" style="text-align: center;">
		{$production[1]}<br/>
		<img src="images/icons/{$positions[1]}.png"/><br/>
		{$production[6]}<img src="images/icons/{$positions[6]}.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/{$positions[2]}.png"/>{$production[2]}<br/>
		{$production[5]}<img src="images/icons/{$positions[5]}.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/{$positions[3]}.png"/>{$production[3]}<br/>
		<img src="images/icons/{$positions[4]}.png"/><br/>
		{$production[4]}
      </div>
    </div>
  </div>
</div>
EOFORM;
echo displayresources($resourcelist, $userinfo['hideicons']);
include("footer.php");
?>