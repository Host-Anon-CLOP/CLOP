<?php
include("backend/backend_autocompounds.php");
$extratitle = "Autocompounds - ";
include("header.php");
$token = $_SESSION["token_autocompounds"];
if ($userinfo['tier'] < 2) {
echo <<<EOFORM
<center>At Tier 1, you can't really use this page yet.</center>
EOFORM;
}
echo <<<EOFORM
<center>This page determines which compounds are automatically created from Tier 1 elements at no extra cost every turn.</center>
<center>
<form name="newcompound" action="autocompounds.php" method="post">
<input type="hidden" name="token_autocompounds" value="{$token}"/>
<div class="input-group">
<select name="resource_id" class="form-control" style="width:210px;">
EOFORM;
echo elementsdropdown(true, false);
echo <<<EOFORM
</select>
<input name="amount" placeholder="Amount" value="" class="form-control" style="width:100px;">
<input type="submit" name="autocompound" value="Create Automatic Compound" class="btn btn-success"/>
</div>
</form></center>
EOFORM;
if ($compounds) {
echo <<<EOFORM
<table class="table">
<thead>
<tr>
EOFORM;
if (!$userinfo['hideicons']) {
echo <<<EOFORM
<td></td>
EOFORM;
}
echo <<<EOFORM
<td>Compound</td><td>Amount</td><td></td></tr>
EOFORM;
foreach ($compounds as $compoundarray) {
echo <<<EOFORM
<tr>
EOFORM;
if (!$userinfo['hideicons']) {
	echo <<<EOFORM
	<td style="width: 16px;"><img src="images/icons/{$compoundarray['resource_id']}.png"/></td>
EOFORM;
}
echo <<<EOFORM
<td>{$compoundarray['name']}</td>
<td>{$compoundarray['amount']}</td>
<td><form name="remove{$compoundarray['resource_id']}" action="autocompounds.php" method="post">
<input type="hidden" name="token_autocompounds" value="{$token}"/>
<input type="hidden" name="resource_id" value="{$compoundarray['resource_id']}"/>
<input type="submit" name="remove" value="Remove" class="btn btn-danger"/>
</form>
</td>
</tr>
EOFORM;
}
echo <<<EOFORM
</tbody></table>
EOFORM;
}
include("footer.php");
?>