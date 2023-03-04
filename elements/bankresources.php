<?php
include("backend/backend_bankresources.php");
$extratitle = "Bank Resources - ";
include("header.php");
$token = $_SESSION["token_bankresources"];
echo <<<EOFORM
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
  <div class="panel-heading">Banked Defense Costs</div>
  <div class="row">
  <div class="col-md-6">Brutality</div>
  <div class="col-md-6">{$constants['securityforbrutal']} Security</div>
  </div>
  <div class="row">
  <div class="col-md-6">Burden</div>
  <div class="col-md-6">{$constants['securityforburden']} Security</div>
  </div>
  <div class="row">
  <div class="col-md-6">Corruption</div>
  <div class="col-md-6">{$constants['serenityforcorrupt']} Serenity</div>
  </div>
  <div class="row">
  <div class="col-md-6">Despair</div>
  <div class="col-md-6">{$constants['serenityfordespair']} Serenity</div>
  </div>
  <div class="row">
  <div class="col-md-6">Robbery</div>
  <div class="col-md-6">{$constants['securityforrobbery']} Security</div>
  </div>
</div>
</div>
<div class="col-md-4">
<div class="panel panel-default">
  <div class="panel-heading">Attraction Costs</div>
  <div class="row">
  <div class="col-md-6">Brutality</div>
  <div class="col-md-6">{$constants['heroismforbrutal']} Heroism</div>
  </div>
  <div class="row">
  <div class="col-md-6">Burden</div>
  <div class="col-md-6">{$constants['heroismforburden']} Heroism</div>
  </div>
  <div class="row">
  <div class="col-md-6">Corruption</div>
  <div class="col-md-6">{$constants['heroismforcorrupt']} Heroism</div>
  </div>
  <div class="row">
  <div class="col-md-6">Despair</div>
  <div class="col-md-6">{$constants['heroismfordespair']} Heroism</div>
  </div>
  <div class="row">
  <div class="col-md-6">Robbery</div>
  <div class="col-md-6">{$constants['heroismforrobbery']} Heroism</div>
  </div>
</div>
</div>
<div class="col-md-4">
<div class="panel panel-default">
  <div class="panel-heading">Other Costs</div>
  <div class="row">
  <div class="col-md-6">Block Spying</div>
  <div class="col-md-6">{$constants['unitytoblock']} Unity</div>
  </div>
  <div class="row">
  <div class="col-md-6">Block &gt;CLOP Spying</div>
  <div class="col-md-6">{$constants['unitytoclopblock']} Unity</div>
  </div>
  <div class="row">
  <div class="col-md-6">Block Marketplace Investigation</div>
  <div class="col-md-6">{$constants['liesabsorbed']} Lies per Times</div>
  </div>
  <div class="row">
  <div class="col-md-6">Block Philippy Investigation</div>
  <div class="col-md-6">1 Lies per {$constants['liesdivisor']} resources</div>
  </div>
  <div class="row">
  <div class="col-md-6">Prevent Being Kicked (for a tick)</div>
  <div class="col-md-6">{$constants['treacheryabsorbed']} Treachery</div>
  </div>
</div>
</div>
</div>
<center>
<div class="panel panel-default" style="width:33%">
<div class="panel-heading">Bank Resources</div>
<form action="bankresources.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_bankresources" value="{$token}"/>
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
<form action="bankresources.php" method="post" class="form-horizontal" role="form">
<input type="hidden" name="token_bankresources" value="{$token}"/>
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