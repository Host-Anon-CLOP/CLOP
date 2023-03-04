<?php
date_default_timezone_set("UTC");
set_time_limit(60);
$mysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_elements");
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
function withinsix($number) {
    if ($number > 6) {
        $number -= 6;
    }
    return $number;
}
function addamount($id, $user, $amount) {
$sql=<<<EOSQL
INSERT INTO resources (user_id, resource_id, amount)
VALUES ({$user}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function allianceaddamount($id, $alliance_id, $amount) {
$sql=<<<EOSQL
INSERT INTO allianceresources (alliance_id, resource_id, amount)
VALUES ({$alliance_id}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function addbanked($id, $user, $amount) {
$sql=<<<EOSQL
INSERT INTO bankedresources (user_id, resource_id, amount)
VALUES ({$user}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function allianceaddbanked($id, $alliance, $amount) {
$sql=<<<EOSQL
INSERT INTO alliancebankedresources (alliance_id, resource_id, amount)
VALUES ({$alliance}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function getelements($id) {
$elementarray = array();
if ($id & 32) $elementarray[32] = "Generosity";
if ($id & 16) $elementarray[16] = "Honesty";
if ($id & 8) $elementarray[8] = "Kindness";
if ($id & 4) $elementarray[4] = "Laughter";
if ($id & 2) $elementarray[2] = "Loyalty";
if ($id & 1) $elementarray[1] = "Magic";
return $elementarray;
}
$sql = "SELECT * FROM resourcedefs";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourcename[$rs['resource_id']] = $rs['name'];
}
$hour = date("H");
$sql=<<<EOSQL
SELECT * FROM positionswaps WHERE effectivedate < DATE_SUB(CONCAT(CURDATE(), ' {$hour}:00:00'), INTERVAL 24 HOUR)
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$sql=<<<EOSQL
SELECT position FROM elementpositions WHERE resource_id = {$rs['changeposition1']}
EOSQL;
	$position1 = onelinequery($sql);
	$sql=<<<EOSQL
SELECT position FROM elementpositions WHERE resource_id = {$rs['changeposition2']}
EOSQL;
	$position2 = onelinequery($sql);
	$sql=<<<EOSQL
UPDATE elementpositions SET position = {$position2['position']} WHERE resource_id = {$rs['changeposition1']}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
UPDATE elementpositions SET position = {$position1['position']} WHERE resource_id = {$rs['changeposition2']}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$newsitem = $GLOBALS['mysqli']->real_escape_string("The positions of {$resourcename[$rs['changeposition1']]} and {$resourcename[$rs['changeposition2']]} have been swapped.");
	$sql = "INSERT INTO news (message, posted) VALUES ('{$newsitem}', NOW())";
	$GLOBALS['mysqli']->query($sql);
}
$sql=<<<EOSQL
DELETE FROM positionswaps WHERE effectivedate < DATE_SUB(CONCAT(CURDATE(), ' {$hour}:00:00'), INTERVAL 24 HOUR)
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
SELECT * FROM elementpositions
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$positions[$rs['position']] = $rs['resource_id'];
}
$elementpositions = array_flip($positions);
for ($id = 0; $id <= 63; $id++) {
    $newid = 0;
    if ($id & 32) $newid += $positions[withinsix($elementpositions['32'] + 3)];
    if ($id & 16) $newid += $positions[withinsix($elementpositions['16'] + 3)];
    if ($id & 8) $newid += $positions[withinsix($elementpositions['8'] + 3)];
    if ($id & 4) $newid += $positions[withinsix($elementpositions['4'] + 3)];
    if ($id & 2) $newid += $positions[withinsix($elementpositions['2'] + 3)];
    if ($id & 1) $newid += $positions[withinsix($elementpositions['1'] + 3)];
    $complements[$id] = $newid;
}
$sql=<<<EOSQL
SELECT ua.user_id FROM user_abilities ua
INNER JOIN abilities a ON a.ability_id = ua.ability_id
WHERE a.name = 'encouraged'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $encouragedusers[$rs['user_id']] = true;
}
$sql = "SELECT a.*, u.* FROM users u INNER JOIN alliances a ON u.alliance_id = a.alliance_id WHERE u.stasismode = 0";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $messages = array();
    $importantmessages = array();
	$ownedresources = array();
    $checkpay = array();
    $nopay = array();
	$bankedresources = array();
	$productionloss = 0;
	$sql=<<<EOSQL
	SELECT resource_id, amount FROM resources
	WHERE user_id = '{$rs['user_id']}'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$ownedresources[$rs2['resource_id']] = $rs2['amount'];
	}
    foreach ($positions as $value) {
		$production[$value] = $rs['production'];
        if ($encouragedusers[$rs['user_id']]) $production[$value] += 5;
	}
	if ($rs['alliancefocus'] == $rs['focus']) {
		$rs['focusamount'] += $rs['alliancefocusamount'];
	} else {
		switch ($rs['alliancefocusamount']) {
			case 1:
			$production[$rs['alliancefocus']] *= 2;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 5)]] *= 1.25;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 1)]] *= 1.25;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 4)]] *= .8;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 2)]] *= .8;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 3)]] *= .5;
			break;
			case 2:
			$production[$rs['alliancefocus']] *= 3;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 5)]] *= 2;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 1)]] *= 2;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 4)]] *= .5;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 2)]] *= .5;
			$production[$positions[withinsix($elementpositions[$rs['alliancefocus']] + 3)]] *= .25;
			break;
			default:
			break;
		}
	}
	switch ($rs['focusamount']) {
		case 1:
		$production[$rs['focus']] *= 2;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 5)]] *= 1.25;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 1)]] *= 1.25;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 4)]] *= .8;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 2)]] *= .8;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 3)]] *= .5;
		break;
		case 2:
		$production[$rs['focus']] *= 3;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 5)]] *= 2;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 1)]] *= 2;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 4)]] *= .5;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 2)]] *= .5;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 3)]] *= .25;
		break;
		case 3:
		$production[$rs['focus']] *= 4;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 5)]] *= 2.5;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 1)]] *= 2.5;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 4)]] *= 0;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 2)]] *= 0;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 3)]] *= 0;
		break;
		case 4:
		$production[$rs['focus']] *= 15;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 5)]] *= 0;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 1)]] *= 0;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 4)]] *= 0;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 2)]] *= 0;
		$production[$positions[withinsix($elementpositions[$rs['focus']] + 3)]] *= 0;
		break;
		default:
		break;
	}
	foreach ($production as $element => $amount) {
        $amount = floor(round($amount, 6));
		addamount($element, $rs['user_id'], $amount);
		$ownedresources[$element] += $amount;
        $messages[] = "You have generated {$amount} {$resourcename[$element]}.";
	}
	$effectivesat = $rs['satisfaction'];
	if ($effectivesat > 1000) {
		$effectivesat = 1000;
	}
	$effectivealliancesat = $rs['alliancesatisfaction'];
	if ($effectivealliancesat > 1000) {
		$effectivealliancesat = 1000;
	}
	$personalsatmult = 1 - (($effectivesat * 1.5) / 2000);
	$alliancesatmult = 1 - (($effectivealliancesat * 1.5) / 2000);
	$sql=<<<EOSQL
	SELECT * FROM autocompounds
	WHERE user_id = {$rs['user_id']}
	ORDER BY amount DESC
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$elementcomponents = getelements($rs2['resource_id']);
		$cando = true;
		foreach ($elementcomponents as $component => $name) {
			if ($ownedresources[$component] < $rs2['amount']) { 
				$cando = false;
				$importantmessages[] = "You did not have the {$name} to make {$rs2['amount']} {$resourcename[$rs2['resource_id']]}.";
			}
		}
		if ($cando) {
			foreach ($elementcomponents as $component => $name) {
				addamount($component, $rs['user_id'], $rs2['amount'] * -1);
				$ownedresources[$component] -= $rs2['amount'];
			}
			addamount($rs2['resource_id'], $rs['user_id'], $rs2['amount']);
            $ownedresources[$rs2['resource_id']] += $rs2['amount'];
			$messages[] = "You made {$rs2['amount']} {$resourcename[$rs2['resource_id']]}.";
		}
	}
	$checkpay = $ownedresources;
    $sql=<<<EOSQL
	SELECT amount, resource_id
	FROM bankedresources
	WHERE user_id = {$rs['user_id']}
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$bankedresources[$rs2['resource_id']] += $rs2['amount'];
		$checkpay[$rs2['resource_id']] += $rs2['amount'];
	}
	$sql=<<<EOSQL
	SELECT SUM(offeredamount * multiplier) AS total, offereditem
	FROM marketplace
	WHERE user_id = {$rs['user_id']}
	GROUP BY offereditem
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$checkpay[$rs2['offereditem']] += $rs2['total'];
	}
    $sql=<<<EOSQL
	SELECT SUM(offeredamount) AS total, offereditem
	FROM philippy
	WHERE user_id = {$rs['user_id']}
	GROUP BY offereditem
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$checkpay[$rs2['offereditem']] += $rs2['total'];
	}
    $sql=<<<EOSQL
	SELECT SUM(amount) AS total, resource_id
	FROM attacks
	WHERE attacker = {$rs['user_id']}
    AND type = 'burden'
	GROUP BY resource_id
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$checkpay[$rs2['offereditem']] += $rs2['total'];
	}
	$sql=<<<EOSQL
	SELECT SUM(dio.amount) AS total, dio.resource_id
	FROM dealitems_offered dio
	INNER JOIN deals d ON d.deal_id = dio.deal_id
	WHERE d.fromuser = {$rs['user_id']}
	GROUP BY dio.resource_id
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$checkpay[$rs2['resource_id']] += $rs2['total'];
	}
	foreach ($checkpay as $resource_id => $amount) {
		if ($amount <= ($rs['production'] * 6) + 50) {
			$nopay[$resource_id] = true;
		}
	}
	if ($bankedresources) {
	foreach ($bankedresources as $resource_id => $amount) {
		if (!$nopay[$resource_id]) {
			$complement = $complements[$resource_id];
			$neededamount = ceil($amount * .4 * $personalsatmult * $alliancesatmult);
			if ($ownedresources[$complement] >= $neededamount) {
				addamount($complement, $rs['user_id'], $neededamount * -1);
				$ownedresources[$complement] -= $neededamount;
				$messages[] = "Your banked {$resourcename[$resource_id]} used up {$neededamount} {$resourcename[$complement]}.";
			} else {
				$lostamount = ceil($amount * .1 * $rs['tier']);
				addbanked($resource_id, $rs['user_id'], $lostamount * -1);
				$importantmessages[] = "You did not have enough {$resourcename[$complement]}, so you lost {$lostamount} banked {$resourcename[$resource_id]}.";
				$productionloss += $rs['tier'];
			}
		}
	}
	}
	$sql=<<<EOSQL
	SELECT deal_id FROM deals WHERE fromuser = '{$rs['user_id']}'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$sql=<<<EOSQL
		SELECT resource_id, amount FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}'
EOSQL;
		$sth3 = $GLOBALS['mysqli']->query($sql);
		while ($rs3 = mysqli_fetch_array($sth3)) {
			if (!$nopay[$rs3['resource_id']]) {
			$complement = $complements[$rs3['resource_id']];
			$neededamount = ceil($rs3['amount'] * .4 * $personalsatmult * $alliancesatmult);
			if ($ownedresources[$complement] >= $neededamount) {
				addamount($complement, $rs['user_id'], $neededamount * -1);
				$ownedresources[$complement] -= $neededamount;
				$messages[] = "Your {$rs3['amount']} {$resourcename[$rs3['resource_id']]} in your deal used up {$neededamount} {$resourcename[$complement]}.";
			} else {
				$importantmessages[] = "You did not have enough {$resourcename[$complement]}, so your offering of {$rs3['amount']} {$resourcename[$rs3['resource_id']]} in your deal was deleted.";
				$productionloss += $rs['tier'];
				$sql=<<<EOSQL
DELETE FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}' AND resource_id = {$rs3['resource_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
			}
		}
	}
	$sql=<<<EOSQL
	SELECT attack_id, amount, resource_id FROM attacks WHERE attacker = '{$rs['user_id']}' AND type = 'burden'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
        if (!$nopay[$rs2['resource_id']]) {
		$complement = $complements[$rs2['resource_id']];
		$neededamount = ceil($rs2['amount'] * .4 * $personalsatmult * $alliancesatmult);
		if ($ownedresources[$complement] >= $neededamount) {
            addamount($complement, $rs['user_id'], $neededamount * -1);
			$ownedresources[$complement] -= $neededamount;
			$messages[] = "Your {$rs2['amount']} {$resourcename[$rs2['resource_id']]} in your Burden attack used up {$neededamount} {$resourcename[$complement]}.";
        } else {
			$sql=<<<EOSQL
DELETE FROM attacks WHERE attack_id = '{$rs2['attack_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "You did not have enough {$resourcename[$complement]}, so your Burden attack of {$rs2['amount']} {$resourcename[$rs2['resource_id']]} was deleted.";
			$productionloss += $rs['tier'];
        }
        }
	}
    $sql=<<<EOSQL
	SELECT marketplace_id, offeredamount, multiplier, offereditem FROM marketplace
	WHERE user_id = '{$rs['user_id']}'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
        if (!$nopay[$rs2['offereditem']]) {
		$complement = $complements[$rs2['offereditem']];
		$neededamount = ceil($rs2['offeredamount'] * $rs2['multiplier'] * .4 * $personalsatmult * $alliancesatmult);
		if ($ownedresources[$complement] >= $neededamount) {
            addamount($complement, $rs['user_id'], $neededamount * -1);
			$ownedresources[$complement] -= $neededamount;
			$messages[] = "Your {$rs2['offeredamount']} {$resourcename[$rs2['offereditem']]} in the marketplace used up {$neededamount} {$resourcename[$complement]}.";
        } else {
			$sql=<<<EOSQL
DELETE FROM marketplace WHERE marketplace_id = '{$rs2['marketplace_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "You did not have enough {$resourcename[$complement]}, so your marketplace items of {$rs2['offeredamount']} {$resourcename[$rs2['offereditem']]} ({$rs2['multiplier']} items) was deleted.";
			$productionloss += ($rs['tier']);
        }
        }
	}
    $sql=<<<EOSQL
	SELECT philippy_id, offeredamount, offereditem FROM philippy
	WHERE user_id = '{$rs['user_id']}'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
        if (!$nopay[$rs2['offereditem']]) {
		$complement = $complements[$rs2['offereditem']];
		$neededamount = ceil($rs2['offeredamount'] * .4 * $personalsatmult * $alliancesatmult);
		if ($ownedresources[$complement] >= $neededamount) {
            addamount($complement, $rs['user_id'], $neededamount * -1);
			$ownedresources[$complement] -= $neededamount;
			$messages[] = "Your {$rs2['offeredamount']} {$resourcename[$rs2['offereditem']]} in Philippy used up {$neededamount} {$resourcename[$complement]}.";
        } else {
			$sql=<<<EOSQL
DELETE FROM philippy WHERE philippy_id = '{$rs2['philippy_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "You did not have enough {$resourcename[$complement]}, so your Philippy offering of {$rs2['offeredamount']} {$resourcename[$rs2['offereditem']]} was deleted.";
			$productionloss += ($rs['tier']);
        }
        }
	}
    foreach ($ownedresources as $checkingresource => $amount) {
        if (!$nopay[$checkingresource]) {
			$complement = $complements[$checkingresource];
			$neededamount = ceil($amount * .4 * $personalsatmult * $alliancesatmult);
			if ($ownedresources[$complement] >= $neededamount) {
				addamount($complement, $rs['user_id'], $neededamount * -1);
				$ownedresources[$complement] -= $neededamount;
				$messages[] = "Your {$resourcename[$checkingresource]} used up {$neededamount} {$resourcename[$complement]}.";
			} else {
				$lostamount = ceil($amount * .1 * $rs['tier']);
				addamount($checkingresource, $rs['user_id'], $lostamount * -1);
				$importantmessages[] = "You did not have enough {$resourcename[$complement]}, so you lost {$lostamount} {$resourcename[$checkingresource]}.";
				$productionloss += ($rs['tier']);
			}
        }
    }
	if ($productionloss) {
        $notifyproblems[$rs['alliance_id']][] = $rs['username'];
		$failsatloss = $productionloss * 10;
		if ($failsatloss >= $rs['satisfaction']) {
			$productionloss -= floor($rs['satisfaction'] / 10);
			$sql=<<<EOSQL
UPDATE users SET satisfaction = 0 WHERE user_id = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "You lost all your satisfaction from not being able to pay for your resources.";
		} else {
			$productionloss = 0;
			$rs['satisfaction'] -= $failsatloss;
			$sql=<<<EOSQL
UPDATE users SET satisfaction = satisfaction - '{$failsatloss}' WHERE user_id = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "You lost {$failsatloss} satisfaction from not being able to pay for your resources.";
		}
		if ($productionloss) {
			if ($productionloss >= $rs['production']) {
				$productionloss = $rs['production'] - 1;
			}
			$rs['production'] -= $productionloss;
			if ($rs['production'] < 6) $newtier = 1;
			else if ($rs['production'] < 21) $newtier = 2;
			else if ($rs['production'] < 41) $newtier = 3;
			else if ($rs['production'] < 71) $newtier = 4;
			else if ($rs['production'] < 101) $newtier = 5;
			else $newtier = 6;
			$sql=<<<EOSQL
			UPDATE users SET production = production - {$productionloss}, tier = {$newtier} WHERE user_id = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "You lost {$productionloss} production from not being able to pay for your resources.";
			if ($newtier < $rs['tier']) {
				$importantmessages[] = "Your tier has dropped to {$newtier}!";
				$rs['tier'] = $newtier;
			}
		}
	}
	$satloss = ceil(pow($rs['tier'], 2) * ($effectivesat / 500));
	if ($rs['satisfaction'] < $satloss) {
		$satloss = $rs['satisfaction'];
	}
	$sql=<<<EOSQL
	UPDATE users SET satisfaction = satisfaction - {$satloss} WHERE user_id = '{$rs['user_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$importantmessages[] = "Your regular satisfaction cost this tick was {$satloss}.";
	if ($productionloss) {
		$sql=<<<EOSQL
		DELETE FROM autocompounds
		WHERE user_id = '{$rs['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$importantmessages[] = "Your automatic compounding has been removed, as you have lost production this tick.";
	}
	$messagelist = implode("<br/>", $messages);
	$importantmessagelist = implode("<br/>", $importantmessages);
	$fullreport =<<<EOFORM
	<div id="{$reportid}" class="report-showbutton"><a href="javascript:;" onclick="document.getElementById('{$reportid}').style.display = 'none';
document.getElementById('{$reportid}x').style.display = 'block';">Show Details</a></div>
	<div id="{$reportid}x" class="report-details"><a href="javascript:;" onclick="document.getElementById('{$reportid}').style.display = 'block';
document.getElementById('{$reportid}x').style.display = 'none';">Hide Details</a><br/>
{$messagelist}
	</div>
	<b>{$importantmessagelist}</b>
EOFORM;
    $mysqlfullreport = $GLOBALS['mysqli']->real_escape_string($fullreport);
    $sql = "INSERT INTO reports (user_id, report, time) VALUES ({$rs['user_id']}, '{$mysqlfullreport}', NOW())";
    $GLOBALS['mysqli']->query($sql);
}
$sql = <<<EOSQL
DELETE FROM resources WHERE amount = '0'
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
SELECT * FROM alliances
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $messages = array();
	$importantmessages = array();
    $ownedresources = array();
    $checkpay = array();
    $bankedresources = array();
    $nopay = array();
	$productionloss = 0;
    $sql=<<<EOSQL
    SELECT COUNT(*) AS count FROM users WHERE alliance_id = '{$rs['alliance_id']}'
EOSQL;
	$usercounter = onelinequery($sql);
	$usercount = $usercounter['count'];
	$effectivealliancesat = $rs['alliancesatisfaction'];
	if ($effectivealliancesat > 1000) {
		$effectivealliancesat = 1000;
	}
	$sql=<<<EOSQL
	SELECT resource_id, amount FROM allianceresources
	WHERE alliance_id = '{$rs['alliance_id']}'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$ownedresources[$rs2['resource_id']] = $rs2['amount'];
	}
    $checkpay = $ownedresources;
    $sql=<<<EOSQL
	SELECT amount, resource_id
	FROM alliancebankedresources
	WHERE alliance_id = {$rs['alliance_id']}
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$bankedresources[$rs2['resource_id']] += $rs2['amount'];
		$checkpay[$rs2['resource_id']] += $rs2['amount'];
	}
    $sql=<<<EOSQL
	SELECT SUM(amount) AS total, resource_id
	FROM allianceattacks
	WHERE attacker = {$rs['alliance_id']}
    AND type = 'burden'
	GROUP BY resource_id
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$checkpay[$rs2['offereditem']] += $rs2['total'];
	}
	$sql=<<<EOSQL
	SELECT SUM(dio.amount) AS total, dio.resource_id
	FROM alliancedealitems_offered dio
	INNER JOIN alliancedeals d ON d.deal_id = dio.deal_id
	WHERE d.fromalliance = {$rs['alliance_id']}
	GROUP BY dio.resource_id
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$checkpay[$rs2['resource_id']] += $rs2['total'];
	}
    foreach ($checkpay as $resource_id => $amount) {
		if ($amount <= ($usercount * 100)) {
			$nopay[$resource_id] = true;
		}
	}
	$alliancesatmult = 1 - ($effectivealliancesat / 2000);
	foreach ($ownedresources as $checkingresource => $amount) {
        if (!$nopay[$checkingresource]) {
            $complement = $complements[$checkingresource];
            $neededamount = ceil($amount * .1 * $alliancesatmult);
            if ($ownedresources[$complement] >= $neededamount) {
                allianceaddamount($complement, $rs['alliance_id'], $neededamount * -1);
                $ownedresources[$complement] -= $neededamount;
                $messages[] = "The alliance's {$resourcename[$checkingresource]} used up {$neededamount} {$resourcename[$complement]}.";
            } else {
                $lostamount = ceil($amount * .5);
                allianceaddamount($checkingresource, $rs['alliance_id'], $lostamount * -1);
                $importantmessages[] = "The alliance did not have enough {$resourcename[$complement]}, so it lost {$lostamount} {$resourcename[$checkingresource]}.";
                $productionloss++;
            }
        }
    }
    if ($bankedresources) {
        foreach ($bankedresources as $resource_id => $amount) {
            if (!$nopay[$resource_id]) {
                $complement = $complements[$resource_id];
                $neededamount = ceil($amount * .1 * $alliancesatmult);
                if ($ownedresources[$complement] >= $neededamount) {
                    allianceaddamount($complement, $rs['alliance_id'], $neededamount * -1);
                    $ownedresources[$complement] -= $neededamount;
                    $messages[] = "The alliance's banked {$resourcename[$resource_id]} used up {$neededamount} {$resourcename[$complement]}.";
                } else {
                    $lostamount = ceil($amount * .5);
                    allianceaddbanked($resource_id, $rs['alliance_id'], $lostamount * -1);
                    $importantmessages[] = "Your alliance did not have enough {$resourcename[$complement]}, so it lost {$lostamount} banked {$resourcename[$resource_id]}.";
                    $productionloss++;
                }
            }
        }
	}
    $sql=<<<EOSQL
	SELECT deal_id FROM alliancedeals WHERE fromalliance = '{$rs['alliance_id']}'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$sql=<<<EOSQL
		SELECT resource_id, amount FROM alliancedealitems_offered WHERE deal_id = '{$rs2['deal_id']}'
EOSQL;
		$sth3 = $GLOBALS['mysqli']->query($sql);
		while ($rs3 = mysqli_fetch_array($sth3)) {
			if (!$nopay[$rs3['resource_id']]) {
			$complement = $complements[$rs3['resource_id']];
			$neededamount = ceil($rs3['amount'] * .1 * $alliancesatmult);
			if ($ownedresources[$complement] >= $neededamount) {
				allianceaddamount($complement, $rs['alliance_id'], $neededamount * -1);
				$ownedresources[$complement] -= $neededamount;
				$messages[] = "Your alliance's {$rs3['amount']} {$resourcename[$rs3['resource_id']]} in its deal used up {$neededamount} {$resourcename[$complement]}.";
			} else {
$importantmessages[] = "Your alliance did not have enough {$resourcename[$complement]}, so its offering of {$rs3['amount']} {$resourcename[$rs3['resource_id']]} in its deal was deleted.";
				$productionloss++;
				$sql=<<<EOSQL
DELETE FROM alliancedealitems_offered WHERE deal_id = '{$rs2['deal_id']}' AND resource_id = {$rs3['resource_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
			}
		}
	}
    $sql=<<<EOSQL
	SELECT attack_id, amount, resource_id FROM allianceattacks WHERE attacker = '{$rs['alliance_id']}' AND type = 'burden'
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
        if (!$nopay[$rs2['resource_id']]) {
		$complement = $complements[$rs2['resource_id']];
		$neededamount = ceil($rs2['amount'] * .1 * $alliancesatmult);
		if ($ownedresources[$complement] >= $neededamount) {
            allianceaddamount($complement, $rs['alliance_id'], $neededamount * -1);
			$ownedresources[$complement] -= $neededamount;
			$messages[] = "Your {$rs2['amount']} {$resourcename[$rs2['resource_id']]} in your Burden attack used up {$neededamount} {$resourcename[$complement]}.";
        } else {
			$sql=<<<EOSQL
DELETE FROM allianceattacks WHERE attack_id = '{$rs2['attack_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "Your alliance did not have enough {$resourcename[$complement]}, so its Burden attack of {$rs2['amount']} {$resourcename[$rs2['resource_id']]} was deleted.";
			$productionloss++;
        }
        }
	}
	if ($productionloss) {
		$failsatloss = $productionloss * 10;
		if ($failsatloss >= $rs['alliancesatisfaction']) {
			$productionloss -= floor($rs['alliancesatisfaction'] / 10);
			$sql=<<<EOSQL
UPDATE alliances SET alliancesatisfaction = 0 WHERE alliance_id = '{$rs['alliance_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "The alliance lost all its satisfaction from not being able to pay for its resources.";
		} else {
			$productionloss = 0;
			$rs['alliancesatisfaction'] -= $failsatloss;
			$sql=<<<EOSQL
UPDATE alliances SET alliancesatisfaction = alliancesatisfaction - '{$failsatloss}' WHERE alliance_id = '{$rs['alliance_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$importantmessages[] = "The alliance lost {$failsatloss} satisfaction from not being able to pay for its resources.";
		}
		if ($productionloss) {
			$importantmessages[] = "Everyone in the alliance lost {$productionloss} production and all of their autocompounding because the alliance could not pay for its resources.";
			$sql=<<<EOSQL
			SELECT production, tier, user_id FROM users WHERE alliance_id = '{$rs['alliance_id']}'
EOSQL;
			$sth2 = $GLOBALS['mysqli']->query($sql);
			while ($rs2 = mysqli_fetch_array($sth2)) {
				if ($productionloss >= $rs2['production']) {
					$productionloss = $rs2['production'] - 1;
				}
				$rs2['production'] -= $productionloss;
				if ($rs2['production'] < 6) $newtier = 1;
				else if ($rs2['production'] < 21) $newtier = 2;
				else if ($rs2['production'] < 41) $newtier = 3;
				else if ($rs2['production'] < 71) $newtier = 4;
				else if ($rs2['production'] < 101) $newtier = 5;
				else $newtier = 6;
				$sql=<<<EOSQL
				UPDATE users SET production = production - {$productionloss}, tier = {$newtier} WHERE user_id = '{$rs2['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
				DELETE FROM autocompounds WHERE user_id = '{$rs2['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
		}
	}
	$satloss = $usercount;
	if ($rs['alliancesatisfaction'] < $satloss) {
		$satloss = $rs['alliancesatisfaction'];
	}
	$sql=<<<EOSQL
	UPDATE alliances SET alliancesatisfaction = alliancesatisfaction - {$satloss} WHERE alliance_id = '{$rs['alliance_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$importantmessages[] = "The alliance's regular satisfaction cost this tick was {$satloss}.";
	$messagelist = implode("<br/>", $messages);
	$importantmessagelist = implode("<br/>", $importantmessages);
	$fullreport =<<<EOFORM
<div id="{$reportid}" class="report-showbutton"><a href="javascript:;" onclick="document.getElementById('{$reportid}').style.display = 'none';
document.getElementById('{$reportid}x').style.display = 'block';">Show Details</a></div>
<div id="{$reportid}x" class="report-details"><a href="javascript:;" onclick="document.getElementById('{$reportid}').style.display = 'block';
document.getElementById('{$reportid}x').style.display = 'none';">Hide Details</a><br/>
{$messagelist}
	</div>
	<b>{$importantmessagelist}</b>
EOFORM;
    $mysqlfullreport = $GLOBALS['mysqli']->real_escape_string($fullreport);
    $sql = "INSERT INTO alliancereports (alliance_id, report, time) VALUES ({$rs['alliance_id']}, '{$mysqlfullreport}', NOW())";
    $GLOBALS['mysqli']->query($sql);
}
if ($notifyproblems) {
foreach ($notifyproblems AS $alliance_id => $userarray) {
    $userproblemlist = implode(",", $userarray);
	//seeproblems
    $sql=<<<EOSQL
    SELECT alliance_id FROM alliance_groupabilities
	WHERE alliance_id = {$alliance_id} AND ability_id = 1
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$message = $GLOBALS['mysqli']->real_escape_string("The following alliance members could not pay complements: {$userproblemlist}");
		$sql=<<<EOSQL
		INSERT INTO alliance_messages (alliance_id, user_id, message, posted)
		VALUES ({$rs['alliance_id']}, 0, '{$message}', NOW())
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	}
}
}
//war
$sql=<<<EOSQL
SELECT * FROM attacks WHERE ticks = 0
ORDER BY sent ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($attack = mysqli_fetch_array($sth)) {
	$defendermessages = array();
	$attackermessages = array();
	$stopped = false;
	$sql=<<<EOSQL
    SELECT * FROM users WHERE user_id = {$attack['attacker']}
EOSQL;
	$attackerinfo = onelinequery($sql);
    $sql=<<<EOSQL
    SELECT * FROM users WHERE user_id = {$attack['defender']}
EOSQL;
	$defenderinfo = onelinequery($sql);
	if (!$defenderinfo['alliance_id']) {
		$stopped = true;
		$attackermessages[] = "Your target lost his alliance before your attack hit!";
		if ($attack['type'] == "burden") {
			addamount($attack['resource_id'], $attack['attacker'], $attack['amount']);
		}
	}
	if (!$stopped) {
		if ($attack['type'] == "burden") {
			$attackermessages[] = "You burdened {$defenderinfo['username']} with {$attack['amount']} {$resourcename[$attack['resource_id']]}!";
			$defendermessages[] = "{$attackerinfo['username']} burdened you with {$attack['amount']} {$resourcename[$attack['resource_id']]}!";
			addamount($attack['resource_id'], $attack['defender'], $attack['amount']);
		} else if ($attack['type'] == "corrupt") {
			if (!$attack['focusamount']) {
				$attackermessages[] = "You corrupted {$defenderinfo['username']} to have a focus of {$resourcename[$attack['resource_id']]}!";
				$defendermessages[] = "{$attackerinfo['username']} corrupted you to have a focus of {$resourcename[$attack['resource_id']]}!";
				$sql=<<<EOSQL
UPDATE users SET focus = {$attack['resource_id']}, focusamount = 1
WHERE user_id = {$attack['defender']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			} else if ($attack['resource_id'] == $defenderinfo['focus']) {
				$attackermessages[] = "You tried to corrupt {$defenderinfo['username']}'s focus into {$resourcename[$attack['resource_id']]}, but that was already his focus!";
				$defendermessages[] = "{$attackerinfo['username']} tried to corrupt your focus into {$resourcename[$attack['resource_id']]}, but that was already your focus!";
			} else {
				$sql=<<<EOSQL
UPDATE users SET focus = {$attack['resource_id']}
WHERE user_id = {$attack['defender']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$attackermessages[] = "You corrupted {$defenderinfo['username']} to have a focus of {$resourcename[$attack['resource_id']]}!";
				$defendermessages[] = "{$attackerinfo['username']} corrupted you to have a focus of {$resourcename[$attack['resource_id']]}!";
			}
		} else if ($attack['type'] == "brutal") {
			if ($defenderinfo['production'] == 1) {
				$attackermessages[] = "You attacked {$defenderinfo['username']} with Brutality, but his production was already 1!";
				$defendermessages[] = "{$attackerinfo['username']} attacked you with Brutality, but your production was already 1!";
			} else {
				$attackermessages[] = "You lowered {$defenderinfo['username']}'s production by 1!";
				$defendermessages[] = "{$attackerinfo['username']} lowered your production by 1!";
				$sql=<<<EOSQL
UPDATE users SET production = production - 1
WHERE user_id = {$attack['defender']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
		} else if ($attack['type'] == "despair") {
			if ($defenderinfo['satisfaction'] == 0) {
				$attackermessages[] = "You attacked {$defenderinfo['username']} with Despair, but he already had a satisfaction of 0!";
				$defendermessages[] = "{$attackerinfo['username']} attacked you with Despair, but your satisfaction was already 0!";
			} else if ($defenderinfo['satisfaction'] <= 200) {
				$attackermessages[] = "You reduced {$defenderinfo['username']}'s satisfaction to 0!";
				$defendermessages[] = "{$attackerinfo['username']} reduced your satisfaction to 0!";
				$sql=<<<EOSQL
UPDATE users SET satisfaction = 0
WHERE user_id = {$attack['defender']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			} else {
				$attackermessages[] = "You reduced {$defenderinfo['username']}'s satisfaction by 200!";
				$defendermessages[] = "{$attackerinfo['username']} reduced your satisfaction by 200!";
				$sql=<<<EOSQL
UPDATE users SET satisfaction = satisfaction - 200
WHERE user_id = {$attack['defender']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
		} else if ($attack['type'] == "robbery") {
            $sql=<<<EOSQL
SELECT amount FROM resources
WHERE user_id = {$attack['defender']}
AND resource_id = {$attack['resource_id']}
EOSQL;
			$robamount = onelinequery($sql);
			if ($robamount['amount']) {
				$attackermessages[] = "You robbed all of {$defenderinfo['username']}'s {$resourcename[$attack['resource_id']]}! ({$robamount['amount']})";
				$defendermessages[] = "{$attackerinfo['username']} robbed all of your {$resourcename[$attack['resource_id']]}! ({$robamount['amount']})";
				addamount($attack['resource_id'], $attack['attacker'], $robamount['amount']);
				$sql=<<<EOSQL
DELETE FROM resources
WHERE user_id = {$attack['defender']}
AND resource_id = {$attack['resource_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			} else {
				$attackermessages[] = "You tried to rob all of {$defenderinfo['username']}'s {$resourcename[$attack['resource_id']]}, but there wasn't any to steal!";
				$defendermessages[] = "{$attackerinfo['username']} tried to rob all of your {$resourcename[$attack['resource_id']]}, but there wasn't any to steal!";
			}
        }
	}
	if (!empty($attackermessages)) {
		$messagelist = implode("<br/>", $attackermessages);
		$fullreport =<<<EOFORM
		<b>{$messagelist}</b>
EOFORM;
		$mysqlfullreport = $GLOBALS['mysqli']->real_escape_string($fullreport);
		$sql = "INSERT INTO reports (user_id, report, time) VALUES ({$attack['attacker']}, '{$mysqlfullreport}', NOW())";
		$GLOBALS['mysqli']->query($sql);
	}
	if (!empty($defendermessages)) {
		$messagelist = implode("<br/>", $defendermessages);
		$fullreport =<<<EOFORM
		<b>{$messagelist}</b>
EOFORM;
		$mysqlfullreport = $GLOBALS['mysqli']->real_escape_string($fullreport);
		$sql = "INSERT INTO reports (user_id, report, time) VALUES ({$attack['defender']}, '{$mysqlfullreport}', NOW())";
		$GLOBALS['mysqli']->query($sql);
	}
}
$sql=<<<EOSQL
SELECT * FROM allianceattacks WHERE ticks = 0
ORDER BY sent ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($attack = mysqli_fetch_array($sth)) {
	$defendermessages = array();
	$attackermessages = array();
	$stopped = false;
	$sql=<<<EOSQL
    SELECT * FROM alliances WHERE alliance_id = {$attack['attacker']}
EOSQL;
	$attackerinfo = onelinequery($sql);
    $sql=<<<EOSQL
    SELECT * FROM alliances WHERE alliance_id = {$attack['defender']}
EOSQL;
	$defenderinfo = onelinequery($sql);
	if ($attack['type'] == "burden") {
		$attackermessages[] = "Your alliance burdened {$defenderinfo['name']} with {$attack['amount']} {$resourcename[$attack['resource_id']]}!";
		$defendermessages[] = "{$attackerinfo['name']} burdened your alliance with {$attack['amount']} {$resourcename[$attack['resource_id']]}!";
		allianceaddamount($attack['resource_id'], $attack['defender'], $attack['amount']);
	} else if ($attack['type'] == "corrupt") {
		if (!$attack['focusamount']) {
			$attackermessages[] = "Your alliance corrupted {$defenderinfo['name']} to have a focus of {$resourcename[$attack['resource_id']]}!";
			$defendermessages[] = "{$attackerinfo['name']} corrupted your alliance to have a focus of {$resourcename[$attack['resource_id']]}!";
			$sql=<<<EOSQL
UPDATE alliances SET alliancefocus = {$attack['resource_id']}, alliancefocusamount = 1
WHERE alliance_id = {$attack['defender']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		} else if ($attack['resource_id'] == $defenderinfo['focus']) {
			$attackermessages[] = "Your alliance tried to corrupt {$defenderinfo['name']}'s focus into {$resourcename[$attack['resource_id']]}, but that was already its focus!";
			$defendermessages[] = "{$attackerinfo['name']} tried to corrupt your focus into {$resourcename[$attack['resource_id']]}, but that was already your alliance's focus!";
		} else {
			$sql=<<<EOSQL
UPDATE alliances SET alliancefocus = {$attack['resource_id']}
WHERE alliance_id = {$attack['defender']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$attackermessages[] = "Your alliance corrupted {$defenderinfo['name']} to have a focus of {$resourcename[$attack['resource_id']]}!";
			$defendermessages[] = "{$attackerinfo['name']} corrupted you to have a focus of {$resourcename[$attack['resource_id']]}!";
		}
	} else if ($attack['type'] == "sadness") {
		if ($defenderinfo['alliancesatisfaction'] == 0) {
			$attackermessages[] = "Your alliance attacked {$defenderinfo['name']} with Despair, but he already had a satisfaction of 0!";
			$defendermessages[] = "{$attackerinfo['name']} attacked you with Despair, but your satisfaction was already 0!";
		} else if ($defenderinfo['alliancesatisfaction'] <= 500) {
			$attackermessages[] = "Your alliance reduced {$defenderinfo['name']}'s satisfaction to 0!";
			$defendermessages[] = "{$attackerinfo['name']} reduced your alliance's satisfaction to 0!";
			$sql=<<<EOSQL
UPDATE alliances SET alliancesatisfaction = 0
WHERE alliance_id = {$attack['defender']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		} else {
			$attackermessages[] = "Your alliance reduced {$defenderinfo['name']}'s satisfaction by 500!";
			$defendermessages[] = "{$attackerinfo['name']} reduced your alliance's satisfaction by 500!";
			$sql=<<<EOSQL
UPDATE alliances SET alliancesatisfaction = alliancesatisfaction - 500
WHERE alliance_id = {$attack['defender']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
	} else if ($attack['type'] == "theft") {
		$sql=<<<EOSQL
SELECT amount FROM allianceresources
WHERE alliance_id = {$attack['defender']}
AND resource_id = {$attack['resource_id']}
EOSQL;
		$allianceamount = onelinequery($sql);
		$sql=<<<EOSQL
		SELECT r.amount, u.user_id, u.username
		FROM resources r
		INNER JOIN users u ON u.user_id = r.user_id
		WHERE r.resource_id = {$attack['resource_id']}
		AND u.alliance_id = {$attack['defender']}
		ORDER BY amount DESC
		LIMIT 1
EOSQL;
		$useramount = onelinequery($sql);
		if ($allianceamount['amount'] >= $useramount['amount']) {
			if ($allianceamount['amount']) {
				allianceaddamount($attack['resource_id'], $attack['attacker'], $allianceamount['amount']);
				$sql=<<<EOSQL
DELETE FROM allianceresources
WHERE alliance_id = {$attack['defender']}
AND resource_id = {$attack['resource_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$attackermessages[] = "Your alliance stole all of {$defenderinfo['name']}'s {$resourcename[$attack['resource_id']]}! ({$allianceamount['amount']})";
				$defendermessages[] = "{$attackerinfo['name']} stole all of your alliance's {$resourcename[$attack['resource_id']]}! ({$allianceamount['amount']})";
			} else {
				$attackermessages[] = "Your alliance tried to steal all of {$defenderinfo['name']}'s {$resourcename[$attack['resource_id']]}, but there wasn't any to steal in the whole alliance!";
				$defendermessages[] = "{$attackerinfo['name']} tried to steal all of your alliance's {$resourcename[$attack['resource_id']]}, but there wasn't any to steal in the whole alliance!";
			}
		} else {
			$attackermessages[] = "Your alliance stole all of {$useramount['username']}'s {$resourcename[$attack['resource_id']]}! ({$useramount['amount']})";
			$defendermessages[] = "The alliance {$attackerinfo['name']} stole all of {$useramount['username']}'s {$resourcename[$attack['resource_id']]}! ({$useramount['amount']})";
			$usermessage = "The alliance {$attackerinfo['name']} stole all of {$useramount['username']}'s {$resourcename[$attack['resource_id']]}! ({$useramount['amount']})";
			$mysqlusermessage = $GLOBALS['mysqli']->real_escape_string($usermessage);
			$sql = "INSERT INTO reports (user_id, report, time) VALUES ({$useramount['user_id']}, '{$mysqlusermessage}', NOW())";
			$GLOBALS['mysqli']->query($sql);
			allianceaddamount($attack['resource_id'], $attack['attacker'], $useramount['amount']);
			$sql=<<<EOSQL
DELETE FROM resources
WHERE user_id = {$useramount['user_id']}
AND resource_id = {$attack['resource_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		} 
	}
	if (!empty($attackermessages)) {
		$messagelist = implode("<br/>", $attackermessages);
		$fullreport =<<<EOFORM
		<b>{$messagelist}</b>
EOFORM;
		$mysqlfullreport = $GLOBALS['mysqli']->real_escape_string($fullreport);
		$sql = "INSERT INTO alliancereports (alliance_id, report, time) VALUES ({$attack['attacker']}, '{$mysqlfullreport}', NOW())";
		$GLOBALS['mysqli']->query($sql);
	}
	if (!empty($defendermessages)) {
		$messagelist = implode("<br/>", $defendermessages);
		$fullreport =<<<EOFORM
		<b>{$messagelist}</b>
EOFORM;
		$mysqlfullreport = $GLOBALS['mysqli']->real_escape_string($fullreport);
		$sql = "INSERT INTO alliancereports (alliance_id, report, time) VALUES ({$attack['defender']}, '{$mysqlfullreport}', NOW())";
		$GLOBALS['mysqli']->query($sql);
	}
}
$sql=<<<EOSQL
DELETE FROM attacks WHERE ticks = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
UPDATE attacks SET ticks = ticks - 1
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM allianceattacks WHERE ticks = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
UPDATE allianceattacks SET ticks = ticks - 1
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql = <<<EOSQL
DELETE FROM allianceresources WHERE amount = '0'
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
UPDATE user_abilities SET turns = turns - 1
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM user_abilities WHERE turns = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
UPDATE alliance_groupabilities SET turns = turns - 1
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM alliance_groupabilities WHERE turns = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
UPDATE peacetreaties SET turns = turns - 1
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM peacetreaties WHERE turns = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM kickattempts WHERE 1=1
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM philippytaken WHERE 1=1
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM messages WHERE sent < DATE_SUB(NOW(), INTERVAL 4 WEEK)
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM alliance_messages WHERE posted < DATE_SUB(NOW(), INTERVAL 4 WEEK)
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM news WHERE posted < DATE_SUB(NOW(), INTERVAL 4 WEEK)
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM resources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM allianceresources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE from logins WHERE logindate < DATE_SUB(NOW(), INTERVAL 3 DAY)
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql = "DELETE FROM alliancereports WHERE time < DATE_SUB(NOW() , INTERVAL 3 DAY)";
$GLOBALS['mysqli']->query($sql);
$sql = "DELETE FROM reports WHERE time < DATE_SUB(NOW() , INTERVAL 3 DAY)";
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM bankedresources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM alliancebankedresources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
UPDATE users SET stasismode = 1
WHERE user_id NOT IN (SELECT user_id FROM logins)
EOSQL;
$GLOBALS['mysqli']->query($sql);
?>