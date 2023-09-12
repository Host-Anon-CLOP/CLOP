<?php
include("backend/backend_empireoverview.php");
$extratitle = "Empire Overview - ";
include("header.php");


echo <<<EOFORM
<div class="row">
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">Resources - Stockpiles</div>
     <table class="table">
      <thead>
        <tr>
        
EOFORM;
        if (!$nationinfo['hideicons']) {
            echo <<<EOFORM
            <td style="width: 16px;"></td>
EOFORM;
        }
        echo <<<EOFORM
        <td style="text-align: left;">Resource</td>
EOFORM;

foreach ($empirenations as $nation_id => $nation_name) {
    echo <<<EOFORM
    <td style="text-align: left;"><form action="overview.php" method="post"><button name="switchnation_id" type="submit" value="{$nation_id}">{$nation_name}</button></form></td>
EOFORM;
}

        echo <<<EOFORM
        <td>TOTAL</td>
        </tr>
      </thead>
      <tbody>
EOFORM;

# get satisfaction per nation
echo <<<EOFORM
    <tr><td></td><td style="text-align: left;">Satisfaction</td>
EOFORM;
foreach ($empirenations as $nation_id => $nation_name) {
    echo <<<EOFORM
    <td style="text-align: left;"><span class="text-success">{$resources[$nation_id]['satisfaction']}</span></td>
EOFORM;
}
echo <<<EOFORM
    <td></td></tr>
EOFORM;

# get nlr rep per nation
echo <<<EOFORM
    <tr><td></td><td style="text-align: left;">NLR Rep</td>
EOFORM;
foreach ($empirenations as $nation_id => $nation_name) {
    echo <<<EOFORM
    <td style="text-align: left;"><span class="text-success">{$resources[$nation_id]['nlr']}</span></td>
EOFORM;
}
echo <<<EOFORM
    <td></td></tr>
EOFORM;

# get se rep per nation
echo <<<EOFORM
    <tr><td></td><td style="text-align: left;">SE Rep</td>
EOFORM;
foreach ($empirenations as $nation_id => $nation_name) {
    echo <<<EOFORM
    <td style="text-align: left;"><span class="text-success">{$resources[$nation_id]['se']}</span></td>
EOFORM;
}
echo <<<EOFORM
    <td></td></tr>
EOFORM;

# get funds per nation
echo <<<EOFORM
    <tr><td></td><td style="text-align: left;">Funds</td>
EOFORM;
$total = 0;
foreach ($empirenations as $nation_id => $nation_name) {
    $total = $total + $resources[$nation_id]['funds'];
    echo <<<EOFORM
    <td style="text-align: left;"><span class="text-success">{$resources[$nation_id]['funds']}</span></td>
EOFORM;
}
echo <<<EOFORM
<td style="text-align: left;"><span class="text-success">{$total}</span></td></tr>
EOFORM;

# iterate all nations, and all resources per nation
foreach ($all_resources_list as $resource_id => $resource_name) {
echo <<<EOFORM
    <tr>
EOFORM;

if (!$nationinfo['hideicons']) {
    echo <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$resource_name}.png"/></td>
EOFORM;
    }

echo <<<EOFORM
    <td style="text-align: left;">{$resource_name}</td>
EOFORM;

foreach ($empirenations as $nation_id => $nation_name) {
    echo <<<EOFORM
    <td style="text-align: left;"><span class="text-success">{$resources[$nation_id][$resource_id]}</span></td>
EOFORM;
}
 

echo <<<EOFORM
    </tr>
EOFORM;
}

echo <<<EOFORM
    </tbody></table>
EOFORM;


include("footer.php");
?>