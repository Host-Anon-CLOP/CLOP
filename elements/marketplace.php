<?php
include("backend/backend_marketplace.php");
needsalliance();
$extratitle = "Marketplace - ";
include("header.php");
$token = $_SESSION["token_marketplace"];
if ($_POST['buyingsearch'] || $_POST['sellingsearch'] || $_POST['everythingsearch']) {
	$returningfields=<<<EOFORM
<input type="hidden" name="resource_id" value="{$mysql['resource_id']}"/>
EOFORM;
	if ($_POST['buyingsearch']) {
		$returningfields.=<<<EOFORM
<input type="hidden" name="buyingsearch" value="1"/>
EOFORM;
	} else if ($_POST['sellingsearch']) {
		$returningfields.=<<<EOFORM
<input type="hidden" name="sellingsearch" value="1"/>
EOFORM;
	} else if ($_POST['everythingsearch']) {
        $returningfields.=<<<EOFORM
<input type="hidden" name="everythingsearch" value="1"/>
EOFORM;
    }
}
$elementslist = elementsdropdown(true, true);
echo <<<EOFORM
<div class="row">
<div class="col-md-4"></div>
<div class="col-md-4">
<div class="panel panel-default">
  <div class="panel-heading">Costs</div>
  <div class="row">
  <div class="col-md-6">Inspect</div>
  <div class="col-md-6">{$constants['truthnecessary']} Truth per Times</div>
  </div>
  <div class="row">
  <div class="col-md-6">Expose</div>
  <div class="col-md-6">{$constants['trustnecessary']} Trust per Times</div>
  </div>
  <div class="row">
  <div class="col-md-6">Loot</div>
  <div class="col-md-6">{$constants['lootingformarketplace']} Looting per Times</div>
  </div>
  <div class="row">
  <div class="col-md-6">Block Inspection</div>
  <div class="col-md-6">{$constants['liesabsorbed']} Lies per Times</div>
  </div>
  <div class="row">
  <div class="col-md-6">Block Looting</div>
  <div class="col-md-6">{$constants['securityformarketplace']} Security per Times</div>
  </div>
</div>
</div>
<div class="col-md-4"></div>
</div>
<center>
  <form action="marketplace.php" method="post" class="form-inline" role="form">
    <input type="hidden" name="token_marketplace" value="{$token}"/>
	<input type="hidden" name="buyingsearch" value="1"/>
    <p>
    I want to buy <select name="resource_id" class="form-control" style="width:210px;" onchange="this.form.submit()">
EOFORM;
echo $elementslist;
echo <<<EOFORM
	</select>
    </p>
  </form>
  <form action="marketplace.php" method="post" class="form-inline" role="form">
    <input type="hidden" name="token_marketplace" value="{$token}"/>
	<input type="hidden" name="sellingsearch" value="1"/>
    <p>
    I want to sell <select name="resource_id" class="form-control" style="width:210px;" onchange="this.form.submit()">
EOFORM;
echo $elementslist;
echo <<<EOFORM
	</select>
    </p>
  </form>
  <form action="marketplace.php" method="post" class="form-inline" role="form">
    <input type="hidden" name="token_marketplace" value="{$token}"/>
    <input type="submit" name="everythingsearch" value="Search Everything" class="btn btn-info"/>
  </form>
</center>
EOFORM;
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
        if (!$offer['apparentuser_id']) {
            $apparentuser =<<<EOFORM
<span class="text-warning">Anonymous</span>
EOFORM;
        } else if (($offer['alliance_id'] == $userinfo['alliance_id']) && $userinfo['alliance_id']) {
            $apparentuser =<<<EOFORM
<a href="viewuser.php?user_id={$offer['apparentuser_id']}"><span class="text-success">{$offer['apparentusername']}</span></a>
EOFORM;
        } else {
            $apparentuser =<<<EOFORM
<a href="viewuser.php?user_id={$offer['apparentuser_id']}">{$offer['apparentusername']}</a>
EOFORM;
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
EOFORM;
	if ($offer['user_id'] != $_SESSION['user_id']) {
		echo <<<EOFORM
	<div class="col-md-2">
<form action="marketplace.php" method="post">
{$returningfields}
<input type="hidden" name="token_marketplace" value="{$token}"/>
<input type="hidden" name="marketplace_id" value="{$offer['marketplace_id']}"/>
<div class="input-group">
<span class="input-group-btn">
<input type="submit" name="buy" value="Buy:" class="btn btn-success"/>
</span>
<input name="multiplier" value="1" class="form-control" type="text"/>
</div>
</form><br/>
<form action="marketplace.php" method="post">
{$returningfields}
<input type="hidden" name="token_marketplace" value="{$token}"/>
<input type="hidden" name="marketplace_id" value="{$offer['marketplace_id']}"/>
<div class="input-group">
<span class="input-group-btn">
<input type="submit" name="loot" value="Loot:" class="btn btn-danger"/>
</span>
<input name="multiplier" value="1" class="form-control" type="text"/>
</div>
</form>
    </div>
	<div class="col-md-1">
  <form action="marketplace.php" method="post">
  {$returningfields}
  <input type="hidden" name="token_marketplace" value="{$token}"/>
	<input type="hidden" name="marketplace_id" value="{$offer['marketplace_id']}"/>
        <input type="submit" name="inspect" value="Inspect" class="btn btn-primary"/><br/>
        <input type="submit" name="expose" value="Expose" class="btn btn-warning"/>
      </form>
    </div>
  </div>
EOFORM;
	} else {
	echo <<<EOFORM
    <div class="col-md-3">
	<form action="marketplace.php" method="post">
	{$returningfields}
	<input type="hidden" name="token_marketplace" value="{$token}"/>
	<input type="hidden" name="marketplace_id" value="{$offer['marketplace_id']}"/>
	<input type="submit" name="remove" value="Remove from Marketplace" class="btn btn-danger btn-sm btn-block"/>
  </form>
    </div>
  </div>
EOFORM;
	}
echo <<<EOFORM
</div>
EOFORM;
if ($offer['unmasked']) {
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
<div class="col-md-2"><a href="viewuser.php?user_id={$offer['user_id']}">{$actualuser}</a></div>
<div class="col-md-2">{$actualname}</div>
<div class="col-md-1">{$offer['offeredamount']}</div>
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
}
include("footer.php");
?>