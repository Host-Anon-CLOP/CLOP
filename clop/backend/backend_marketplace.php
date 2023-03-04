<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$getpost = array_merge($_GET, $_POST);
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
if ($getpost['mode'] == "weapons") {
    $mode = "weapons";
    $marketplace = "weaponsmarketplace";
    $resourcesname = "weapons";
    $resourcedefs = "weapondefs";
    $resource_id = "weapon_id";
    $tradeable1 = "";
    $tradeable2 = "";
    $max = 10000 + (int)($nationinfo['funds'] / 200000);
} else if ($getpost['mode'] == "armor") {
    $mode = "armor";
    $marketplace = "armormarketplace";
    $resourcesname = "armor";
    $resourcedefs = "armordefs";
    $resource_id = "armor_id";
    $tradeable1 = "";
    $tradeable2 = "";
    $max = 10000 + (int)($nationinfo['funds'] / 200000);
} else {
    $mode = "";
    $marketplace = "marketplace";
    $resourcesname = "resources";
    $resourcedefs = "resourcedefs";
    $resource_id = "resource_id";
    $tradeable1 = "AND rd.is_tradeable = 1";
    $tradeable2 = "WHERE rd.is_tradeable = 1";
    $max = 50000 + (int)($nationinfo['funds'] / 100000);
}
$displayfunds = commas($nationinfo['funds']);
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
$mysql['amount'] = (int)$mysql['amount'];
$mysql['price'] = (int)$mysql['price'];
$mysql['resource_id'] = (int)$mysql['resource_id'];
if ($_POST && (($_POST["token_{$marketplace}"] == "") || ($_POST["token_{$marketplace}"] != $_SESSION["token_{$marketplace}"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_{$marketplace}"] == "")) {
    $_SESSION["token_{$marketplace}"] = sha1(rand() . $_SESSION["token_{$marketplace}"]);
}
if (!$errors) {
if ($_POST['action'] == "Place on Market") {
    if ($nationinfo['government'] == "Oppression") {
        $errors[] = "Your Oppressive government cannot buy nor sell.";
    }
    $sql = "SELECT SUM(amount) AS totalamount FROM {$marketplace} m WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = {$mysql['resource_id']}";
    $currenttotal = onelinequery($sql);
    if ($mysql['amount'] + $currenttotal['totalamount'] > $max) {
        $errors[] = "You may only have {$max} of this kind of item on the market.";
    }
    $sql = "SELECT r.amount, rd.name FROM {$resourcesname} r INNER JOIN {$resourcedefs} rd ON r.{$resource_id} = rd.{$resource_id}
    WHERE r.nation_id = '{$_SESSION['nation_id']}' AND r.{$resource_id} = {$mysql['resource_id']} {$tradeable1}";
    $rs = onelinequery($sql);
    if ($mysql['price'] < 1000) {
		$errors[] = "Price must be at least 1000.";
	}
	if ($mysql['amount'] < 1) {
        $errors[] = "Amount must be above zero.";
    }
    if ($rs['amount'] < $mysql['amount']) {
        $errors[] = "You do not have that much {$rs['name']} to sell.";
    }
    if (empty($errors)) {
        if ($rs['amount'] == $mysql['amount']) {
            $sql = "DELETE FROM {$resourcesname} WHERE {$resource_id} = '{$mysql['resource_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
        } else {
            $sql = "UPDATE {$resourcesname} SET amount = amount - '{$mysql['amount']}' WHERE {$resource_id} = '{$mysql['resource_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
        }
        $GLOBALS['mysqli']->query($sql);
        $sql =<<<EOSQL
        INSERT INTO {$marketplace} (nation_id, {$resource_id}, amount, price) VALUES ({$_SESSION['nation_id']}, {$mysql['resource_id']}, {$mysql['amount']}, {$mysql['price']})
        ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "You have put {$mysql['amount']} {$rs['name']} on the market.";
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
if ($_POST['action'] == "Remove from Marketplace") {
    if ($mysql['buyingfrom_id'] != $_SESSION['nation_id']) {
        $errors[] = "C'mon, you really didn't think I'd check for THIS?";
    } else {
        $sql = "SELECT m.amount, rd.name FROM {$marketplace} m INNER JOIN {$resourcedefs} rd ON m.{$resource_id} = rd.{$resource_id}
        WHERE m.nation_id = '{$mysql['buyingfrom_id']}' AND m.{$resource_id} = '{$mysql['resource_id']}' AND m.price = '{$mysql['price']}'";
        $rs = onelinequery($sql);
        if (!$rs) {
            $errors[] = "Too late, somepony bought it all.";
        } else {
            $sql = "DELETE FROM {$marketplace} WHERE nation_id = '{$mysql['buyingfrom_id']}' AND {$resource_id} = '{$mysql['resource_id']}' AND price = '{$mysql['price']}'";
            $GLOBALS['mysqli']->query($sql);
            $sql = "INSERT INTO {$resourcesname} (nation_id, {$resource_id}, amount) VALUES ({$_SESSION['nation_id']}, {$mysql['resource_id']}, {$rs['amount']}) ON DUPLICATE KEY UPDATE amount = amount + {$rs['amount']}";
            $GLOBALS['mysqli']->query($sql);
            $infos[] = "You have removed {$rs['amount']} {$rs['name']} from the market.";
        }
    }
}
if ($_POST['action'] == "Buy One" || $_POST['action'] == "Buy All" || $_POST['action'] == "Buy:") {
    //todo: locking tables
    $sql = "SELECT u.user_id, u.alliance_id, n.government FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE n.nation_id = '{$mysql['buyingfrom_id']}'";
    $rs = onelinequery($sql);
    if ($rs['alliance_id'] && ($rs['alliance_id'] == $nationinfo['alliance_id'])) {
        $samealliance = true;
    } else {
        $samealliance = false;
    }
    if (in_array($rs['user_id'], $embargoed)) {
        $errors[] = "There's an embargo prohibiting that!";
    } else if ($nationinfo['government'] == "Oppression") {
        $errors[] = "Your Oppressive government cannot buy nor sell.";
    } else if ($nationinfo['government'] == "Authoritarianism" && (!$samealliance)) {
        $errors[] = "Your Authoritarian government cannot buy from someone not in your alliance!";
    } else if ($rs['government'] == "Authoritarianism" && (!$samealliance)) {
        $errors[] = "That Authoritarian government will not sell to you, as you are not in its alliance!";
    } else if ($_SESSION['user_id'] == $rs['user_id']) {
        $errors[] = "You cannot buy from another of your nations. Use Empire Transfers instead.";
    } else {
        $sql = "SELECT m.amount, rd.name, n.name AS nationname FROM {$marketplace} m INNER JOIN {$resourcedefs} rd ON m.{$resource_id} = rd.{$resource_id}
        INNER JOIN nations n ON n.nation_id = m.nation_id
        WHERE m.nation_id = '{$mysql['buyingfrom_id']}' AND m.{$resource_id} = '{$mysql['resource_id']}' AND m.price = '{$mysql['price']}'";
        $rs = onelinequery($sql);
        if (!$rs['amount']) {
            $errors[] = "Somepony else bought the last one!";
        } else {
            if ($_POST['action'] == "Buy One") {
                $buyingamount = 1;
            } else if ($_POST['action'] == "Buy All") {
                $buyingamount = $rs['amount'];
            } else if ($_POST['action'] == "Buy:") {
                $buyingamount = (int)$mysql['buyingamount'];
                if ($buyingamount < 1) {
                    $errors[] = "Whole numbers greater than 0.";
                } else if ($buyingamount > $rs['amount']) {
                    $errors[] = "That vendor doesn't have that many for sale!";
                }
            }
            if (empty($errors)) {
            $cost = floor($mysql['price'] * $buyingamount * $buyingmultiplier);
            if ($nationinfo['funds'] < $cost) {
                $errors[] = "You can't afford it!";
            } else {
                if ($buyingamount < $rs['amount']) {
                    $sql = "UPDATE {$marketplace} SET amount = amount - {$buyingamount} WHERE nation_id = '{$mysql['buyingfrom_id']}' AND {$resource_id} = '{$mysql['resource_id']}' AND price = '{$mysql['price']}'";
                    $GLOBALS['mysqli']->query($sql);
                    $sql = "INSERT INTO {$resourcesname} (nation_id, {$resource_id}, amount) VALUES ({$_SESSION['nation_id']}, {$mysql['resource_id']}, {$buyingamount}) ON DUPLICATE KEY UPDATE amount = amount + {$buyingamount}";
                    $GLOBALS['mysqli']->query($sql);
                } else {
                    $sql = "DELETE FROM {$marketplace} WHERE nation_id = '{$mysql['buyingfrom_id']}' AND {$resource_id} = '{$mysql['resource_id']}' AND price = '{$mysql['price']}'";
                    $GLOBALS['mysqli']->query($sql);
                    $sql = "INSERT INTO {$resourcesname} (nation_id, {$resource_id}, amount) VALUES ({$_SESSION['nation_id']}, {$mysql['resource_id']}, {$rs['amount']})
                    ON DUPLICATE KEY UPDATE amount = amount + {$rs['amount']}";
                    $GLOBALS['mysqli']->query($sql);
                }
                $newfunds = floor($mysql['price'] * $buyingamount * getsellingmultiplier($mysql['buyingfrom_id']));
                $displaynewfunds = commas($newfunds);
                $displaycost = commas($cost);
                $infos[] =<<<EOFORM
You bought {$buyingamount} {$rs['name']} from <a href="viewnation.php?nation_id={$mysql['buyingfrom_id']}">{$rs['nationname']}</a> for {$displaycost} bits.
EOFORM;
                if ($samealliance) {
                $buyermessage =<<<EOFORM
You bought {$buyingamount} {$rs['name']} from <a href="viewnation.php?nation_id={$mysql['buyingfrom_id']}"><span class="text-success">{$rs['nationname']}</span></a> for {$displaycost} bits.
EOFORM;
                } else {
                $buyermessage =<<<EOFORM
You bought {$buyingamount} {$rs['name']} from <a href="viewnation.php?nation_id={$mysql['buyingfrom_id']}">{$rs['nationname']}</a> for {$displaycost} bits.
EOFORM;
                }
                $mysql['buyermessage'] = $GLOBALS['mysqli']->real_escape_string($buyermessage);
                $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$_SESSION['nation_id']}, '{$mysql['buyermessage']}', NOW())";
                $GLOBALS['mysqli']->query($sql);
                $nationinfo['funds'] -= $cost;
                $displayfunds = commas($nationinfo['funds']);
                if ($samealliance) {
                $sellermessage =<<<EOFORM
You sold {$buyingamount} {$rs['name']} to <a href="viewnation.php?nation_id={$_SESSION['nation_id']}"><span class="text-success">{$nationinfo['name']}</span></a> and made {$displaynewfunds} bits.
EOFORM;
                } else {
                $sellermessage =<<<EOFORM
You sold {$buyingamount} {$rs['name']} to <a href="viewnation.php?nation_id={$_SESSION['nation_id']}">{$nationinfo['name']}</a> and made {$displaynewfunds} bits.
EOFORM;
                }
                $sql = "UPDATE nations SET funds = funds - {$cost} WHERE nation_id = '{$_SESSION['nation_id']}'";
                $GLOBALS['mysqli']->query($sql);
                $sql = "UPDATE nations SET funds = funds + {$newfunds} WHERE nation_id = '{$mysql['buyingfrom_id']}'";
                $GLOBALS['mysqli']->query($sql);
                $mysql['sellermessage'] = $GLOBALS['mysqli']->real_escape_string($sellermessage);
                $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$mysql['buyingfrom_id']}, '{$mysql['sellermessage']}', NOW())";
                $GLOBALS['mysqli']->query($sql);
            }
            }
        }
    }
}
if ($_POST) {
    $sql = "SELECT m.*, n.nation_id, n.name, u.user_id, u.alliance_id FROM {$marketplace} m INNER JOIN nations n ON n.nation_id = m.nation_id
    INNER JOIN users u ON u.user_id = n.user_id WHERE m.{$resource_id} = '{$mysql['resource_id']}' ORDER BY m.price ASC, n.nation_id DESC";
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
?>