<?
include("backend/backend_viewuser.php");
$extratitle = "View User - ";
include("header.php");
$cssoptions = array(0 => "White", 1 => "Black", 2 => "Gray");
if (!$thisuser) {
echo <<<EOFORM
<center><h3>User not found!</h3></center>
EOFORM;
} else {
if ($thisuser['donator']) {
    echo <<<EOFORM
<center><b>This user has donated to The Compounds of Harmony!</b></center>
EOFORM;
}
if ($thisuser['stasismode']) {
    echo <<<EOFORM
<center><b>This user is in stasis.</b></center>
EOFORM;
}
echo <<<EOFORM
<center>{$thisuser['username']}</center>
EOFORM;
if ($thisuser['alliancename']) {
echo <<<EOFORM
<center><a href="viewalliance.php?alliance_id={$thisuser['alliance_id']}">{$thisuser['alliancename']}</a></center>
EOFORM;
}
echo <<<EOFORM
<center>{$display['description']}</center>
EOFORM;
}
if ($thisuser && $_SESSION['user_id'] && ($_SESSION['user_id'] != $getpost['user_id'])) {
echo <<<EOFORM
<center>Send Message</center>
<form name="newmessage" method="post" action="viewuser.php">
<input type="hidden" name="token_viewuser" value="{$_SESSION['token_viewuser']}"/>
<input type="hidden" name="user_id" value="{$mysql['user_id']}"/>
<center><textarea name="message" class="form-control" style="width:75%">{$display['message']}</textarea></center>
<center><input type="submit" name="action" value="Send Message" class="btn btn-info"/></center>
</form>
EOFORM;
}
if (!$resourcelist && $userinfo['alliance_id'] && $thisuser['alliance_id']) {
	echo <<<EOFORM
	<center>Cost to Spy: {$constants['equalitytospy']} Equality</center>
	<center>
<form name="spy" method="post" action="viewuser.php">
<input type="hidden" name="token_viewuser" value="{$_SESSION['token_viewuser']}"/>
<input type="hidden" name="user_id" value="{$mysql['user_id']}"/>
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
      <div class="panel-heading">Status</div>
      <table class="table">
        <tbody>
          <tr><td style="text-align: right; width: 50%;">Satisfaction</td><td><span class="text-success">{$thisuser['satisfaction']}</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Alliance Satisfaction</td><td><span class="text-success">{$thisuser['alliancesatisfaction']}</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Production</td><td><span class="text-success">{$thisuser['production']}</span></td></tr>
		  <tr><td style="text-align: right; width: 50%;">Tier</td><td><span class="text-success">{$thisuser['tier']}</span></td></tr>
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
}
include("footer.php");
?>