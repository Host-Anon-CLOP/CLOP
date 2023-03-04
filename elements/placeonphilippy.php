<?php
include("backend/backend_placeonphilippy.php");
needsalliance();
$extratitle = "Place on Philippy - ";
include("header.php");
$token = $_SESSION["token_placeonphilippy"];
echo <<<EOFORM
<center>Offering anything costs 1 Philippy per {$constants['philippydivisor']} resources.</center>
<form action="placeonphilippy.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_placeonphilippy" value="{$token}"/>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Offering</div>
<div class="row input-group">
<div class="col-sm-4">
<input name="amount" placeholder="Amount" value="" class="form-control">
</div>
<div class="col-sm-8">
<select name="resource_id" class="form-control" style="max-width:250px">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select>
</div></div></div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Limits</div>
<div class="row input-group">
<div class="col-sm-6"><input name="maxpertick" placeholder="Max per tick" value="" class="form-control"></div>
<div class="col-sm-6">Highest tier:
<select name="maxtier" class="form-control" style="max-width:100px">
<option value=""></option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
</select>
</div></div></div></div></div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Bullshit</div>
<div class="row input-group">
<div class="col-sm-6">
<input name="bullshit" value="0" type="radio" checked/> Don't fake your offered items and their amount<br/>
<input name="bullshit" value="1" type="radio"/> Lie, and foist all the resources onto the first unsuspecting newbie (Cost: 1 Bullshit per {$constants['bullshitdivisor']} resources)
</div>
<div class="col-sm-6">
<select name="apparentresource_id" class="form-control" style="max-width:170px">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select><br/>
<input name="apparentamount" placeholder="Amount" value="" class="form-control"/>
</div></div>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Identity</div>
<div class="row input-group">
<div class="col-sm-6">
<input name="libel" value="0" type="radio" checked/> Don't pretend to be someone else<br/>
<input name="libel" value="1" type="radio"/> Anonymous (Cost: 1 Wit per {$constants['witdivisor']} resources)<br/>
<input name="libel" value="2" type="radio"/> Fake identity (Cost: 1 Libel per {$constants['libeldivisor']} resources)
</div>
<div class="col-sm-6">
<input name="username" placeholder="Name" value="" class="form-control"/>
</div></div></div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Priority</div>
Cost: Priority times 1 Delight per {$constants['delightdivisor']} resources
<input name="priority" placeholder="Priority" value="" class="form-control"/>
</div></div></div></div>
<center><input type="submit" name="place" value="Offer for free" class="btn btn-success"/></center>
</form>
EOFORM;
?>