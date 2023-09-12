<?php
include("backend/backend_empireoverview.php");
$extratitle = "Empire Overview - ";
include("header.php");




echo <<<EOFORM
<a role="button" aria-label="submit form" href="javascript:void(0)" onclick="document.querySelector('form').submit()">Submit</a>
EOFORM;

echo <<<EOFORM
<li><a><form action="" method="post"><select name="switchnation_id" onclick='this.form.submit()'><option value="2">testuser2</option></select></form></a></li>
EOFORM;

# <li><a><form action="" method="post"><select name="switchnation_id" onclick='this.form.submit()'><option value="2">testuser2</option></select></form></a></li>
# <td><a href="viewnation.php?nation_id={$attack['attackerid']}">{$attack['attackername']}</a></td>






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
    <td style="text-align: left;">{$nation_name}</td>
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