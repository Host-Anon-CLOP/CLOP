<?php
include("backend/backend_incoming.php");
$extratitle = "Incoming Attacks - ";
include("header.php");
$token = $_SESSION["token_incoming"];
$usetime = time();
if (date("H") % 3 == 1) {
    $usetime += 7200;
} else if (date("H") % 3 == 2) {
    $usetime += 3600;
} else {
    $usetime += 10800;
}
echo <<<EOFORM
<div class="row">
<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Nullification Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['shelterforbrutal']} Shelter</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['shelterforburden']} Shelter</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['shelterforcorrupt']} Shelter</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['shelterfordespair']} Shelter</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['shelterforrobbery']} Shelter</div>
	  </div>
    </div>
	</div>
	<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Redirection Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['maliceforbrutal']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['maliceforburden']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['maliceforcorrupt']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['malicefordespair']} Malice</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['maliceforrobbery']} Malice</div>
	  </div>
    </div>
	</div>
</div>
EOFORM;
if ($attacks) {
    echo <<<EOFORM
<table class="table table-striped">
<tr><td><div class="row"><div class="col-md-6">Attack</div><div class="col-md-2">Arrives</div><div class="col-md-4">Actions</div></div></td></tr>
EOFORM;
	foreach ($attacks as $attack) {
	echo <<<EOFORM
<tr><td><div class="row">
EOFORM;
		switch ($attack['type']) {
			case "burden":
	echo <<<EOFORM
<div class="col-md-6">{$attack['attackername']} is burdening you with a resource.</div>
EOFORM;
			break;
			case "corrupt":
	echo <<<EOFORM
<div class="col-md-6">{$attack['attackername']} is corrupting your focus to {$resourcename[$attack['resource_id']]}.</div>
EOFORM;
			break;
			case "brutal":
	echo <<<EOFORM
<div class="col-md-6">{$attack['attackername']} is brutalizing you for 1 point of production.</div>
EOFORM;
			break;
			case "despair":
	echo <<<EOFORM
<div class="col-md-6">{$attack['attackername']} is despairing you for 100 points of satisfaction.</div>
EOFORM;
			break;
            case "robbery":
            echo <<<EOFORM
<div class="col-md-6">{$attack['attackername']} is robbing you of a resource.</div>
EOFORM;
			break;
			default:
			break;
		}
	$addtime = $attack['ticks'] * 10800;
	$arrivaldate = date("g A M j", $usetime + $addtime);
		echo <<<EOFORM
<div class="col-md-2">{$arrivaldate}</div>
<form name="repelform" action="incoming.php" method="post">
<input type="hidden" name="attack_id" value="{$attack['attack_id']}"/>
<input type="hidden" name="token_incoming" value="{$token}"/>
<div class="col-md-1">
<input type="submit" name="nullify" value="Nullify" class="btn btn-success"/>
</div>
</form>
<form name="repelform" action="incoming.php" method="post">
<input type="hidden" name="attack_id" value="{$attack['attack_id']}"/>
<input type="hidden" name="token_incoming" value="{$token}"/>
<div class="col-md-3">
<div class="input-group">
<span class="input-group-btn">
<input type="submit" name="redirect" value="Redirect to:" class="btn btn-danger"/>
</span>
<input name="target" placeholder="Target" class="form-control" type="text"/>
</div></div>
</form>
</div></td></tr>
EOFORM;
	}
	echo <<<EOFORM
</table>
EOFORM;
} else {
    echo <<<EOFORM
	<center>No one is attacking you.</center>
EOFORM;
}
?>