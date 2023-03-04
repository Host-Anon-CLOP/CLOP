<?php
include("backend/backend_outgoing.php");
$extratitle = "Outgoing Attacks - ";
include("header.php");
$token = $_SESSION["token_outgoing"];
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
<div class="col-md-4"></div>
<div class="col-md-4">
	<div class="panel panel-default">
      <div class="panel-heading">Cancellation Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['compassionforbrutal']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['compassionforburden']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['compassionforcorrupt']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['compassionfordespair']} Compassion</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['compassionforrobbery']} Compassion</div>
	  </div>
    </div>
	</div>
<div class="col-md-4"></div>
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
<div class="col-md-6">Burdening {$attack['defendername']} with {$attack['amount']} {$resourcename[$attack['resource_id']]}.</div>
EOFORM;
			break;
			case "corrupt":
	echo <<<EOFORM
<div class="col-md-6">Corrupting {$attack['defendername']}'s focus to {$resourcename[$attack['resource_id']]}.</div>
EOFORM;
			break;
			case "brutal":
	echo <<<EOFORM
<div class="col-md-6">Brutalizing {$attack['defendername']} for 1 point of production.</div>
EOFORM;
			break;
			case "despair":
	echo <<<EOFORM
<div class="col-md-6">Despairing {$attack['defendername']} for 200 points of satisfaction.</div>
EOFORM;
			break;
            case "robbery":
	echo <<<EOFORM
<div class="col-md-6">Robbing {$attack['defendername']} of {$resourcename[$attack['resource_id']]}.</div>
EOFORM;
			break;
			default:
			break;
		}
		$addtime = $attack['ticks'] * 10800;
        $arrivaldate = date("g A M j", $usetime + $addtime);
		echo <<<EOFORM
<div class="col-md-2">{$arrivaldate}</div>
<div class="col-md-4">
<form name="cancelform" action="outgoing.php" method="post">
<input type="hidden" name="attack_id" value="{$attack['attack_id']}"/>
<input type="hidden" name="token_outgoing" value="{$token}"/>
<input type="submit" name="cancel" value="Cancel" class="btn btn-danger" onclick="return confirm('Really cancel this attack?')"/>
</form>
</div></td></tr>
EOFORM;
	}
	echo <<<EOFORM
</table>
EOFORM;
} else {
    echo <<<EOFORM
	<center>You are not attacking anyone.</center>
EOFORM;
}
include("footer.php");
?>