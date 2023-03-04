<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_alliancedeals"] == "") || ($_POST["token_alliancedeals"] != $_SESSION["token_alliancedeals"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_alliancedeals"] == "")) {
    $_SESSION["token_alliancedeals"] = sha1(rand() . $_SESSION["token_alliancedeals"]);
}
if ($_POST && !hasability("alliancemakedeals", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
    $errors[] = "You may not affect the deals of your alliance.";
}
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$deals = array();
$askitems = array();
$offeritems = array();
$sql=<<<EOSQL
SELECT d.deal_id, d.fromalliance, d.peaceturns, a.name, a.alliance_id FROM alliancedeals d INNER JOIN alliances a ON d.fromalliance = a.alliance_id
WHERE d.toalliance = '{$userinfo['alliance_id']}' AND d.finalized = '1'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$offeritems[$rs['deal_id']] = array();
	$askitems[$rs['deal_id']] = array();
    $deals[$rs['deal_id']] = $rs;
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs['deal_id']][] = $rs2;
    }
}
$sql=<<<EOSQL
SELECT d.deal_id, d.fromalliance, d.peaceturns, d.finalized, a.name, a.alliance_id FROM alliancedeals d INNER JOIN alliances a ON d.toalliance = a.alliance_id
WHERE d.fromalliance = '{$userinfo['alliance_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$offeritems[$rs['deal_id']] = array();
	$askitems[$rs['deal_id']] = array();
    $outgoingdeals[$rs['deal_id']] = $rs;
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_offered d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offeritems[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.resource_id from alliancedealitems_requested d INNER JOIN resourcedefs rd ON rd.resource_id = d.resource_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askitems[$rs['deal_id']][] = $rs2;
    }
}
if (!$errors) {
if ($_POST['makedeal']) {
    $sql=<<<EOSQL
    SELECT alliance_id FROM alliances WHERE name = '{$mysql['name']}'
EOSQL;
    $rs = onelinequery($sql);
    if (!$rs) {
        $errors[] = "Alliance not found.";
    } else if ($rs['alliance_id'] == $userinfo['alliance_id']) {
		$errors[] = "Clop, clop, clop...";
    } else {
		$sql=<<<EOSQL
		INSERT INTO alliancedeals (fromalliance, toalliance) VALUES ('{$userinfo['alliance_id']}', '{$rs['alliance_id']}')
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		$newdeal = mysqli_insert_id($GLOBALS['mysqli']);
		header("Location: alliancemakedeal.php?deal_id={$newdeal}");
		exit;
    }
}
if ($_POST['acceptdeal']) {
    if (!$deals[$_POST['deal_id']]) {
        $errors[] = "Deal not found!";
    } else {
        $deal = $_POST['deal_id'];
    }
    if (!$errors) {
        $sql =<<<EOSQL
        SELECT resource_id, amount FROM allianceresources WHERE alliance_id = '{$userinfo['alliance_id']}'
EOSQL;
        $sth = $GLOBALS['mysqli']->query($sql);
        while ($rs = mysqli_fetch_array($sth)) {
            $ownedresources[$rs['resource_id']] = $rs['amount'];
        }
        foreach ($askitems[$deal] AS $dealitem) {
            if ($ownedresources[$dealitem['resource_id']] < $dealitem['amount']) {
                $errors[] = "Your alliance doesn't have enough {$dealitem['name']} to do this deal!";
            }
        }
        if (!$errors) {
            $infos[] =<<<EOFORM
Your alliance accepted a deal with <a href="viewalliance.php?alliance_id={$deals[$deal]['fromalliance']}">{$deals[$deal]['name']}</a>.
EOFORM;
            $messages[] =<<<EOFORM
Your alliance's deal with <a href="viewalliance.php?alliance_id={$userinfo['alliance_id']}">{$allianceinfo['name']}</a> was accepted.
EOFORM;
            foreach ($askitems[$deal] AS $dealitem) {
                allianceaddamount($dealitem['resource_id'], $userinfo['alliance_id'], $dealitem['amount'] * -1);
                allianceaddamount($dealitem['resource_id'], $deals[$deal]['fromalliance'], $dealitem['amount']);
                $displayamount = commas($dealitem['amount']);
                $infos[] = "Your alliance dealt away {$displayamount} {$dealitem['name']}.";
                $messages[] = "Your alliance received {$displayamount} {$dealitem['name']} as part of its deal.";
            }
            foreach ($offeritems[$deal] AS $dealitem) {
                allianceaddamount($dealitem['resource_id'], $userinfo['alliance_id'], $dealitem['amount']);
                $infos[] = "You received {$dealitem['amount']} {$dealitem['name']}.";
                $messages[] = "You dealt away {$dealitem['amount']} {$dealitem['name']}.";
            }
            if ($deals[$deal]['peaceturns']) {
				if ($userinfo['alliance_id'] < $deals[$deal]['fromalliance']) {
					$alliance1 = $userinfo['alliance_id'];
					$alliance2 = $deals[$deal]['fromalliance'];
				} else {
					$alliance1 = $deals[$deal]['fromalliance'];
					$alliance2 = $userinfo['alliance_id'];
				}
					$sql=<<<EOSQL
					INSERT INTO peacetreaties (alliance1, alliance2, turns)
					VALUES ({$alliance1}, {$alliance2}, {$deals[$deal]['peaceturns']})
					ON DUPLICATE KEY UPDATE turns = turns + {$deals[$deal]['peaceturns']}
EOSQL;
					$GLOBALS['mysqli']->query($sql);
					$sql=<<<EOSQL
					UPDATE peacetreaties SET turns = 56
					WHERE turns > 56;
EOSQL;
					$GLOBALS['mysqli']->query($sql);
            }
            $messagelist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $messages));
            $sql = "INSERT INTO alliancereports (alliance_id, report, time) VALUES ({$deals[$deal]['fromalliance']}, '{$messagelist}', NOW())";
            $GLOBALS['mysqli']->query($sql);
            $infoslist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $infos));
            $sql = "INSERT INTO alliancereports (alliance_id, report, time) VALUES ({$userinfo['alliance_id']}, '{$infoslist}', NOW())";
            $GLOBALS['mysqli']->query($sql);
            $sql = <<<EOSQL
            DELETE FROM alliancedealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $sql = <<<EOSQL
            DELETE FROM alliancedealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $sql = <<<EOSQL
            DELETE FROM alliancedeals WHERE deal_id = '{$deal}'
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
       allianceaddamount($dealitem['resource_id'], $deals[$deal]['fromalliance'], $dealitem['amount']);
    }
    $message = "Your alliance's deal with {$allianceinfo['name']} was rejected.";
    $mysql['message'] = $GLOBALS['mysqli']->real_escape_string($message);
    $sql = <<<EOSQL
INSERT INTO alliancereports SET report = '{$mysql['message']}', alliance_id = '{$deals[$deal]['fromalliance']}', time = NOW()
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM alliancedealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM alliancedealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM alliancedeals WHERE deal_id = '{$deal}'
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
       allianceaddamount($dealitem['resource_id'], $userinfo['alliance_id'], $dealitem['amount']);
    }
    $sql = <<<EOSQL
    DELETE FROM alliancedealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM alliancedealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM alliancedeals WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
	$infos[] = "Your deal with {$outgoingdeals[$deal]['name']} was canceled.";
    unset($outgoingdeals[$deal]);
	}
}
}
?>