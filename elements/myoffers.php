<?php
include("backend/backend_myoffers.php");
needsalliance();
$extratitle = "My Offers - ";
include("header.php");
$token = $_SESSION["token_myoffers"];
if ($offers) {
echo <<<EOFORM
<center>
<table class="table table-striped table-bordered">
<thead><tr><td><div class="row">
	<div class="col-md-2">Offering Player</div>
	<div class="col-md-2">Offered Item</div>
	<div class="col-md-1">Offered Amount</div>
	<div class="col-md-2">Requested Item</div>
	<div class="col-md-1">Requested Amount</div>
	<div class="col-md-1">Times</div>
	<div class="col-md-3">Actions</div>
</div></td></tr></thead><tbody>
EOFORM;
    foreach ($offers as $offer) {
        if (($offer['alliance_id'] == $userinfo['alliance_id']) && $userinfo['alliance_id']) {
            $apparentuser =<<<EOFORM
<span class="text-success">{$offer['apparentusername']}</span>
EOFORM;
        } else {
            $apparentuser = $offer['apparentusername'];
        }
		$apparentname = $offer['apparentname'];
		$requestedname = $offer['requestedname'];
	echo <<<EOFORM
<tr><td><div class="row">
<div class="col-md-2">{$apparentuser}</div>
<div class="col-md-2">{$apparentname}</div>
<div class="col-md-1">{$offer['apparentamount']}</div>
<div class="col-md-2">{$requestedname}</div>
<div class="col-md-1">{$offer['requestedamount']}</div>
<div class="col-md-1">{$offer['multiplier']}</div>
<div class="col-md-3">
	<form action="myoffers.php" method="post">
	<input type="hidden" name="token_myoffers" value="{$token}"/>
	<input type="hidden" name="marketplace_id" value="{$offer['marketplace_id']}"/>
	<input type="submit" name="remove" value="Remove from Marketplace" class="btn btn-danger btn-sm btn-block"/>
  </form>
    </div>
  </div>
</div>
EOFORM;
	if (($offer['alliance_id'] == $userinfo['alliance_id']) && $userinfo['alliance_id']) {
            $displayname =<<<EOFORM
<span class="text-success">{$offer['username']}</span>
EOFORM;
        } else {
            $actualuser = $offer['username'];
        }
		$actualname = $offer['offeredname'];
echo <<<EOFORM
<div class="row">
<div class="col-md-2">{$actualuser}</div>
<div class="col-md-2">{$actualname}</div>
<div class="col-md-1">{$offer['offeredamount']}</div>
EOFORM;
if ($offer['unmasked']) {
echo <<<EOFORM
<div class="col-md-3">Unmasked by {$offer['unmasker']}</div>
EOFORM;
}
echo <<<EOFORM
</div></td></tr>
EOFORM;
}
echo <<<EOFORM
</tbody></table>
EOFORM;
} else {
    echo <<<EOFORM
<center>You have nothing on the marketplace.</center>
EOFORM;
}
?>