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
if (!$mysql['deal_id']) {
    header("Location: deals.php");
    exit;
}
$sql=<<<EOSQL
SELECT d.*, u.username FROM deals d INNER JOIN users u ON d.touser = u.user_id
WHERE d.deal_id = '{$mysql['deal_id']}' AND d.fromuser = '{$_SESSION['user_id']}' AND d.finalized = '0'
EOSQL;
$dealinfo = onelinequery($sql);
if (!$dealinfo) {
    header("Location: deals.php");
    exit;
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$charitycost = $constants['charityfordeals'];
$dealcost = $constants['fairnessfordeals'];
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
if ($_POST && (($_POST['token_makedeal'] == "") || ($_POST['token_makedeal'] != $_SESSION['token_makedeal']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_makedeal'] == "")) {
    $_SESSION['token_makedeal'] = sha1(rand() . $_SESSION['token_makedeal']);
}
if (!$errors) {
	$offerarray = $offeritems;
	$askarray = $askitems;
if ($_POST['offeritem']) {
    if ($mysql['resource_id'] === "" || $mysql['resource_id'] < 0 || $mysql['resource_id'] > 63) {
        $errors[] = "Select a resource.";
    }
    if ($mysql['amount'] < 1) {
        $errors[] = "No amount entered.";
    } else if (!hasamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'])) {
        $errors[] = "You don't have enough to offer!";
    }
    if (!$errors) {
    addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * -1);
    $sql =<<<EOSQL
    INSERT INTO dealitems_offered (deal_id, resource_id, amount) VALUES ('{$mysql['deal_id']}', '{$mysql['resource_id']}',
    '{$mysql['amount']}') ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Item added to offerings.";
    }
} else if ($_POST['removeoffer']) {
    addamount($mysql['resource_id'], $_SESSION['user_id'], $offerarray[$mysql['resource_id']]['amount']);
    $sql =<<<EOSQL
    DELETE FROM dealitems_offered WHERE deal_id = '{$mysql['deal_id']}' AND resource_id = '{$mysql['resource_id']}'
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
        INSERT INTO dealitems_requested (deal_id, resource_id, amount) VALUES ('{$mysql['deal_id']}', '{$mysql['resource_id']}',
        '{$mysql['amount']}') ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Item added to requests.";
    }
} else if ($_POST['removeask']) {
    $sql =<<<EOSQL
    DELETE FROM dealitems_requested WHERE deal_id = '{$mysql['deal_id']}' AND resource_id = '{$mysql['resource_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $infos[] = "Item removed from requests.";
} else if ($_POST['canceldeal']) {
    foreach ($offeritems AS $dealitem) {
       $sql = "INSERT INTO resources (user_id, resource_id, amount) VALUES ({$_SESSION['user_id']}, {$dealitem['resource_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + '{$dealitem['amount']}'";
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
    DELETE FROM deals WHERE deal_id = '{$mysql['deal_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    header("Location: deals.php");
    exit;
} else if ($_POST['finalizedeal']) {
    if (empty($askitems)) {
        if (!hasamount(40, $_SESSION['user_id'], $charitycost)) {
            $errors[] = "You don't have the Charity to give these compounds away.";
        }
    } else {
        if (!hasamount(24, $_SESSION['user_id'], $dealcost)) {
            $errors[] = "You don't have the Fairness to make this deal.";
        }
    }
    if (!$dealinfo['amount'] && empty($offeritems) && empty($askitems)) {
        $errors[] = "There's nothing to finalize!";
    }
    if (!$errors) {
    if (empty($askitems)) {
        addamount(40, $_SESSION['user_id'], $charitycost * -1);
    } else {
        addamount(24, $_SESSION['user_id'], $dealcost * -1);
    }
    $sql=<<<EOSQL
    UPDATE deals SET finalized = 1 WHERE deal_id = '{$mysql['deal_id']}'
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
SELECT d.*, u.username FROM deals d INNER JOIN users u ON d.touser = u.user_id
WHERE d.deal_id = '{$mysql['deal_id']}' AND d.fromuser = '{$_SESSION['user_id']}' AND d.finalized = '0'
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
}
?>