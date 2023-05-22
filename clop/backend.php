<?php
require_once("backend/allfunctions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attackerData = [];
    $defenderData = [];

    $forcetypes = array("Cavalry" => 1, "Tanks" => 2, "Pegasi" => 3, "Unicorns" => 4, "Naval" => 5, "Alicorns" => 6);
    $weapontypes = array("Scrounged" => 0, "PRC-E6" => 1,"PRC-E7" => 2,"PRC-E8" => 3,"ACFU" => 4,"ATFU" => 5,"APFU" => 6,"AUFU" => 7,"K9P" => 8,"ELBO-GRS" => 9,"Chem-Light" => 10,"PropWash" => 11,"SteamBucket" => 12,"CanopyLights" => 13,"LongStand" => 14,"LongWeight" => 15,"GridSquares" => 16,"Shoreline" => 17,"WaterHammer" => 18,"WaterlineEraser" => 19);
    $armortypes = array("Scrounged" => 0,"Barding" => 1,"Bigdog" => 2,"Nope" => 3,"Trundle" => 4,"Shepherd" => 5,"Ohno" => 6,"Titan" => 7,"Cooler" => 8,"Wonder" => 9,"Griffin" => 10,"Dragon" => 11,"Hornshield" => 12,"Librarian" => 13,"Shining" => 14,"D2A" => 15,"C-PON3" => 16,"Esohes" => 17,"Shubidu" => 18);

    // Retrieve data for attackers
    if (!empty($_POST['type']) && is_array($_POST['type'])) {

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


        foreach ($_POST['type'] as $index => $which_type) {
            if (!empty($which_type)) {
                $name = $which_type . '_' . $_POST['weapon'][$index] . '_' . $_POST['armor'][$index] . '_' . $_POST['training'][$index] . '_' . $_POST['size'][$index];

                $attackerData[] = [
                    'type' => $which_type,
                    'forcetype' => $forcetypes[$which_type],
                    'weapon' => $_POST['weapon'][$index],
                    'weapontype' => $weapontypes[$_POST['weapon'][$index]],
                    'armor' => $_POST['armor'][$index],
                    'armortype' => $armortypes[$_POST['armor'][$index]],
                    'size' => $_POST['size'][$index],
                    'training' => $_POST['training'][$index],
                    'name' => $name
                ];

# Create Force
$sql=<<<EOSQL
INSERT INTO forces_calc (nation_id, size, type, weapon_id, armor_id, training, name, forcegroup_id) VALUES (1, {$_POST['size'][$index]}, {$forcetypes[$which_type]}, '{$weapontypes[$_POST['weapon'][$index]]}', {$armortypes[$_POST['armor'][$index]]}, {$_POST['training'][$index]}, 'A_$name', 1)
EOSQL;
$GLOBALS['mysqli']->query($sql);

# Create Force - Mirror for defenders
$sql=<<<EOSQL
INSERT INTO forces_calc (nation_id, size, type, weapon_id, armor_id, training, name, forcegroup_id) VALUES (2, {$_POST['size'][$index]}, {$forcetypes[$which_type]}, '{$weapontypes[$_POST['weapon'][$index]]}', {$armortypes[$_POST['armor'][$index]]}, {$_POST['training'][$index]}, 'D_$name', 2)
EOSQL;
$GLOBALS['mysqli']->query($sql);

            }
        }
    }

    // Retrieve data for defenders
    // ...

    // Display the entered data
    echo "<h2>Attacker Data:</h2>";
    echo "<pre>";
    print_r($attackerData);
    echo "</pre>";

    echo "<h2>MySQL Result</h2>";
    #echo "$sql";
    // Display defender data
    // ...

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

// TO DO
$defenderbonus = true;

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
					if ($defenderbonus == true) {
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
        echo "Your {$units[$attackerid]['name']} (size {$units[$attackerid]['size']}) hit defenders {$units[$defenderid]['name']} (size {$units[$defenderid]['size']}) for {$damage} damage ({$hitinfo[$attackerid][$defenderid]} hits)";
        /*
        $messages[$units[$attackerid]['nation_id']][] =<<<EOFORM
Your {$units[$attackerid]['name']} (size {$units[$attackerid]['size']}) hit
<a href="viewnation.php?nation_id={$units[$defenderid]['nation_id']}">{$units[$defenderid]['nationname']}</a>'s {$units[$defenderid]['name']} (size {$units[$defenderid]['size']})
for {$damage} damage ({$hitinfo[$attackerid][$defenderid]} hits)
EOFORM;
*/
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
			UPDATE forces_calc SET size = size - {$unit['damage']} WHERE force_id = '{$unit['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$messages[$unit['nation_id']][] = "Your {$unit['name']} lost {$unit['damage']} size!";
		} else {
			$sql =<<<EOSQL
			DELETE FROM forces_calc WHERE force_id = '{$unit['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$messages[$unit['nation_id']][] = "Your {$unit['name']} scattered to the four winds!";
		}
	}
}
}
?>