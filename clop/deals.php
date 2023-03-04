<?php
include("backend/backend_deals.php");
$extratitle = "Deals - ";
include("header.php");
if ($nationinfo['economy'] == "Free Market") {
	echo <<<EOFORM
<center>Your economy type is unable to make deals.</center>
EOFORM;
} else if ($nationinfo['government'] == "Oppression") {
echo <<<EOFORM
<center>You cannot deal with nations outside of your empire.</center>
EOFORM;
} else {
if ($nationinfo['economy'] == "State Controlled" && $nationinfo['active_economy']) {
echo <<<EOFORM
<center>Make a deal with the nation of
<form action="deals.php" method="post">
<input type="hidden" name="token_deals" value="{$_SESSION['token_deals']}"/>
<input type="text" class="form-control" name="nationname" style="width:200px;"/>
<input type="submit" name="makedeal" value="Make Deal" class="btn btn-success btn-primary"/>
</form>
EOFORM;
if (!empty($outgoingdeals)) {
foreach ($outgoingdeals as $deal) {
    if ($deal['amount']) {
        $money = commas($deal['amount']);
        if (!$deal['askingformoney']) {
            $moneyline = "You offer {$money} bits for this deal";
        } else {
            $moneyline = "You request {$money} bits for this deal";
        }
    } else {
	    $moneyline = "You do not involve money in this deal";
	}
    echo <<<EOFORM
    <center><h4><a href="viewnation.php?nation_id={$deal['nation_id']}"/>{$deal['name']}</a></h4></center>
    <center>{$moneyline}</center>
EOFORM;
    if (!empty($offeritems[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Offered Items</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($offeritems[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
		echo "</table></center>";
    }
    if (!empty($offerweapons[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Offered Weapons</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($offerweapons[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
		echo "</table></center>";
    }
    if (!empty($offerarmor[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Offered Armor</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($offerarmor[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
		echo "</table></center>";
    }
	if (!empty($askitems[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Requested Items</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($askitems[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
	echo "</table></center>";
    }
    if (!empty($askweapons[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Requested Weapons</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($askweapons[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
	echo "</table></center>";
    }
    if (!empty($askarmor[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Requested Armor</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($askarmor[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
	echo "</table></center>";
    }
	echo <<<EOFORM
	<form action="deals.php" method="post">
    <input type="hidden" name="token_deals" value="{$_SESSION['token_deals']}"/>
	<input type="hidden" name="deal_id" value="{$deal['deal_id']}"/>
EOFORM;
	if (!$deal['finalized']) {
	echo <<<EOFORM
	<center><a href="makedeal.php?deal_id={$deal['deal_id']}">Edit Deal</a></center>
EOFORM;
	} else {
	echo <<<EOFORM
	<center>This deal is finalized and sent; you cannot edit it.</center>
EOFORM;
	}
	echo <<<EOFORM
	<input type="submit" name="canceldeal" value="Cancel Deal" class="btn btn-danger btn-primary"/>
	</form>
EOFORM;
}
} else {
echo <<<EOFORM
<center>You have no outgoing deal requests.</center>
EOFORM;
}
}

if (!empty($deals)) {
foreach ($deals as $deal) {
    if ($deal['amount']) {
        $money = commas($deal['amount']);
        if (!$deal['askingformoney']) {
            $moneyline = "offers {$money} bits for this deal";
        } else {
            $moneyline = "requests {$money} bits from you for this deal";
        }
    } else {
	    $moneyline = "does not involve money in this deal";
	}
    echo <<<EOFORM
    <center><h4><a href="viewnation.php?nation_id={$deal['fromnation']}"/>{$deal['name']}</a></h4></center>
    <center>{$moneyline}</center>
EOFORM;
    if (!empty($offeritems[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Offered Items</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($offeritems[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
		echo "</table></center>";
    }
    if (!empty($offerweapons[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Offered Weapons</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($offerweapons[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
		echo "</table></center>";
    }
    if (!empty($offerarmor[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Offered Armor</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($offerarmor[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
		echo "</table></center>";
    }
	if (!empty($askitems[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Requested Items</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($askitems[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
	echo "</table></center>";
    }
    if (!empty($askweapons[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Requested Items</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($askweapons[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
	echo "</table></center>";
    }
    if (!empty($askarmor[$deal['deal_id']])) {
    echo <<<EOFORM
    <center><h4>Requested Items</h4></center>
    <center><table class="table table-striped table-bordered" style="width:400px;">
EOFORM;
        foreach ($askarmor[$deal['deal_id']] as $item) {
		$itemamount = commas($item['amount']);
           echo <<<EOFORM
<tr><td>{$item['name']}</td><td>{$itemamount}</td></tr>
EOFORM;
		}
	echo "</table></center>";
    }
	echo <<<EOFORM
	<form action="deals.php" method="post">
    <input type="hidden" name="token_deals" value="{$_SESSION['token_deals']}"/>
	<input type="hidden" name="deal_id" value="{$deal['deal_id']}"/>
	<center><input type="submit" name="acceptdeal" value="Accept Deal" class="btn btn-success btn-primary"/>
	<input type="submit" name="rejectdeal" value="Reject Deal" class="btn btn-danger btn-primary"/></center>
	</form>
EOFORM;
}
} else {
echo <<<EOFORM
<center>You have no incoming deal requests.</center>
EOFORM;
}
}
include("footer.php");
?>