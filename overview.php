<?php
include("backend/backend_overview.php");
$extratitle = "Overview - ";
include("header.php");
$satColor = $nationinfo['satisfaction'] > 0 ? "text-success" : "text-danger";
if ($satperturn > 0) {
    $satColor2 = "text-success";
} else if ($satperturn < 0) {
    $satColor2 = "text-danger";
} else {
    $satColor2 = "text-warning";
}
if ($nationinfo['se_relation'] > 0) {
    $secolor = "text-success";
} else if ($nationinfo['se_relation'] == 0) {
    $secolor = "text-warning";
} else {
    $secolor = "text-danger";
}
if ($nationinfo['nlr_relation'] > 0) {
    $nlrcolor = "text-success";
} else if ($nationinfo['nlr_relation'] == 0) {
    $nlrcolor = "text-warning";
} else {
    $nlrcolor = "text-danger";
}
if ($seperturn && $seperturn == (int)$seperturn) {
    if ($seperturn > 0) {
        $secolor2 = "text-warning";
    } else {
        $secolor2 = "text-danger";
    }
} else {
    $secolor2 = "text-success";
}
if ($nlrperturn && $nlrperturn == (int)$nlrperturn) {
    if ($nlrperturn > 0) {
        $nlrcolor2 = "text-warning";
    } else {
        $nlrcolor2 = "text-danger";
    }
} else {
    $nlrcolor2 = "text-success";
}
if ($attackers) {
echo <<<EOFORM
<center><span class="text-danger">You have attackers in your nation!</span></center>
EOFORM;
}
if ($nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Transponyism") {
    $sedisplay = "(Ascending)";
	$nlrdisplay = "(Ascending)";
} else {
	$sedisplay=<<<EOFORM
(<span class="{$secolor2}">{$seperturn}</span> per tick)
EOFORM;
    $nlrdisplay=<<<EOFORM
(<span class="{$nlrcolor2}">{$nlrperturn}</span> per tick)
EOFORM;
}
echo <<<EOFORM
<center><h3>{$nationinfo['name']}</h3></center>
<center><h4>{$nationinfo['subregionname']}{$nationinfo['regionname']}</h4></center>
<center><h4>Age: {$nationinfo['age']}</h4></center>
<div class="row">
  <div class="col-md-8">
    <div class="panel panel-default">
      <div class="panel-heading">Nation</div>
      <table class="table">
        <tbody>
        <tr><td style="text-align: right;">Government Type</td><td>{$nationinfo['government']}</td></tr>
        <tr><td style="text-align: right;">Economic Type</td><td>{$nationinfo['economy']}</td></tr>
EOFORM;
        if (!$nationinfo['active_economy']) {
        echo <<<EOFORM
        <tr><td style="text-align: right;"><span class="text-danger">Warning:</span></td><td><span class="text-danger">Your economic type is not active!</span></td></tr>
EOFORM;
        }
          echo <<<EOFORM
          <tr><td style="text-align: right;">Relationship with Solar Empire</td><td><span class="{$secolor}">{$nationinfo['se_relation']}</span>
          {$sedisplay}</td></tr>
          <tr><td style="text-align: right;">Relationship with New Lunar Republic</td><td><span class="{$nlrcolor}">{$nationinfo['nlr_relation']}</span>
          {$nlrdisplay}</td></tr>
          <tr><td style="text-align: right;">Satisfaction</td><td><span class="{$satColor}">{$nationinfo['satisfaction']}</span> (<span class="{$satColor2}">{$satperturn}</span> per tick)</td></tr>
          <tr><td style="text-align: right;">GDP</td><td><span class="text-success">{$displaygdp}</span> bits per tick</td></tr>
          <tr><td style="text-align: right;">Funds</td><td><span class="text-success">{$displayfunds}</span> bits</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Description</div>
      <div class="panel-body">
        <form name="changeinfo" method="post" action="overview.php" class="form" role="form">
          <textarea class="form-control" name="description">{$display['description']}</textarea>
          <input type="hidden" name="token_overview" value="{$_SESSION['token_overview']}"/>
          </br>
          <input name="action" type="submit" value="Update Information" class="btn btn-success"/>
        </form><br/>
        <a href="viewnation.php?nation_id={$nationinfo['nation_id']}">View Nation</a>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Resources</div>
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
          <td>Qty</td>
          <td>Per-Tick Generated</td>
          <td>Per-Tick Used</td>
          <td>Per-Tick Net</td>
        </tr>
      </thead>
      <tbody>
EOFORM;
foreach($affectedresources as $name => $amount) {
if (!$resources[$name]) $resources[$name] = 0;
}
foreach($requiredresources as $name => $amount) {
if (!$resources[$name]) $resources[$name] = 0;
}
ksort($resources);
foreach($resources as $name => $amount) {
  if (!$affectedresources[$name]) {
    $affectedresources[$name] = 0;
  }
  if (!$requiredresources[$name]) {
    $requiredresources[$name] = 0;
  }
  if($amount >= $requiredresources[$name]) $amountClass = "text-success";
  elseif($amount >= ($affectedresources[$name] - $requiredresources[$name])) $amountClass = "text-warning";
  else $amountClass = "text-danger";

  $amountNet = ($affectedresources[$name] - $requiredresources[$name]);

  if($amountNet > 0) $amountNetClass = "text-success";
  elseif($amountNet == 0) $amountNetClass = "text-warning";
  else $amountNetClass = "text-danger";
  $displayamount = commas($amount);
  $displayaffected = commas($affectedresources[$name]);
  $displayrequired = commas($requiredresources[$name]);
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
    <td><span class="{$amountClass}">{$displayamount}</span></td>
    <td style="text-align: center;"><span class="text-success">{$displayaffected}</span></td>
    <td style="text-align: center;"><span class="text-danger">{$displayrequired}</span></td>
    <td style="text-align: center;"><span class="{$amountNetClass}">{$displaynet}</span></td>
    </tr>
EOFORM;
}
$dmilsugar = commas($milsugar);
$dmilgems = commas($milgems);
$dmilcoffee = commas($milcoffee);
$dmilgasoline = commas($milgasoline);
echo <<<EOFORM
       </tbody>
     </table>
     <center>Your military also uses <b>{$dmilsugar} sugar, {$dmilgems} gems, {$dmilcoffee} coffee, and {$dmilgasoline} gasoline</b> every 12 hours.</center>
   </div>
  </div>
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Buildings</div>
     <table class="table">
       <tbody>
EOFORM;
foreach ($buildings as $buildinginfo) {
   $reenablebutton = "";
   $disablebutton = "";
   $disabledinfo = "";
   $satwarning = "";
   if ($buildinginfo['satisfaction_on_destroy']) {
		if ($buildinginfo['satisfaction_on_destroy'] > 0) {
			$gainlose = "gain";
		} else {
			$gainlose = "lose";
		}
		$satondestroy = abs($buildinginfo['satisfaction_on_destroy']);
		$satwarning = " You will {$gainlose} {$satondestroy} satisfaction for each building you destroy!";
   }
   if ($buildinginfo['disabled']) {
       $disabledinfo = "({$buildinginfo['disabled']} disabled)";
       $reenablebutton =<<<EOFORM
       <div class="input-group">
        <span class="input-group-btn">
		<input type="submit" name="reenable" value="Reenable {$buildinginfo['name']}" class="btn btn-success">
		</span>
		<input name="reenableamount" placeholder="Amount" value="" class="form-control">
		</div>
EOFORM;
   }
   if ($buildinginfo['disabled'] < $buildinginfo['amount']) {
       $disablebutton =<<<EOFORM
		<div class="input-group">
		<span class="input-group-btn">
		<input type="submit" name="disable" value="Disable {$buildinginfo['name']}" class="btn btn-warning">
		</span>
		<input name="disableamount" placeholder="Amount" value="" class="form-control">
		</div>
EOFORM;
   }
echo <<<EOFORM
 <tr>
   <td style="text-align: right;">{$buildinginfo['name']}</td>
   <td><span class="text-success">{$buildinginfo['amount']} {$disabledinfo}</span></td>
   <td>
	 <form name="recycle" method="post" action="overview.php">
	   <input type="hidden" name="token_overview" value="{$_SESSION['token_overview']}"/>
	   <input type="hidden" name="resource_id" value="{$buildinginfo['resource_id']}"/>
	   {$reenablebutton}
	   {$disablebutton}
	   <div class="input-group">
	   <span class="input-group-btn">
	   <input type="submit" onclick="return confirm('Really destroy {$buildinginfo['name']}?{$satwarning}')" name="recycle" value="Destroy {$buildinginfo['name']}" class="btn btn-danger"/>
	   </span>
	   <input name="recycleamount" type="text" placeholder="Amount" value="" class="form-control">
	   </div>
	 </form>
   </td>
 </tr>
EOFORM;
}
echo  <<<EOFORM
       </tbody>
     </table>
   </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Weapons</div>
     <table class="table">
      <thead>
        <tr>
          <td style="text-align: right;">Weapon</td>
          <td>Qty</td>
        </tr>
      </thead>
      <tbody>
EOFORM;
foreach ($weapons as $name => $amount) {
	$displayamount = commas($amount);
    echo <<<EOFORM
    <tr>
    <td style="text-align: right;">{$name}</td>
    <td>{$displayamount}</td>
    </tr>
EOFORM;
}
echo <<<EOFORM
       </tbody>
     </table>
   </div>
  </div>
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Armor</div>
     <table class="table">
      <thead>
        <tr>
          <td style="text-align: right;">Armor</td>
          <td>Qty</td>
        </tr>
      </thead>
      <tbody>
EOFORM;
foreach ($armor as $name => $amount) {
	$displayamount = commas($amount);
    echo <<<EOFORM
    <tr>
    <td style="text-align: right;">{$name}</td>
    <td>{$displayamount}</td>
    </tr>
EOFORM;
}
echo <<<EOFORM
      </tbody>
     </table>
   </div>
  </div>
</div>
EOFORM;
$tempid = "";
if ($attackers) {
echo <<<EOFORM
<center><h4>Attackers</h4></center>
<div id="container1" class="js-masonry" data-masonry-options='{ "itemSelector": ".masonryitem", "isFitWidth": true }'>
EOFORM;
foreach ($attackers as $attacker) {
    if ($tempid != $attacker['forcegroup_id']) {
    if ($attacker['ownernation_id'] > 0) {
        $nationlink=<<<EOFORM
<a href="viewnation.php?nation_id={$attacker['ownernation_id']}">{$attacker['ownername']}</a> ({$attacker['ownerregionname']})
EOFORM;
    } else {
        $nationlink = $attacker['ownername'];
    }
    if ($tempid != "") echo "</table></div></div>"; 
	echo <<<EOFORM
<div class="masonryitem" style="padding: 10px; width: 300px">
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
include("footer.php");
?>