<?php
include("backend/backend_deals.php");
$extratitle = "Deals - ";
include("header.php");
echo <<<EOFORM
<center>Make a deal with
<form action="deals.php" method="post">
<input type="hidden" name="token_deals" value="{$_SESSION['token_deals']}"/>
<input type="text" class="form-control" name="username" style="width:200px;"/>
<input type="submit" name="makedeal" value="Make Deal" class="btn btn-success btn-primary"/>
</form>
EOFORM;
if (!empty($outgoingdeals)) {
foreach ($outgoingdeals as $deal) {
    echo <<<EOFORM
    <center><h4><a href="viewuser.php?user_id={$deal['user_id']}"/>{$deal['username']}</a></h4></center>
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

if (!empty($deals)) {
foreach ($deals as $deal) {
    echo <<<EOFORM
    <center><h4><a href="viewuser.php?user_id={$deal['fromuser']}"/>{$deal['username']}</a></h4></center>
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
include("footer.php");
?>