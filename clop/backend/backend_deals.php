<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$deals = array();
$askitems = array();
$offeritems = array();
//all of this is probably somewhat redundant, especially with the weapons and armor, but I just do not care right now
//to be honest I just loathe working on deals code, maybe you won't, in which case, enjoy
$sql=<<<EOSQL
SELECT d.deal_id, d.amount, d.fromnation, d.askingformoney, d.paid, n.name, n.nation_id FROM deals d INNER JOIN nations n ON d.fromnation = n.nation_id
WHERE d.tonation = '{$_SESSION['nation_id']}' AND d.finalized = '1'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$offeritems[$rs['deal_id']] = array();
	$askitems[$rs['deal_id']] = array();
	$offerweapons[$rs['deal_id']] = array();
	$askweapons[$rs['deal_id']] = array();
	$offerarmor[$rs['deal_id']] = array();
	$askarmor[$rs['deal_id']] = array();
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
	$sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_offered d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerweapons[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_requested d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askweapons[$rs['deal_id']][] = $rs2;
    }
	$sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_offered d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerarmor[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_requested d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askarmor[$rs['deal_id']][] = $rs2;
    }
}
$sql=<<<EOSQL
SELECT d.deal_id, d.amount, d.fromnation, d.askingformoney, d.finalized, d.paid, n.name, n.nation_id FROM deals d INNER JOIN nations n ON d.tonation = n.nation_id
WHERE d.fromnation = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$offeritems[$rs['deal_id']] = array();
	$askitems[$rs['deal_id']] = array();
    $offerweapons[$rs['deal_id']] = array();
	$askweapons[$rs['deal_id']] = array();
    $offerarmor[$rs['deal_id']] = array();
	$askarmor[$rs['deal_id']] = array();
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
	$sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_offered d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerweapons[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.weapon_id from dealweapons_requested d INNER JOIN weapondefs rd ON rd.weapon_id = d.weapon_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askweapons[$rs['deal_id']][] = $rs2;
    }
	$sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_offered d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $offerarmor[$rs['deal_id']][] = $rs2;
    }
    $sql=<<<EOSQL
    SELECT rd.name, d.amount, d.armor_id from dealarmor_requested d INNER JOIN armordefs rd ON rd.armor_id = d.armor_id WHERE d.deal_id = '{$rs['deal_id']}'
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $askarmor[$rs['deal_id']][] = $rs2;
    }
}
if ($_POST && (($_POST['token_deals'] == "") || ($_POST['token_deals'] != $_SESSION['token_deals']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_deals'] == "")) {
    $_SESSION['token_deals'] = sha1(rand() . $_SESSION['token_deals']);
}
if (!$errors) {
if ($_POST['makedeal']) {
    if (!($nationinfo['economy'] == "State Controlled" && $nationinfo['active_economy'] && $nationinfo['government'] != "Oppression")) {
        $errors[] = "Tamper Data is one of my favorite extensions.";
    } else {
    $sql=<<<EOSQL
    SELECT n.economy, n.nation_id, n.government, u.user_id, u.alliance_id FROM nations n INNER JOIN users u ON n.user_id = u.user_id WHERE n.name = '{$mysql['nationname']}'
EOSQL;
    $rs = onelinequery($sql);
    if (!$rs) {
        $errors[] = "Nation not found.";
    } else if ($rs['stasismode']) {
        $errors[] = "That nation's owner is in stasis.";
    } else if ($nationinfo['government'] == "Authoritarianism" && ($rs['alliance_id'] != $nationinfo['alliance_id'] || !$nationinfo['alliance_id'])) {
        $errors[] = "That nation's owner is not in your alliance, Authoritarian.";
	} else if ($rs['government'] == "Authoritarianism" && ($rs['alliance_id'] != $nationinfo['alliance_id'] || !$nationinfo['alliance_id'])) {
        $errors[] = "That Authoritarian nation is not in your alliance.";
	} else if ($rs['user_id'] == $_SESSION['user_id']) {
		$errors[] = "Use Empire Transfers instead of deals to trade with yourself.";
    } else if ($rs['economy'] == "Free Market") {
        $errors[] = "That Free Market nation is unable to accept deals!";
	} else if ($rs['government'] == "Oppression") {
        $errors[] = "That Oppressive government is unable to accept deals!";
	} else if ($rs['nation_id'] == $_SESSION['nation_id']) {
		$errors[] = "Clop, clop, clop...";
    } else {
		$sql=<<<EOSQL
		INSERT INTO deals (fromnation, tonation) VALUES ('{$_SESSION['nation_id']}', '{$rs['nation_id']}')
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		$newdeal = mysqli_insert_id($GLOBALS['mysqli']);
		header("Location: makedeal.php?deal_id={$newdeal}");
		exit;
    }
	}
}
//Someday I will implement table locking. Eventually.
if ($_POST['acceptdeal']) {
    if (!$deals[$_POST['deal_id']]) {
        $errors[] = "Deal not found!";
    } else {
        $deal = $_POST['deal_id'];
    }
    if ($nationinfo['government'] == "Oppression") {
        $errors[] = "Your Oppressive government cannot accept deals.";
    }
    $sql=<<<EOSQL
	SELECT u.alliance_id, n.name FROM users u INNER JOIN nations n ON u.user_id = n.user_id WHERE n.nation_id = '{$deals[$deal]['fromnation']}'
EOSQL;
	$rs = onelinequery($sql);
    $othernationname = $rs['name'];
    if ($rs['alliance_id'] && $rs['alliance_id'] == $nationinfo['alliance_id']) {
        $samealliance = true;
    }
	if ($nationinfo['government'] == "Authoritarianism" && !$samealliance) {
		$errors[] = "You cannot make deals with someone not in your alliance.";
	}
    if (!$errors) {
		if (($nationinfo['funds'] < $deals[$deal]['amount']) && $deals[$deal]['askingformoney']) {
            $errors[] = "You don't have enough money to do this deal!";
        }
        $sql =<<<EOSQL
        SELECT resource_id, amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}'
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
		$sql =<<<EOSQL
        SELECT weapon_id, amount FROM weapons WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $sth = $GLOBALS['mysqli']->query($sql);
        while ($rs = mysqli_fetch_array($sth)) {
            $ownedweapons[$rs['weapon_id']] = $rs['amount'];
        }
        foreach ($askweapons[$deal] AS $dealweapon) {
            if ($ownedweapons[$dealweapon['weapon_id']] < $dealweapon['amount']) {
                $errors[] = "You don't have enough {$dealweapon['name']} to do this deal!";
            }
        }
		$sql =<<<EOSQL
        SELECT armor_id, amount FROM armor WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $sth = $GLOBALS['mysqli']->query($sql);
        while ($rs = mysqli_fetch_array($sth)) {
            $ownedarmor[$rs['armor_id']] = $rs['amount'];
        }
        foreach ($askarmor[$deal] AS $dealarmor) {
            if ($ownedarmor[$dealarmor['armor_id']] < $dealarmor['amount']) {
                $errors[] = "You don't have enough {$dealarmor['name']} to do this deal!";
            }
        }
			if (!$errors) {
				if ($samealliance) {
                $infos[] =<<<EOFORM
You accepted a deal with <a href="viewnation.php?nation_id={$deals[$deal]['fromnation']}"><span class="text-success">{$othernationname}</span></a>.
EOFORM;
				$messages[] =<<<EOFORM
Your deal with <a href="viewnation.php?nation_id={$_SESSION['nation_id']}"><span class="text-success">{$nationinfo['name']}</span></a> was accepted.
EOFORM;
				} else {
                $infos[] =<<<EOFORM
You accepted a deal with <a href="viewnation.php?nation_id={$deals[$deal]['fromnation']}">{$othernationname}</a>.
EOFORM;
                $messages[] =<<<EOFORM
Your deal with <a href="viewnation.php?nation_id={$_SESSION['nation_id']}">{$nationinfo['name']}</a> was accepted.
EOFORM;
				}
                foreach ($askitems[$deal] AS $dealitem) {
                    $sql = "UPDATE resources SET amount = amount - '{$dealitem['amount']}' WHERE resource_id = '{$dealitem['resource_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
                    $GLOBALS['mysqli']->query($sql);
                    $sql = "INSERT INTO resources (nation_id, resource_id, amount) VALUES ({$deals[$deal]['fromnation']}, {$dealitem['resource_id']}, {$dealitem['amount']})
                    ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
                    $GLOBALS['mysqli']->query($sql);
					$displayamount = commas($dealitem['amount']);
                    $infos[] = "You dealt away {$displayamount} {$dealitem['name']}.";
                    $messages[] = "You received {$displayamount} {$dealitem['name']} as part of your deal.";
                }
				$sql = "DELETE FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND amount = 0";
                $GLOBALS['mysqli']->query($sql);
                foreach ($offeritems[$deal] AS $dealitem) {
                    $sql = "INSERT INTO resources (nation_id, resource_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealitem['resource_id']}, {$dealitem['amount']})
                    ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
                    $GLOBALS['mysqli']->query($sql);
					$displayamount = commas($dealitem['amount']);
                    $infos[] = "You received {$displayamount} {$dealitem['name']}.";
                    $messages[] = "You dealt away {$displayamount} {$dealitem['name']}.";
                }
				foreach ($askweapons[$deal] AS $dealweapon) {
                    $sql = "UPDATE weapons SET amount = amount - '{$dealweapon['amount']}' WHERE weapon_id = '{$dealweapon['weapon_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
                    $GLOBALS['mysqli']->query($sql);
                    $sql = "INSERT INTO weapons (nation_id, weapon_id, amount) VALUES ({$deals[$deal]['fromnation']}, {$dealweapon['weapon_id']}, {$dealweapon['amount']})
                    ON DUPLICATE KEY UPDATE amount = amount + {$dealweapon['amount']}";
                    $GLOBALS['mysqli']->query($sql);
					$displayamount = commas($dealweapon['amount']);
                    $infos[] = "You dealt away {$displayamount} {$dealweapon['name']}.";
                    $messages[] = "You received {$displayamount} {$dealweapon['name']} as part of your deal.";
                }
				$sql = "DELETE FROM weapons WHERE nation_id = '{$_SESSION['nation_id']}' AND amount = 0";
                $GLOBALS['mysqli']->query($sql);
                foreach ($offerweapons[$deal] AS $dealweapon) {
                    $sql = "INSERT INTO weapons (nation_id, weapon_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealweapon['weapon_id']}, {$dealweapon['amount']})
                    ON DUPLICATE KEY UPDATE amount = amount + {$dealweapon['amount']}";
                    $GLOBALS['mysqli']->query($sql);
					$displayamount = commas($dealweapon['amount']);
                    $infos[] = "You received {$displayamount} {$dealweapon['name']}.";
                    $messages[] = "You dealt away {$displayamount} {$dealweapon['name']}.";
                }
				foreach ($askarmor[$deal] AS $dealarmor) {
                    $sql = "UPDATE armor SET amount = amount - '{$dealarmor['amount']}' WHERE armor_id = '{$dealarmor['armor_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
                    $GLOBALS['mysqli']->query($sql);
                    $sql = "INSERT INTO armor (nation_id, armor_id, amount) VALUES ({$deals[$deal]['fromnation']}, {$dealarmor['armor_id']}, {$dealarmor['amount']})
                    ON DUPLICATE KEY UPDATE amount = amount + {$dealarmor['amount']}";
                    $GLOBALS['mysqli']->query($sql);
					$displayamount = commas($dealarmor['amount']);
                    $infos[] = "You dealt away {$displayamount} {$dealarmor['name']}.";
                    $messages[] = "You received {$displayamount} {$dealarmor['name']} as part of your deal.";
                }
				$sql = "DELETE FROM armor WHERE nation_id = '{$_SESSION['nation_id']}' AND amount = 0";
                $GLOBALS['mysqli']->query($sql);
                foreach ($offerarmor[$deal] AS $dealarmor) {
                    $sql = "INSERT INTO armor (nation_id, armor_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealarmor['armor_id']}, {$dealarmor['amount']})
                    ON DUPLICATE KEY UPDATE amount = amount + {$dealarmor['amount']}";
                    $GLOBALS['mysqli']->query($sql);
					$displayamount = commas($dealarmor['amount']);
                    $infos[] = "You received {$displayamount} {$dealarmor['name']}.";
                    $messages[] = "You dealt away {$displayamount} {$dealarmor['name']}.";
                }
                if ($deals[$deal]['amount']) {
					if ($deals[$deal]['askingformoney']) {
						$sql = <<<EOSQL
UPDATE nations SET funds = funds - '{$deals[$deal]['amount']}' WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
						$GLOBALS['mysqli']->query($sql);
						$sql = <<<EOSQL
UPDATE nations SET funds = funds + '{$deals[$deal]['amount']}' WHERE nation_id = '{$deals[$deal]['fromnation']}'
EOSQL;
						$GLOBALS['mysqli']->query($sql);
						$displaybits = commas($deals[$deal]['amount']);
						$infos[] = "You dealt away {$displaybits} bits.";
						$messages[] = "You received {$displaybits} bits as part of your deal.";
					} else {
						//offering nation already paid
						$sql = <<<EOSQL
UPDATE nations SET funds = funds + '{$deals[$deal]['amount']}' WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
						$GLOBALS['mysqli']->query($sql);
						$displaybits = commas($deals[$deal]['amount']);
						$infos[] = "You received {$displaybits} bits.";
						$messages[] = "You dealt away {$displaybits} bits.";
					}
                }
                $messagelist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $messages));
                $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$deals[$deal]['fromnation']}, '{$messagelist}', NOW())";
                $GLOBALS['mysqli']->query($sql);
                $infoslist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $infos));
                $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$_SESSION['nation_id']}, '{$infoslist}', NOW())";
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
                DELETE FROM dealweapons_requested WHERE deal_id = '{$deal}'
EOSQL;
                $GLOBALS['mysqli']->query($sql);
                $sql = <<<EOSQL
                DELETE FROM dealweapons_offered WHERE deal_id = '{$deal}'
EOSQL;
                $GLOBALS['mysqli']->query($sql);
				$sql = <<<EOSQL
                DELETE FROM dealarmor_requested WHERE deal_id = '{$deal}'
EOSQL;
                $GLOBALS['mysqli']->query($sql);
                $sql = <<<EOSQL
                DELETE FROM dealarmor_offered WHERE deal_id = '{$deal}'
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
       $sql = "INSERT INTO resources (nation_id, resource_id, amount) VALUES ({$deals[$deal]['fromnation']}, {$dealitem['resource_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
       $GLOBALS['mysqli']->query($sql);
    }
    foreach ($offerweapons[$deal] AS $dealitem) {
       $sql = "INSERT INTO weapons (nation_id, weapon_id, amount) VALUES ({$deals[$deal]['fromnation']}, {$dealitem['weapon_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
       $GLOBALS['mysqli']->query($sql);
    }
    foreach ($offerarmor[$deal] AS $dealitem) {
       $sql = "INSERT INTO armor (nation_id, armor_id, amount) VALUES ({$deals[$deal]['fromnation']}, {$dealitem['armor_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
       $GLOBALS['mysqli']->query($sql);
    }
    $message = "Your deal with {$nationinfo['name']} was rejected.";
    $mysql['message'] = $GLOBALS['mysqli']->real_escape_string($message);
    $sql = <<<EOSQL
INSERT INTO reports SET report = '{$mysql['message']}', nation_id = '{$deals[$deal]['fromnation']}', time = NOW()
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    if (!$deals[$deal]['askingformoney']) {
    $sql = <<<EOSQL
UPDATE nations SET funds = funds + {$deals[$deal]['amount']} + {$deals[$deal]['paid']} WHERE nation_id = '{$deals[$deal]['fromnation']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    } else {
    $sql = <<<EOSQL
UPDATE nations SET funds = funds + {$deals[$deal]['paid']} WHERE nation_id = '{$deals[$deal]['fromnation']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    }
    $sql = <<<EOSQL
    DELETE FROM dealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealarmor_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealweapons_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealarmor_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealweapons_offered WHERE deal_id = '{$deal}'
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
       $sql = "INSERT INTO resources (nation_id, resource_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealitem['resource_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
       $GLOBALS['mysqli']->query($sql);
    }
    foreach ($offerweapons[$deal] AS $dealitem) {
       $sql = "INSERT INTO weapons (nation_id, weapon_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealitem['weapon_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
       $GLOBALS['mysqli']->query($sql);
    }
    foreach ($offerarmor[$deal] AS $dealitem) {
       $sql = "INSERT INTO armor (nation_id, armor_id, amount) VALUES ({$_SESSION['nation_id']}, {$dealitem['armor_id']}, {$dealitem['amount']})
       ON DUPLICATE KEY UPDATE amount = amount + {$dealitem['amount']}";
       $GLOBALS['mysqli']->query($sql);
    }
    if (!$outgoingdeals[$deal]['askingformoney']) {
    $sql = <<<EOSQL
UPDATE nations SET funds = funds + {$outgoingdeals[$deal]['amount']} + {$outgoingdeals[$deal]['paid']} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    } else {
    $sql = <<<EOSQL
UPDATE nations SET funds = funds + {$outgoingdeals[$deal]['paid']} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    }
    $sql = <<<EOSQL
    DELETE FROM dealitems_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealarmor_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealweapons_requested WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealitems_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealarmor_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM dealweapons_offered WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM deals WHERE deal_id = '{$deal}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
	$infos[] = "Your deal with {$outgoingdeals[$deal]['name']} was canceled.";
    unset($outgoingdeals[$deal]);
	}
}
}
?>