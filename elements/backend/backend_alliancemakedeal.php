<?php
include_once("allfunctions.php");
needsalliance();
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
$mysql['deal_id'] = (int)$mysql['deal_id'];
if (!$mysql['deal_id']) {
    header("Location: alliancedeals.php");
    exit;
}
$sql=<<<EOSQL
SELECT d.*, a.name FROM alliancedeals d INNER JOIN alliances a ON d.toalliance = a.alliance_id
WHERE d.deal_id = '{$mysql['deal_id']}' AND d.fromalliance = '{$userinfo['alliance_id']}' AND d.finalized = '0'
EOSQL;
$dealinfo = onelinequery($sql);
if (!$dealinfo) {
    header("Location: alliancedeals.php");
    exit;
}
if (!$dealinfo['peaceturns']) {
	$dealinfo['peaceturns'] = "";
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
    $honorcost = $constants['honorfordeals'];
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs2['resource_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs2['resource_id']] = $rs2;
    }
if ($_POST && (($_POST['token_alliancemakedeal'] == "") || ($_POST['token_alliancemakedeal'] != $_SESSION['token_alliancemakedeal']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_alliancemakedeal'] == "")) {
    $_SESSION['token_alliancemakedeal'] = sha1(rand() . $_SESSION['token_alliancemakedeal']);
}
if ($_POST && !hasability("alliancemakedeals", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
    $errors[] = "You may not affect the deals of your alliance.";
}
if (!$errors) {
	$offerarray = $offeritems;
	$askarray = $askitems;
if ($_POST['changepeace']) {
	$mysql['peaceturns'] = (int)$mysql['peaceturns'];
	if ($mysql['peaceturns'] <= 0) {
		$mysql['peaceturns'] = 0;
	}
    $sql =<<<EOSQL
	UPDATE alliancedeals SET peaceturns = {$mysql['peaceturns']} WHERE deal_id = {$mysql['deal_id']}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    $infos[] = "Peace agreement added.";
} else if ($_POST['offeritem']) {
    if ($mysql['resource_id'] === "" || $mysql['resource_id'] < 0 || $mysql['resource_id'] > 63) {
        $errors[] = "Select a resource.";
    }
    if ($mysql['amount'] < 1) {
        $errors[] = "No amount entered.";
    } else if (!alliancehasamount($mysql['resource_id'], $userinfo['alliance_id'], $mysql['amount'])) {
        $errors[] = "Your alliance doesn't have enough to offer!";
    }
    if (!$errors) {
    allianceaddamount($mysql['resource_id'], $userinfo['alliance_id'], $mysql['amount'] * -1);
    $sql =<<<EOSQL
    INSERT INTO alliancedealitems_offered (deal_id, resource_id, amount) VALUES ('{$mysql['deal_id']}', '{$mysql['resource_id']}',
    '{$mysql['amount']}') ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Item added to offerings.";
    }
} else if ($_POST['removeoffer']) {
    allianceaddamount($mysql['resource_id'], $userinfo['alliance_id'], $offerarray[$mysql['resource_id']]['amount']);
    $sql =<<<EOSQL
    DELETE FROM alliancedealitems_offered WHERE deal_id = '{$mysql['deal_id']}' AND resource_id = '{$mysql['resource_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $infos[] = "Item removed from offerings.";
} else if ($_POST['askitem']) {
    if ($mysql['resource_id'] === "" || $mysql['resource_id'] < 0 || $mysql['resource_id'] > 63) {
        $errors[] = "Select a resource.";
    }
    if ($mysql['amount'] < 1) {
        $errors[] = "No amount entered.";
    }
    if (!$errors) {
        $sql =<<<EOSQL
        INSERT INTO alliancedealitems_requested (deal_id, resource_id, amount) VALUES ('{$mysql['deal_id']}', '{$mysql['resource_id']}',
        '{$mysql['amount']}') ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Item added to requests.";
    }
} else if ($_POST['removeask']) {
    $sql =<<<EOSQL
    DELETE FROM alliancedealitems_requested WHERE deal_id = '{$mysql['deal_id']}' AND resource_id = '{$mysql['resource_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $infos[] = "Item removed from requests.";
} else if ($_POST['canceldeal']) {
    foreach ($offeritems AS $dealitem) {
       allianceaddamount($dealitem['resource_id'], $userinfo['alliance_id'], $dealitem['amount']);
    }
    $sql = <<<EOSQL
    DELETE FROM alliancedealitems_requested WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM alliancedealitems_offered WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM alliancedeals WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    header("Location: alliancedeals.php");
    exit;
} else if ($_POST['finalizedeal']) {
    if (!alliancehasamount(18, $userinfo['alliance_id'], $honorcost)) {
        $errors[] = "Your alliance doesn't have the Honor to make this deal.";
    }
    if (empty($offeritems) && empty($askitems) && !$dealinfo['peaceturns']) {
        $errors[] = "There's nothing to finalize!";
    }
    if (!$errors) {
    allianceaddamount(18, $userinfo['alliance_id'], $honorcost * -1);
    $sql=<<<EOSQL
    UPDATE alliancedeals SET finalized = 1 WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    }
}
}
if ($_POST && !$errors) {
    //this is inefficient as shit
    unset($offeritems);
    unset($askitems);
$sql=<<<EOSQL
SELECT d.*, a.name FROM alliancedeals d INNER JOIN alliances a ON d.toalliance = a.alliance_id
WHERE d.deal_id = '{$mysql['deal_id']}' AND d.fromalliance = '{$userinfo['alliance_id']}' AND d.finalized = '0'
EOSQL;
$dealinfo = onelinequery($sql);
if (!$dealinfo) {
    header("Location: alliancedeals.php");
}
if (!$dealinfo['peaceturns']) {
	$dealinfo['peaceturns'] = "";
}
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs2['resource_id']] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$mysql['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs2['resource_id']] = $rs2;
    }
}
?>