<?php
include("backend/allfunctions.php");
$extratitle = "Overview (Demo) - ";
include("header.php");
echo <<<EOFORM
<center><h3>This page is a nonfunctional sample!</h3></center>
<center><h4>Join an alliance to actually play!</h4></center>
<div class="row">
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Abilities</div>
      <table class="table">
		<thead>
		<tr><td style="text-align: right;">Ability</td><td>Ticks</td></tr>
		</thead>
        <tbody>
	<tr><td style="text-align: right;">Log Marketplace</td><td>3</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Status</div>
      <table class="table">
        <tbody>
          <tr><td style="text-align: right; width: 50%;">Satisfaction</td><td><span class="text-success">1200</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Alliance Satisfaction</td><td><span class="text-success">1000</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Production</td><td><span class="text-success">10</span></td></tr>
		  <tr><td style="text-align: right; width: 50%;">Tier</td><td><span class="text-success">2</span></td></tr>
          <tr><td style="text-align: right; width: 50%;">Need complements at</td><td><span class="text-success">110</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Current Element Positions and Production</div>
      <div class="panel-body" style="text-align: center;">
		10<br/>
		<img src="images/icons/1.png"/><br/>
		10<img src="images/icons/32.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/2.png"/>10<br/>
		10<img src="images/icons/16.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/4.png"/>10<br/>
		<img src="images/icons/8.png"/><br/>
		10
      </div>
    </div>
  </div>
</div>
<div class="panel panel-default">
     <div class="panel-heading">Resources</div>
     <table class="table">
      <thead>
        <tr>
          <td></td>
          <td>Resource</td>
          <td>Stock</td>
		  <td>Other</td>
          <td>Per-Tick Generated</td>
          <td></td>
          <td>Complement</td>
          <td>Complement Required Next Tick</td>
        </tr>
      </thead>
<tbody>
<tr><td style="width: 16px;"><img src="images/icons/1.png"/></td><td>Magic</td><td>20</td><td>0</td><td>10</td>
<td style="width: 16px;"><img src="images/icons/8.png"/></td><td>Kindness</td><td><span class="text-success">0</span></td></tr>
<tr><td style="width: 16px;"><img src="images/icons/2.png"/></td><td>Loyalty</td><td>20</td><td>0</td><td>10</td>
<td style="width: 16px;"><img src="images/icons/16.png"/></td><td>Honesty</td><td><span class="text-success">0</span></td></tr>
<tr><td style="width: 16px;"><img src="images/icons/4.png"/></td><td>Laughter</td><td>20</td><td>0</td><td>10</td>
<td style="width: 16px;"><img src="images/icons/32.png"/></td><td>Generosity</td><td><span class="text-success">0</span></td></tr>
<tr><td style="width: 16px;"><img src="images/icons/8.png"/></td><td>Kindness</td><td>390</td><td>0</td><td>10</td>
<td style="width: 16px;"><img src="images/icons/1.png"/></td><td>Magic</td><td><span class="text-danger">10</span></td></tr>
<tr><td style="width: 16px;"><img src="images/icons/16.png"/></td><td>Honesty</td><td>20</td><td>0</td><td>10</td>
<td style="width: 16px;"><img src="images/icons/2.png"/></td><td>Loyalty</td><td><span class="text-success">0</span></td></tr>
<tr><td style="width: 16px;"><img src="images/icons/32.png"/></td><td>Generosity</td><td>20</td><td>0</td><td>10</td>
<td style="width: 16px;"><img src="images/icons/4.png"/></td><td>Laughter</td><td><span class="text-success">0</span></td></tr>
<tr><td style="width: 16px;"><img src="images/icons/10.png"/></td><td>Devotion</td><td>10</td><td>0</td><td>0</td>
<td style="width: 16px;"><img src="images/icons/17.png"/></td><td>Truth</td><td><span class="text-success">0</span></td></tr>
</tbody>
</table>
</div>
EOFORM;
include("footer.php");
?>