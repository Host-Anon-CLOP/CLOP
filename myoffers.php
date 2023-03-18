<?php
include("backend/backend_myoffers.php");
$extratitle = "My Offers";
include("header.php");
$token = $_SESSION["token_myoffers"];
$displayfunds = commas($nationinfo['funds']);
echo <<<EOFORM
<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
EOFORM;
$types = array('buyingitems' => $buyeritems, 'selleritems' => $selleritems, 'buyerarmor' => $buyerarmor,
'sellerarmor' => $sellerarmor, 'buyerweapons' => $buyerweapons, 'sellerweapons' => $sellerweapons);
foreach ($types as $key => $type) {
switch ($key) {
	case 'buyingitems':
	$buysell = "buy";
	$itemtype = "items";
	break;
    case 'selleritems':
	$buysell = "sell";
	$itemtype = "items";
    break;
    case 'buyerarmor':
	$buysell = "buy";
	$itemtype = "armor";
	break;
    case 'sellerarmor':
	$buysell = "sell";
	$itemtype = "armor";
	break;
	case 'buyerweapons':
	$buysell = "buy";
	$itemtype = "weapons";
	break;
	case 'sellerweapons':
	$buysell = "sell";
	$itemtype = "weapons";
	break;
}
if ($type) {
$capsbuysell = ucfirst($buysell);
$capsitemtype = ucfirst($itemtype);
echo <<<EOFORM
<center><h4>{$capsbuysell}ing {$capsitemtype}</h4>
<center>
<table class="table table-striped table-bordered">
<thead><tr><td><div class="row">
<div class="col-md-1">{$capsbuysell}ing for</div>
<div class="col-md-1">Amount</div>
<div class="col-md-5">Resource</div>
<div class="col-md-5">Actions</div>
</div></td></tr></thead><tbody>
EOFORM;
foreach ($type as $deal) {
	$display['price'] = commas($deal['price']);
	$display['amount'] = commas($deal['amount']);
    echo <<<EOFORM
  <tr><td><div class="row">
  <div class="col-md-1"><p class="text-danger">{$display['price']}</p></div>
  <div class="col-md-1"><p class="text-success">{$display['amount']}</p></div>
  <div class="col-md-5"><p>{$deal['name']}</p></div>
  <div class="col-md-5"><form action="myoffers.php" method="post">
  <input type="hidden" name="token_myoffers" value="{$token}"/>
  <input type="hidden" name="resource_id" value="{$deal['resource_id']}"/>
  <input type="hidden" name="price" value="{$deal['price']}"/>
  <div class="row">
  <div class="col-xs-6">
  <input type="submit" name="remove{$buysell}{$itemtype}" value="Remove from Marketplace" class="btn btn-danger btn-sm btn-block"/>
  </div>
  </div>
</form></div>
</div></td></tr>
EOFORM;
}
echo <<<EOFORM
</tbody></table></center>
EOFORM;
} else {
echo <<<EOFORM
<center>You have no offers to {$buysell} {$itemtype}.</center>
EOFORM;
}
}
include("footer.php");
?>