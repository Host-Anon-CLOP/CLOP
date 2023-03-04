<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$buildings = array();
$resources = array();
$weapons = array();
$armor = array();
$regiontypes = array(0 => "The Heavily Fortified Island of Admin", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
$subregiontypes = array(0 => "", 1 => "North ", 2 => "Central ", 3 => "South ");
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval", 6 => "Alicorns");
$nationinfo['regionname'] = $regiontypes[$nationinfo['region']];
$nationinfo['subregionname'] = $subregiontypes[$nationinfo['subregion']];
$requiredresources = array();
$affectedresources = array();
$display['description'] = htmlentities($nationinfo['description'], ENT_SUBSTITUTE, "UTF-8");
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
    $display[$key] = htmlentities($value, ENT_SUBSTITUTE, "UTF-8");
}
$displayfunds = commas($nationinfo['funds']);
if ($_POST && (($_POST['token_overview'] == "") || ($_POST['token_overview'] != $_SESSION['token_overview']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_overview'] == "")) {
    $_SESSION['token_overview'] = sha1(rand() . $_SESSION['token_overview']);
}
if (!$errors) {
    if ($_POST['action'] == "Update Information") {
        $sql = "UPDATE nations SET description = '{$mysql['description']}' WHERE nation_id = '{$_SESSION['nation_id']}'";
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Description updated.";
    }
    if ($_POST['recycle']) {
        $mysql['recycleamount'] = (int)$mysql['recycleamount'];
        $sql = "SELECT r.*, rd.name, rd.satisfaction_on_destroy FROM resources r
        INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id
        WHERE r.nation_id = '{$_SESSION['nation_id']}' AND r.resource_id = '{$mysql['resource_id']}'";
        $rs = onelinequery($sql);
        if (!$rs) {
            $errors[] = "You already recycled your last one!";
        }
        if ($mysql['recycleamount'] > $rs['amount']) {
            $errors[] = "You don't have that many to destroy.";
        }
        if ($mysql['resource_id'] == "76") {
            $errors[] = "That's a good way to be violently deposed.";
        }
        if ($mysql['recycleamount'] < 1) {
            $errors[] = "No amount entered.";
        }
        if (!$errors) {
            if ($rs['amount'] == $mysql['recycleamount']) {
                $sql = "DELETE FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$mysql['resource_id']}'";
                $GLOBALS['mysqli']->query($sql);
            } else {
                $sql = "UPDATE resources SET amount = amount - {$mysql['recycleamount']} WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$mysql['resource_id']}'";
                $GLOBALS['mysqli']->query($sql);
				if ($rs['disabled'] > $mysql['recycleamount']) {
					$sql = "UPDATE resources SET disabled = disabled - {$mysql['recycleamount']} WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$mysql['resource_id']}'";
					$GLOBALS['mysqli']->query($sql);
				} else {
                    $sql = "UPDATE resources SET disabled = 0 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$mysql['resource_id']}'";
					$GLOBALS['mysqli']->query($sql);
                }
            }
            if ($rs['satisfaction_on_destroy'] > 0) {
                $displaysat = affectsatisfaction_silent($_SESSION['nation_id'], $nationinfo['satisfaction'], ($rs['satisfaction_on_destroy'] * $mysql['recycleamount']), $nationinfo['government']);
                $infos[] = "Your little ponies are happy to be rid of the {$rs['name']}! ({$displaysat} sat)";
                $nationinfo['satisfaction'] += $displaysat;
            } else if ($rs['satisfaction_on_destroy'] < 0) {
                $displaysat = affectsatisfaction_silent($_SESSION['nation_id'], $nationinfo['satisfaction'], ($rs['satisfaction_on_destroy'] * $mysql['recycleamount']), $nationinfo['government']);
                $infos[] = "Your little ponies are sad to see the {$rs['name']} go! ({$displaysat} sat)";
                $nationinfo['satisfaction'] += $displaysat;
            } else {
                $infos[] = "{$mysql['recycleamount']} {$rs['name']} destroyed.";
			}
        }
    }
    if ($_POST['disable']) {
        $mysql['disableamount'] = (int)$mysql['disableamount'];
        $sql = "SELECT r.*, rd.name FROM resources r
		INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id
		WHERE r.nation_id = '{$_SESSION['nation_id']}' AND r.resource_id = '{$mysql['resource_id']}'";
        $rs = onelinequery($sql);
        if ($mysql['disableamount'] < 1) {
            $errors[] = "No amount entered.";
        }
        if ($mysql['disableamount'] > $rs['amount'] - $rs['disabled']) {
            $errors[] = "You don't have that many to disable.";
        }
        if ($mysql['resource_id'] == "36") {
            $errors[] = "There's no disabling the Barracks.";
        }
        if ($mysql['resource_id'] == "76") {
            $errors[] = "Hell no.";
        }
        if (!$errors) {
			$sql = "UPDATE resources SET disabled = disabled + {$mysql['disableamount']} WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$mysql['resource_id']}'";
            $GLOBALS['mysqli']->query($sql);
			$infos[] = "{$mysql['disableamount']} {$rs['name']} disabled.";
		}
    }
	if ($_POST['reenable']) {
		$mysql['reenableamount'] = (int)$mysql['reenableamount'];
		$sql = "SELECT r.*, rd.name FROM resources r
		INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id
		WHERE r.nation_id = '{$_SESSION['nation_id']}' AND r.resource_id = '{$mysql['resource_id']}'";
        $rs = onelinequery($sql);
        if ($mysql['reenableamount'] > $rs['disabled']) {
            $errors[] = "You did not have that many {$rs['name']} disabled.";
        }
        if ($mysql['reenableamount'] < 1) {
            $errors[] = "No amount entered.";
        }
        if (!$errors) {
			$math = $rs['disabled'] - $mysql['reenableamount'];
			$sql = "UPDATE resources SET disabled = '{$math}' WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$mysql['resource_id']}'";
            $GLOBALS['mysqli']->query($sql);
			$infos[] = "{$mysql['reenableamount']} {$rs['name']} reenabled.";
		}
	}
}
if ($nationinfo['funds'] > 500000000) {
	$nationtax = ceil(($nationinfo['funds'] - 500000000)/500);
	$displaytax = commas($nationtax);
} else {
	$nationtax = 0;
	$displaytax = 0;
}
$gdp = getgdp($_SESSION['nation_id']);
if ($nationinfo['government'] == "Authoritarianism") {
    $gdp += $gdp * 1.5;
} else if ($nationinfo['government'] == "Oppression") {
    $gdp += $gdp * 2;
} else if ($nationinfo['government'] == "Repression") {
    $gdp += $gdp;
} else if ($nationinfo['government'] == "Alicorn Elite") {
    $gdp += $gdp * 5;
} else if ($nationinfo['government'] == "Transponyism") {
    $gdp += $gdp * 7;
} else {
    $gdpchange = $gdp * ($nationinfo['satisfaction']/1000);
    $gdp += $gdpchange;
}
$displaygdp = commas($gdp);
$sql = "SELECT r.amount, rd.name, r.resource_id, rd.is_building, r.disabled, rd.satisfaction_on_destroy FROM resources r INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id WHERE r.nation_id = '{$_SESSION['nation_id']}' ORDER BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['is_building']) {
		$buildings[] = $rs;
	} else {
		$resources[$rs['name']] = $rs['amount'];
		if ($rs['amount'] > 50000) {
			$taxes[$rs['name']] = ceil(($rs['amount'] - 50000)/500);
		} else {
			$taxes[$rs['name']] = 0;
		}
	}
}
$sql = "SELECT r.amount, rd.name, r.weapon_id FROM weapons r INNER JOIN weapondefs rd ON r.weapon_id = rd.weapon_id WHERE r.nation_id = '{$_SESSION['nation_id']}' ORDER BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $weapons[$rs['name']] = $rs['amount'];
	if ($rs['amount'] > 1000) {
		$taxes[$rs['name']] = ceil(($rs['amount'] - 1000)/500);
	}
}
$sql = "SELECT r.amount, rd.name, r.armor_id FROM armor r INNER JOIN armordefs rd ON r.armor_id = rd.armor_id WHERE r.nation_id = '{$_SESSION['nation_id']}' ORDER BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $armor[$rs['name']] = $rs['amount'];
	if ($rs['amount'] > 1000) {
		$taxes[$rs['name']] = ceil(($rs['amount'] - 1000)/500);
	}
}
$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS required
FROM resourcerequirements rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
WHERE r.nation_id = '{$_SESSION['nation_id']}' GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $requiredresources[$rs['name']] = $rs['required'];
}
if ($nationinfo['government'] == "Democracy") {
	$requiredresources["Gasoline"] += 20;
	$requiredresources["Vehicle Parts"] += 2;
} else if ($nationinfo['government'] == "Repression") {
	$requiredresources["Gasoline"] += 10;
} else if ($nationinfo['government'] == "Independence") {
	$requiredresources["Gasoline"] += 40;
	$requiredresources["Vehicle Parts"] += 4;
} else if ($nationinfo['government'] == "Decentralization") {
	$requiredresources["Gasoline"] += 50;
	$requiredresources["Vehicle Parts"] += 5;
} else if ($nationinfo['government'] == "Authoritarianism") {
	$requiredresources["Gasoline"] += 10;
	$requiredresources["Machinery Parts"] += 3;
} else if ($nationinfo['government'] == "Oppression") {
	$requiredresources["Gasoline"] += 10;
	$requiredresources["Machinery Parts"] += 5;
}
if ($nationinfo['economy'] == "Free Market") {
	$requiredresources["Coffee"] += 6;
} else if ($nationinfo['economy'] == "State Controlled") {
	$requiredresources["Vodka"] += 6;
}
$milsugar = 0;
$milgems = 0;
$milgasoline = 0;
$milcoffee = 0;
$sql=<<<EOSQL
SELECT SUM(size) AS totalsize, type FROM forces WHERE nation_id = '{$_SESSION['nation_id']}' GROUP BY type
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['type'] == 1) {
        $milsugar += ($rs['totalsize'] * 5);
    } else if ($rs['type'] == 2 || $rs['type'] == 5) {
        $milgasoline += ($rs['totalsize'] * 5);
    } else if ($rs['type'] == 3) {
        $milcoffee += ($rs['totalsize'] * 5);
    } else if ($rs['type'] == 4) {
        $milgems += ($rs['totalsize'] * 5);
    } else if ($rs['type'] == 6) {
        $milgems += ($rs['totalsize'] * 10);
    }
}
$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS affected
FROM resourceeffects rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
WHERE r.nation_id = '{$_SESSION['nation_id']}' GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $affectedresources[$rs['name']] = $rs['affected'];
}
$envirodamage = 0;
$satperturn = 0;
$seperturn = 0;
$nlrperturn = 0;
$tempse = $nationinfo['se_relation'];
$tempnlr = $nationinfo['nlr_relation'];
if ($nationinfo['se_relation'] > 250) {
	$seperturn -= floor(($nationinfo['se_relation'] - 250) / 50);
	if ($nationinfo['se_relation'] > 400) {
		$seperturn -= floor(($nationinfo['se_relation'] - 400) / 50);
		if ($nationinfo['se_relation'] > 800) {
			$seperturn -= floor(($nationinfo['se_relation'] - 800) / 50);
		}
	}
}
if ($nationinfo['nlr_relation'] > 250) {
	$nlrperturn -= floor(($nationinfo['nlr_relation'] - 250) / 50);
	if ($nationinfo['nlr_relation'] > 400) {
		$nlrperturn -= floor(($nationinfo['nlr_relation'] - 400) / 50);
		if ($nationinfo['nlr_relation'] > 800) {
            $nlrperturn -= floor(($nationinfo['nlr_relation'] - 800) / 50);
        }
    }
}
if ($nationinfo['se_relation'] < -450) {
	$seperturn -= ceil(($nationinfo['se_relation'] + 450) / 50);
	if ($nationinfo['se_relation'] < -700) {
		$seperturn -= ceil(($nationinfo['se_relation'] + 700) / 50);
		if ($nationinfo['se_relation'] < -900) {
			$seperturn -= ceil(($nationinfo['se_relation'] + 900) / 50);
		}
	}
}
if ($nationinfo['nlr_relation'] < -450) {
	$nlrperturn -= ceil(($nationinfo['nlr_relation'] + 450) / 50);
	if ($nationinfo['nlr_relation'] < -700) {
		$nlrperturn -= ceil(($nationinfo['nlr_relation'] + 700) / 50);
		if ($nationinfo['nlr_relation'] < -900) {
			$nlrperturn -= ceil(($nationinfo['nlr_relation'] + 900) / 50);
		}
	}
}
$tempse += $seperturn;
$tempnlr += $nlrperturn;
if ($tempse > 0) {
	$nlrperturn -= floor($tempse / 50);
}
if ($tempnlr > 0) {
	$seperturn -= floor($tempnlr / 50);
}
if ($nationinfo['economy'] == "Free Market") {
    $seperturn -= 3;
    $nlrperturn += 1;
} else if ($nationinfo['economy'] == "State Controlled") {
    $seperturn += 1;
    $nlrperturn -= 3;
}
switch ($nationinfo['government']) {
    case "Democracy":
    $seperturn -= 3;
    $nlrperturn += 2;
    break;
    case "Decentralization":
    $seperturn -= 3;
    $nlrperturn += 4;
    break;
    case "Independence":
    $seperturn -= 3;
    $nlrperturn += 6;
    break;
    case "Repression":
    $seperturn += 2;
    $nlrperturn -= 3;
    break;
    case "Authoritarianism":
    $seperturn += 4;
    $nlrperturn -= 3;
    break;
    case "Oppression":
    $seperturn += 6;
    $nlrperturn -= 3;
    break;
    case "Solar Vassal":
    $seperturn = "Fixed";
    break;
    case "Lunar Client":
    $nlrperturn = "Fixed";
    break;
    case "Alicorn Elite":
    case "Transponyism":
    $seperturn = "Ascending";
    $nlrperturn = "Ascending";
    break;
    default:
    break;
}
$sql=<<<EOSQL
SELECT rd.*, r.amount, r.disabled, rd.se_relation, rd.nlr_relation
FROM resourcedefs rd
INNER JOIN resources r ON r.resource_id = rd.resource_id
WHERE r.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$satperturn += ($rs['satisfaction'] * ($rs['amount'] - $rs['disabled']));
		$satperturn -= $rs['disabled'];
		if ($rs['bad_min'] && (($rs['amount'] - $rs['disabled']) > $rs['bad_min'])) {
			$satloss = ceil(pow((($rs['amount'] - $rs['disabled']) - $rs['bad_min']), 2) / $rs['bad_div']);
			$satperturn -= $satloss;
			$envirodamage += $satloss;
		}
		if ($rs['resource_id'] == 44 || $rs['resource_id'] == 45) {
			$envirocleaners += ($rs['amount'] - $rs['disabled']);
		}
        if ($rs['se_relation'] && is_numeric($seperturn)) {
            $seperturn += (($rs['amount'] - $rs['disabled']) * $rs['se_relation']);
        }
        if ($rs['nlr_relation'] && is_numeric($nlrperturn)) {
            $nlrperturn += (($rs['amount'] - $rs['disabled']) * $rs['nlr_relation']);
        }
	}
	if ($envirocleaners) {
		$satperturn += $envirodamage - ceil($envirodamage * pow(.9, $envirocleaners));
	}
}
$sql=<<<EOSQL
SELECT sum(size) AS totalsize FROM forces WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$rs = onelinequery($sql);
if ($rs['totalsize'] > 20) {
	$satperturn -= ceil(($rs['totalsize'] - 20)/2);
}
$sql=<<<EOSQL
SELECT COUNT(*) AS empiresize FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
$rs = onelinequery($sql);
if ($nationinfo['government'] == "Transponyism" || $nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Oppression") {
    $satperturn -= ceil((pow(($rs['empiresize'] - 1), 2) * 20) / 3);
} else {
    $satperturn -= pow(($rs['empiresize'] - 1), 2) * 20;
}
if ($nationinfo['government'] == "Democracy") {
	$satperturn += 15;
} else if ($nationinfo['government'] == "Decentralization") {
	$satperturn += 30;
} else if ($nationinfo['government'] == "Independence") {
	$satperturn += 50;
}
if ($nationinfo['government'] == "Transponyism") {
	$satmultiplier = 7;
} else if ($nationinfo['government'] == "Alicorn Elite") {
	$satmultiplier = 5;
} else if ($nationinfo['government'] == "Independence") {
	$satmultiplier = 2.5;
} else if ($nationinfo['government'] == "Decentralization") {
	$satmultiplier = 2;
} else if ($nationinfo['government'] == "Democracy") {
	$satmultiplier = 1.5;
} else if ($nationinfo['government'] == "Solar Vassal" || $nationinfo['government'] == "Lunar Client") {
    $satmultiplier = 1.25;
} else {
	$satmultiplier = 1;
}
if ($nationinfo['satisfaction'] > (250 * $satmultiplier)) {
	$satperturn -= floor(($nationinfo['satisfaction'] - (250 * $satmultiplier)) / (50 * $satmultiplier));
	if ($nationinfo['satisfaction'] > (500 * $satmultiplier)) {
		$satperturn -= floor(($nationinfo['satisfaction'] - (500 * $satmultiplier)) / (50 * $satmultiplier));
		if ($nationinfo['satisfaction'] > (750 * $satmultiplier)) {
			$satperturn -= floor(($nationinfo['satisfaction'] - (750 * $satmultiplier)) / (50 * $satmultiplier));
		}
	}
}
$sql=<<<EOSQL
SELECT fg.name AS groupname, fg.forcegroup_id, fg.attack_mission, fg.departuredate, fg.location_id,
fg.nation_id AS ownernation_id, n.name AS ownername, n.region AS ownerregion,
fg.location_id, f.*, rd1.name AS weaponname, rd2.name AS armorname FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
LEFT JOIN weapondefs rd1 ON f.weapon_id = rd1.weapon_id
LEFT JOIN armordefs rd2 ON f.armor_id = rd2.armor_id
LEFT JOIN nations n ON fg.nation_id = n.nation_id
WHERE fg.departuredate IS NULL AND fg.location_id = {$nationinfo['nation_id']}
ORDER BY fg.forcegroup_id, f.name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['ownernation_id'] == -1) {
		$rs['ownername'] = "Solar Empire";
    } else if ($rs['ownernation_id'] == -2) {
        $rs['ownername'] = "New Lunar Republic";
    } else if ($rs['ownernation_id'] == -3) {
        $rs['ownername'] = "Occupy Equestria";
    } else {
        $rs['ownerregionname'] = $regiontypes[$rs['ownerregion']];
    }
    $rs['lowertype'] = strtolower($forcetypes[$rs['type']]);
	if ($rs['weaponname'] == "") {
        $rs['weapon_id'] = 0;
		$rs['weaponname'] = "Scrounged Weapons";
	}
	if ($rs['armorname'] == "") {
        $rs['armor_id'] = 0;
		$rs['armorname'] = "Scrounged Armor";
	}
    if ($rs['attack_mission']) {
        $attackers[] = $rs;
    } else {
        $defenders[] = $rs;
    }
}
?>