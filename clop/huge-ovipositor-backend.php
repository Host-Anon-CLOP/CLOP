<?php
require_once("backend/allfunctions.php");
if ( (strpos($_ENV["DOMAIN_URL"], "test.4clop") !== true ) or (strpos($_ENV["DOMAIN_URL"], "localhost") !== true ) ) {

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attackerData = [];
    $defenderData = [];

    $forcetypes = array("Cavalry" => 1, "Tanks" => 2, "Pegasi" => 3, "Unicorns" => 4, "Naval" => 5, "Alicorns" => 6);
    $weapontypes = array("ScroungedWeapons" => 0, "PRC-E6" => 1,"PRC-E7" => 2,"PRC-E8" => 3,"ACFU" => 4,"ATFU" => 5,"APFU" => 6,"AUFU" => 7,"K9P" => 8,"ELBO-GRS" => 9,"Chem-LightBattery" => 10,"PropWash" => 11,"SteamBucket" => 12,"CanopyLights" => 13,"LongStand" => 14,"LongWeight" => 15,"GridSquares" => 16,"Shoreline" => 17,"WaterHammer" => 18,"WaterlineEraser" => 19);
    $armortypes = array("ScroungedArmor" => 0,"Barding" => 1,"Bigdog" => 2,"Nope" => 3,"Trundle" => 4,"Shepherd" => 5,"Ohno" => 6,"Titan" => 7,"Cooler" => 8,"Wonder" => 9,"Griffin" => 10,"Dragon" => 11,"Hornshield" => 12,"Librarian" => 13,"Shining" => 14,"D2A" => 15,"C-PON3" => 16,"Esohes" => 17,"Shubidu" => 18);

# Declare Initial Variables
foreach ($forcetypes as $key => $value) {
    $Attackers_Damage_{$key} = 0;
	$Defenders_Damage_{$key} = 0;
	$Attackers_Initial_{$key} = 0;
	$Defenders_Initial_{$key} = 0;
	$Attackers_Died_{$key} = 0;
	$Defenders_Died_{$key} = 0;
	$Attackers_Remaining_{$key} = 0;
	$Defenders_Remaining_{$key} = 0;
	$Attackers_Damage_Initial_{$key} = 0;
	$Defenders_Damage_Initial_{$key} = 0;
	$Attackers_Damage_Remaining_{$key} = 0;
	$Defenders_Damage_Remaining_{$key} = 0;
}
$Initial_Attackers = 0;
$Initial_Defenders = 0;
$Attackers_Died = 0;
$Defenders_Died = 0;
$Attackers_Remaining = 0;
$Defenders_Remaining = 0;

# Clear Previous Results
$sql=<<<EOSQL
truncate forcegroups_calc
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
truncate forces_calc
EOSQL;
$GLOBALS['mysqli']->query($sql);

# Create Attackers Forcegroup
$sql=<<<EOSQL
INSERT INTO forcegroups_calc (nation_id, location_id, attack_mission, name) VALUES (1, 2, 1, 'attackers')
EOSQL;
$GLOBALS['mysqli']->query($sql);

# Create Defenders Forcegroup
$sql=<<<EOSQL
INSERT INTO forcegroups_calc (nation_id, location_id, attack_mission, name) VALUES (2, 2, 0, 'Defenders')
EOSQL;
$GLOBALS['mysqli']->query($sql);

    $count = 0;
    // Retrieve data for attackers
    if (!empty($_POST['attack_type']) && is_array($_POST['attack_type'])) {

        foreach ($_POST['attack_type'] as $index => $which_type) {
            if (!empty($which_type)) {
                $name = $which_type . '_' . $_POST['weapon'][$count] . '_' . $_POST['armor'][$count] . '_size_' . $_POST['size'][$count]. '_train_' . $_POST['training'][$count];

                $attackerData[] = [
                    'unit' => $which_type,
                    'weapon' => $_POST['weapon'][$count],
                    'armor' => $_POST['armor'][$count],
                    'size' => $_POST['size'][$count],
                    'training' => $_POST['training'][$count],
                    'name' => $name
                ];

# Create Attackers
$sql=<<<EOSQL
INSERT INTO forces_calc (nation_id, size, type, weapon_id, armor_id, training, name, forcegroup_id) VALUES (1, {$_POST['size'][$count]}, {$forcetypes[$which_type]}, '{$weapontypes[$_POST['weapon'][$count]]}', {$armortypes[$_POST['armor'][$count]]}, {$_POST['training'][$count]}, 'A_$name', 1)
EOSQL;
$GLOBALS['mysqli']->query($sql);

            $count += 1;
            }
        }
    }

    // Retrieve data for Defenders
    if (!empty($_POST['defend_type']) && is_array($_POST['defend_type'])) {

        foreach ($_POST['defend_type'] as $index => $which_type) {
            if (!empty($which_type)) {
                $name = $which_type . '_' . $_POST['weapon'][$count] . '_' . $_POST['armor'][$count] . '_size_' . $_POST['size'][$count]. '_train_' . $_POST['training'][$count];

                $defenderData[] = [
                    'unit' => $which_type,
                    'weapon' => $_POST['weapon'][$count],
                    'armor' => $_POST['armor'][$count],
                    'size' => $_POST['size'][$count],
                    'training' => $_POST['training'][$count],
                    'name' => $name
                ];

# Create Defenders
$sql=<<<EOSQL
INSERT INTO forces_calc (nation_id, size, type, weapon_id, armor_id, training, name, forcegroup_id) VALUES (2, {$_POST['size'][$count]}, {$forcetypes[$which_type]}, '{$weapontypes[$_POST['weapon'][$count]]}', {$armortypes[$_POST['armor'][$count]]}, {$_POST['training'][$count]}, 'D_$name', 2)
EOSQL;
$GLOBALS['mysqli']->query($sql);

            $count += 1;
            }
        }
    }

	// Display Defender Bonus
	echo "Defender Bonus: " . $_POST['defender_bonus'];

    // Display the entered data
	/*
    echo "<h2>Attacker Data:</h2>";
    echo "<pre>";
    foreach ($attackerData as $attacker) {
        echo $attacker['unit'] . ' ' . $attacker['weapon'] . ' ' . $attacker['armor'] . ' size:' . $attacker['size'] . ' train:' . $attacker['training'] . '<br>';
    }
    echo "</pre>";

    echo "<h2>Defender Data:</h2>";
    echo "<pre>";
    foreach ($defenderData as $defender) {
        echo $defender['unit'] . ' ' . $defender['weapon'] . ' ' . $defender['armor'] . ' size:' . $defender['size'] . ' train:' . $defender['training'] . '<br>';
    }
    echo "</pre>";
	*/

// CALCULATE INITIAL TROOPS FOR RESULTS
# Attackers
$sql = "SELECT * from forces_calc fc WHERE forcegroup_id = '1' ORDER BY size DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$Initial_Attackers = $Initial_Attackers + $rs['size'];
	$Attackers_Initial_{array_search($rs['type'], $forcetypes)} = $Attackers_Initial_{array_search($rs['type'], $forcetypes)} + $rs['size'];
	}
$sql = "SELECT * from forces_calc fc LEFT JOIN weapondefs w ON fc.weapon_id = w.weapon_id LEFT JOIN armordefs a ON fc.armor_id = a.armor_id WHERE forcegroup_id = '1' ORDER BY size DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if (!$rs['weapon_id']) {
	$Attackers_Damage_Initial_Cavalry = $Attackers_Damage_Initial_Cavalry + (.25 * $rs['size']);
	$Attackers_Damage_Initial_Pegasi = $Attackers_Damage_Initial_Pegasi + (.25 * $rs['size']);
	$Attackers_Damage_Initial_Tanks = $Attackers_Damage_Initial_Tanks + (.25 * $rs['size']);
	$Attackers_Damage_Initial_Unicorns = $Attackers_Damage_Initial_Unicorns + (.25 * $rs['size']);
	$Attackers_Damage_Initial_Naval = $Attackers_Damage_Initial_Naval + (.25 * $rs['size']);
	} else {
	$Attackers_Damage_Initial_Cavalry = $Attackers_Damage_Initial_Cavalry + ($rs['dmg_cavalry'] * $rs['size']);
	$Attackers_Damage_Initial_Pegasi = $Attackers_Damage_Initial_Pegasi + ($rs['dmg_pegasi'] * $rs['size']);
	$Attackers_Damage_Initial_Tanks = $Attackers_Damage_Initial_Tanks + ($rs['dmg_tanks'] * $rs['size']);
	$Attackers_Damage_Initial_Unicorns = $Attackers_Damage_Initial_Unicorns + ($rs['dmg_unicorns'] * $rs['size']);
	$Attackers_Damage_Initial_Naval = $Attackers_Damage_Initial_Naval + ($rs['dmg_naval'] * $rs['size']);
	}
	}	
# Defenders
$sql = "SELECT * from forces_calc fc WHERE forcegroup_id = '2' ORDER BY size DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$Initial_Defenders = $Initial_Defenders + $rs['size'];
	$Defenders_Initial_{array_search($rs['type'], $forcetypes)} = $Defenders_Initial_{array_search($rs['type'], $forcetypes)} + $rs['size'];
}
$sql = "SELECT * from forces_calc fc LEFT JOIN weapondefs w ON fc.weapon_id = w.weapon_id LEFT JOIN armordefs a ON fc.armor_id = a.armor_id WHERE forcegroup_id = '2' ORDER BY size DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if (!$rs['weapon_id']) {
	$Defenders_Damage_Initial_Cavalry = $Defenders_Damage_Initial_Cavalry + (.25 * $rs['size']);
	$Defenders_Damage_Initial_Pegasi = $Defenders_Damage_Initial_Pegasi + (.25 * $rs['size']);
	$Defenders_Damage_Initial_Tanks = $Defenders_Damage_Initial_Tanks + (.25 * $rs['size']);
	$Defenders_Damage_Initial_Unicorns = $Defenders_Damage_Initial_Unicorns + (.25 * $rs['size']);
	$Defenders_Damage_Initial_Naval = $Defenders_Damage_Initial_Naval + (.25 * $rs['size']);
	} else {
	$Defenders_Damage_Initial_Cavalry = $Defenders_Damage_Initial_Cavalry + ($rs['dmg_cavalry'] * $rs['size']);
	$Defenders_Damage_Initial_Pegasi = $Defenders_Damage_Initial_Pegasi + ($rs['dmg_pegasi'] * $rs['size']);
	$Defenders_Damage_Initial_Tanks = $Defenders_Damage_Initial_Tanks + ($rs['dmg_tanks'] * $rs['size']);
	$Defenders_Damage_Initial_Unicorns = $Defenders_Damage_Initial_Unicorns + ($rs['dmg_unicorns'] * $rs['size']);
	$Defenders_Damage_Initial_Naval = $Defenders_Damage_Initial_Naval + ($rs['dmg_naval'] * $rs['size']);
	}
}


echo "<h2>Battle Result</h2>";
// WAR CALCS
$types = array(1 => "cavalry", 2 => "tanks", 3 => "pegasi", 4 => "unicorns", 5 => "naval");
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
FROM forces_calc f
LEFT JOIN nations n ON f.nation_id = n.nation_id
INNER JOIN forcegroups_calc fg ON f.forcegroup_id = fg.forcegroup_id
LEFT JOIN armordefs ad ON f.armor_id = ad.armor_id
LEFT JOIN weapondefs wd ON f.weapon_id = wd.weapon_id
WHERE fg.location_id = 2 AND fg.departuredate IS NULL
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
					if ($_POST['defender_bonus'] == "Yes") {
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
    echo "{$units[$attackerid]['name']} (size {$units[$attackerid]['size']}) hit {$units[$defenderid]['name']} (size {$units[$defenderid]['size']}) for {$damage} damage ({$hitinfo[$attackerid][$defenderid]} hits)<br>";
	/*
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
    */
    }
}

foreach ($units as $unit) {
	$unit['damage'] = floor(round($unit['damage'], 6)); //Seriously, fuck floating point errors and fuck hidden precision
	if ($unit['damage'] > 0) {
		if ($unit['damage'] < $unit['size']) {
			$sql =<<<EOSQL
			UPDATE forces_calc SET size = size - {$unit['damage']} WHERE force_id = '{$unit['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			echo "{$unit['name']} lost {$unit['damage']} size!<br>";
		} else {
			$sql =<<<EOSQL
			DELETE FROM forces_calc WHERE force_id = '{$unit['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			echo "{$unit['name']} IS KILL<br>";
		}
	}
}

// CALCULATE REMAINING TROOPS FOR RESULTS
# Attackers
$sql = "SELECT * from forces_calc fc WHERE forcegroup_id = '1' ORDER BY size DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$Remaining_Attackers = $Remaining_Attackers + $rs['size'];
	$Attackers_Remaining_{array_search($rs['type'], $forcetypes)} = $Attackers_Remaining_{array_search($rs['type'], $forcetypes)} + $rs['size'];
	}
# Defenders
$sql = "SELECT * from forces_calc fc WHERE forcegroup_id = '2' ORDER BY size DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$Remaining_Defenders = $Remaining_Defenders + $rs['size'];
	$Defenders_Remaining_{array_search($rs['type'], $forcetypes)} = $Defenders_Remaining_{array_search($rs['type'], $forcetypes)} + $rs['size'];
	}

// CALCULATE DIED TROOPS FOR RESULTS
foreach ($forcetypes as $key => $value) {
	$Attackers_Died_{$key} = $Attackers_Initial_{$key} - $Attackers_Remaining_{$key};
    $Defenders_Died_{$key} = $Defenders_Initial_{$key} - $Defenders_Remaining_{$key};
}


#Display Results - Attackers
echo "<h2>Attackers Summary:</h2>";
echo "<div><table border=\"1\" style=\"float: left\"><tr><td>Attack vs</td><td>Total Damage</td></tr>";
echo "<tr><td>Cavalry</td><td>" . $Attackers_Damage_Initial_Cavalry . "</td></tr>";
echo "<tr><td>Tanks</td><td>" . $Attackers_Damage_Initial_Tanks . "</td></tr>";
echo "<tr><td>Pegasi</td><td>" . $Attackers_Damage_Initial_Pegasi . "</td></tr>";
echo "<tr><td>Unicorns</td><td>" . $Attackers_Damage_Initial_Unicorns . "</td></tr>";
echo "<tr><td>Naval</td><td>" . $Attackers_Damage_Initial_Naval . "</td></tr>";
echo "<tr><td>Alicorns</td><td>" . $Attackers_Damage_Initial_Naval . "</td></tr>";
echo "</table></div>";

echo "<div><table border=\"1\" style=\"float: left\"><tr><td>Attackers</td><td>Initial</td><td>Died</td><td>Remaining</td></tr>";
foreach ($forcetypes as $key => $value) {
	if ($key == 'Alicorns') {
		continue;
	}
    echo "<tr><td>" . $key . "</td><td>" . $Attackers_Initial_{$key} . " </td><td>" . $Attackers_Died_{$key} . "</td><td>" . $Attackers_Remaining_{$key} . "</td></tr>";
}
echo "</table></div>";

#Display Results - Defenders
echo "<h2>Defenders Summary:</h2>";
echo "<div><table border=\"1\" style=\"float: left\"><tr><td>Attack vs</td><td>Total Damage</td></tr>";
echo "<tr><td>Cavalry</td><td>" . $Defenders_Damage_Initial_Cavalry . "</td></tr>";
echo "<tr><td>Tanks</td><td>" . $Defenders_Damage_Initial_Tanks . "</td></tr>";
echo "<tr><td>Pegasi</td><td>" . $Defenders_Damage_Initial_Pegasi . "</td></tr>";
echo "<tr><td>Unicorns</td><td>" . $Defenders_Damage_Initial_Unicorns . "</td></tr>";
echo "<tr><td>Naval</td><td>" . $Defenders_Damage_Initial_Naval . "</td></tr>";
echo "</table></div>";

echo "<div><table border=\"1\" style=\"float: left\"><tr><td>Defenders</td><td>Initial</td><td>Died</td><td>Remaining</td></tr>";
foreach ($forcetypes as $key => $value) {
    echo "<tr><td>" . $key . "</td><td>" . $Defenders_Initial_{$key} . " </td><td>" . $Defenders_Died_{$key} . "</td><td>" . $Defenders_Remaining_{$key} . "</td></tr>";
}
echo "</table></div>";
}
} else {
	echo "Warcalc not available on main server as a security precaution. See test.4clop.com/huge-ovipositor.html";
}
?>