<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_deals"] == "") || ($_POST["token_deals"] != $_SESSION["token_deals"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_deals"] == "")) {
    $_SESSION["token_deals"] = sha1(rand() . $_SESSION["token_deals"]);
}
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$deals = array();
$askitems = array();
$offeritems = array();
//to be honest I just loathe working on deals code, even in Compounds, maybe you won't, in which case, enjoy
$sql=<<<EOSQL
SELECT d.deal_id, d.fromuser, u.username, u.user_id FROM deals d INNER JOIN users u ON d.fromuser = u.user_id
WHERE d.touser = '{$_SESSION['user_id']}' AND d.finalized = '1'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$offeritems[$rs['deal_id']] = array();
	$askitems[$rs['deal_id']] = array();
    $deals[$rs['deal_id']] = $rs;
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs['deal_id']][] = $rs2;
    }
}
$sql=<<<EOSQL
SELECT d.deal_id, d.fromuser, d.finalized, u.username, u.user_id FROM deals d INNER JOIN users u ON d.touser = u.user_id
WHERE d.fromuser = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$offeritems[$rs['deal_id']] = array();
	$askitems[$rs['deal_id']] = array();
    $outgoingdeals[$rs['deal_id']] = $rs;
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from dealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs['deal_id']][] = $rs2;
    }
}
if (!$errors) {
if ($_POST['makedeal']) {
    $sql=<<<EOSQL
    SELECT user_id, alliance_id, stasismode FROM users WHERE username = '{$mysql['username']}'
EOSQL;
    $rs = onelinequery($sql);
    if (!$rs) {
        $errors[] = "User not found.";
    } else if ($rs['stasismode']) {
        $errors[] = "That user's owner is in stasis.";
    } else if ($rs['user_id'] == $_SESSION['user_id']) {
		$errors[] = "Clop, clop, clop...";
    } else {
		$sql=<<<EOSQL
		INSERT INTO deals (fromuser, touser) VALUES ('{$_SESSION['user_id']}', '{$rs['user_id']}')
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		$newdeal = mysqli_insert_id($GLOBALS['mysqli']);
		header("Location: makedeal.php?deal_id={$newdeal}");
		exit;
    }
}
//Someday I will implement table locking. Eventually.
if ($_POST['acceptdeal']) {
    if (!$deals[$_POST['deal_id']]) {
        $errors[] = "Deal not found!";
    } else {
        $deal = $_POST['deal_id'];
    }
    if (!$errors) {
        $sql =<<<EOSQL
        SELECT resource_id, amount FROM resources WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
        $sth = $GLOBALS['mysqli']->query($sql);
        while ($rs = mysqli_fetch_array($sth)) {
            $ownedresources[$rs['resource_id']] = $rs['amount'];
        }
        foreach ($askitems[$deal] AS $dealitem) {
            if ($ownedresources[$dealitem['resource_id']] < $dealitem['amount']) {
                $errors[] = "You don't have enough {$dealitem['name']} to do this deal!";
            }
        }
        if (!$errors) {
            $infos[] =<<<EOFORM
You accepted a deal with <a href="viewuser.php?user_id={$deals[$deal]['fromuser']}">{$deals[$deal]['username']}</a>.
EOFORM;
            $messages[] =<<<EOFORM
Your deal with <a href="viewuser.php?user_id={$_SESSION['user_id']}">{$userinfo['username']}</a> was accepted.
EOFORM;
            foreach ($askitems[$deal] AS $dealitem) {
                addamount($dealitem['resource_id'], $_SESSION['user_id'], $dealitem['amount'] * -1);
                addamount($dealitem['resource_id'], $deals[$deal]['fromuser'], $dealitem['amount']);
                $displayamount = commas($dealitem['amount']);
                $infos[] = "You dealt away {$displayamount} {$dealitem['name']}.";
                $messages[] = "You received {$displayamount} {$dealitem['name']} as part of your deal.";
            }
            $sql = "DELETE FROM resources WHERE user_id = '{$_SESSION['user_id']}' AND amount = 0";
            $GLOBALS['mysqli']->query($sql);
            foreach ($offeritems[$deal] AS $dealitem) {
                addamount($dealitem['resource_id'], $_SESSION['user_id'], $dealitem['amount']);
                $infos[] = "You received {$dealitem['amount']} {$dealitem['name']}.";
                $messages[] = "You dealt away {$dealitem['amount']} {$dealitem['name']}.";
            }
            $messagelist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $messages));
            $sql = "INSERT INTO reports (user_id, report, time) VALUES ({$deals[$deal]['fromuser']}, '{$messagelist}', NOW())";
            $GLOBALS['mysqli']->query($sql);
            $infoslist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $infos));
            $sql = "INSERT INTO reports (user_id, report, time) VALUES ({$_SESSION['user_id']}, '{$infoslist}', NOW())";
            $GLOBALS['mysqli']->query($sql);
            $sql = <<<EOSQL
            DELETE FROM dealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $sql = <<<EOSQL
            DELETE FROM dealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $sql = <<<EOSQL
            DELETE FROM deals WHERE deal_id = '{$deal}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            unset($deals[$deal]);
        }
    }
}
if ($_POST['rejectdeal']) {
    if (!$deals[$_POST['deal_id']]) {
        $errors[] = "Deal not found!";
    } else {
        $deal = $_POST['deal_id'];
    }
	if (!$errors) {
    foreach ($offeritems[$deal] AS $dealitem) {
       addamount($dealitem['resource_id'], $deals[$deal]['fromuser'], $dealitem['amount']);
    }
    $message = "Your deal with {$userinfo['username']} was rejected.";
    $mysql['message'] = $GLOBALS['mysqli']->real_escape_string($message);
    $sql = <<<EOSQL
INSERT INTO reports SET report = '{$mysql['message']}', user_id = '{$deals[$deal]['fromuser']}', time = NOW()
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM deals WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    unset($deals[$deal]);
	$infos[] = "Deal rejected.";
	}
}
if ($_POST['canceldeal']) {
    if (!$outgoingdeals[$_POST['deal_id']]) {
        $errors[] = "Deal not found!";
    } else {
        $deal = $_POST['deal_id'];
    }
	if (!$errors) {
    foreach ($offeritems[$deal] AS $dealitem) {
       addamount($dealitem['resource_id'], $_SESSION['user_id'], $dealitem['amount']);
    }
    $sql = <<<EOSQL
    DELETE FROM dealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM deals WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
	$infos[] = "Your deal with {$outgoingdeals[$deal]['username']} was canceled.";
    unset($outgoingdeals[$deal]);
	}
}
}
?>