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
        </tr>
      </thead>
      <tbody>
EOFORM;


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