<?php
include("backend/backend_buyermarketplace.php");
if ($mode == "weapons") {
	$extratitle = "Weapons Buyer's Marketplace - ";
} else if ($mode == "armor") {
	$extratitle = "Armor Buyer's Marketplace - ";
} else {
	$extratitle = "Buyer's Marketplace - ";
}
include("header.php");
$token = $_SESSION["token_{$buyermarketplace}"];
echo <<<EOFORM
<div class="alert alert-info">Due to your economic type, you will pay {$displaybuyingmultiplier}% extra when offering to buy these items
and receive {$displaysellingmultiplier}% less than their listed price when selling.</div>
<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
<center>
  <form action="buyermarketplace.php" method="post" class="form-inline" role="form">
    <input type="hidden" name="token_{$buyermarketplace}" value="{$token}"/>
    <input type="hidden" name="mode" value="{$mode}"/>
    <p>
      <select name="resource_id" class="form-control" style="width:210px;" onchange="this.form.submit()">
      <option value=""></option>
EOFORM;

foreach($resourceoptions as $option) {
    echo <<<EOFORM
        <option value="{$option['resource_id']}"
EOFORM;
    if ($option['resource_id'] == $mysql['resource_id']) {
        echo " selected ";
    }
    echo <<<EOFORM
>{$option['optionslistname']}</option>
EOFORM;
//there's also now an $option['name'] which is just the name by itself, not the extra "Have" if any
}
echo <<<EOFORM
      </select>
    </p>
    <p>
      Offer to buy
      <input name="amount" class="form-control" placeholder="Qty" style="width:100px;"/>
      of the selected item for
      <input name="price" class="form-control" placeholder="Bits" style="width:100px;"/>
      <input type="submit" name="offer" value="Offer to Buy" class="btn btn-success"/>
    </p>
  </form>
</center>
EOFORM;
if ($deals) {
echo <<<EOFORM
<center>
<table class="table table-striped table-bordered">
<thead><tr><td><div class="row">
  <div class="col-md-1">Offering</div>
  <div class="col-md-1">Amount</div>
  <div class="col-md-5">Buyer</div>
  <div class="col-md-5">Actions</div>
</div></td></tr></thead><tbody>
EOFORM;
    foreach ($deals as $deal) {
        if (($deal['alliance_id'] == $nationinfo['alliance_id']) && $nationinfo['alliance_id']) {
            $displayname =<<<EOFORM
<span class="text-success">{$deal['name']}</span>
EOFORM;
        } else {
            $displayname = $deal['name'];
        }
        $display['price'] = commas($deal['price']);
        echo <<<EOFORM
<tr><td><div class="row">
  <div class="col-md-1"><p class="text-danger">{$display['price']}</p></div>
  <div class="col-md-1"><p class="text-success">{$deal['amount']}</p></div>
  <div class="col-md-5"><p><a href="viewnation.php?nation_id={$deal['nation_id']}">{$displayname}</a></p></div>
  <div class="col-md-5">
EOFORM;
        if ($deal['nation_id'] != $_SESSION['nation_id']) {
            echo <<<EOFORM
  <div class="row">
    <div class="col-xs-6">
<form action="buyermarketplace.php" method="post">
<input type="hidden" name="token_{$buyermarketplace}" value="{$token}"/>
<input type="hidden" name="mode" value="{$mode}"/>
<input type="hidden" name="resource_id" value="{$deal['resource_id']}"/>
<input type="hidden" name="sellingto_id" value="{$deal['nation_id']}"/>
<input type="hidden" name="price" value="{$deal['price']}"/>
<div class="btn-group btn-block">
<input type="submit" name="sellone" value="Sell One" class="btn btn-primary" style="width:50%"/>
<input type="submit" name="sellall" value="Sell All" class="btn btn-warning" style="width:50%"/>
</div>
</form>
</div>
<div class="col-xs-6">
<form action="buyermarketplace.php" method="post">
<input type="hidden" name="token_{$buyermarketplace}" value="{$token}"/>
<input type="hidden" name="mode" value="{$mode}"/>
<input type="hidden" name="resource_id" value="{$deal['resource_id']}"/>
<input type="hidden" name="sellingto_id" value="{$deal['nation_id']}"/>
<input type="hidden" name="price" value="{$deal['price']}"/>
<div class="input-group">
<span class="input-group-btn">
<input type="submit" name="sellamount" value="Sell:" class="btn btn-success"/>
</span>
<input name="sellingamount" value="1" class="form-control" type="text"/>
</div>
</form>
</div>
</div>
EOFORM;
        } else {
            echo <<<EOFORM
  <div class="row">
    <div class="col-xs-6">
<form action="buyermarketplace.php" method="post">
  <input type="hidden" name="token_{$buyermarketplace}" value="{$token}"/>
  <input type="hidden" name="mode" value="{$mode}"/>
  <input type="hidden" name="resource_id" value="{$deal['resource_id']}"/>
  <input type="hidden" name="sellingto_id" value="{$deal['nation_id']}"/>
  <input type="hidden" name="price" value="{$deal['price']}"/>
  <input type="submit" name="remove" value="Remove from Marketplace" class="btn btn-danger btn-sm btn-block"/>
  </form>
    </div>
    <div class="col-xs-6">
      
    </div>
  </div>
EOFORM;
        }
        echo <<<EOFORM
</div>
</div></td></tr>
EOFORM;
    }
echo <<<EOFORM
</tbody></table>
EOFORM;
} else if ($_POST['resource_id'] && empty($errors)) {
echo <<<EOFORM
<div class="alert alert-warning">Nobody wants to buy that item.</div>
EOFORM;
}
include("footer.php");
?>