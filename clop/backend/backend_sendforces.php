<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval", 6 => "Alicorns");
$regiontypes = array(0 => "OH SHIT NIGGA", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
if ($_POST && (($_POST['token_sendforces'] == "") || ($_POST['token_sendforces'] != $_SESSION['token_sendforces']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_sendforces'] == "")) {
    $_SESSION['token_sendforces'] = sha1(rand() . $_SESSION['token_sendforces']);
}
if (!$errors) {
    if ($_POST['attack'] || $_POST['defend']) {
		if ($_POST['attack']) {
			$mission = 1;
			$opposite = 0;
		} else {
			$mission = 0;
			$opposite = 1;
		}
		$sql=<<<EOSQL
		SELECT fg.forcegroup_id, fg.departuredate, fg.location_id, n.region FROM forcegroups fg
		LEFT JOIN nations n ON fg.location_id = n.nation_id
		WHERE fg.forcegroup_id = '{$mysql['forcegroup_id']}' AND fg.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$thisforce = onelinequery($sql);
        $sql=<<<EOSQL
        SELECT n.*, u.stasismode, u.alliance_id FROM nations n INNER JOIN users u ON n.user_id = u.user_id WHERE n.name = '{$mysql['name']}'
EOSQL;
		$targetnation = onelinequery($sql);
		if (!$thisforce) {
			$errors[] = "Shenanigans.";
		} else if (!$targetnation) {
			$errors[] = "Nation not found.";
		} else if ($targetnation['user_id'] == 1) {
            $errors[] = "You're just a fountain of bad ideas today, aren't you?";
        } else if ($thisforce['location_id'] == $targetnation['nation_id']) {
			$errors[] = "They're already there.";
		} else if (($targetnation['user_id'] == $_SESSION['user_id']) && $_POST['attack']) {
			$errors[] = "You already got that one.";
		} else if ($thisforce['departuredate']) {
			$errors[] = "That force is already on the move.";
		} else if ($targetnation['age'] < 21 && $mission) {
			$errors[] = "That nation hasn't been active for three weeks.";
		} else if ($nationinfo['age'] < 21) {
			$errors[] = "Your nation hasn't been active for three weeks.";
		} else if ($targetnation['stasismode']) {
            $errors[] = "That nation's owner is in stasis.";
        } else if (($nationinfo['alliance_id'] == $targetnation['alliance_id']) && $nationinfo['alliance_id'] && $_POST['attack']) {
            $errors[] = "You cannot attack someone in your alliance.";
        }
		if (!$errors) {
			if ($targetnation['region'] != $thisforce['region']) {
				$sql=<<<EOSQL
SELECT SUM(ad.carrying * f.size) as totalcarrier FROM forces f INNER JOIN armordefs ad ON f.armor_id = ad.armor_id WHERE f.forcegroup_id = '{$mysql['forcegroup_id']}'
EOSQL;
				$carrier = onelinequery($sql);
				$sql=<<<EOSQL
SELECT SUM(size) AS totalcarried FROM forces WHERE forcegroup_id = '{$mysql['forcegroup_id']}' AND (type = 1 OR type = 2 OR type = 4)
EOSQL;
				$noncarrier = onelinequery($sql);
				if ($carrier['totalcarrier'] < $noncarrier['totalcarried']) {
					$errors[] = "You don't have the carrying capacity in your group to go to a different region!";
				}
			}
            if ($_POST['attack']) {
                $sql=<<<EOSQL
SELECT SUM(size) AS totalalicorns FROM forces WHERE forcegroup_id = '{$mysql['forcegroup_id']}' AND type = 6
EOSQL;
				$alicorns = onelinequery($sql);
                if ($alicorns['totalalicorns']) {
					$errors[] = "Your alicorns refuse to attack another nation.";
                }
            }
			$sql=<<<EOSQL
			SELECT forcegroup_id FROM forcegroups
            WHERE ((attack_mission = {$opposite} AND destination_id = {$targetnation['nation_id']} AND departuredate IS NOT NULL)
            OR (location_id = {$targetnation['nation_id']} AND departuredate IS NOT NULL AND oldmission = {$opposite})
            OR (location_id = {$targetnation['nation_id']} AND departuredate IS NULL AND attack_mission = {$opposite}))
			AND nation_id = '{$_SESSION['nation_id']}'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs) {
				$errors[] = "You already have a force doing the opposite!";
			}
			if (!$errors) {
                if (($targetnation['economy'] == $nationinfo['economy']) && ($nationinfo['economy'] == "Free Market") && $_POST['attack']) {
                    $infos[] = "You lose 100 satisfaction for attacking another Free Market economy!";
                    $sql=<<<EOSQL
					UPDATE nations SET satisfaction = satisfaction - 100 WHERE nation_id = '{$_SESSION['nation_id']}';
EOSQL;
					$GLOBALS['mysqli']->query($sql);
                } else if (($targetnation['economy'] == $nationinfo['economy']) && ($nationinfo['economy'] == "State Controlled") && $_POST['attack']) {
                    $infos[] = "You lose 100 satisfaction for attacking another State Controlled economy!";
                    $sql=<<<EOSQL
					UPDATE nations SET satisfaction = satisfaction - 100 WHERE nation_id = '{$_SESSION['nation_id']}';
EOSQL;
					$GLOBALS['mysqli']->query($sql);
                }
				if (($targetnation['government'] == "Democracy" || $targetnation['government'] == "Independence" || $targetnation['government'] == "Decentralization") &&
                ($nationinfo['government'] == "Democracy" || $nationinfo['government'] == "Independence" || $nationinfo['government'] == "Decentralization") && $_POST['attack']) {
                    $infos[] = "You lose 200 satisfaction for attacking another free country!";
                    $sql=<<<EOSQL
					UPDATE nations SET satisfaction = satisfaction - 200 WHERE nation_id = '{$_SESSION['nation_id']}';
EOSQL;
					$GLOBALS['mysqli']->query($sql);
                }
				$sql=<<<EOSQL
				UPDATE forcegroups SET destination_id = {$targetnation['nation_id']}, oldmission = attack_mission,
                attack_mission = {$mission}, departuredate = NOW() WHERE forcegroup_id = {$mysql['forcegroup_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$infos[] = "Force en route.";
			}
		}
    }
    if ($_POST['transfer']) {
		$sql=<<<EOSQL
		SELECT fg.forcegroup_id, fg.departuredate, fg.location_id FROM forcegroups fg
		LEFT JOIN nations n ON fg.location_id = n.nation_id
		WHERE fg.forcegroup_id = '{$mysql['forcegroup_id']}' AND fg.location_id != '{$_SESSION['nation_id']}'
		AND fg.nation_id = '{$_SESSION['nation_id']}' AND n.user_id = '{$_SESSION['user_id']}'
EOSQL;
		$thisforce = onelinequery($sql);
		if (!$thisforce) {
			$errors[] = "Shenanigans.";
		} else if ($thisforce['departuredate']) {
			$errors[] = "Ixnay.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
			UPDATE forcegroups SET nation_id = '{$thisforce['location_id']}' WHERE forcegroup_id = '{$mysql['forcegroup_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
			UPDATE forces SET nation_id = '{$thisforce['location_id']}' WHERE forcegroup_id = '{$mysql['forcegroup_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Group transferred.";
		}
    }
	if ($_POST['recall']) {
		$sql=<<<EOSQL
		SELECT forcegroup_id, departuredate, oldmission FROM forcegroups WHERE forcegroup_id = '{$mysql['forcegroup_id']}' AND nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$thisforce = onelinequery($sql);
		if (!$thisforce) {
			$errors[] = "Shenanigans.";
		} else if (!$thisforce['departuredate']) {
			$errors[] = "They're not on the move.";
		}
		if (!$errors) {
            if ($thisforce['oldmission']) {
            $sql=<<<EOSQL
			UPDATE forcegroups SET departuredate = NOW(), destination_id = {$_SESSION['nation_id']}, attack_mission = 0 WHERE forcegroup_id = {$mysql['forcegroup_id']}
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $infos[] = "Force sent home.";
            } else {
			$sql=<<<EOSQL
			UPDATE forcegroups SET departuredate = NULL, destination_id = 0, attack_mission = oldmission WHERE forcegroup_id = {$mysql['forcegroup_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Force recalled.";
            }
		}
	}
}
$sql=<<<EOSQL
SELECT fg.name AS groupname, fg.attack_mission, fg.departuredate, n.name AS destinationname, n.region AS destinationregion, fg.destination_id, fg.location_id,
n2.name AS locationname, n2.region AS locationregion, n2.user_id AS locationowner, fg.oldmission,
fg.location_id, f.*, rd1.name AS weaponname, rd2.name AS armorname FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
LEFT JOIN weapondefs rd1 ON f.weapon_id = rd1.weapon_id
LEFT JOIN armordefs rd2 ON f.armor_id = rd2.armor_id
LEFT JOIN nations n ON fg.destination_id = n.nation_id
LEFT JOIN nations n2 ON fg.location_id = n2.nation_id
WHERE f.nation_id = '{$_SESSION['nation_id']}'
ORDER BY fg.forcegroup_id, f.name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['departuredate']) {
        if (date("G", strtotime($rs['departuredate'])) < 12) {
            $tickafter = date("Y-m-d", strtotime($rs['departuredate'])) . " 12:00:00";
        } else {
            $tickafter = date("Y-m-d", strtotime($rs['departuredate'] . " +1 day")) . " 0:00:00";
        }
        if (!$rs['attack_mission'] && ($rs['destinationregion'] == $rs['locationregion'])) {
            $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +12 hours"));
        } else if ($rs['attack_mission'] && $rs['destinationregion'] == $rs['locationregion']) {
            $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +24 hours"));
        } else if (!$rs['attack_mission'] && $rs['destinationregion'] != $rs['locationregion']) {
            $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +36 hours"));
        } else {
            $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +48 hours"));
        }
    }
	if ($rs['attack_mission']) {
		$rs['missiondescription'] = "attack";
	} else {
		$rs['missiondescription'] = "defend";
	}
	$rs['locationregionname'] = $regiontypes[$rs['locationregion']];
	$rs['destinationregionname'] = $regiontypes[$rs['destinationregion']];
    $rs['lowertype'] = strtolower($forcetypes[$rs['type']]);
	if ($rs['weaponname'] == "") {
        $rs['weapon_id'] = 0;
		$rs['weaponname'] = "Scrounged Weapons";
	}
	if ($rs['armorname'] == "") {
        $rs['armor_id'] = 0;
		$rs['armorname'] = "Scrounged Armor";
	}
    $forces[] = $rs;
}
?>