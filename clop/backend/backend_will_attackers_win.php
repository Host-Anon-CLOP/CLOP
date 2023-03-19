<?php
//// WAR
$midnight = "unset"
if (strtotime('now') < strtotime('midnight')) {
	$midnight =	strtotime('midnight');
} else {
	$midnight = strtotime('tomorrow midnight');
}

$midday = "unset"
if (strtotime('now') < strtotime('noon')) {
	$midday = strtotime('noon');
 } else {
	$midday = strtotime('tomorrow noon');
 }
#$TimeUntilNextWarTick = ?;

/*
$hour = date("H");
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
//are there any defenders remaining?
$sql=<<<EOSQL
SELECT forcegroup_id FROM forcegroups
WHERE location_id = {$battleground}
AND attack_mission = 0
AND departuredate IS NULL
EOSQL;
$defendersremaining = onelinequery($sql);
if (!$defendersremaining) {
}
*/
?>
