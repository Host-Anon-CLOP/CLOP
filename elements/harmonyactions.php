<?php
include("backend/backend_harmonyactions.php");
$extratitle = "Harmony Actions - ";
include("header.php");
$token = $_SESSION["token_harmonyactions"];
echo <<<EOFORM
<center>Raising a value multiplies it by 5/4 (rounded up), and lowering a value multiplies it by 4/5 (rounded down). The cost to do either is 6000 Harmony.</center>
EOFORM;
foreach ($groupnames as $intername => $extername) {
    echo <<<EOFORM
<center><h3>{$extername}</h3></center>
<table class="table table-striped">
<thead><tr><td>Name</td><td>Description</td><td>Value</td><td>Actions</td></tr></thead>
EOFORM;
    foreach ($constantgroups[$intername] as $constant) {
	echo <<<EOFORM
<tr><td>{$constant['friendlyname']}</td><td>{$constant['description']}</td><td>{$constants[$constant['name']]['value']}</td>
<td>
<form name="raiselower{$constant['name']}" method="post" class="form-inline" role="form">
<input type="hidden" name="token_harmonyactions" value="{$_SESSION['token_harmonyactions']}"/>
<input type="hidden" name="name" value="{$constant['name']}"/>
<input type="submit" name="loweramount" value="Lower" class="btn btn-sm btn-success" onclick="return confirm('Really lower the cost of {$constant['friendlyname']}?')"/>
<input type="submit" name="raiseamount" value="Raise" class="btn btn-sm btn-warning" onclick="return confirm('Really raise the cost of {$constant['friendlyname']}?')"/>
</form>
</td>
</tr>
EOFORM;
	}
	echo <<<EOFORM
</table>
EOFORM;
}
include("footer.php");
?>