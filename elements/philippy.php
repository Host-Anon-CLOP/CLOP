<?php
include("backend/backend_philippy.php");
needsalliance();
$extratitle = "Philippy - ";
include("header.php");
$token = $_SESSION["token_philippy"];
if ($_POST['search'] || $_POST['everythingsearch']) {
	$returningfields=<<<EOFORM
<input type="hidden" name="resource_id" value="{$mysql['resource_id']}"/>
EOFORM;
	if ($_POST['search']) {
		$returningfields.=<<<EOFORM
<input type="hidden" name="buyingsearch" value="1"/>
EOFORM;
	} else if ($_POST['everythingsearch']) {
        $returningfields.=<<<EOFORM
<input type="hidden" name="everythingsearch" value="1"/>
EOFORM;
    }
}
$elementslist = elementsdropdown(true, true);
echo <<<EOFORM
<center>It currently costs 1 Truth per {$constants['truthdivisor']} items to inspect and 1 Trust per {$constants['trustdivisor']} items to expose to the public.</center>
<center>
  <form action="philippy.php" method="post" class="form-inline" role="form">
    <input type="hidden" name="token_philippy" value="{$token}"/>
	<input type="hidden" name="search" value="1"/>
    <p>
    I want to receive <select name="resource_id" class="form-control" style="width:210px;" onchange="this.form.submit()">
EOFORM;
echo $elementslist;
echo <<<EOFORM
	</select>
    </p>
  </form>
  <form action="philippy.php" method="post" class="form-inline" role="form">
    <input type="hidden" name="token_philippy" value="{$token}"/>
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
	<div class="col-md-3">Max Items per Player per Tick</div>
	<div class="col-md-1">Max Tier</div>
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
	if ($offer['maxpertick']) $showmax = $offer['maxpertick'];
	else $showmax = "Unlimited";
	echo <<<EOFORM
<tr><td><div class="row">
<div class="col-md-2">{$apparentuser}</div>
<div class="col-md-2">{$apparentname}</div>
<div class="col-md-1">{$offer['apparentamount']}</div>
<div class="col-md-3">{$showmax}</div>
<div class="col-md-1">{$offer['maxtier']}</div>
EOFORM;
	if ($offer['user_id'] != $_SESSION['user_id']) {
		echo <<<EOFORM
	<div class="col-md-2">
<form action="philippy.php" method="post">
{$returningfields}
<input type="hidden" name="token_philippy" value="{$token}"/>
<input type="hidden" name="philippy_id" value="{$offer['philippy_id']}"/>
<div class="input-group">
<span class="input-group-btn">
<input type="submit" name="receive" value="Receive:" class="btn btn-success"/>
</span>
<input name="amount" placeholder="Amount" class="form-control" type="text"/>
</div>
</form>
    </div>
	<div class="col-md-1">
  <form action="philippy.php" method="post">
  {$returningfields}
  <input type="hidden" name="token_philippy" value="{$token}"/>
	<input type="hidden" name="philippy_id" value="{$offer['philippy_id']}"/>
        <input type="submit" name="inspect" value="Inspect" class="btn btn-primary"/><br/>
        <input type="submit" name="expose" value="Expose" class="btn btn-warning"/>
      </form>
    </div>
  </div>
EOFORM;
	} else {
	echo <<<EOFORM
    <div class="col-md-3">
	<form action="philippy.php" method="post">
	{$returningfields}
	<input type="hidden" name="token_philippy" value="{$token}"/>
	<input type="hidden" name="philippy_id" value="{$offer['philippy_id']}"/>
	<input type="submit" name="remove" value="Remove from Philippy" class="btn btn-danger btn-sm btn-block"/>
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
if ($offer['bullshit']) {
	$bullshitwarning =<<<EOFORM
<span class="text-danger">Bullshit!</span>
EOFORM;
} else {
	$bullshitwarning =<<<EOFORM
<span class="text-success">Not Bullshit</span>
EOFORM;
}
echo <<<EOFORM
<div class="row">
<div class="col-md-2"><a href="viewuser.php?user_id={$offer['user_id']}">{$actualuser}</a></div>
<div class="col-md-2">{$actualname}</div>
<div class="col-md-1">{$offer['offeredamount']}</div>
<div class="col-md-3">{$bullshitwarning}</div>
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