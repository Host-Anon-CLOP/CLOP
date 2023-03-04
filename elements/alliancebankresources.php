<?php
include("backend/backend_alliancebankresources.php");
$extratitle = "Bank Alliance Resources - ";
include("header.php");
$token = $_SESSION["token_alliancebankresources"];
echo <<<EOFORM
<div class="row">
<div class="col-md-4">
</div>
<div class="col-md-4">
<div class="panel panel-default">
  <div class="panel-heading">Banked Defense Costs (Alliance)</div>
  <div class="row">
  <div class="col-md-6">Burden</div>
  <div class="col-md-6">{$constants['alliancesecurityforburden']} Security</div>
  </div>
  <div class="row">
  <div class="col-md-6">Corruption</div>
  <div class="col-md-6">{$constants['allianceserenityforcorrupt']} Serenity</div>
  </div>
  <div class="row">
  <div class="col-md-6">Sadness</div>
  <div class="col-md-6">{$constants['allianceserenityforsadness']} Serenity</div>
  </div>
  <div class="row">
  <div class="col-md-6">Theft</div>
  <div class="col-md-6">{$constants['alliancesecurityfortheft']} Security</div>
  </div>
  <div class="row">
  <div class="col-md-6">Spying</div>
  <div class="col-md-6">{$constants['allianceunitytoblock']} Unity</div>
  </div>
</div>
</div>
<div class="col-md-4">
</div>
</div>
<center>
<div class="panel panel-default" style="width:33%">
<div class="panel-heading">Bank Resources</div>
<form action="alliancebankresources.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_alliancebankresources" value="{$token}"/>
<div class="form-group row">
<div class="col-sm-6">
<select name="resource_id" class="form-control">
EOFORM;
foreach ($bankableresources as $resource_id => $name) {
echo <<<EOFORM
<option value="{$resource_id}">{$name}</option>
EOFORM;
}
echo <<<EOFORM
</select>
</div>
<div class="col-sm-6">
<div class="input-group">
<input name="amount" placeholder="Amount" value="" class="form-control" type="text"/>
<span class="input-group-btn">
<input type="submit" name="bank" value="Bank" class="btn btn-success"/>
</span></div></div></div></form></div>
</center>
<center>
<div class="panel panel-default" style="max-width:800px">
<div class="panel-heading">Banked Resources</div>
<table class="table table-striped">
EOFORM;
if ($bankedresources) {
foreach ($bankedresources as $resource_id => $amount) {
    if (!$userinfo['hideicons']) {
    echo <<<EOFORM
<td style="width: 16px;"><img src="images/icons/{$resource_id}.png"/></td>
EOFORM;
}
echo <<<EOFORM
<td>{$bankableresources[$resource_id]}</td>
<td>{$amount}</td>
<td style="width:50%">
<form action="alliancebankresources.php" method="post" class="form-horizontal" role="form">
<input type="hidden" name="token_alliancebankresources" value="{$token}"/>
<input type="hidden" name="resource_id" value="{$resource_id}"/>
<div class="row">
<div class="col-sm-6">
<div class="input-group">
<span class="input-group-btn"><input type="submit" name="unbank" value="Unbank:" class="btn btn-warning"/></span>
<input name="amount" placeholder="Amount" value="" class="form-control" type="text"/></div></div>
<div class="col-sm-6"><input type="submit" name="unbankall" value="Unbank All" class="btn btn-danger"/></div></div></div>
</form></td></tr>
EOFORM;
}
}
echo <<<EOFORM
</table></center>
EOFORM;
include("footer.php");
?>