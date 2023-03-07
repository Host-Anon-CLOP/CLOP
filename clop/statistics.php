<?php
include("backend/backend_statistics.php");
$extratitle = "Statistics - ";
include("header.php");

# New Players
echo <<<EOFORM
<center>New Nations</center>
<table class="table table-striped table-bordered">
<tr><th>Last 24 Hours:</th><th>$nations_new_24h</th></tr>
<tr><th>Last 7 Days:</th><th>$nations_new_week</th></tr>
<tr><th>Last 30 Days:</th><th>$nations_new_month</th></tr>
</table>
EOFORM;


# Active Players
echo <<<EOFORM
<center>Players Active</center>
<table class="table table-striped table-bordered">
<tr><th>Last 24 Hours:</th><th>$players_active_24h</th></tr>
<tr><th>Last 7 Days:</th><th>$players_active_week</th></tr>
<tr><th>Last 30 Days:</th><th>$players_active_month</th></tr>
</table>
EOFORM;


# Census
echo <<<EOFORM
<center>Census</center>
<table class="table table-striped table-bordered">
<tr><th>Burrozil:</th><th>$census_burrozil</th></tr>
<tr><th>Zebrica:</th><th>$census_zebrica</th></tr>
<tr><th>Saddle Arabia:</th><th>$census_saddle</th></tr>
<tr><th>Przewalskia :</th><th>$census_prze</th></tr>
</table>
EOFORM;


# Global Resources
echo <<<EOFORM
<div class="row">
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Global Resources</div>
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
          <td>Generated</td>
          <td>Used</td>
		      <td>Loss</td>
          <td>Net</td>
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
  else {
    $taxclass = "text-danger";
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
	  <td style="text-align: center;"><span class="{$taxclass}">{$displaythistax}</span></td>
    <td style="text-align: center;"><span class="{$amountNetClass}">{$displaynet}</span></td>
    </tr>
EOFORM;
}

include("footer.php");
?>