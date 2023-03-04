<?php
include_once("allfunctions.php");
needsnation();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval", 6 => "Alicorns");
if ($_POST && (($_POST['token_groupforces'] == "") || ($_POST['token_groupforces'] != $_SESSION['token_groupforces']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_groupforces'] == "")) {
    $_SESSION['token_groupforces'] = sha1(rand() . $_SESSION['token_groupforces']);
}
if (!$errors) {
	if ($_POST['renameforce']) {
		if ($mysql['name'] == "") {
			$errors[] = "No name entered.";
		}
		if ($mysql['name'] != preg_replace('/[^0-9a-zA-Z_\s]/' ,"", $mysql['name'])) {
			$errors[] = "Only English letters and numbers for the force name.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
			UPDATE forces SET name = '{$mysql['name']}' WHERE force_id = '{$mysql['force_id']}' AND nation_id = '{$_SESSION['nation_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
	}
	if ($_POST['renamegroup']) {
		if ($mysql['name'] == "") {
			$errors[] = "No name entered.";
		}
		if ($mysql['name'] != preg_replace('/[^0-9a-zA-Z_\s]/' ,"", $mysql['name'])) {
			$errors[] = "Only English letters and numbers for the group name.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
			UPDATE forcegroups SET name = '{$mysql['name']}' WHERE forcegroup_id = '{$mysql['forcegroup_id']}' AND nation_id = '{$_SESSION['nation_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
	}
    if ($_POST['mergegroups']) {
        $sql=<<<EOSQL
        SELECT location_id, departuredate FROM forcegroups WHERE forcegroup_id = '{$mysql['forcegroup_id']}' AND nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $thisgroup = onelinequery($sql);
        if (!$thisgroup) {
            $errors[] = "Group not found!";
        } else if ($thisgroup['departuredate']) {
            $errors[] = "Groups in transit cannot merge!";
        }
        $sql=<<<EOSQL
        SELECT location_id, departuredate FROM forcegroups WHERE forcegroup_id = '{$mysql['targetgroup_id']}' AND nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $thatgroup = onelinequery($sql);
        if (!$thatgroup) {
            $errors[] = "Target group not found!";
        }
        if ($thatgroup['departuredate']) {
            $errors[] = "Groups in transit cannot merge!";
        }
        if ($thatgroup['location_id'] != $thisgroup['location_id']) {
            $errors[] = "Groups must be in the same place to merge!";
        }
        if (!$errors) {
            $sql=<<<EOSQL
UPDATE forces SET forcegroup_id = '{$mysql['targetgroup_id']}' WHERE forcegroup_id = '{$mysql['forcegroup_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
DELETE from forcegroups WHERE forcegroup_id = '{$mysql['forcegroup_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
        }
    }
    if ($_POST['splitgroup']) {
        $sql=<<<EOSQL
        SELECT fg.departuredate, fg.forcegroup_id, fg.location_id, fg.attack_mission, fg.oldmission, f.name FROM forcegroups fg INNER JOIN forces f ON f.forcegroup_id = fg.forcegroup_id
        WHERE f.force_id = '{$mysql['force_id']}' and f.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $thisgroup = onelinequery($sql);
        if (!$thisgroup) {
            $errors[] = "Group not found!";
        }
        if ($thisgroup['departuredate']) {
			$errors[] = "Cannot split a force group in transit!";
		}
		$sql=<<<EOSQL
		SELECT COUNT(*) as count FROM forces WHERE forcegroup_id = '{$thisgroup['forcegroup_id']}'
EOSQL;
		$forcecount = onelinequery($sql);
		if ($forcecount['count'] == 1) {
			$errors[] = "That force is already in its own group!";
		}
		$mysql['name'] = $GLOBALS['mysqli']->real_escape_string($thisgroup['name']);
		if (!$errors) {
			$sql=<<<EOSQL
			INSERT INTO forcegroups SET name = '{$mysql['name']}', location_id = {$thisgroup['location_id']}, nation_id = {$_SESSION['nation_id']},
attack_mission = {$thisgroup['attack_mission']}, oldmission = {$thisgroup['oldmission']} 
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$forcegroup_id = mysqli_insert_id($GLOBALS['mysqli']);
			$sql=<<<EOSQL
			UPDATE forces SET forcegroup_id = '{$forcegroup_id}' WHERE force_id = '{$mysql['force_id']}' 
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
    }
	if ($_POST['combineforces']) {
		$sql=<<<EOSQL
        SELECT forcegroup_id, weapon_id, size, training, armor_id, nation_id, type FROM forces WHERE force_id = '{$mysql['force_id']}'
EOSQL;
		$thisforce = onelinequery($sql);
		$sql=<<<EOSQL
        SELECT forcegroup_id, weapon_id, training, armor_id, nation_id, type FROM forces WHERE force_id = '{$mysql['targetforce_id']}'
EOSQL;
		$thatforce = onelinequery($sql);
		if ($thisforce['nation_id'] != $_SESSION['nation_id']) {
			$errors[] = "Force not found.";
		}
        if ($thatforce['nation_id'] != $_SESSION['nation_id']) {
			$errors[] = "Force not found.";
		}
		if ($thisforce['forcegroup_id'] != $thatforce['forcegroup_id']) {
			$errors[] = "Forces must be in the same group to be combined.";
		}
		if ($thisforce['weapon_id'] != $thatforce['weapon_id']) {
			$errors[] = "Forces must have the same weapons to be combined.";
		}
		if ($thisforce['armor_id'] != $thatforce['armor_id']) {
			$errors[] = "Forces must have the same armor to be combined.";
		}
		if ($thisforce['training'] != $thatforce['training']) {
			$errors[] = "Forces must have the same amount of training to be combined.";
		}
		if ($thisforce['type'] != $thatforce['type']) {
			$errors[] = "Forces must be of the same type to be combined.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
			UPDATE forces SET size = size + '{$thisforce['size']}' WHERE force_id = '{$mysql['targetforce_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
			DELETE FROM forces WHERE force_id = '{$mysql['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
	}
	if ($_POST['splitforce']) {
		$sql=<<<EOSQL
        SELECT forcegroup_id, weapon_id, size, training, armor_id, type, name FROM forces WHERE force_id = '{$mysql['force_id']}' AND nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$thisforce = onelinequery($sql);
		if (!$thisforce) {
			$errors[] = "Force not found.";
		} else if ($mysql['size'] >= $thisforce['size']) {
			$errors[] = "Can't split off all of it.";
		} else if ($mysql['size'] < 1) {
			$errors[] = "No size entered.";
		}
        $sql=<<<EOSQL
        SELECT COUNT(*) AS totalcount FROM forces WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $totalcount = onelinequery($sql);
        if ($totalcount['totalcount'] >= 100) {
            $errors[] = "You have too many different forces.";
        }
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE forces SET size = size - {$mysql['size']} WHERE force_id = '{$mysql['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
INSERT INTO forces (forcegroup_id, weapon_id, size, training, armor_id, type, name, nation_id)
VALUES('{$thisforce['forcegroup_id']}', '{$thisforce['weapon_id']}', '{$mysql['size']}', '{$thisforce['training']}',
'{$thisforce['armor_id']}', '{$thisforce['type']}', '{$thisforce['name']} Splitoff', {$_SESSION['nation_id']})
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
	}
    if ($_POST['destroy']) {
		$sql=<<<EOSQL
        SELECT fg.location_id, f.forcegroup_id, f.weapon_id, f.armor_id, f.size FROM forces f
        INNER JOIN forcegroups fg ON fg.forcegroup_id = f.forcegroup_id
        WHERE f.force_id = '{$mysql['force_id']}' AND f.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$thisforce = onelinequery($sql);
		if (!$thisforce) {
			$errors[] = "Force not found.";
		} else {
            if ($thisforce['location_id'] == $_SESSION['nation_id']) {
                if ($thisforce['weapon_id']) {
                $sql=<<<EOSQL
				INSERT INTO weapons (weapon_id, nation_id, amount) VALUES ('{$thisforce['weapon_id']}', '{$_SESSION['nation_id']}', '{$thisforce['size']}')
				ON DUPLICATE KEY UPDATE amount = amount + {$thisforce['size']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
                }
				if ($thisforce['armor_id']) {
				$sql=<<<EOSQL
				INSERT INTO armor (armor_id, nation_id, amount) VALUES ('{$thisforce['armor_id']}', '{$_SESSION['nation_id']}', '{$thisforce['size']}')
				ON DUPLICATE KEY UPDATE amount = amount + {$thisforce['size']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				}
            }
            $sql=<<<EOSQL
			DELETE FROM forces WHERE force_id = '{$mysql['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
DELETE FROM forcegroups WHERE forcegroup_id NOT IN (SELECT forcegroup_id FROM forces)
EOSQL;
			$GLOBALS['mysqli']->query($sql);
        }
    }
}
$sql=<<<EOSQL
SELECT fg.name AS groupname, fg.location_id, f.*, rd1.name AS weaponname, rd2.name AS armorname FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
LEFT JOIN weapondefs rd1 ON f.weapon_id = rd1.weapon_id
LEFT JOIN armordefs rd2 ON f.armor_id = rd2.armor_id
WHERE f.nation_id = '{$_SESSION['nation_id']}'
ORDER BY fg.forcegroup_id, f.name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['weaponname'] == "") {
		$rs['weaponname'] = "Scrounged Weapons";
	}
	if ($rs['armorname'] == "") {
		$rs['armorname'] = "Scrounged Armor";
	}
    $forces[] = $rs;
	$eligibleforces[$rs['forcegroup_id']][] = $rs;
	$eligiblegroups[$rs['location_id']][$rs['forcegroup_id']] = $rs;
}
?>