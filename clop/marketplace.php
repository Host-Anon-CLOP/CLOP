<?php
include("backend/backend_marketplace.php");
if ($mode == "weapons") {
	$extratitle = "Weapons Marketplace - ";
} else if ($mode == "armor") {
	$extratitle = "Armor Marketplace - ";
} else {
	$extratitle = "Marketplace - ";
}
include("header.php");
$token = $_SESSION["token_{$marketplace}"];
echo <<<EOFORM
<div class="alert alert-info">Due to your economic type, you will pay {$displaybuyingmultiplier}% more than these listed prices
and receive {$displaysellingmultiplier}% less than your listed price when selling.</div>
<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
<center>
  <form action="marketplace.php" method="post" class="form-inline" role="form">
    <input type="hidden" name="token_{$marketplace}" value="{$token}"/>
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
      Place
      <input name="amount" class="form-control" placeholder="Qty" style="width:100px;"/>
      of the selected item on the market for
      <input name="price" class="form-control" placeholder="Bits" style="width:100px;"/>
      <input type="submit" name="action" value="Place on Market" class="btn btn-success"/>
    </p>
  </form>
</center>
EOFORM;
if ($deals) {
echo <<<EOFORM
<center>
<table class="table table-striped table-bordered">
<thead><tr><td><div class="row">
  <div class="col-md-1">Unit Price</div>
  <div class="col-md-1">Units Available</div>
  <div class="col-md-5">Seller</div>
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
  <form action="marketplace.php" method="post">
  <input type="hidden" name="token_{$marketplace}" value="{$token}"/>
  <input type="hidden" name="mode" value="{$mode}"/>
  <input type="hidden" name="resource_id" value="{$deal['resource_id']}"/>
  <input type="hidden" name="buyingfrom_id" value="{$deal['nation_id']}"/>
  <input type="hidden" name="price" value="{$deal['price']}"/>
      <div class="btn-group btn-block">
        <input type="submit" name="action" value="Buy One" class="btn btn-primary" style="width:50%"/>
        <input type="submit" name="action" value="Buy All" class="btn btn-warning" style="width:50%"/>
      </div>
      </form>
    </div>
    <div class="col-xs-6">
   <form action="marketplace.php" method="post">
  <input type="hidden" name="token_{$marketplace}" value="{$token}"/>
  <input type="hidden" name="mode" value="{$mode}"/>
  <input type="hidden" name="resource_id" value="{$deal['resource_id']}"/>
  <input type="hidden" name="buyingfrom_id" value="{$deal['nation_id']}"/>
  <input type="hidden" name="price" value="{$deal['price']}"/>
      <div class="input-group">
          <span class="input-group-btn">
  <input type="submit" name="action" value="Buy:" class="btn btn-success"/>
           </span>
  <input name="buyingamount" value="1" class="form-control" type="text"/>
      </div>
  </form>
    </div>
  </div>

EOFORM;
        } else {
            echo <<<EOFORM
  <div class="row">
    <div class="col-xs-6">
      <form action="marketplace.php" method="post">
  <input type="hidden" name="token_{$marketplace}" value="{$token}"/>
  <input type="hidden" name="mode" value="{$mode}"/>
  <input type="hidden" name="resource_id" value="{$deal['resource_id']}"/>
  <input type="hidden" name="buyingfrom_id" value="{$deal['nation_id']}"/>
  <input type="hidden" name="price" value="{$deal['price']}"/>
      <input type="submit" name="action" value="Remove from Marketplace" class="btn btn-danger btn-sm btn-block"/>
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
<div class="alert alert-warning">That item is not on the market.</div>
EOFORM;
}
include("footer.php");
?>