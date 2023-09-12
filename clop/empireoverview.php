<?php
include("backend/backend_empireoverview.php");
$extratitle = "Empire Overview - ";
include("header.php");

echo <<<EOFORM
<div class="row">
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Resources - stockpiles / net / ticksworth</div>
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
EOFORM;

foreach ($empirenations as $key => $value) {
    echo <<<EOFORM
<td>$value</td>
EOFORM;
}

        echo <<<EOFORM
        </tr>
      </thead>
      <tbody>
EOFORM;


foreach ($all_resources_list as $key => $value) {
echo <<<EOFORM
    <tr>
EOFORM;

if (!$nationinfo['hideicons']) {
    echo <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$value}.png"/></td>
EOFORM;
    }

echo <<<EOFORM
    <td style="text-align: right;">{$value}</td>
EOFORM;

foreach ($empirenations as $key => $value) {
    echo <<<EOFORM
    <td>{$resources[$key][0]}</td>
EOFORM;

echo <<<EOFORM
    </tr>
EOFORM;
# subsequent td are format: <td style="text-align: center;"><span class="text-success">{$displayaffected}</span></td>

}

/*
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
*/

include("footer.php");
?>