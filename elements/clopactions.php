<?
include("backend/backend_clopactions.php");
$extratitle = "&gt;CLOP Actions - ";
include("header.php");
$token = $_SESSION["token_clopactions"];
if ($nationinfo && !$errors) {
echo <<<EOFORM
<center><h3>Spying on {$mysql['nationname']}</h3></center>
EOFORM;
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
if ($nationtax == 0) $taxclass = "text-success";
else $taxclass = "text-danger";
echo <<<EOFORM
<center><h4>{$nationinfo['subregionname']}{$nationinfo['regionname']}</h4></center>
<center><h4>Age: {$nationinfo['age']}</h4></center>
<div class="panel panel-default">
<div class="panel-heading">Nation</div>
<table class="table">
        <tbody>
        <tr><td>Government Type</td><td>{$nationinfo['government']}</td></tr>
        <tr><td>Economic Type</td><td>{$nationinfo['economy']}</td></tr>
EOFORM;
        if (!$nationinfo['active_economy']) {
        echo <<<EOFORM
        <tr><td><span class="text-danger">Warning:</span></td><td><span class="text-danger">This nation's economic type is not active!</span></td></tr>
EOFORM;
        }
          echo <<<EOFORM
          <tr><td>Relationship with Solar Empire</td><td><span class="{$secolor}">{$nationinfo['se_relation']}</span>
          {$sedisplay}</td></tr>
          <tr><td>Relationship with New Lunar Republic</td><td><span class="{$nlrcolor}">{$nationinfo['nlr_relation']}</span>
          {$nlrdisplay}</td></tr>
          <tr><td>Satisfaction</td><td><span class="{$satColor}">{$nationinfo['satisfaction']}</span> (<span class="{$satColor2}">{$satperturn}</span> per tick)</td></tr>
          <tr><td>GDP</td><td><span class="text-success">{$displaygdp}</span> bits per tick</td></tr>
          <tr><td>Funds</td><td><span class="text-success">{$displayfunds}</span> bits</td></tr>
          <tr><td>Inflation Loss</td><td><span class="{$taxclass}">{$displaytax}</span> bits per tick</td></tr>
        </tbody>
      </table>
</div>
<div class="row">
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Resources</div>
     <table class="table">
      <thead>
        <tr>
          <td style="text-align: right;">Resource</td>
          <td>Qty</td>
          <td>Per-Tick Generated</td>
          <td>Per-Tick Used</td>
		  <td>Per-Tick Loss</td>
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

  $amountNet = ($affectedresources[$name] - $requiredresources[$name]) - $taxes[$name];

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
  $displaythistax = commas($taxes[$name]);
  if($taxes[$name] == 0) $taxclass = "text-success";
  else $taxclass = "text-danger";
  echo <<<EOFORM
    <tr>
    <td style="text-align: right;">{$name}</td>
    <td><span class="{$amountClass}">{$displayamount}</span></td>
    <td style="text-align: center;"><span class="text-success">{$displayaffected}</span></td>
    <td style="text-align: center;"><span class="text-danger">{$displayrequired}</span></td>
	<td style="text-align: center;"><span class="{$taxclass}">{$displaythistax}</span></td>
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
     <center>This nation's military also uses <b>{$dmilsugar} sugar, {$dmilgems} gems, {$dmilcoffee} coffee, and {$dmilgasoline} gasoline</b> every 12 hours.</center>
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
   }
echo <<<EOFORM
 <tr>
   <td>{$buildinginfo['name']}</td>
   <td><span class="text-success">{$buildinginfo['amount']} {$disabledinfo}</span></td>
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
		  <td>Loss per Tick</td>
        </tr>
      </thead>
      <tbody>
EOFORM;
foreach ($weapons as $name => $amount) {
	$displayamount = commas($amount);
	$displaythistax = commas($taxes[$name]);
	if($taxes[$name] == 0) $taxclass = "text-success";
	else $taxclass = "text-danger";
    echo <<<EOFORM
    <tr>
    <td style="text-align: right;">{$name}</td>
    <td>{$displayamount}</td>
	<td><span class="{$taxclass}">{$displaythistax}</span></td>
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
		  <td>Loss per Tick</td>
        </tr>
      </thead>
      <tbody>
EOFORM;
foreach ($armor as $name => $amount) {
	$displayamount = commas($amount);
	$displaythistax = commas($taxes[$name]);
	if($taxes[$name] == 0) $taxclass = "text-success";
	else $taxclass = "text-danger";
    echo <<<EOFORM
    <tr>
    <td style="text-align: right;">{$name}</td>
    <td>{$displayamount}</td>
	<td><span class="{$taxclass}">{$displaythistax}</span></td>
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
}
echo <<<EOFORM
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase &gt;CLOP Nation Satisfaction by {$constants['satisfactionpercheer']}</div>
Cost: 1 Cheer
<form action="clopactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_clopactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-4">
<input name="nationname" placeholder="Nation" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="satisfy" value="Increase" class="btn btn-success"/>
</div></div>
</form>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Spy</div>
Cost: {$constants['equalitytoclopspy']} Equality
<form action="clopactions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_clopactions" value="{$token}"/>
<div class="row input-group">
<div class="col-sm-6">
<input name="nationname" placeholder="Nation" value="" class="form-control"/>
</div>
<div class="col-sm-6">
<input type="submit" name="spy" value="Spy" class="btn btn-success"/>
</div></div>
</form>
</div></div></div>
EOFORM;
include("footer.php");
?>