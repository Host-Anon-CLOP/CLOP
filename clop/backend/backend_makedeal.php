<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$getpost = array_merge($_GET, $_POST);
$offeritems = array();
$askitems = array();
$offerweapons = array();
$askweapons = array();
$offerarmor = array();
$askarmor = array();
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$mysql['resource_id'] = (int)$mysql['resource_id'];
if ($nationinfo['economy'] != "State Controlled" || !$nationinfo['active_economy']) {
    header("Location: overview.php");
    exit;
}
if (!$mysql['deal_id']) {
    header("Location: deals.php");
    exit;
}
$sql=<<<EOSQL
SELECT d.*, n.name FROM deals d INNER JOIN nations n ON d.tonation = n.nation_id
WHERE d.deal_id = '{$mysql['deal_id']}' AND d.fromnation = '{$_SESSION['nation_id']}' AND d.finalized = '0'
EOSQL;
$dealinfo = onelinequery($sql);
if (!$dealinfo) {
    header("Location: deals.php");
    exit;
}
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs2['resource_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs2['resource_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_offered d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerweapons[$rs2['weapon_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_requested d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askweapons[$rs2['weapon_id']] = $rs2;
    }
	$sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_offered d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerarmor[$rs2['armor_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_requested d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askarmor[$rs2['armor_id']] = $rs2;
    }
if ($_POST && (($_POST['token_makedeal'] == "") || ($_POST['token_makedeal'] != $_SESSION['token_makedeal']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_makedeal'] == "")) {
    $_SESSION['token_makedeal'] = sha1(rand() . $_SESSION['token_makedeal']);
}
if (!$errors) {
$bitsmax = 100000000 + (int)($nationinfo['funds'] / 10);
if ($_POST['type'] == "weapon") {
	$offerarray = $offerweapons;
	$askarray = $askweapons;
	$basetable = "weapons";
	$resource_id = "weapon_id";
	$resourcestable = "weapons";
	$resourcedefs = "weapondefs";
	$tradeable = "";
	$max = 5000 + (int)($nationinfo['funds'] / 200000);
} else if ($_POST['type'] == "armor") {
	$offerarray = $offerarmor;
	$askarray = $askarmor;
	$basetable = "armor";
	$resource_id = "armor_id";
	$resourcestable = "armor";
	$resourcedefs = "armordefs";
	$tradeable = "";
	$max = 5000 + (int)($nationinfo['funds'] / 200000);
} else {
	$offerarray = $offeritems;
	$askarray = $askitems;
	$basetable = "items";
	$resource_id = "resource_id";
	$resourcestable = "resources";
	$resourcedefs = "resourcedefs";
	$tradeable = "AND is_tradeable = 1";
	$max = 10000 + (int)($nationinfo['funds'] / 100000);
}
if ($_POST['offeritem']) {
    $sql =<<<EOSQL
    SELECT {$resource_id} FROM {$resourcedefs} WHERE {$resource_id} = {$mysql['resource_id']} {$tradeable}
EOSQL;
    $rs = onelinequery($sql);
    if (!$rs) {
        $errors[] = "Did you know that people like you only exist to make my life harder?";
    }
	$sql =<<<EOSQL
	SELECT SUM(dio.amount) AS totalamount FROM deal{$basetable}_offered dio
	INNER JOIN deals d ON dio.deal_id = d.deal_id
	WHERE d.fromnation = '{$_SESSION['nation_id']}' AND dio.{$resource_id} = {$mysql['resource_id']}
EOSQL;
	$currenttotal = onelinequery($sql);
    if ($mysql['amount'] + $currenttotal['totalamount'] > $max) {
        $errors[] = "You may only have {$max} of this kind of item in deals.";
    }
    $sql =<<<EOSQL
    SELECT amount FROM {$resourcestable} WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = {$mysql['resource_id']}
EOSQL;
    $rs = onelinequery($sql);
    $paythis = $mysql['amount'] * 100;
    if ($nationinfo['funds'] < $paythis) {
        $errors[] = "You don't have the money to include that many items!";
    }
    if ($rs['amount'] < $mysql['amount']) {
        $errors[] = "You don't have enough to offer!";
    } else if ($mysql['amount'] < 1) {
        $errors[] = "No amount entered.";
    }
    if (!$errors) {
    if ($rs['amount'] == $mysql['amount']) {
        $sql = <<<EOSQL
        DELETE FROM {$resourcestable} WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = '{$mysql['resource_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    } else {
        $sql = <<<EOSQL
        UPDATE {$resourcestable} SET amount = amount - {$mysql['amount']} WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = '{$mysql['resource_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    }
    $sql =<<<EOSQL
    INSERT INTO deal{$basetable}_offered (deal_id, {$resource_id}, amount) VALUES ('{$mysql['deal_id']}', '{$mysql['resource_id']}',
    '{$mysql['amount']}') ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql =<<<EOSQL
        UPDATE nations SET funds = funds - {$paythis} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql =<<<EOSQL
        UPDATE deals SET paid = paid + {$paythis} WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Item added to offerings.";
    }
} else if ($_POST['removeoffer']) {
    $sql =<<<EOSQL
    INSERT INTO {$resourcestable} (nation_id, {$resource_id}, amount) VALUES ({$_SESSION['nation_id']}, '{$mysql['resource_id']}', '{$offerarray[$mysql['resource_id']]['amount']}')
    ON DUPLICATE KEY UPDATE amount = amount + '{$offerarray[$mysql['resource_id']]['amount']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql =<<<EOSQL
    DELETE FROM deal{$basetable}_offered WHERE deal_id = '{$mysql['deal_id']}' AND {$resource_id} = '{$mysql['resource_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $paythis = $offerarray[$mysql['resource_id']]['amount'] * 100;
    $sql=<<<EOSQL
    UPDATE nations SET funds = funds + {$paythis} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql =<<<EOSQL
    UPDATE deals SET paid = paid - {$paythis} WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $infos[] = "Item removed from offerings.";
} else if ($_POST['askitem']) {
    $sql =<<<EOSQL
    SELECT {$resource_id} FROM {$resourcedefs} WHERE {$resource_id} = '{$mysql['resource_id']}' {$tradeable}
EOSQL;
    $rs = onelinequery($sql);
    if (!$rs) {
        $errors[] = "Did you know that people like you only exist to make my life harder?";
    }
    if (!$mysql['amount']) {
        $errors[] = "No amount entered.";
    } else if ($mysql['amount'] < 1) {
        $errors[] = "Nope.";
    }
    $paythis = $mysql['amount'] * 100;
    if ($nationinfo['funds'] < $paythis) {
        $errors[] = "You don't have the money to include that many items!";
    }
    if (!$errors) {
        $sql =<<<EOSQL
        INSERT INTO deal{$basetable}_requested (deal_id, {$resource_id}, amount) VALUES ('{$mysql['deal_id']}', '{$mysql['resource_id']}',
        '{$mysql['amount']}') ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql =<<<EOSQL
        UPDATE nations SET funds = funds - {$paythis} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql =<<<EOSQL
        UPDATE deals SET paid = paid + {$paythis} WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Item added to requests.";
    }
} else if ($_POST['removeask']) {
    $sql =<<<EOSQL
    DELETE FROM deal{$basetable}_requested WHERE deal_id = '{$mysql['deal_id']}' AND {$resource_id} = '{$mysql['resource_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $paythis = $askarray[$mysql['resource_id']]['amount'] * 100;
    $sql=<<<EOSQL
    UPDATE nations SET funds = funds + {$paythis} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql =<<<EOSQL
    UPDATE deals SET paid = paid - {$paythis} WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $infos[] = "Item removed from requests.";
} else if ($_POST['offermoney']) {
    if ($mysql['amount'] <= 0) {
        $errors[] = "No amount entered.";
    }
	$sql =<<<EOSQL
	SELECT SUM(amount) AS totalamount FROM deals
	WHERE fromnation = {$_SESSION['nation_id']}
EOSQL;
	$currenttotal = onelinequery($sql);
    if ($mysql['amount'] + $currenttotal['totalamount'] > $bitsmax) {
        $commasbitsmax = commas($bitsmax);
        $errors[] = "You may only offer {$commasbitsmax} bits total in deals.";
    }
    if ($nationinfo['funds'] < $mysql['amount']) {
        $errors[] = "You do not have that much money.";
    }
    if ($dealinfo['amount']) {
        $errors[] = "There's already money in this deal!";
    }
    if (!$errors) {
        $sql=<<<EOSQL
        UPDATE nations SET funds = funds - '{$mysql['amount']}' WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
        UPDATE deals SET amount = '{$mysql['amount']}', askingformoney = '0' WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    }
} else if ($_POST['requestmoney']) {
    if ($mysql['amount'] <= 0) {
        $errors[] = "No amount entered.";
    }
    if ($dealinfo['amount']) {
        $errors[] = "There's already money in this deal!";
    }
    if (!$errors) {
        $sql=<<<EOSQL
        UPDATE deals SET amount = '{$mysql['amount']}', askingformoney = '1' WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    }
} else if ($_POST['removemoney']) {
    if (!$dealinfo['askingformoney']) {
        $sql=<<<EOSQL
        UPDATE nations SET funds = funds + '{$dealinfo['amount']}' WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    }
    $sql =<<<EOSQL
    UPDATE deals SET amount = 0, askingformoney = 0 WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
} else if ($_POST['canceldeal']) {
    foreach ($offeritems AS $dealitem) {
       $sql = "INSERT INTO resources (nation_id, resource_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealitem['resource_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + '{$dealitem['amount']}'";
       $GLOBALS['mysqli']->query($sql);
    }
	foreach ($offerweapons AS $dealitem) {
       $sql = "INSERT INTO weapons (nation_id, weapon_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealitem['weapon_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + '{$dealitem['amount']}'";
       $GLOBALS['mysqli']->query($sql);
    }
	foreach ($offerarmor AS $dealitem) {
       $sql = "INSERT INTO armor (nation_id, armor_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealitem['armor_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + '{$dealitem['amount']}'";
       $GLOBALS['mysqli']->query($sql);
    }
    if (!$dealinfo['askingformoney']) {
    $sql = <<<EOSQL
UPDATE nations SET funds = funds + {$dealinfo['amount']} + {$dealinfo['paid']} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    } else {
    $sql = <<<EOSQL
UPDATE nations SET funds = funds + {$dealinfo['paid']} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    }
    $sql = <<<EOSQL
    DELETE FROM dealitems_requested WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealitems_offered WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
	$sql = <<<EOSQL
    DELETE FROM dealweapons_requested WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealweapons_offered WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
	$sql = <<<EOSQL
    DELETE FROM dealarmor_requested WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealarmor_offered WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM deals WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    header("Location: deals.php");
    exit;
} else if ($_POST['finalizedeal']) {
    if (!$dealinfo['amount'] && empty($offeritems) && empty($askitems) && empty($offerweapons) && empty($askweapons) && empty($offerarmor) && empty($askarmor)) {
        $errors[] = "There's nothing to finalize!";
    } else {
    $sql=<<<EOSQL
    UPDATE deals SET finalized = 1 WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    }
}
}
$sql=<<<EOSQL
SELECT rd.resource_id, rd.name, r.amount from resourcedefs rd LEFT JOIN resources r ON r.resource_id = rd.resource_id AND r.nation_id = '{$_SESSION['nation_id']}'
WHERE rd.is_tradeable = 1 AND rd.name NOT LIKE 'DNA%' ORDER BY name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourceoptions[$rs['resource_id']]['resource_id'] = $rs['resource_id'];
    $resourceoptions[$rs['resource_id']]['name'] = $rs['name'];
    if ($rs['amount']) {
        $resourceoptions[$rs['resource_id']]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
    } else {
        $resourceoptions[$rs['resource_id']]['optionslistname'] = $rs['name'];
    }
}
$sql=<<<EOSQL
SELECT rd.resource_id, rd.name, r.amount from resourcedefs rd LEFT JOIN resources r ON r.resource_id = rd.resource_id AND r.nation_id = '{$_SESSION['nation_id']}'
WHERE rd.is_tradeable = 1 AND rd.name LIKE 'DNA%' ORDER BY name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourceoptions[$rs['resource_id']]['resource_id'] = $rs['resource_id'];
    $resourceoptions[$rs['resource_id']]['name'] = $rs['name'];
    if ($rs['amount']) {
        $resourceoptions[$rs['resource_id']]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
    } else {
        $resourceoptions[$rs['resource_id']]['optionslistname'] = $rs['name'];
    }
}
$sql = "SELECT rd.weapon_id, rd.name, r.amount from weapondefs rd LEFT JOIN weapons r ON r.weapon_id = rd.weapon_id AND r.nation_id = '{$_SESSION['nation_id']}' ORDER BY name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $weaponoptions[$rs['weapon_id']]['resource_id'] = $rs['weapon_id'];
    $weaponoptions[$rs['weapon_id']]['name'] = $rs['name'];
    if ($rs['amount']) {
        $weaponoptions[$rs['weapon_id']]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
    } else {
        $weaponoptions[$rs['weapon_id']]['optionslistname'] = $rs['name'];
    }
}
$sql = "SELECT rd.armor_id, rd.name, r.amount from armordefs rd LEFT JOIN armor r ON r.armor_id = rd.armor_id AND r.nation_id = '{$_SESSION['nation_id']}' ORDER BY name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $armoroptions[$rs['armor_id']]['resource_id'] = $rs['armor_id'];
    $armoroptions[$rs['armor_id']]['name'] = $rs['name'];
    if ($rs['amount']) {
        $armoroptions[$rs['armor_id']]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
    } else {
        $armoroptions[$rs['armor_id']]['optionslistname'] = $rs['name'];
    }
}
if ($_POST && !$errors) {
    //this is inefficient as shit
    unset($offeritems);
    unset($askitems);
	unset($offerweapons);
    unset($askweapons);
	unset($offerarmor);
    unset($askarmor);
$sql=<<<EOSQL
SELECT d.*, n.name FROM deals d INNER JOIN nations n ON d.tonation = n.nation_id
WHERE d.deal_id = '{$mysql['deal_id']}' AND d.fromnation = '{$_SESSION['nation_id']}' AND d.finalized = '0'
EOSQL;
$dealinfo = onelinequery($sql);
if (!$dealinfo) {
    header("Location: deals.php");
}
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs2['resource_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs2['resource_id']] = $rs2;
    }
	$sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_offered d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerweapons[$rs2['weapon_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_requested d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askweapons[$rs2['weapon_id']] = $rs2;
    }
	$sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_offered d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerarmor[$rs2['armor_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_requested d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askarmor[$rs2['armor_id']] = $rs2;
    }
}
?>