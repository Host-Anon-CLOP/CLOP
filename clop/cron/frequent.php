<?php
date_default_timezone_set("UTC");
set_time_limit(60);
$mysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_clop");
$reportid = "report" . time();
function onelinequery($sql) {
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        return mysqli_fetch_array($sth);
    } else {
        return false;
    }
}
function commas($nm) {
    for ($done=strlen($nm); $done > 3;$done -= 3) {
        $returnNum = ",".substr($nm,$done-3,3).$returnNum;
    }
    return substr($nm,0,$done).$returnNum;
}
function chooseword($current, $alteration) {
    if ($current >= 0) {
        if ($alteration > 0) {
            return "improved";
        } else {
            return "dwindled";
        }
    } else {
        if ($alteration > 0) {
            return "recovered";
        } else {
            return "worsened";
        }
    }
}

function affectempirerelations($nation_id, $startingrelation, $relationeffect, $affector, $empire) {
    if ($empire == "Solar Empire") {
        $dbempire = "se_relation";
    } else if ($empire == "New Lunar Republic") {
        $dbempire = "nlr_relation";
    } else {
        return "Serious empire problem - Report this bug!";
    }
    $chosenword = chooseword($startingrelation, $relationeffect);
    $sql = "UPDATE nations SET {$dbempire} = {$dbempire} + {$relationeffect} WHERE nation_id = {$nation_id}";
    $GLOBALS['mysqli']->query($sql);
    return "Your relationship with the {$empire} has {$chosenword} due to your {$affector}. ({$relationeffect})";
}

function affectsatisfaction($nation_id, $startingrelation, $relationeffect, $affector, $government = "") {
    $chosenword = chooseword($startingrelation, $relationeffect);
    $sql = "UPDATE nations SET satisfaction = satisfaction + {$relationeffect} WHERE nation_id = {$nation_id}";
    $GLOBALS['mysqli']->query($sql);
    return "Your population's satisfaction has {$chosenword} due to your {$affector}. ({$relationeffect})";
}
$resourceeffects = array();
$resourcerequirements = array();
$sql = "SELECT * FROM resourcedefs";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    foreach ($rs as $key => $value) {
        $resourceinfo[$rs['resource_id']][$key] = $value;
        $resourceeffects[$rs['resource_id']] = array();
        $resourcerequirements[$rs['resource_id']] = array();
    }
}
$sql = "SELECT * FROM resourceeffects";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourceeffects[$rs['resource_id']][$rs['affectedresource_id']] = $rs['amount'];
}
$sql = "SELECT * FROM resourcerequirements";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourcerequirements[$rs['resource_id']][$rs['requiredresource_id']] = $rs['amount'];
}
$sql=<<<EOSQL
SELECT COUNT(*) AS empiresize, user_id FROM nations GROUP BY user_id
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $empireusers[$rs['user_id']] = $rs['empiresize'];
}
$sql = "SELECT n.*, u.* FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE u.stasismode = 0";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $beginningsat = $rs['satisfaction'];
	$beginningse = $rs['se_relation'];
	$beginningnlr = $rs['nlr_relation'];
    $oversatloss = 0;
    $empiresatloss = 0;
	$serelationeffect = 0;
	$nlrrelationeffect = 0;
    $serelationneg = 0;
    $nlrrelationneg = 0;
	$sejealousy = 0;
	$nlrjealousy = 0;
    $messages = array();
    $ownedresources = array();
    $buildings = 0;
    $gdp = 50000;
    $disabled = 0;
    $envirodamage = 0;
    $envirocleaners = 0;
	if ($rs['funds'] > 500000000) {
		$tax = ceil(($rs['funds'] - 500000000)/500);
		$displaytax = commas($tax);
		$messages[] = "Inflation has taken away {$displaytax} bits.";
		$sql=<<<EOSQL
UPDATE nations SET funds = funds - {$tax}
WHERE nation_id = {$rs['nation_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	}
    if (date("G") == 0) {
        $sql=<<<EOSQL
UPDATE nations SET age = age + 1
WHERE nation_id = {$rs['nation_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
    }
    if ($rs['government'] == "Transponyism") {
        $satmultiplier = 7;
    } else if ($rs['government'] == "Alicorn Elite") {
        $satmultiplier = 5;
    } else if ($rs['government'] == "Independence") {
        $satmultiplier = 2.5;
    } else if ($rs['government'] == "Decentralization") {
        $satmultiplier = 2;
    } else if ($rs['government'] == "Democracy") {
        $satmultiplier = 1.5;
    } else if ($rs['government'] == "Solar Vassal" || $rs['government'] == "Lunar Client") {
        $satmultiplier = 1.25;
    } else {
        $satmultiplier = 1;
    }
    //nested ifs are just easier to edit here
    if ($rs['satisfaction'] > (250 * $satmultiplier)) {
        $oversatloss += floor(($rs['satisfaction'] - (250 * $satmultiplier)) / (50 * $satmultiplier));
        $satdescription = "A satisfied population";
        if ($rs['satisfaction'] > (500 * $satmultiplier)) {
            $oversatloss += floor(($rs['satisfaction'] - (500 * $satmultiplier)) / (50 * $satmultiplier));
            $satdescription = "A very satisfied population";
            if ($rs['satisfaction'] > (750 * $satmultiplier)) {
                $oversatloss += floor(($rs['satisfaction'] - (750 * $satmultiplier)) / (50 * $satmultiplier));
                $satdescription = "A loving population";
            }
        }
    }
    if ($oversatloss) {
        $rs['satisfaction'] -= $oversatloss;
        $messages[] = "{$satdescription} is hard to keep. (-{$oversatloss} sat)";
        $sql = "UPDATE nations SET satisfaction = satisfaction - {$oversatloss} WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
    }
    if ($empireusers[$rs['user_id']] > 1) {
        $empiresatloss = pow(($empireusers[$rs['user_id']] - 1), 2) * 20;
        if ($rs['government'] == "Oppression" || $rs['government'] == "Alicorn Elite" || $rs['government'] == "Transponyism") {
            $empiresatloss = ceil($empiresatloss / 3);
        }
        $messages[] = "You lose {$empiresatloss} sat for having an empire of {$empireusers[$rs['user_id']]} nations.";
        $sql = "UPDATE nations SET satisfaction = satisfaction - {$empiresatloss} WHERE nation_id = {$rs['nation_id']}";
        $rs['satisfaction'] -= $empiresatloss;
        $GLOBALS['mysqli']->query($sql);
    }
	if ($rs['se_relation'] > 250) {
        $serelationeffect += floor(($rs['se_relation'] - 250) / 50);
        $sedescription = "A good friend";
        if ($rs['se_relation'] > 400) {
            $serelationeffect += floor(($rs['se_relation'] - 400) / 50);
            $sedescription = "A very good friend";
            if ($rs['se_relation'] > 800) {
                $serelationeffect += floor(($rs['se_relation'] - 800) / 50);
                $sedescription = "An extremely good friend";
            }
        }
    }
	if ($rs['nlr_relation'] > 250) {
        $nlrrelationeffect += floor(($rs['nlr_relation'] - 250) / 50);
        $nlrdescription = "A good friend";
        if ($rs['nlr_relation'] > 400) {
            $nlrrelationeffect += floor(($rs['nlr_relation'] - 400) / 50);
            $nlrdescription = "A very good friend";
            if ($rs['nlr_relation'] > 800) {
                $nlrrelationeffect += floor(($rs['nlr_relation'] - 800) / 50);
                $nlrdescription = "An extremely good friend";
            }
        }
    }
	if ($serelationeffect) {
        $rs['se_relation'] -= $serelationeffect;
        $messages[] = "{$sedescription} is hard to keep; you lose {$serelationeffect} relationship with the Solar Empire.";
        $sql = "UPDATE nations SET se_relation = se_relation - {$serelationeffect} WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
    }
	if ($nlrrelationeffect) {
        $rs['nlr_relation'] -= $nlrrelationeffect;
        $messages[] = "{$nlrdescription} is hard to keep; you lose {$nlrrelationeffect} relationship with the New Lunar Republic.";
        $sql = "UPDATE nations SET nlr_relation = nlr_relation - {$nlrrelationeffect} WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
    }
    // using -= to get a positive number
	if (!$rs['empiremax']) {
		if ($rs['se_relation'] < -450) {
			$serelationneg -= ceil(($rs['se_relation'] + 450) / 50);
			$sedescription = "A bad enemy";
			if ($rs['se_relation'] < -700) {
				$serelationeffect -= ceil(($rs['se_relation'] + 700) / 50);
				$sedescription = "A very bad enemy";
				if ($rs['se_relation'] < -900) {
					$serelationeffect -= ceil(($rs['se_relation'] + 900) / 50);
					$sedescription = "An extremely bad enemy";
				}
			}
		}
		if ($rs['nlr_relation'] < -450) {
			$nlrrelationneg -= ceil(($rs['nlr_relation'] + 450) / 50);
			$nlrdescription = "A bad enemy";
			if ($rs['nlr_relation'] < -700) {
				$nlrrelationneg -= ceil(($rs['nlr_relation'] + 700) / 50);
				$nlrdescription = "A very bad enemy";
				if ($rs['nlr_relation'] < -900) {
					$nlrrelationneg -= ceil(($rs['nlr_relation'] + 900) / 50);
					$nlrdescription = "An extremely bad enemy";
				}
			}
		}
		if ($serelationneg) {
			$rs['se_relation'] += $serelationneg;
			$messages[] = "{$sedescription} forgets eventually; you gain {$serelationneg} relationship with the Solar Empire.";
			$sql = "UPDATE nations SET se_relation = se_relation + {$serelationneg} WHERE nation_id = {$rs['nation_id']}";
			$GLOBALS['mysqli']->query($sql);
		}
		if ($nlrrelationneg) {
			$rs['nlr_relation'] += $nlrrelationneg;
			$messages[] = "{$nlrdescription} forgets eventually; you gain {$nlrrelationneg} relationship with the New Lunar Republic.";
			$sql = "UPDATE nations SET nlr_relation = nlr_relation + {$nlrrelationneg} WHERE nation_id = {$rs['nation_id']}";
			$GLOBALS['mysqli']->query($sql);
		}
	}
	if ($rs['se_relation'] > 0 && floor($rs['se_relation'] / 50)) {
		$nlrjealousy = floor($rs['se_relation'] / 50);
	}
	if ($rs['nlr_relation'] > 0 && floor($rs['nlr_relation'] / 50)) {
		$sejealousy = floor($rs['nlr_relation'] / 50);
	}
	if ($sejealousy) {
		$messages[] = "The Solar Empire doesn't like your good relations with the New Lunar Republic. (-{$sejealousy})";
        $sql = "UPDATE nations SET se_relation = se_relation - {$sejealousy} WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
		$rs['se_relation'] -= $sejealousy;
	}
	if ($nlrjealousy) {
		$messages[] = "The New Lunar Republic doesn't like your good relations with the Solar Empire. (-{$nlrjealousy})";
        $sql = "UPDATE nations SET nlr_relation = nlr_relation - {$nlrjealousy} WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
		$rs['nlr_relation'] -= $nlrjealousy;
	}
    $sql = "SELECT * from resources WHERE nation_id = {$rs['nation_id']}";
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $ownedresources[$rs2['resource_id']] = $rs2['amount'] - $rs2['disabled'];
        $disabled += $rs2['disabled'];
    }
    if ($disabled > 0) {
        $sql = "UPDATE nations SET satisfaction = satisfaction - {$disabled} WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] -= $disabled;
        $messages[] = "You lose {$disabled} satisfaction for having {$disabled} disabled buildings.";
    }
    if (!empty($ownedresources)) {
    foreach ($ownedresources as $checkingresource => $amount) {
        if ($amount) {
        if ($resourceinfo[$checkingresource]['is_building'] && $checkingresource != 78 && $checkingresource != 79) {
            $buildings += $amount;
        }
        if ($resourceinfo[$checkingresource]['bad_min'] && ($amount > $resourceinfo[$checkingresource]['bad_min'])) {
            $satloss = ceil(pow(($amount - $resourceinfo[$checkingresource]['bad_min']), 2) / $resourceinfo[$checkingresource]['bad_div']);
            $sql = "UPDATE nations SET satisfaction = satisfaction - {$satloss} WHERE nation_id = {$rs['nation_id']}";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Too many {$resourceinfo[$checkingresource]['name']} cause environmental damage! (-{$satloss} sat)";
            $rs['satisfaction'] -= $satloss;
            $envirodamage += $satloss;
        }
        $hasenough = true;
		if ($resourcerequirements[$checkingresource]) {
        foreach ($resourcerequirements[$checkingresource] as $needsthis => $requirement) {
            if ($ownedresources[$needsthis] < $requirement * $amount) {
                if ($hasenough) {
                $messages[] = "You don't have enough {$resourceinfo[$needsthis]['name']} to run your {$amount} {$resourceinfo[$checkingresource]['name']}! (-{$amount} sat)";
                $sql = "UPDATE nations SET satisfaction = satisfaction - {$amount} WHERE nation_id = {$rs['nation_id']}";
                $GLOBALS['mysqli']->query($sql);
                $rs['satisfaction'] -= $amount;
                } else {
                $messages[] = "You don't have enough {$resourceinfo[$needsthis]['name']} to run your {$amount} {$resourceinfo[$checkingresource]['name']}!";
                }
                $hasenough = false;
            }
        }
		}
        if ($hasenough && $amount) {
            if ($checkingresource == 44 || $checkingresource == 45) {
                $envirocleaners += $amount;
            }
            if ($resourceinfo[$checkingresource]['se_relation']) {
                $relationeffect = $resourceinfo[$checkingresource]['se_relation'] * $amount;
                $messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], $relationeffect, "{$amount} {$resourceinfo[$checkingresource]['name']}", "Solar Empire");
                $rs['se_relation'] += $relationeffect;
            }
            if ($resourceinfo[$checkingresource]['nlr_relation']) {
                $relationeffect = $resourceinfo[$checkingresource]['nlr_relation'] * $amount;
                $messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], $relationeffect, "{$amount} {$resourceinfo[$checkingresource]['name']}", "New Lunar Republic");
                $rs['nlr_relation'] += $relationeffect;
            }
            if ($resourceinfo[$checkingresource]['satisfaction']) {
                $relationeffect = $resourceinfo[$checkingresource]['satisfaction'] * $amount;
                $messages[] = affectsatisfaction($rs['nation_id'], $rs['satisfaction'], $relationeffect, "{$amount} {$resourceinfo[$checkingresource]['name']}", $rs['government']);
                $rs['satisfaction'] += $relationeffect;
            }
            if ($resourceinfo[$checkingresource]['gdp']) {
                $gdp += $resourceinfo[$checkingresource]['gdp'] * $amount;
            }
            //I don't like having to update both the local array and the database, but there don't seem to be many good ways of doing this...
			if ($resourceeffects[$checkingresource]) {
            foreach ($resourceeffects[$checkingresource] as $key => $value) {
                $addedamount = $value * $amount;
                $messages[] = "You gained {$addedamount} {$resourceinfo[$key]['name']} from your {$amount} {$resourceinfo[$checkingresource]['name']}.";
                $sql = "INSERT INTO resources(nation_id, resource_id, amount) VALUES ({$rs['nation_id']}, {$key}, {$addedamount}) ON DUPLICATE KEY UPDATE amount = amount + {$addedamount}";
                $GLOBALS['mysqli']->query($sql);
            }
			}
			if ($resourcerequirements[$checkingresource]) {
            foreach ($resourcerequirements[$checkingresource] as $needsthis => $requirement) {
				$usedamount = $requirement * $amount;
				$messages[] = "Your {$amount} {$resourceinfo[$checkingresource]['name']} used up {$usedamount} {$resourceinfo[$needsthis]['name']}.";
				$sql = "UPDATE resources SET amount = amount - '{$usedamount}' WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '{$needsthis}'";
				$GLOBALS['mysqli']->query($sql);
				$ownedresources[$needsthis] -= $usedamount;
            }
			}
        }
    }
		if ($amount > 50000) {
			$tax = ceil(($amount - 50000)/500);
			$sql = "UPDATE resources SET amount = amount - {$tax} WHERE nation_id = {$rs['nation_id']} AND resource_id = {$checkingresource}";
            $GLOBALS['mysqli']->query($sql);
			$messages[] = "As you have more than 50,000 {$resourceinfo[$checkingresource]['name']}, {$tax} was siphoned off.";
		}
    }
    if ($envirocleaners) {
		$fixeddamage = $envirodamage - ceil($envirodamage * pow(.9, $envirocleaners));
		$sql = "UPDATE nations SET satisfaction = satisfaction + {$fixeddamage} WHERE nation_id = {$rs['nation_id']}";
		$rs['satisfaction'] += $fixeddamage;
		$GLOBALS['mysqli']->query($sql);
		$messages[] = "Some of the environmental damage has been repaired. ({$fixeddamage} sat)";
    }
    }
    $sql = "SELECT w.weapon_id, w.amount, wd.name FROM weapons w INNER JOIN weapondefs wd ON wd.weapon_id = w.weapon_id WHERE w.nation_id = {$rs['nation_id']}";
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        if ($rs2['amount'] > 1000) {
            $tax = ceil(($rs2['amount'] - 1000)/500);
            $sql = "UPDATE weapons SET amount = amount - {$tax} WHERE nation_id = {$rs['nation_id']} AND weapon_id = {$rs2['weapon_id']}";
            $GLOBALS['mysqli']->query($sql);
			$messages[] = "As you have more than 1,000 {$rs2['name']}, {$tax} were siphoned off.";
        }
    }
    $sql = "SELECT a.armor_id, a.amount, ad.name FROM armor a INNER JOIN armordefs ad ON ad.armor_id = a.armor_id WHERE a.nation_id = {$rs['nation_id']}";
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        if ($rs2['amount'] > 1000) {
            $tax = ceil(($rs2['amount'] - 1000)/500);
            $sql = "UPDATE armor SET amount = amount - {$tax} WHERE nation_id = {$rs['nation_id']} AND armor_id = {$rs2['armor_id']}";
            $GLOBALS['mysqli']->query($sql);
			$messages[] = "As you have more than 1,000 {$rs2['name']}, {$tax} were siphoned off.";
        }
    }
    if (date("G") == 0 || date("G") == 12) {
	$sql=<<<EOSQL
	SELECT size, type, name, force_id FROM forces WHERE nation_id = '{$rs['nation_id']}'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
		switch ($rs2['type']) {
		case 1:
			if ($ownedresources['3'] < ($rs2['size'] * 5)) { //apples
				$sql=<<<EOSQL
				DELETE FROM forces WHERE force_id = '{$rs2['force_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$messages[] = "You couldn't pay the upkeep for your {$rs2['name']} and it's gone!";
			} else {
                $usedapples = $rs2['size'] * 5;
				$sql = "UPDATE resources SET amount = amount - {$usedapples} WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '3'";
				$GLOBALS['mysqli']->query($sql);
				$ownedresources['3'] -= $usedapples;
				$messages[] = "Your {$rs2['name']} used up {$usedapples} apples.";
			}
			break;
		case 2:
			if ($ownedresources['25'] < ($rs2['size'] * 5)) { //gasoline
				$sql=<<<EOSQL
				DELETE FROM forces WHERE force_id = '{$rs2['force_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$messages[] = "You couldn't pay the upkeep for your {$rs2['name']} and it's gone!";
			} else {
                $usedgas = $rs2['size'] * 5;
				$sql = "UPDATE resources SET amount = amount - {$usedgas} WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
				$GLOBALS['mysqli']->query($sql);
				$ownedresources['25'] -= $usedgas;
				$messages[] = "Your {$rs2['name']} used up {$usedgas} gasoline.";
			}
			break;
		case 3:
			if ($ownedresources['20'] < ($rs2['size'] * 5)) { //coffee
				$sql=<<<EOSQL
				DELETE FROM forces WHERE force_id = '{$rs2['force_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$messages[] = "You couldn't pay the upkeep for your {$rs2['name']} and it's gone!";
			} else {
                $usedcoffee = $rs2['size'] * 5;
				$sql = "UPDATE resources SET amount = amount - {$usedcoffee} WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '20'";
				$GLOBALS['mysqli']->query($sql);
				$ownedresources['20'] -= $usedcoffee;
				$messages[] = "Your {$rs2['name']} used up {$usedcoffee} coffee.";
			}
			break;
		case 4:
			if ($ownedresources['26'] < ($rs2['size'] * 5)) { //gems
				$sql=<<<EOSQL
				DELETE FROM forces WHERE force_id = '{$rs2['force_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$messages[] = "You couldn't pay the upkeep for your {$rs2['name']} and it's gone!";
			} else {
                $usedgems = $rs2['size'] * 5;
				$sql = "UPDATE resources SET amount = amount - {$usedgems} WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '26'";
				$GLOBALS['mysqli']->query($sql);
				$ownedresources['26'] -= $usedgems;
				$messages[] = "Your {$rs2['name']} used up {$usedgems} gems.";
			}
			break;
		case 5:
			if ($ownedresources['25'] < ($rs2['size'] * 5)) { //gasoline
				$sql=<<<EOSQL
				DELETE FROM forces WHERE force_id = '{$rs2['force_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$messages[] = "You couldn't pay the upkeep for your {$rs2['name']} and it's gone!";
			} else {
                $usedgas = $rs2['size'] * 5;
				$sql = "UPDATE resources SET amount = amount - {$usedgas} WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
				$GLOBALS['mysqli']->query($sql);
				$ownedresources['25'] -= $usedgas;
				$messages[] = "Your {$rs2['name']} used up {$usedgas} gasoline.";
			}
			break;
		case 6:
			if ($ownedresources['26'] < ($rs2['size'] * 10)) { //gems
				$sql=<<<EOSQL
				DELETE FROM forces WHERE force_id = '{$rs2['force_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$messages[] = "You couldn't pay the upkeep for your {$rs2['name']} and it's gone!";
			} else {
                $usedgems = $rs2['size'] * 10;
				$sql = "UPDATE resources SET amount = amount - {$usedgems} WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '26'";
				$GLOBALS['mysqli']->query($sql);
				$ownedresources['26'] -= $usedgems;
				$messages[] = "Your {$rs2['name']} used up {$usedgems} gems.";
			}
			break;
		default:
		break;
		}
	}
    }
    if ($ownedresources['75'] >= 200) {
		$forbiddenmessage =<<<EOFORM
You have completed the forbidden research, and the facility has been automatically dismantled. A new Major Action is available to you.
EOFORM;
        $messages[] = $forbiddenmessage;
        $mysqlforbiddenmessage = $GLOBALS['mysqli']->real_escape_string($forbiddenmessage);
        $sql = "INSERT INTO messages (fromuser, touser, message, fromdeleted, sent) VALUES(0, {$rs['user_id']}, '{$mysqlforbiddenmessage}', 1, NOW())";
		$GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
		DELETE FROM resources WHERE (resource_id = 74 OR resource_id = 75) AND nation_id = '{$rs['nation_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
        UPDATE users SET seesecrets = 1 WHERE user_id = '{$rs['user_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    }
    if ($rs['government'] == "Independence") {
        if ($ownedresources['25'] < 40 || $ownedresources['9'] < 4) {
            $messages[] = "Your Independence lacks the gasoline and vehicle parts to function properly! (-100 sat)";
            $sql ="UPDATE nations SET satisfaction = satisfaction - 100 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
            $rs['satisfaction'] -= 100;
        } else {
            $sql = "UPDATE resources SET amount = amount - 40 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your Independence used 40 gasoline.";
            $sql = "UPDATE resources SET amount = amount - 4 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '9'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your Independence used 4 vehicle parts.";
            $messages[] = affectsatisfaction($rs['nation_id'], $rs['satisfaction'], 50, "Independence", $rs['government']);
            $rs['satisfaction'] += 50;
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], -3, "Independence", "Solar Empire");
		$rs['se_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], 6, "Independence", "New Lunar Republic");
		$rs['nlr_relation'] += 6;
    } else if ($rs['government'] == "Decentralization") {
        if ($ownedresources['25'] < 50 || $ownedresources['9'] < 5) {
            $messages[] = "Your decentralized government lacks the gasoline and vehicle parts to function properly! (-100 sat)";
            $sql ="UPDATE nations SET satisfaction = satisfaction - 100 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
            $rs['satisfaction'] -= 100;
        } else {
            $sql = "UPDATE resources SET amount = amount - 50 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your decentralized government used 50 gasoline.";
            $sql = "UPDATE resources SET amount = amount - 5 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '9'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your decentralized government used 5 vehicle parts.";
            $messages[] = affectsatisfaction($rs['nation_id'], $rs['satisfaction'], 30, "Decentralization", $rs['government']);
            $rs['satisfaction'] += 30;
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], -3, "Decentralization", "Solar Empire");
		$rs['se_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], 4, "Decentralization", "New Lunar Republic");
		$rs['nlr_relation'] += 4;
    } else if ($rs['government'] == "Democracy") {
        if ($ownedresources['25'] < 20 || $ownedresources['9'] < 2) {
            $messages[] = "Your government lacks the gasoline and vehicle parts to function properly! (-20 sat)";
            $sql ="UPDATE nations SET satisfaction = satisfaction - 20 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
            $rs['satisfaction'] -= 20;
        } else {
            $sql = "UPDATE resources SET amount = amount - 20 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your Democracy used 20 gasoline.";
            $sql = "UPDATE resources SET amount = amount - 2 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '9'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your Democracy used 2 vehicle parts.";
            $messages[] = affectsatisfaction($rs['nation_id'], $rs['satisfaction'], 15, "Democracy", $rs['government']);
            $rs['satisfaction'] += 15;
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], -3, "Democracy", "Solar Empire");
		$rs['se_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], 2, "Democracy", "New Lunar Republic");
		$rs['nlr_relation'] += 2;
    } else if ($rs['government'] == "Repression") {
        if ($ownedresources['25'] < 10) {
            $messages[] = "Your government lacks the gasoline to function properly! (-50 sat)";
            $sql ="UPDATE nations SET satisfaction = satisfaction - 50 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
            $rs['satisfaction'] -= 50;
        } else {
            $sql = "UPDATE resources SET amount = amount - 10 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your machinery of Repression used 10 gasoline.";
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], -3, "Repression", "New Lunar Republic");
		$rs['nlr_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], 2, "Repression", "Solar Empire");
		$rs['se_relation'] += 2;
    } else if ($rs['government'] == "Authoritarianism") {
        if ($ownedresources['25'] < 10 || $ownedresources['10'] < 3) {
            $messages[] = "Your government lacks the gasoline and machinery parts to function properly! (-400 sat)";
            $sql ="UPDATE nations SET satisfaction = satisfaction - 400 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
            $rs['satisfaction'] -= 300;
        } else {
            $sql = "UPDATE resources SET amount = amount - 10 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
            $GLOBALS['mysqli']->query($sql);
            $sql = "UPDATE resources SET amount = amount - 3 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '10'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your Authoritarian government used 10 gasoline and 3 machinery parts.";
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], -3, "Authoritarianism", "New Lunar Republic");
		$rs['nlr_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], 4, "Authoritarianism", "Solar Empire");
		$rs['se_relation'] += 4;
    } else if ($rs['government'] == "Oppression") {
        if ($ownedresources['25'] < 10 || $ownedresources['10'] < 5) {
            $messages[] = "Your government lacks the gasoline and machinery parts to function properly! (-500 sat)";
            $sql ="UPDATE nations SET satisfaction = satisfaction - 500 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
            $rs['satisfaction'] -= 300;
        } else {
            $sql = "UPDATE resources SET amount = amount - 10 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '25'";
            $GLOBALS['mysqli']->query($sql);
            $sql = "UPDATE resources SET amount = amount - 5 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '10'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your machinery of Oppression used 10 gasoline and 5 machinery parts.";
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], -3, "Oppression", "New Lunar Republic");
		$rs['nlr_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], 6, "Oppression", "Solar Empire");
		$rs['se_relation'] += 6;
    } else if ($rs['government'] == "Lunar Client") {
        $messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], 60, "Lunar Client status", "New Lunar Republic");
        $rs['nlr_relation'] += 60;
    } else if ($rs['government'] == "Solar Vassal") {
        $messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], 60, "Solar Vassal status", "Solar Empire");
        $rs['se_relation'] += 60;
    }
    if ($rs['economy'] == "State Controlled") {
        if ($ownedresources['18'] < 6) { // cider
            $messages[] = "Your economy lacks the cider to function properly! (-25 sat, unable to make deals)";
            $sql ="UPDATE nations SET satisfaction = satisfaction - 25, active_economy = 0 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
        } else {
            $sql = "UPDATE resources SET amount = amount - 6 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '18'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your State Controllers drank 6 cider.";
            $sql = "UPDATE nations SET active_economy = 1 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], -3, "State Controlled economy", "New Lunar Republic");
		$rs['nlr_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], 1, "State Controlled economy", "Solar Empire");
		$rs['se_relation'] += 1;
    } else if ($rs['economy'] == "Free Market") {
        if ($ownedresources['20'] < 6) { // coffee
            $messages[] = "Your economy lacks the coffee to function properly! (-25 sat, trading efficiency eliminated)";
            $sql = "UPDATE nations SET satisfaction = satisfaction - 25, active_economy = 0 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
        } else {
            $sql = "UPDATE resources SET amount = amount - 6 WHERE nation_id = '{$rs['nation_id']}' AND resource_id = '20'";
            $GLOBALS['mysqli']->query($sql);
            $messages[] = "Your Free Marketeers drank 6 coffee.";
            $sql = "UPDATE nations SET active_economy = 1 WHERE nation_id = '{$rs['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
        }
		$messages[] = affectempirerelations($rs['nation_id'], $rs['se_relation'], -3, "Free Market", "Solar Empire");
		$rs['se_relation'] -= 3;
		$messages[] = affectempirerelations($rs['nation_id'], $rs['nlr_relation'], 1, "Free Market", "New Lunar Republic");
		$rs['nlr_relation'] += 1;
    }
    if (!$buildings) {
        $messages[] = "You lost 5 satisfaction for not having any buildings!";
        $rs['satisfaction'] -= 5;
        $sql = "UPDATE nations SET satisfaction = satisfaction - 5 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
    }
	$sql=<<<EOSQL
    SELECT sum(size) AS totalsize FROM forces WHERE nation_id = '{$rs['nation_id']}'
EOSQL;
	$rs2 = onelinequery($sql);
	if ($rs2['totalsize'] > 20) {
		$satpenalty = ceil(($rs2['totalsize'] - 20)/2);
		$sql = "UPDATE nations SET satisfaction = satisfaction - {$satpenalty} WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] -= $satpenalty;
		$messages[] = "You lost {$satpenalty} satisfaction for having a military of total size {$rs2['totalsize']}.";
	}
    if ($rs['government'] == "Transponyism") {
      if ($rs['satisfaction'] > 7000) {
        $amountlost = $rs['satisfaction'] - 7000;
        $messages[] = "You hit the Transponyism satisfaction cap of 7000. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 7000 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 7000;
     }
    } else if ($rs['government'] == "Alicorn Elite") {
      if ($rs['satisfaction'] > 5000) {
        $amountlost = $rs['satisfaction'] - 5000;
        $messages[] = "You hit the Alicorn Elite satisfaction cap of 5000. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 5000 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 5000;
     }
    } else if ($rs['government'] == "Independence") {
      if ($rs['satisfaction'] > 2500) {
        $amountlost = $rs['satisfaction'] - 2500;
        $messages[] = "You hit the Independence satisfaction cap of 2500. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 2500 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 2500;
     }
    } else if ($rs['government'] == "Decentralization") {
      if ($rs['satisfaction'] > 2000) {
        $amountlost = $rs['satisfaction'] - 2000;
        $messages[] = "You hit the Decentralization satisfaction cap of 2000. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 2000 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 2000;
      }
    } else if ($rs['government'] == "Democracy") {
      if ($rs['satisfaction'] > 1500) {
        $amountlost = $rs['satisfaction'] - 1500;
        $messages[] = "You hit the Democracy satisfaction cap of 1500. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 1500 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 1500;
     }
    } else if ($rs['government'] == "Solar Vassal") {
      if ($rs['satisfaction'] > 1250) {
        $amountlost = $rs['satisfaction'] - 1250;
        $messages[] = "You hit the Solar Vassal satisfaction cap of 1250. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 1250 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 1250;
     }
    } else if ($rs['government'] == "Lunar Client") {
      if ($rs['satisfaction'] > 1250) {
        $amountlost = $rs['satisfaction'] - 1250;
        $messages[] = "You hit the Lunar Client satisfaction cap of 1250. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 1250 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 1250;
     }
    } else {
       if ($rs['satisfaction'] > 1000) {
        $amountlost = $rs['satisfaction'] - 1000;
        $messages[] = "You hit the satisfaction cap of 1000. (-{$amountlost})";
        $sql = "UPDATE nations SET satisfaction = 1000 WHERE nation_id = {$rs['nation_id']}";
        $GLOBALS['mysqli']->query($sql);
        $rs['satisfaction'] = 1000;
      }
    }
	if ($rs['se_relation'] > 1000) {
     $amountlost = $rs['se_relation'] - 1000;
     $messages[] = "You hit the Solar Empire relationship cap of 1000. (-{$amountlost})";
     $sql = "UPDATE nations SET se_relation = 1000 WHERE nation_id = {$rs['nation_id']}";
     $GLOBALS['mysqli']->query($sql);
     $rs['se_relation'] = 1000;
    }
	if ($rs['nlr_relation'] > 1000) {
     $amountlost = $rs['nlr_relation'] - 1000;
     $messages[] = "You hit the New Lunar Republic relationship cap of 1000. (-{$amountlost})";
     $sql = "UPDATE nations SET nlr_relation = 1000 WHERE nation_id = {$rs['nation_id']}";
     $GLOBALS['mysqli']->query($sql);
     $rs['nlr_relation'] = 1000;
    }
	if (!$rs['empiremax']) {
		if ($rs['se_relation'] < -1000) {
			$amountgained = 0 - ($rs['se_relation'] + 1000);
			$messages[] = "Even for the Solar Empire, there are limits to hate. (+{$amountgained})";
			$sql = "UPDATE nations SET se_relation = -1000 WHERE nation_id = {$rs['nation_id']}";
			$GLOBALS['mysqli']->query($sql);
			$rs['se_relation'] = -1000;
		}
		if ($rs['nlr_relation'] < -1000) {
			$amountgained = 0 - ($rs['nlr_relation'] + 1000);
			$messages[] = "Even for the New Lunar Republic, there are limits to hate. (+{$amountgained})";
			$sql = "UPDATE nations SET nlr_relation = -1000 WHERE nation_id = {$rs['nation_id']}";
			$GLOBALS['mysqli']->query($sql);
			$rs['nlr_relation'] = -1000;
		}
	}
	if ($rs['government'] == "Transponyism") {
		$gdp += $gdp * 7;
	} else if ($rs['government'] == "Alicorn Elite") {
		$gdp += $gdp * 5;
	} else if ($rs['government'] == "Authoritarianism") {
		$gdp += $gdp * 1.5;
	} else if ($rs['government'] == "Oppression") {
		$gdp += $gdp * 2;
	} else if ($rs['government'] == "Repression") {
		$gdp += $gdp;
    } else {
    $gdpchange = $gdp * ($rs['satisfaction']/1000);
    $gdp += $gdpchange;
    }
    $messagelist = implode("<br/>", $messages);
	$satdifference = $rs['satisfaction'] - $beginningsat;
	if (!$rs['empiremax']) {
	$sedifference = $rs['se_relation'] - $beginningse;
	$nlrdifference = $rs['nlr_relation'] - $beginningnlr;
	$empiredifferences =<<<EOFORM
	Change in SE Relation: {$sedifference}<br/>
	Change in NLR Relation: {$nlrdifference}<br/>
EOFORM;
	} else {
	$empiredifferences =<<<EOFORM
	You're ascending; your relationships with the Solar Empire and New Lunar Republic can only go down.<br/>
EOFORM;
	}
	$fullreport =<<<EOFORM
	<div id="{$reportid}" class="report-showbutton"><a href="javascript:;" onclick="document.getElementById('{$reportid}').style.display = 'none';
document.getElementById('{$reportid}x').style.display = 'block';">Show Details</a></div>
	<div id="{$reportid}x" class="report-details"><a href="javascript:;" onclick="document.getElementById('{$reportid}').style.display = 'block';
document.getElementById('{$reportid}x').style.display = 'none';">Hide Details</a><br/>
{$messagelist}
	</div>
	<b>
	Change in Satisfaction: {$satdifference}<br/>
	{$empiredifferences}
	</b>
EOFORM;
    $mysqlfullreport = $GLOBALS['mysqli']->real_escape_string($fullreport);
    $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$rs['nation_id']}, '{$mysqlfullreport}', NOW())";
    $GLOBALS['mysqli']->query($sql);
    $sql = "UPDATE nations SET funds = funds + '{$gdp}', gdp_last_turn = '{$gdp}' WHERE nation_id = {$rs['nation_id']}";
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    DELETE FROM resources WHERE amount = '0' AND nation_id = '{$rs['nation_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
}
$sql = "DELETE FROM reports WHERE time < DATE_SUB(NOW() , INTERVAL 1 DAY)";
$GLOBALS['mysqli']->query($sql);
$sql =<<<EOSQL
SELECT user_id, nation_id, funds, creationdate, economy, government, name FROM nations WHERE satisfaction < -5000
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if (strtotime($rs['creationdate']) < time() - 2419200) {
		$resourceslist = "";
		$buildingslist = "";
		$reportslist = "";
		$sql=<<<EOSQL
		SELECT rd.name, r.amount FROM resources r INNER JOIN resourcedefs rd ON rd.resource_id = r.resource_id WHERE rd.is_building = 0 AND r.nation_id = {$rs['nation_id']}
EOSQL;
		$sth2 = $GLOBALS['mysqli']->query($sql);
		while ($rs2 = mysqli_fetch_array($sth2)) {
			$resourceslist .=<<<EOFORM
<td>{$rs2['name']}</td><td>{$rs2['amount']}</td></tr>
EOFORM;
			}
		$sql=<<<EOSQL
		SELECT rd.name, r.amount FROM resources r INNER JOIN resourcedefs rd ON rd.resource_id = r.resource_id WHERE rd.is_building = 1 AND r.nation_id = {$rs['nation_id']}
EOSQL;
		$sth2 = $GLOBALS['mysqli']->query($sql);
		while ($rs2 = mysqli_fetch_array($sth2)) {
			$buildingslist .=<<<EOFORM
<td>{$rs2['name']}</td><td>{$rs2['amount']}</td></tr>
EOFORM;
		}
		$sql=<<<EOSQL
		SELECT * FROM reports WHERE nation_id = '{$rs['nation_id']}' ORDER BY time DESC
EOSQL;
		$sth2 = $GLOBALS['mysqli']->query($sql);
		while ($rs2 = mysqli_fetch_array($sth2)) {
			$reportslist .=<<<EOFORM
<tr><td>{$rs2['report']}</td><td>{$rs2['time']}</td></tr>
EOFORM;
		}
		$commasfunds = commas($rs['funds']);
		$details=<<<EOFORM
<center>Killed by Massive Uprising<br/>
{$commasfunds} Bits<br/>
{$rs['government']}<br/>
{$rs['economy']}</center>
<center><h4 class="graveyardresourcesheading">Resources</h4></center>
<table class="graveyardresourcestable table table-striped table-bordered">{$resourceslist}</table>
<center><h4 class="graveyardbuildingsheading">Buildings</h4></center>
<table class="graveyardbuildingstable table table-striped table-bordered">{$buildingslist}</table>
<center><h4 class="graveyardreportsheading">Reports</h4></center>
<table class="graveyardreportstable table table-striped table-bordered">{$reportslist}</table>
EOFORM;
		$mysqldetails = $GLOBALS['mysqli']->real_escape_string($details);
		$sql=<<<EOSQL
		INSERT INTO graveyard SET name = '{$rs['name']}', details = '{$mysqldetails}', killer = 'Massive Uprising', deathdate = NOW()
EOSQL;
		$GLOBALS['mysqli']->query($sql);
    }
    $sql = "DELETE FROM resources WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
	$sql = "DELETE FROM marketplace WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
	$sql = "DELETE FROM nations WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
	$sql = "DELETE FROM weapons WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
	$sql = "DELETE FROM armor WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM recipefavorites WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
	$sql = "DELETE FROM forcegroups WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
	$sql = "DELETE FROM forces WHERE nation_id = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
	$sql = <<<EOSQL
	UPDATE forcegroups SET location_id = nation_id, departuredate = NULL, attack_mission = 0 WHERE destination_id = {$rs['nation_id']} OR location_id = {$rs['nation_id']}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$sql = "SELECT deal_id FROM deals WHERE fromnation = '{$rs['nation_id']}' OR tonation = '{$rs['nation_id']}'";
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$sql = "DELETE FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM dealitems_requested WHERE deal_id = '{$rs2['deal_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM dealarmor_offered WHERE deal_id = '{$rs2['deal_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM dealarmor_requested WHERE deal_id = '{$rs2['deal_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM dealweapons_offered WHERE deal_id = '{$rs2['deal_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM dealweapons_requested WHERE deal_id = '{$rs2['deal_id']}'";
		$GLOBALS['mysqli']->query($sql);
	}
	$sql = "DELETE FROM deals WHERE fromnation = '{$rs['nation_id']}' OR tonation = '{$rs['nation_id']}'";
	$GLOBALS['mysqli']->query($sql);
    $goodbyemessage = $GLOBALS['mysqli']->real_escape_string("Congratulations! Your nation of {$rs['name']} got so low in satisfaction that it collapsed instantly.");
	$sql = "INSERT INTO messages (fromuser, touser, message, fromdeleted, sent) VALUES(0, {$rs['user_id']}, '{$goodbyemessage}', 1, NOW())";
	$GLOBALS['mysqli']->query($sql);
	$sql = "SELECT username FROM users WHERE user_id = {$rs['user_id']}";
	$nationowner = onelinequery($sql);
    $rawnews=<<<EOFORM
The nation of {$rs['name']} (<a href="viewuser.php?user_id={$rs['user_id']}">{$nationowner['username']}</a>) has exploded from a massive uprising!
EOFORM;
	$newsitem = $GLOBALS['mysqli']->real_escape_string($rawnews);
	$sql = "INSERT INTO news (message, posted) VALUES('{$newsitem}', NOW())";
	$GLOBALS['mysqli']->query($sql);
}
$sql =<<<EOSQL
SELECT nation_id, satisfaction, government FROM nations WHERE (satisfaction < -100 AND government = 'Loose Despotism') OR
(satisfaction < -100 AND government = 'Solar Vassal') OR
(satisfaction < -100 AND government = 'Lunar Client') OR
(satisfaction < 0 AND government = 'Democracy') OR
(satisfaction < -300 AND government = 'Repression') OR
(satisfaction < 0 AND government = 'Independence') OR
(satisfaction < 0 AND government = 'Decentralization') OR
(satisfaction < -500 AND government = 'Oppression') OR
(satisfaction < -400 AND government = 'Authoritarianism') OR
(satisfaction < -500 AND government = 'Alicorn Elite') OR
(satisfaction < -500 AND government = 'Transponyism')
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $rebelstrength = ceil((0 - $rs['satisfaction']) / 4);
    if ($rs['government'] == "Loose Despotism" || $rs['government'] == "Solar Vassal" || $rs['government'] == "Lunar Client") {
        $rebelstrength -= 25;
    } else if ($rs['government'] == "Repression") {
        $rebelstrength -= 75;
    } else if ($rs['government'] == "Oppression" || $rs['government'] == "Alicorn Elite" || $rs['government'] == "Transponyism") {
        $rebelstrength -= 125;
    } else if ($rs['government'] == "Authoritarianism") {
        $rebelstrength -= 100;
    }
    $sql=<<<EOSQL
    INSERT INTO forcegroups SET name = 'Rebels', location_id = {$rs['nation_id']}, attack_mission = 1, nation_id = -3
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$forcegroup_id = mysqli_insert_id($GLOBALS['mysqli']);
	$sql=<<<EOSQL
	INSERT INTO forces SET weapon_id = 0, armor_id = 0, training = 0, nation_id = -3, type = 1,
	size = {$rebelstrength}, name = 'Rebel Group {$forcegroup_id}', forcegroup_id = {$forcegroup_id}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$sql = "UPDATE nations SET satisfaction = satisfaction + {$rebelstrength} WHERE nation_id = {$rs['nation_id']}";
    $GLOBALS['mysqli']->query($sql);
	$report = $GLOBALS['mysqli']->real_escape_string("Your satisfaction is below the minimum - your ponies are revolting!
(You gain {$rebelstrength} sat among the rest of your nation as the subversives stop participating in it.)");
	$sql=<<<EOSQL
	INSERT INTO reports SET nation_id = {$rs['nation_id']}, report = '{$report}', time = NOW()
EOSQL;
	$GLOBALS['mysqli']->query($sql);
}
$sql =<<<EOSQL
SELECT n.se_relation, n.nlr_relation, n.nation_id, u.empiremax FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.nlr_relation + n.se_relation < -50
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['se_relation'] < -25) {
		$attacker = -1; 
		$strength = ceil((0 - $rs['se_relation'])/4);
		$attackername = "Solar Empire";
		$relation = "se_relation";
	$sql=<<<EOSQL
    INSERT INTO forcegroups SET name = '{$attackername}', location_id = {$rs['nation_id']}, attack_mission = 1, nation_id = {$attacker}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$forcegroup_id = mysqli_insert_id($GLOBALS['mysqli']);
	//canopy lights, dragon armor, max training
	$sql=<<<EOSQL
	INSERT INTO forces SET weapon_id = 13, armor_id = 11, training = 20, nation_id = {$attacker}, type = 3,
	size = {$strength}, name = '{$attackername} {$forcegroup_id}', forcegroup_id = {$forcegroup_id}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	if (!$rs['empiremax']) {
		$sql = "UPDATE nations SET {$relation} = {$relation} + {$strength} WHERE nation_id = {$rs['nation_id']}";
		$GLOBALS['mysqli']->query($sql);
		$report = $GLOBALS['mysqli']->real_escape_string("The {$attackername} hates you enough to send an airstrike and you don't have enough support from its opponent!
(Wishing to avoid a protracted war, it has reduced its hate for you by {$strength}.)");
	} else {
		$report = $GLOBALS['mysqli']->real_escape_string("The {$attackername} has attacked you for daring to ascend! ({$strength})");
	}
	$sql=<<<EOSQL
	INSERT INTO reports SET nation_id = {$rs['nation_id']}, report = '{$report}', time = NOW()
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	}
	if ($rs['nlr_relation'] < -25) {
		$attacker = -2;
		$strength = ceil((0 - $rs['nlr_relation'])/4);
		$attackername = "New Lunar Republic";
		$relation = "nlr_relation";
	$sql=<<<EOSQL
    INSERT INTO forcegroups SET name = '{$attackername}', location_id = {$rs['nation_id']}, attack_mission = 1, nation_id = {$attacker}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$forcegroup_id = mysqli_insert_id($GLOBALS['mysqli']);
	//canopy lights, dragon armor, max training
	$sql=<<<EOSQL
	INSERT INTO forces SET weapon_id = 13, armor_id = 11, training = 20, nation_id = {$attacker}, type = 3,
	size = {$strength}, name = '{$attackername} {$forcegroup_id}', forcegroup_id = {$forcegroup_id}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	if (!$rs['empiremax']) {
		$sql = "UPDATE nations SET {$relation} = {$relation} + {$strength} WHERE nation_id = {$rs['nation_id']}";
		$GLOBALS['mysqli']->query($sql);
		$report = $GLOBALS['mysqli']->real_escape_string("The {$attackername} hates you enough to send an airstrike and you don't have enough support from its opponent!
(Wishing to avoid a protracted war, it has reduced its hate for you by {$strength}.)");
	} else {
		$report = $GLOBALS['mysqli']->real_escape_string("The {$attackername} has attacked you for daring to ascend! ({$strength})");
	}
	$sql=<<<EOSQL
	INSERT INTO reports SET nation_id = {$rs['nation_id']}, report = '{$report}', time = NOW()
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	}
}
$sql =<<<EOSQL
UPDATE users SET empiremax = empiremax - 1 WHERE empiremax < 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
SELECT empiremax, user_id FROM users WHERE empiremax IS NOT NULL
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$sql=<<<EOSQL
	UPDATE nations SET se_relation = '{$rs['empiremax']}' WHERE se_relation > {$rs['empiremax']} AND user_id = '{$rs['user_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$sql=<<<EOSQL
	UPDATE nations SET nlr_relation = '{$rs['empiremax']}' WHERE nlr_relation > {$rs['empiremax']} AND user_id = '{$rs['user_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
}
//// WAR

# all ticks on test are war ticks
if ( (date("G") == 0 || date("G") == 12) || (strpos($_ENV["DOMAIN_URL"], "test.4clop") !== false) ) {
$hour = date("H");

# no travel time on test
$sql="";
if(strpos($_ENV["DOMAIN_URL"], "test.4clop") !== false) {
$sql=<<<EOSQL
SELECT fg.forcegroup_id FROM forcegroups fg
LEFT JOIN nations n ON fg.location_id = n.nation_id
LEFT JOIN nations n2 ON fg.destination_id = n2.nation_id
WHERE fg.departuredate IS NOT NULL AND (
(n.region = n2.region AND fg.attack_mission = 0) OR
(n.region = n2.region AND fg.attack_mission = 1) OR
(fg.attack_mission = 0) OR
(fg.attack_mission = 1)
)
EOSQL;
} else {
$sql=<<<EOSQL
SELECT fg.forcegroup_id FROM forcegroups fg
LEFT JOIN nations n ON fg.location_id = n.nation_id
LEFT JOIN nations n2 ON fg.destination_id = n2.nation_id
WHERE fg.departuredate IS NOT NULL AND (
(fg.departuredate < DATE_SUB(CONCAT(CURDATE(), ' {$hour}:00:00'), INTERVAL 12 HOUR) AND n.region = n2.region AND fg.attack_mission = 0) OR
(fg.departuredate < DATE_SUB(CONCAT(CURDATE(), ' {$hour}:00:00'), INTERVAL 24 HOUR) AND n.region = n2.region AND fg.attack_mission = 1) OR
(fg.departuredate < DATE_SUB(CONCAT(CURDATE(), ' {$hour}:00:00'), INTERVAL 36 HOUR) AND fg.attack_mission = 0) OR
(fg.departuredate < DATE_SUB(CONCAT(CURDATE(), ' {$hour}:00:00'), INTERVAL 48 HOUR) AND fg.attack_mission = 1)
)
EOSQL;	
}

$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $sql=<<<EOSQL
    UPDATE forcegroups SET location_id = destination_id, destination_id = 0, departuredate = NULL WHERE forcegroup_id = '{$rs['forcegroup_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
}
//abandon all hope, ye who enter here
//hope you like loops, motherfucker!
//multidimensional arrays ahead because I seriously can't think of a better way to do this
//I could try to use references for this shit but I don't want to make this code any more incomprehensible
//If you find a better way to do this let me know - just make sure it does the exact same thing w/r/t target priorities
//(which is what all the obscene looping is for)
//looping through arrays rapes webhosts a lot less than continuous large SQL queries
$types = array(1 => "cavalry", 2 => "tanks", 3 => "pegasi", 4 => "unicorns", 5 => "naval");
$NPCforces = array(-1 => "Solar Empire", -2 => "New Lunar Republic", -3 => "Rebel Forces");
$battlegrounds = array();
$sql=<<<EOSQL
SELECT u.stasismode, fg.location_id
FROM forcegroups fg
LEFT JOIN nations n ON (n.nation_id = fg.location_id)
LEFT JOIN users u ON (u.user_id = n.user_id)
WHERE fg.departuredate IS NULL
AND fg.attack_mission = 1
ORDER BY fg.location_id
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $battlegrounds[$rs['location_id']] = $rs['location_id'];
    $stasismodes[$rs['location_id']] = $rs['stasismode'];
}
foreach ($battlegrounds as $battleground) {
//if this ends up on The Daily WTF I'm gonna smack a bitch
$invaderattackers = array();
$invaderdefenders = array();
$repellerattackers = array();
$repellerdefenders = array();
$units = array();
$invaderdamages = array();
$invaderarmors = array();
$invaderarmorsizes = array();
$invaderarmornations = array();
$invaderdamagesizes = array();
$invaderdamagenations = array();
$repellerdamages = array();
$repellerarmors = array();
$repellerarmorsizes = array();
$repellerarmornations = array();
$repellerdamagesizes = array();
$repellerdamagenations = array();
$invaders = array();
$repellers = array();
$messages = array();
$hitinfo = array();
$damageinfo = array();
$sql=<<<EOSQL
SELECT f.*, fg.attack_mission, wd.weapon_id, wd.dmg_cavalry, wd.dmg_tanks, wd.dmg_pegasi, wd.dmg_unicorns, wd.dmg_naval,
ad.armor_id, ad.arm_cavalry, ad.arm_tanks, ad.arm_pegasi, ad.arm_unicorns, ad.arm_naval, n.name AS nationname
FROM forces f
LEFT JOIN nations n ON f.nation_id = n.nation_id
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
LEFT JOIN armordefs ad ON f.armor_id = ad.armor_id
LEFT JOIN weapondefs wd ON f.weapon_id = wd.weapon_id
WHERE fg.location_id = {$battleground} AND fg.departuredate IS NULL
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['nation_id'] < 0) {
        $rs['nationname'] = $NPCforces[$rs['nation_id']];
    }
	$rs['hits'] = $rs['size']; //might not be necessary but code clarity
	if ($rs['type'] == 6) { //alicorns
		$rs['type'] = 5; // treat them like naval for now
		foreach ($types as $typenumber => $type) {
			$rs["dmg_{$type}"] = 10;
		}
		foreach ($types as $typenumber => $type) {
			$rs["arm_{$type}"] = .1;
		}
	} else {
		if (!$rs['weapon_id']) {
			foreach ($types as $typenumber => $type) {
				$rs["dmg_{$type}"] = .25;
			}
		}
		if (!$rs['armor_id']) {
			foreach ($types as $typenumber => $type) {
				$rs["arm_{$type}"] = 1;
			}
		}
	}
	$units[$rs['force_id']] = $rs;
    //invaders are the invasion force and repellers are the ones keeping them out
    //both sides play "attacker" and "defender" to each other
    if ($rs['attack_mission']) {
        $invaders[$rs['force_id']] = $rs;
        foreach ($types as $typenumber => $type) {
            $invaderarmorsizes[$rs['type']][$typenumber][] = $rs['size']; // DO NOT FUCK WITH THIS - there's an array_multisort issue
            $invaderarmornations[$rs['type']][$typenumber][] = $rs['nation_id']; //involving multiple sorts using the same array as a guideline
			$invaderdamages[(string)$rs["dmg_{$type}"]][$type][] = $rs; // it'll fucking convert to an int if I don't specify string
			$invaderdamagesizes[(string)$rs["dmg_{$type}"]][$type][] = $rs['size'];
			$invaderdamagenations[(string)$rs["dmg_{$type}"]][$type][] = $rs['nation_id'];
			$invaderarmors[$rs['type']][$typenumber][] = $rs["arm_{$type}"];
            $invaderattackers[$typenumber][] = $rs;
            $invaderdefenders[$rs['type']][$typenumber][] = $rs;
        }
    } else {
        $repellers[$rs['force_id']] = $rs;
		foreach ($types as $typenumber => $type) {
            $repellerarmorsizes[$rs['type']][$typenumber][] = $rs['size'];
            $repellerarmornations[$rs['type']][$typenumber][] = $rs['nation_id'];
			$repellerdamages[(string)$rs["dmg_{$type}"]][$type][] = $rs;
			$repellerdamagesizes[(string)$rs["dmg_{$type}"]][$type][] = $rs['size'];
			$repellerdamagenations[(string)$rs["dmg_{$type}"]][$type][] = $rs['nation_id'];
			$repellerarmors[$rs['type']][$typenumber][] = $rs["arm_{$type}"];
            $repellerattackers[$typenumber][] = $rs;
            $repellerdefenders[$rs['type']][$typenumber][] = $rs;
        }
    }
}
krsort($invaderdamages, SORT_NUMERIC); //after converting to a string, now we sort the strings numerically! PHP is derpy.
krsort($repellerdamages, SORT_NUMERIC);
//praise Satan for array_multisort()
foreach ($invaderdamages as $damage => $restofit) {
	foreach ($types as $typenumber => $type) {
        if ($invaderdamagesizes[$damage][$type] || $invaderdamagenations[$damage][$type] || $invaderdamages[$damage][$type]) {
            array_multisort($invaderdamagesizes[$damage][$type], SORT_DESC, $invaderdamagenations[$damage][$type], SORT_ASC, $invaderdamages[$damage][$type]);
        }
	}
}
foreach ($repellerdamages as $damage => $restofit) {
	foreach ($types as $typenumber => $type) {
        if ($repellerdamagesizes[$damage][$type] || $repellerdamagenations[$damage][$type] || $repellerdamages[$damage][$type]) {
            array_multisort($repellerdamagesizes[$damage][$type], SORT_DESC, $repellerdamagenations[$damage][$type], SORT_ASC, $repellerdamages[$damage][$type]);
        }
	}
}
foreach ($types as $typenumber => $type) {
	foreach ($types as $typenumber2 => $type2) {
		if ($invaderarmors[$typenumber][$typenumber2]) {
			array_multisort($invaderarmors[$typenumber][$typenumber2], SORT_ASC, $invaderarmorsizes[$typenumber][$typenumber2], SORT_DESC, $invaderarmornations[$typenumber][$typenumber2], SORT_ASC, $invaderdefenders[$typenumber][$typenumber2]);
        }
		if ($repellerarmors[$typenumber][$typenumber2]) {
			array_multisort($repellerarmors[$typenumber][$typenumber2], SORT_ASC, $repellerarmorsizes[$typenumber][$typenumber2], SORT_DESC, $repellerarmornations[$typenumber][$typenumber2], SORT_ASC, $repellerdefenders[$typenumber][$typenumber2]);
		}
	}
}
if (!empty($repellers)) {
foreach ($invaderdamages as $invaderdamage => $invaderattackers) { //whoever does the most damage goes first
	foreach ($types as $typenumber => $type) { //then, we go through the types list
        if ($invaderattackers[$type]) {
		foreach ($invaderattackers[$type] AS $attacker) {
			while ($units[$attacker['force_id']]['hits'] > 0) {
				$fought = false;
                if ($repellerdefenders[$typenumber][$attacker['type']]) {
				foreach ($repellerdefenders[$typenumber][$attacker['type']] as $defender) {
                    $damage = round(($invaderdamage * $defender["arm_{$types[$attacker['type']]}"] * pow(1.5, (($attacker['training']-$defender['training'])/20))), 3);
					if ($defender['nation_id'] == $battleground && !$stasismodes[$battleground]) {
						$damage = $damage * .75;
					}
					while ($units[$defender['force_id']]['size'] > $units[$defender['force_id']]['damage']) {
						$fought = true;
						$units[$defender['force_id']]['damage'] += $damage;
						$hitinfo[$attacker['force_id']][$defender['force_id']] += 1;
                        $damageinfo[$attacker['force_id']][$defender['force_id']] += $damage;
						$units[$attacker['force_id']]['hits']--;
						if ($units[$attacker['force_id']]['hits'] == 0) {
							//we're out of hits, back out
							break 2;
						}
					}
				}
				if (!$fought) {
					break 2; // no defenders of this type remaining, so let's go to the next type
				}
                } else {
                    break;
                }
			}
		}
        }
	}
}
foreach ($repellerdamages as $repellerdamage => $repellerattackers) { //whoever does the most damage goes first
	foreach ($types as $typenumber => $type) { //then, we go through the types list
        if ($repellerattackers[$type]) {
		foreach ($repellerattackers[$type] AS $attacker) {
			while ($units[$attacker['force_id']]['hits'] > 0) {
				$fought = false;
                if ($invaderdefenders[$typenumber][$attacker['type']]) {
				foreach ($invaderdefenders[$typenumber][$attacker['type']] as $defender) {
                    $damage = round(($repellerdamage * $defender["arm_{$types[$attacker['type']]}"] * pow(1.5, (($attacker['training']-$defender['training'])/20))), 3);
					while ($units[$defender['force_id']]['size'] > $units[$defender['force_id']]['damage']) {
						$fought = true;
						$units[$defender['force_id']]['damage'] += $damage;
                        $hitinfo[$attacker['force_id']][$defender['force_id']] += 1;
                        $damageinfo[$attacker['force_id']][$defender['force_id']] += $damage;
						$units[$attacker['force_id']]['hits']--;
						if ($units[$attacker['force_id']]['hits'] == 0) {
							//we're out of hits, back out
							break 2;
						}
					}
				}
				if (!$fought) {
					break 2; // no defenders of this type remaining, so let's go to the next type
				}
                } else {
                    break;
                }
			}
		}
        }
	}
}
}
foreach ($damageinfo as $attackerid => $restofit) {
    foreach ($restofit as $defenderid => $damage) {
    $damage = round($damage, 6);
	if ($units[$defenderid]['nation_id'] > 0) {
        $messages[$units[$attackerid]['nation_id']][] =<<<EOFORM
Your {$units[$attackerid]['name']} (size {$units[$attackerid]['size']}) hit
<a href="viewnation.php?nation_id={$units[$defenderid]['nation_id']}">{$units[$defenderid]['nationname']}</a>'s {$units[$defenderid]['name']} (size {$units[$defenderid]['size']})
for {$damage} damage ({$hitinfo[$attackerid][$defenderid]} hits)
EOFORM;
	} else {
		$messages[$units[$attackerid]['nation_id']][] =<<<EOFORM
Your {$units[$attackerid]['name']} (size {$units[$attackerid]['size']}) hit
{$units[$defenderid]['nationname']}'s {$units[$defenderid]['name']} (size {$units[$defenderid]['size']})
for {$damage} damage ({$hitinfo[$attackerid][$defenderid]} hits)
EOFORM;
	}
	if ($units[$attackerid]['nation_id'] > 0) {
		$messages[$units[$defenderid]['nation_id']][] =<<<EOFORM
Your {$units[$defenderid]['name']} (size {$units[$defenderid]['size']}) were hit by
<a href="viewnation.php?nation_id={$units[$attackerid]['nation_id']}">{$units[$attackerid]['nationname']}</a>'s {$units[$attackerid]['name']} (size {$units[$attackerid]['size']})
for {$damage} damage ({$hitinfo[$attackerid][$defenderid]} hits)
EOFORM;
	} else {
		$messages[$units[$defenderid]['nation_id']][] =<<<EOFORM
Your {$units[$defenderid]['name']} (size {$units[$defenderid]['size']}) were hit by
{$units[$attackerid]['nationname']}'s {$units[$attackerid]['name']} (size {$units[$attackerid]['size']})
for {$damage} damage ({$hitinfo[$attackerid][$defenderid]} hits)
EOFORM;
	}
    }
}
foreach ($units as $unit) {
	$unit['damage'] = floor(round($unit['damage'], 6)); //Seriously, fuck floating point errors and fuck hidden precision
	if ($unit['damage'] > 0) {
		if ($unit['damage'] < $unit['size']) {
			$sql =<<<EOSQL
			UPDATE forces SET size = size - {$unit['damage']} WHERE force_id = '{$unit['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$messages[$unit['nation_id']][] = "Your {$unit['name']} lost {$unit['damage']} size!";
		} else {
			$sql =<<<EOSQL
			DELETE FROM forces WHERE force_id = '{$unit['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$messages[$unit['nation_id']][] = "Your {$unit['name']} scattered to the four winds!";
		}
	}
}

if (!empty($messages)) {
    foreach ($messages as $nation_id => $messagearray) {
	if ($nation_id > 0) {
    $messagelist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $messagearray));
    $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$nation_id}, '{$messagelist}', NOW())";
        $GLOBALS['mysqli']->query($sql);
	}
    }
}
$sql=<<<EOSQL
DELETE FROM forcegroups WHERE forcegroup_id NOT IN (SELECT forcegroup_id FROM forces)
EOSQL;
$GLOBALS['mysqli']->query($sql);
//are there any defenders remaining?
$sql=<<<EOSQL
SELECT forcegroup_id FROM forcegroups
WHERE location_id = {$battleground}
AND attack_mission = 0
AND departuredate IS NULL
EOSQL;
$defendersremaining = onelinequery($sql);
if (!$defendersremaining) {
    $sql=<<<EOSQL
SELECT SUM(f.size) AS sumsize, n.user_id, n.name, n.government, f.nation_id FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
INNER JOIN nations n ON f.nation_id = n.nation_id
WHERE fg.location_id = {$battleground}
AND fg.attack_mission = 1
AND fg.departuredate IS NULL
AND n.nation_id > 0
GROUP BY n.user_id
ORDER BY sumsize DESC, n.user_id DESC LIMIT 1
EOSQL;
    $biggestarmy = onelinequery($sql);
	if (!$biggestarmy) {
    $sql=<<<EOSQL
SELECT SUM(f.size) AS sumsize, n.user_id, n.name, n.government, f.nation_id FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
LEFT JOIN nations n ON f.nation_id = n.nation_id
WHERE fg.location_id = {$battleground}
AND fg.attack_mission = 1
AND fg.departuredate IS NULL
GROUP BY n.user_id
ORDER BY sumsize DESC, n.user_id DESC LIMIT 1
EOSQL;
    $biggestarmy = onelinequery($sql);
	}
    if ($biggestarmy) { //if there's no attackers either (mutual kill) we do nothing
    if (!$biggestarmy['user_id'] || $biggestarmy['government'] == "Independence") {
		$sql=<<<EOSQL
		SELECT u.username, n.*
		FROM nations n
		INNER JOIN users u ON u.user_id = n.user_id
		WHERE nation_id = {$battleground}
EOSQL;
		$lastuser = onelinequery($sql);
        if ($biggestarmy['nation_id'] == -1) {
			$goodbyemessage = $GLOBALS['mysqli']->real_escape_string("The Solar Empire has destroyed {$lastuser['name']}!");
			$killer = "The Solar Empire";
			$rawnews =<<<EOFORM
The Solar Empire has destroyed {$lastuser['name']} (<a href="viewuser.php?user_id={$lastuser['user_id']}">{$lastuser['username']}</a>)!
EOFORM;
		} else if ($biggestarmy['nation_id'] == -2) {
			$goodbyemessage = $GLOBALS['mysqli']->real_escape_string("The New Lunar Republic has destroyed {$lastuser['name']}!");
			$killer = "The New Lunar Republic";
			$rawnews =<<<EOFORM
The New Lunar Republic has destroyed {$lastuser['name']} (<a href="viewuser.php?user_id={$lastuser['user_id']}">{$lastuser['username']}</a>)!
EOFORM;
		} else if ($biggestarmy['nation_id'] == -3) {
			$goodbyemessage = $GLOBALS['mysqli']->real_escape_string("The ponies of {$lastuser['name']} have successfully revolted against you!");
			$killer = "Rebels";
			$rawnews =<<<EOFORM
The ponies of {$lastuser['name']} have successfully revolted against <a href="viewuser.php?user_id={$lastuser['user_id']}">{$lastuser['username']}</a>!
EOFORM;
		} else {
            $rawgoodbyemessage =<<<EOFORM
Your nation of {$lastuser['name']} has been destroyed by the independent nation of {$biggestarmy['name']}!
EOFORM;
			$goodbyemessage = $GLOBALS['mysqli']->real_escape_string($rawgoodbyemessage);
			$killer = $biggestarmy['name'];
			$rawnews =<<<EOFORM
The nation of {$lastuser['name']} (<a href="viewuser.php?user_id={$lastuser['user_id']}">{$lastuser['username']}</a>) has been destroyed by the independent nation of <a href="viewnation.php?nation_id={$biggestarmy['nation_id']}">{$biggestarmy['name']}</a>!
EOFORM;
        }
		$newsitem = $GLOBALS['mysqli']->real_escape_string($rawnews);
        if (strtotime($lastuser['creationdate']) < time() - 2419200) {
			$resourceslist = "";
			$buildingslist = "";
			$reportslist = "";
			$sql=<<<EOSQL
			SELECT rd.name, r.amount FROM resources r INNER JOIN resourcedefs rd ON rd.resource_id = r.resource_id WHERE rd.is_building = 0 AND r.nation_id = {$battleground}
EOSQL;
			$sth2 = $GLOBALS['mysqli']->query($sql);
			while ($rs2 = mysqli_fetch_array($sth2)) {
				$resourceslist .=<<<EOFORM
<td>{$rs2['name']}</td><td>{$rs2['amount']}</td></tr>
EOFORM;
			}
			$sql=<<<EOSQL
			SELECT rd.name, r.amount FROM resources r INNER JOIN resourcedefs rd ON rd.resource_id = r.resource_id WHERE rd.is_building = 1 AND r.nation_id = {$battleground}
EOSQL;
			$sth2 = $GLOBALS['mysqli']->query($sql);
			while ($rs2 = mysqli_fetch_array($sth2)) {
				$buildingslist .=<<<EOFORM
<td>{$rs2['name']}</td><td>{$rs2['amount']}</td></tr>
EOFORM;
			}
			$sql=<<<EOSQL
			SELECT * FROM reports WHERE nation_id = '{$battleground}' ORDER BY time DESC
EOSQL;
			$sth2 = $GLOBALS['mysqli']->query($sql);
			while ($rs2 = mysqli_fetch_array($sth2)) {
				$reportslist .=<<<EOFORM
<tr><td>{$rs2['report']}</td><td>{$rs2['time']}</td></tr>
EOFORM;
			}
			$commasfunds = commas($lastuser['funds']);
			$details=<<<EOFORM
<center>Killed by {$killer}<br/>
{$commasfunds} Bits<br/>
{$lastuser['government']}<br/>
{$lastuser['economy']}</center>
<center><h4 class="graveyardresourcesheading">Resources</h4></center>
<table class="graveyardresourcestable table table-striped table-bordered">{$resourceslist}</table>
<center><h4 class="graveyardbuildingsheading">Buildings</h4></center>
<table class="graveyardbuildingstable table table-striped table-bordered">{$buildingslist}</table>
<center><h4 class="graveyardreportsheading">Reports</h4></center>
<table class="graveyardreportstable table table-striped table-bordered">{$reportslist}</table>
EOFORM;
			$mysqldetails = $GLOBALS['mysqli']->real_escape_string($details);
            $mysqlkiller = $GLOBALS['mysqli']->real_escape_string($killer);
            $sql=<<<EOSQL
			INSERT INTO graveyard SET name = '{$lastuser['name']}', details = '{$mysqldetails}', killer = '{$mysqlkiller}', deathdate = NOW()
EOSQL;
			$GLOBALS['mysqli']->query($sql);
        }
        $sql = "DELETE FROM resources WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM marketplace WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM nations WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM weapons WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM armor WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
        $sql = "DELETE FROM recipefavorites WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM forcegroups WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM forces WHERE nation_id = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = <<<EOSQL
		UPDATE forcegroups SET location_id = nation_id, departuredate = NULL, destination_id = 0, attack_mission = 0 WHERE destination_id = {$battleground} OR location_id = {$battleground}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql = "SELECT deal_id FROM deals WHERE fromnation = '{$battleground}' OR tonation = '{$battleground}'";
		$sth2 = $GLOBALS['mysqli']->query($sql);
		while ($rs2 = mysqli_fetch_array($sth2)) {
			$sql = "DELETE FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealitems_requested WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealarmor_offered WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealarmor_requested WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealweapons_offered WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealweapons_requested WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
		}
		$sql = "DELETE FROM deals WHERE fromnation = '{$battleground}' OR tonation = '{$battleground}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "INSERT INTO messages (fromuser, touser, message, fromdeleted, sent) VALUES(0, {$lastuser['user_id']}, '{$goodbyemessage}', 1, NOW())";
		$GLOBALS['mysqli']->query($sql);
		$sql = "INSERT INTO news (message, posted) VALUES ('{$newsitem}', NOW())";
		$GLOBALS['mysqli']->query($sql);
    } else {
		$sql=<<<EOSQL
		SELECT u.user_id, u.username, n.name
        FROM nations n
        INNER JOIN users u ON n.user_id = u.user_id
        WHERE n.nation_id = {$battleground}
EOSQL;
		$lastuser = onelinequery($sql);
        $sql=<<<EOSQL
SELECT n.government, n.economy, n.name, u.username FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE u.user_id = {$biggestarmy['user_id']} ORDER BY n.nation_id ASC LIMIT 1
EOSQL;
		$oldestnation = onelinequery($sql);
        $rawgoodbyemessage =<<<EOFORM
Your nation of {$lastuser['name']} has been conquered by the armies of {$oldestnation['username']}!
EOFORM;
		$goodbyemessage = $GLOBALS['mysqli']->real_escape_string($rawgoodbyemessage);
		$sql = "INSERT INTO messages (fromuser, touser, message, fromdeleted, sent) VALUES(0, {$lastuser['user_id']}, '{$goodbyemessage}', 1, NOW())";
		$GLOBALS['mysqli']->query($sql);
		$rawnews=<<<EOFORM
The nation of <a href="viewnation.php?nation_id={$battleground}">{$lastuser['name']}</a> (<a href="viewuser.php?user_id={$lastuser['user_id']}">{$lastuser['username']}</a>)
has been conquered by the armies of <a href="viewuser.php?user_id={$biggestarmy['user_id']}">{$oldestnation['username']}</a>!
EOFORM;
		$newsitem = $GLOBALS['mysqli']->real_escape_string($rawnews);
		$sql = "INSERT INTO news (message, posted) VALUES ('{$newsitem}', NOW())";
		$GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
UPDATE nations SET user_id = {$biggestarmy['user_id']}, government = '{$oldestnation['government']}', economy = '{$oldestnation['economy']}' WHERE nation_id = {$battleground}
EOSQL;
        $GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
		DELETE FROM forces WHERE nation_id = {$battleground}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
		DELETE FROM forcegroups WHERE nation_id = {$battleground}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
    //anyone coming to attack it is now coming to defend it and vice versa
        $sql=<<<EOSQL
UPDATE forcegroups SET attack_mission = !attack_mission WHERE (destination_id = {$battleground} AND departuredate IS NOT NULL) OR (location_id = {$battleground} AND departuredate IS NULL)
EOSQL;
		$GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
UPDATE forcegroups SET oldmission = !oldmission WHERE (location_id = {$battleground} AND departuredate IS NOT NULL)
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		if ($oldestnation['government'] == "Solar Vassal" || $oldestnation['government'] == "Lunar Client") {
			$sql=<<<EOSQL
DELETE FROM resources WHERE nation_id = '{$battleground}' AND resource_id = 41	
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			if ($oldestnation['government'] == "Solar Vassal") {
			$sql=<<<EOSQL
UPDATE nations SET se_relation = 1000 WHERE nation_id = {$battleground}
EOSQL;
			} else {
			$sql=<<<EOSQL
UPDATE nations SET nlr_relation = 1000 WHERE nation_id = {$battleground}
EOSQL;
			}
			$GLOBALS['mysqli']->query($sql);
		}
    }
    //SE, NLR, rebels - all go away
    $sql=<<<EOSQL
    SELECT forcegroup_id FROM forcegroups WHERE (location_id = {$battleground} OR location_id < 0) AND nation_id < 0
EOSQL;
    $sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$sql=<<<EOSQL
		DELETE FROM forcegroups WHERE forcegroup_id = {$rs2['forcegroup_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
		DELETE FROM forces WHERE forcegroup_id = {$rs2['forcegroup_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	}
	}
}
}
}
if (date("G") == 0) {
    $sql =<<<EOSQL
    UPDATE forces SET training = training + 1
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql =<<<EOSQL
    UPDATE forces SET training = 20 WHERE training > 20
EOSQL;
    $GLOBALS['mysqli']->query($sql);
}
$sql=<<<EOSQL
DELETE FROM forcegroups WHERE forcegroup_id NOT IN (SELECT forcegroup_id FROM forces)
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
DELETE FROM messages WHERE sent < DATE_SUB(NOW(), INTERVAL 4 WEEK) AND fromuser != 0
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
DELETE FROM messages WHERE sent < DATE_SUB(NOW(), INTERVAL 12 WEEK)
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
DELETE FROM alliance_messages WHERE posted < DATE_SUB(NOW(), INTERVAL 4 WEEK)
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
DELETE FROM logins WHERE logindate < DATE_SUB(NOW(), INTERVAL 3 DAY)
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
UPDATE users SET stasismode = 1
WHERE stasismode = 0
AND lastactive < DATE_SUB(NOW(), INTERVAL 3 DAY) OR lastactive IS NULL
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
DELETE FROM news WHERE posted < DATE_SUB(NOW(), INTERVAL 4 WEEK)
EOSQL;
$GLOBALS['mysqli']->query($sql);
?>