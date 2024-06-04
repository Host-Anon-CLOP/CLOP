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
<tr><th>REGION:</th><th>NORTH</th><th>CENTRAL</th><th>SOUTH</th><th>TOTAL</th></tr>
<tr><th>Burrozil:</th><th>$census_burrozil_north</th><th>$census_burrozil_central</th><th>$census_burrozil_south</th><th>$census_burrozil_total</th></tr>
<tr><th>Zebrica:</th><th>$census_zebrica_north</th><th>$census_zebrica_central</th><th>$census_zebrica_south</th><th>$census_zebrica_total</th></tr>
<tr><th>Saddle Arabia:</th><th>$census_saddle_north</th><th>$census_saddle_central</th><th>$census_saddle_south</th><th>$census_saddle_total</th></tr>
<tr><th>Przewalskia :</th><th>$census_prze_north</th><th>$census_prze_central</th><th>$census_prze_south</th><th>$census_prze_total</th></tr>
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
          <td>Generated</td>
          <td>Used</td>
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
  $amountNet = ($affectedresources[$name] - $requiredresources[$name]);

  if($amountNet > 0) $amountNetClass = "text-success";
  elseif($amountNet == 0) $amountNetClass = "text-warning";
  else $amountNetClass = "text-danger";
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
    <td style="text-align: center;"><span class="text-success">{$displayaffected}</span></td>
    <td style="text-align: center;"><span class="text-danger">{$displayrequired}</span></td>
    <td style="text-align: center;"><span class="{$amountNetClass}">{$displaynet}</span></td>
    </tr>
EOFORM;
}

include("footer.php");
?>