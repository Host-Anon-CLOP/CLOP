<?php
include("backend/backend_voidactions.php");
$extratitle = "Void Actions - ";
include("header.php");
$token = $_SESSION["token_voidactions"];
echo <<<EOFORM
<center>Void attacks are <b>completely indefensible</b>, <b>can target almost anyone</b>, and <b>happen instantly</b>.
Nothing bad happens to you or your alliance if you use them. Not directly, anyway...</center>
<div class="row">
	<div class="col-md-4">
	</div>
	<div class="col-md-4">
	<div class="panel panel-default">
      <div class="panel-heading">Costs</div>
	  <div class="row">
	  <div class="col-md-6">Destruction</div>
	  <div class="col-md-6">{$constants['voidfordestruction']} Void</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Depression</div>
	  <div class="col-md-6">{$constants['voidfordepression']} Void</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Pollution</div>
	  <div class="col-md-6">{$constants['voidforpollution']} Void</div>
	  </div>
    </div>
	</div>
	<div class="col-md-4">
	</div>
	</div>
<form name="voidactions" action="voidactions.php" method="post">
<input type="hidden" name="token_voidactions" value="{$token}"/>
<center><input name="username" placeholder="Target" class="form-control" type="text" style="width:200px;"/></center>
<div class="row">
	<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Pollution</div>
<center>Reduce the target's production by 1.</center>
<center><input type="submit" name="pollute" value="Pollute" class="btn btn-danger" onclick="return confirm('Really destroy the target's production?')"/></center>
	</div>
	</div>
	<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Destruction</div>
	  <center>Destroy all of a target's resource.</center>
<div class="row">
<div class="col-sm-6">
<select name="resource_id" class="form-control">
EOFORM;
echo elementsdropdown(true, false);
echo <<<EOFORM
</select>
</div>
<div class="col-sm-6">
	<input type="submit" name="destroy" value="Destroy" class="btn btn-danger" onclick="return confirm('Really destroy all of the target's resource?')"/>
</div></div>
	</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
    <div class="panel panel-default">
		<div class="panel-heading">Depression</div>
	<center>Reduce the target's satisfaction to 0.</center>
	<center><input type="submit" name="depress" value="Depression" class="btn btn-danger" onclick="return confirm('Really destroy the user's satisfaction?')"/></center>
	</div>
	</div>
</div>
</form>
EOFORM;
include("footer.php");
?>