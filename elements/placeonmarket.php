<?php
include("backend/backend_placeonmarket.php");
needsalliance();
$extratitle = "Place on Market - ";
include("header.php");
$token = $_SESSION["token_placeonmarket"];
if ($timesremaining > 0) {
    $timesexplanation = "You may place items {$timesremaining} more Times on the market.";
} else {
    $timesexplanation = "You may not place any more items on the market.";
}
echo <<<EOFORM
<center>You currently have items {$totaltimes} Times on the market. {$timesexplanation}</center>
<center>Placing anything on the market costs {$constants['plentynecessary']} Plenty per Times; you can recover this by removing your items if they do not sell.</center>
<form action="placeonmarket.php" method="post" class="form-inline" role="form">
<input type="hidden" name="token_placeonmarket" value="{$token}"/>
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
<div class="panel-heading">Requesting</div>
<div class="row input-group">
<div class="col-sm-4">
<input name="requestedamount" placeholder="Amount" value="" class="form-control"></div>
<div class="col-sm-8"><select name="requestedresource_id" class="form-control" style="max-width:250px">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select>
</div></div></div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Times</div>
<input name="multiplier" placeholder="Amount" value="" class="form-control"/>
</div></div></div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Fraud</div>
<div class="row input-group">
<div class="col-sm-4">
<input name="fraud" value="0" type="radio" checked/> Don't fake your offered items and their amount<br/>
<input name="fraud" value="1" type="radio"/> Commit fraud (Cost: {$constants['fraudnecessary']} Fraud per Times)
</div>
<div class="col-sm-8">
<select name="apparentresource_id" class="form-control" style="max-width:250px">
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
<input name="libel" value="1" type="radio"/> Anonymous (Cost: {$constants['witnecessary']} Wit per Times)<br/>
<input name="libel" value="2" type="radio"/> Fake identity (Cost: {$constants['libelnecessary']} Libel per Times)
</div>
<div class="col-sm-6">
<input name="username" placeholder="Name" value="" class="form-control"/>
</div></div></div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Priority</div>
Cost: {$constants['delightnecessary']} Delight per Times per Priority
<input name="priority" placeholder="Priority" value="" class="form-control"/>
</div></div></div></div>
<center><input type="submit" name="place" value="Place on Market" class="btn btn-success"/></center>
</form>
EOFORM;
?>