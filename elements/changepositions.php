<?php
include("backend/backend_changepositions.php");
$extratitle = "Change Positions - ";
include("header.php");
$token = $_SESSION["token_changepositions"];
echo <<<EOFORM
<center>The cost to change the positions of two elements is 10,000 Harmony. It will take 24 hours for the change to take effect.</center>
<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Current Element Positions</div>
<div class="panel-body" style="text-align: center;">
<img src="images/icons/{$positions[1]}.png"/><br/>
{$production[6]}<img src="images/icons/{$positions[6]}.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/{$positions[2]}.png"/><br/>
{$production[5]}<img src="images/icons/{$positions[5]}.png"/>&nbsp;&nbsp;&nbsp;<img src="images/icons/{$positions[3]}.png"/><br/>
<img src="images/icons/{$positions[4]}.png"/><br/>
</div></div></div>
<div class="col-md-4"></div></div>
<center>
<form action="changepositions.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_changepositions" value="{$token}"/>
Swap the positions of <select name="changeposition1">
<option value=""></option>
EOFORM;
foreach ($validelements as $number => $name) {
	echo <<<EOFORM
<option value="{$number}">{$name}</option>
EOFORM;
	}
echo <<<EOFORM
</select> and <select name="changeposition2">
<option value=""></option>
EOFORM;
foreach ($validelements as $number => $name) {
	echo <<<EOFORM
<option value="{$number}">{$name}</option>
EOFORM;
	}
echo <<<EOFORM
</select><input type="submit" name="changepositions" value="Swap" class="btn btn-sm btn-danger" onclick="return confirm('Really swap these two positions?')"/></form></center>
EOFORM;
?>