//// WAR

if (date("G") == 0 || date("G") == 12) {
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