<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$getpost = array_merge($_GET, $_POST);
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
if ($getpost['mode'] == "weapons") {
    $mode = "weapons";
    $buyermarketplace = "weaponsbuyermarketplace";
    $resourcesname = "weapons";
    $resourcedefs = "weapondefs";
    $resource_id = "weapon_id";
    $tradeable1 = "";
    $tradeable2 = "";
    $max = 100000000 + (int)($nationinfo['funds'] / 10);
} else if ($getpost['mode'] == "armor") {
    $mode = "armor";
    $buyermarketplace = "armorbuyermarketplace";
    $resourcesname = "armor";
    $resourcedefs = "armordefs";
    $resource_id = "armor_id";
    $tradeable1 = "";
    $tradeable2 = "";
    $max = 100000000 + (int)($nationinfo['funds'] / 10);
} else {
    $mode = "";
    $buyermarketplace = "buyermarketplace";
    $resourcesname = "resources";
    $resourcedefs = "resourcedefs";
    $resource_id = "resource_id";
    $tradeable1 = "AND rd.is_tradeable = 1";
    $tradeable2 = "WHERE rd.is_tradeable = 1";
    $max = 200000000 + (int)($nationinfo['funds'] / 10);
}
$resources = array();
$deals = array();
$embargoed = array();
$buyingmultiplier = getbuyingmultiplier($_SESSION['nation_id']);
$displaybuyingmultiplier = ($buyingmultiplier - 1) * 100;
$sellingmultiplier = getsellingmultiplier($_SESSION['nation_id']);
$displaysellingmultiplier = (1 - $sellingmultiplier) * 100;
if ($mysql['amount'] && $mysql['price'] && (!ctype_digit($mysql['amount']) || !ctype_digit($mysql['price']))) {
    $errors[] = "Digits only- no commas, periods, or other markers.";
}
$mysql['resource_id'] = (int)$mysql['resource_id'];
$mysql['amount'] = (int)$mysql['amount'];
$mysql['price'] = (int)$mysql['price'];
if ($_POST && (($_POST["token_{$buyermarketplace}"] == "") || ($_POST["token_{$buyermarketplace}"] != $_SESSION["token_{$buyermarketplace}"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_{$buyermarketplace}"] == "")) {
    $_SESSION["token_{$buyermarketplace}"] = sha1(rand() . $_SESSION["token_{$buyermarketplace}"]);
}
if (!$errors) {
if ($_POST['offer']) {
    if ($nationinfo['government'] == "Oppression") {
        $errors[] = "Your Oppressive government cannot buy nor sell.";
    }
    if ($mysql['price'] < 1000) {
		$errors[] = "Price must be at least 1000.";
	}
	if ($mysql['amount'] < 1) {
        $errors[] = "Amount must be above zero.";
    }
    $sql =<<<EOSQL
	SELECT SUM(price * amount) AS totalamount FROM {$buyermarketplace}
	WHERE nation_id = {$_SESSION['nation_id']}
EOSQL;
	$currenttotal = onelinequery($sql);
    $commasmax = commas($max);
    if (($mysql['price'] * $mysql['amount']) + $currenttotal['totalamount'] > $max) {
		if ($max > $currenttotal['totalamount']) {
			$remaining = commas($max - $currenttotal['totalamount']);
		} else {
			$remaining = "No more";
		}
		$errors[] = "You may only offer {$commasmax} bits total for all {$resourcesname} on the buyer's marketplace. ({$remaining} available)";
    }
	$cost = floor($mysql['price'] * $mysql['amount'] * $buyingmultiplier);
	$displaycost = commas($cost);
    if ($nationinfo['funds'] < $cost) {
        $errors[] = "You cannot afford to make that offer.";
    }
	$sql=<<<EOSQL
	SELECT name FROM {$resourcedefs} rd WHERE {$resource_id} = {$mysql['resource_id']} {$tradeable1}
EOSQL;
	$item = onelinequery($sql);
	if (!$item['name']) {
		$errors[] = "No item selected.";
	}
    if (empty($errors)) {
        $sql =<<<EOSQL
        INSERT INTO {$buyermarketplace} (nation_id, {$resource_id}, amount, price) VALUES ({$_SESSION['nation_id']}, {$mysql['resource_id']}, {$mysql['amount']}, {$mysql['price']})
        ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
		UPDATE nations SET funds = funds - {$cost} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$nationinfo['funds'] -= $cost;
        $infos[] = "You have requested to buy {$mysql['amount']} {$item['name']} for {$displaycost} bits.";
    }
}
if ($_POST) {
	$sql = "SELECT u.user_id FROM embargoes e INNER JOIN users u ON e.embargoee = u.user_id WHERE e.embargoer = '{$_SESSION['user_id']}'";
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
    while ($rs = mysqli_fetch_array($sth)) {
        $embargoed[$rs['user_id']] = $rs['user_id'];
    }
    }
    $sql = "SELECT u.user_id FROM embargoes e INNER JOIN users u ON e.embargoer = u.user_id WHERE e.embargoee = '{$_SESSION['user_id']}'";
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
    while ($rs = mysqli_fetch_array($sth)) {
        $embargoed[$rs['user_id']] = $rs['user_id'];
    }
    }
}
if ($_POST['remove']) {
    if ($mysql['sellingto_id'] != $_SESSION['nation_id']) {
        $errors[] = "C'mon, you really didn't think I'd check for THIS?";
    } else {
        $sql = "SELECT m.price, m.amount, rd.name FROM {$buyermarketplace} m INNER JOIN {$resourcedefs} rd ON m.{$resource_id} = rd.{$resource_id}
        WHERE m.nation_id = '{$mysql['sellingto_id']}' AND m.{$resource_id} = '{$mysql['resource_id']}' AND m.price = '{$mysql['price']}'";
        $rs = onelinequery($sql);
        if (!$rs) {
            $errors[] = "Too late, somepony sold it to you already.";
        } else {
            $sql = "DELETE FROM {$buyermarketplace} WHERE nation_id = '{$mysql['sellingto_id']}' AND {$resource_id} = '{$mysql['resource_id']}' AND price = '{$mysql['price']}'";
            $GLOBALS['mysqli']->query($sql);
            $returnfunds = floor(getbuyingmultiplier($_SESSION['nation_id']) * $rs['price'] * $rs['amount']);
			$nationinfo['funds'] += $returnfunds;
            $sql=<<<EOSQL
			UPDATE nations SET funds = funds + {$returnfunds} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $infos[] = "You have removed your request of {$rs['amount']} {$rs['name']} from the market.";
        }
    }
}
if ($_POST['sellone'] || $_POST['sellall'] || $_POST['sellamount']) {
    //todo: locking tables
    $sql = "SELECT u.user_id, u.alliance_id, n.government FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE n.nation_id = '{$mysql['sellingto_id']}'";
    $rs = onelinequery($sql);
    if ($rs['alliance_id'] && $rs['alliance_id'] == $nationinfo['alliance_id']) {
        $samealliance = true;
    } else {
        $samealliance = false;
    }
    if (in_array($rs['user_id'], $embargoed)) {
        $errors[] = "There's an embargo prohibiting that!";
    } else if ($nationinfo['government'] == "Oppression") {
        $errors[] = "Your Oppressive government cannot buy nor sell.";
    } else if ($nationinfo['government'] == "Authoritarianism" && !$samealliance) {
        $errors[] = "Your Authoritarian government cannot sell to someone not in your alliance!";
    } else if ($rs['government'] == "Authoritarianism" && !$samealliance) {
        $errors[] = "That Authoritarian government will not buy from you, as you are not in its alliance!";
    } else if ($_SESSION['user_id'] == $rs['user_id']) {
        $errors[] = "You cannot sell to another of your nations. Use Empire Transfers instead.";
    } else {
        $sql = "SELECT m.amount, rd.name, n.name AS nationname FROM {$buyermarketplace} m INNER JOIN {$resourcedefs} rd ON m.{$resource_id} = rd.{$resource_id}
        INNER JOIN nations n ON n.nation_id = m.nation_id
        WHERE m.nation_id = '{$mysql['sellingto_id']}' AND m.{$resource_id} = '{$mysql['resource_id']}' AND m.price = '{$mysql['price']}'";
        $rs = onelinequery($sql);
        if (!$rs['amount']) {
            $errors[] = "Somepony else fulfilled this order!";
        } else {
            if ($_POST['sellone']) {
                $sellingamount = 1;
            } else if ($_POST['sellall']) {
                $sellingamount = $rs['amount'];
            } else if ($_POST['sellamount']) {
                $sellingamount = (int)$mysql['sellingamount'];
                if ($sellingamount < 1) {
                    $errors[] = "Whole numbers greater than 0.";
                } else if ($sellingamount > $rs['amount']) {
                    $errors[] = "That buyer doesn't want that many!";
                }
            }
            if (empty($errors)) {
			$sql = "SELECT r.amount, rd.name FROM {$resourcesname} r INNER JOIN {$resourcedefs} rd ON r.{$resource_id} = rd.{$resource_id}
			WHERE r.nation_id = '{$_SESSION['nation_id']}' AND r.{$resource_id} = '{$mysql['resource_id']}'";
			$have = onelinequery($sql);
            if ($have['amount'] < $sellingamount) {
                $errors[] = "You don't have that many to sell!";
            } else {
                if ($sellingamount < $rs['amount']) {
                    $sql = "UPDATE {$buyermarketplace} SET amount = amount - {$sellingamount} WHERE nation_id = '{$mysql['sellingto_id']}' AND {$resource_id} = '{$mysql['resource_id']}' AND price = '{$mysql['price']}'";
                    $GLOBALS['mysqli']->query($sql);
                } else {
                    $sql = "DELETE FROM {$buyermarketplace} WHERE nation_id = '{$mysql['sellingto_id']}' AND {$resource_id} = '{$mysql['resource_id']}' AND price = '{$mysql['price']}'";
                    $GLOBALS['mysqli']->query($sql);
                }
                $sql = "INSERT INTO {$resourcesname} (nation_id, {$resource_id}, amount) VALUES ({$mysql['sellingto_id']}, {$mysql['resource_id']}, {$sellingamount})
                ON DUPLICATE KEY UPDATE amount = amount + {$sellingamount}";
                $GLOBALS['mysqli']->query($sql);
				if ($have['amount'] == $sellingamount) {
					$sql = "DELETE FROM {$resourcesname} WHERE {$resource_id} = '{$mysql['resource_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
				} else {
					$sql = "UPDATE {$resourcesname} SET amount = amount - '{$sellingamount}' WHERE {$resource_id} = '{$mysql['resource_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
				}
				$GLOBALS['mysqli']->query($sql);
                $newfunds = floor($mysql['price'] * $sellingamount * getsellingmultiplier($_SESSION['nation_id']));
                $displaynewfunds = commas($newfunds);
				$cost = floor($mysql['price'] * $sellingamount * getbuyingmultiplier($mysql['sellingto_id']));
                $displaycost = commas($cost);
                if ($samealliance) {
                $buyermessage =<<<EOFORM
You bought {$sellingamount} {$rs['name']} from <a href="viewnation.php?nation_id={$_SESSION['nation_id']}"><span class="text-success">{$nationinfo['name']}</span></a> for {$displaycost} bits.
EOFORM;
                } else {
                $buyermessage =<<<EOFORM
You bought {$sellingamount} {$rs['name']} from <a href="viewnation.php?nation_id={$_SESSION['nation_id']}">{$nationinfo['name']}</a> for {$displaycost} bits.
EOFORM;
                }
                $mysql['buyermessage'] = $GLOBALS['mysqli']->real_escape_string($buyermessage);
                $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$mysql['sellingto_id']}, '{$mysql['buyermessage']}', NOW())";
                $GLOBALS['mysqli']->query($sql);
                $displayfunds = commas($nationinfo['funds']);
                $infos[] =<<<EOFORM
You sold {$sellingamount} {$rs['name']} to <a href="viewnation.php?nation_id={$mysql['sellingto_id']}">{$rs['nationname']}</a> and made {$displaynewfunds} bits.
EOFORM;
                if ($samealliance) {
                $sellermessage =<<<EOFORM
You sold {$sellingamount} {$rs['name']} to <a href="viewnation.php?nation_id={$mysql['sellingto_id']}"><span class="text-success">{$rs['nationname']}</span></a> and made {$displaynewfunds} bits.
EOFORM;
                } else {
                $sellermessage =<<<EOFORM
You sold {$sellingamount} {$rs['name']} to <a href="viewnation.php?nation_id={$mysql['sellingto_id']}">{$rs['nationname']}</a> and made {$displaynewfunds} bits.
EOFORM;
                }
				$nationinfo['funds'] += $newfunds;
                $sql = "UPDATE nations SET funds = funds + {$newfunds} WHERE nation_id = '{$_SESSION['nation_id']}'";
                $GLOBALS['mysqli']->query($sql);
                $mysql['sellermessage'] = $GLOBALS['mysqli']->real_escape_string($sellermessage);
                $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$_SESSION['nation_id']}, '{$mysql['sellermessage']}', NOW())";
                $GLOBALS['mysqli']->query($sql);
            }
            }
        }
    }
}
if ($_POST) {
    $sql = "SELECT m.*, n.nation_id, n.name, u.user_id, u.alliance_id FROM {$buyermarketplace} m INNER JOIN nations n ON n.nation_id = m.nation_id
    INNER JOIN users u ON u.user_id = n.user_id WHERE m.{$resource_id} = '{$mysql['resource_id']}' ORDER BY m.price DESC, n.nation_id DESC";
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        while ($rs = mysqli_fetch_array($sth)) {
            if (!in_array($rs['user_id'], $embargoed)) {
                $rs['resource_id'] = $rs[$resource_id];
                $deals[] = $rs;
            }
        }
    }
}
}
if ($mode) {
$sql = "SELECT rd.{$resource_id}, rd.name, r.amount from {$resourcedefs} rd LEFT JOIN {$resourcesname} r ON r.{$resource_id} = rd.{$resource_id} AND r.nation_id = '{$_SESSION['nation_id']}' {$tradeable2} ORDER BY name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourceoptions[$rs["{$resource_id}"]]['resource_id'] = $rs["{$resource_id}"];
    $resourceoptions[$rs["{$resource_id}"]]['name'] = $rs['name'];
    if ($rs['amount']) {
        $resourceoptions[$rs["{$resource_id}"]]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
    } else {
        $resourceoptions[$rs["{$resource_id}"]]['optionslistname'] = $rs['name'];
    }
}
} else {
$sql = "SELECT rd.{$resource_id}, rd.name, r.amount from {$resourcedefs} rd LEFT JOIN {$resourcesname} r ON r.{$resource_id} = rd.{$resource_id} AND r.nation_id = '{$_SESSION['nation_id']}' {$tradeable2}
AND name NOT LIKE 'DNA%' ORDER BY name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourceoptions[$rs["{$resource_id}"]]['resource_id'] = $rs["{$resource_id}"];
    $resourceoptions[$rs["{$resource_id}"]]['name'] = $rs['name'];
    if ($rs['amount']) {
        $resourceoptions[$rs["{$resource_id}"]]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
    } else {
        $resourceoptions[$rs["{$resource_id}"]]['optionslistname'] = $rs['name'];
    }
}
$sql = "SELECT rd.{$resource_id}, rd.name, r.amount from {$resourcedefs} rd LEFT JOIN {$resourcesname} r ON r.{$resource_id} = rd.{$resource_id} AND r.nation_id = '{$_SESSION['nation_id']}' {$tradeable2}
AND name LIKE 'DNA%' ORDER BY name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourceoptions[$rs["{$resource_id}"]]['resource_id'] = $rs["{$resource_id}"];
    $resourceoptions[$rs["{$resource_id}"]]['name'] = $rs['name'];
    if ($rs['amount']) {
        $resourceoptions[$rs["{$resource_id}"]]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
    } else {
        $resourceoptions[$rs["{$resource_id}"]]['optionslistname'] = $rs['name'];
    }
}
}
$displayfunds = commas($nationinfo['funds']);
?>