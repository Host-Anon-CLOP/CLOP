<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
foreach ($_POST as $key => $value) {
    $mysql[$key] = (int)$value;
}
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval", 6 => "Alicorns");
if ($_POST && (($_POST['token_equipforces'] == "") || ($_POST['token_equipforces'] != $_SESSION['token_equipforces']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_equipforces'] == "")) {
    $_SESSION['token_equipforces'] = sha1(rand() . $_SESSION['token_equipforces']);
}
if (!$errors && $_POST) {
    $sql=<<<EOSQL
    SELECT f.* FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
WHERE fg.location_id = '{$_SESSION['nation_id']}' AND f.nation_id = '{$_SESSION['nation_id']}'
AND f.force_id = '{$mysql['force_id']}'
EOSQL;
    $forceinfo = onelinequery($sql);
    if (!$forceinfo) {
    $errors[] = "Choke on a million dicks.";
    }
    if (!$errors) {
    if ($_POST['changeweapon']) {
        if ($mysql['weapon_id']) {
			$sql =<<<EOSQL
        SELECT wd.name, w.amount FROM weapondefs wd LEFT JOIN weapons w ON (wd.weapon_id = w.weapon_id
		AND w.nation_id = '{$_SESSION['nation_id']}') WHERE wd.weapon_id = '{$mysql['weapon_id']}' AND wd.type = '{$forceinfo['type']}'
EOSQL;
			$weapon = onelinequery($sql);
			if ($weapon['amount'] < $forceinfo['size']) {
				$errors[] = "You don't have enough {$weapon['name']} to equip {$forceinfo['name']}!";
			}
		} else {
			$mysql['weapon_id'] = 0;
			$weapon['name'] = "scrounged weapons";
		}
		if ($mysql['weapon_id'] == $forceinfo['weapon_id']) {
			$errors[] = "That's already that force's weapon.";
		}
        if (!$errors) {
			if ($forceinfo['weapon_id']) {
				$sql =<<<EOSQL
INSERT INTO weapons SET nation_id = '{$_SESSION['nation_id']}', weapon_id = '{$forceinfo['weapon_id']}',
amount = '{$forceinfo['size']}' ON DUPLICATE KEY UPDATE amount = amount + '{$forceinfo['size']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
			if ($mysql['weapon_id']) {
			if ($forceinfo['size'] == $weapon['amount']) {
				$sql = "DELETE FROM weapons WHERE weapon_id = '{$mysql['weapon_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
			} else {
				$sql = "UPDATE weapons SET amount = amount - '{$forceinfo['size']}' WHERE weapon_id = '{$mysql['weapon_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
			}
            $GLOBALS['mysqli']->query($sql);
			}
			$sql=<<<EOSQL
UPDATE forces SET weapon_id = '{$mysql['weapon_id']}' WHERE force_id = '{$mysql['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "{$forceinfo['name']} armed with {$weapon['name']}.";
        }
    }
	if ($_POST['changearmor']) {
		if ($mysql['armor_id']) {
			$sql =<<<EOSQL
        SELECT wd.name, w.amount FROM armordefs wd LEFT JOIN armor w ON (wd.armor_id = w.armor_id AND
		w.nation_id = '{$_SESSION['nation_id']}') WHERE wd.armor_id = '{$mysql['armor_id']}' AND wd.type = '{$forceinfo['type']}'
EOSQL;
			$armor = onelinequery($sql);
			if ($armor['amount'] < $forceinfo['size']) {
				$errors[] = "You don't have enough {$armor['name']} to equip {$forceinfo['name']}!";
			}
		} else {
			$mysql['armor_id'] = 0;
			$armor['name'] = "scrounged armor";
		}
		if ($mysql['armor_id'] == $forceinfo['armor_id']) {
			$errors[] = "That's already that force's armor.";
		}
        if (!$errors) {
			if ($forceinfo['armor_id']) {
				$sql =<<<EOSQL
INSERT INTO armor SET nation_id = '{$_SESSION['nation_id']}', armor_id = '{$forceinfo['armor_id']}',
amount = '{$forceinfo['size']}' ON DUPLICATE KEY UPDATE amount = amount + '{$forceinfo['size']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
            if ($mysql['armor_id']) {
			if ($forceinfo['size'] == $armor['amount']) {
				$sql = "DELETE FROM armor WHERE armor_id = '{$mysql['armor_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
			} else {
				$sql = "UPDATE armor SET amount = amount - '{$forceinfo['size']}' WHERE armor_id = '{$mysql['armor_id']}' AND nation_id = '{$_SESSION['nation_id']}'";
			}
            $GLOBALS['mysqli']->query($sql);
            }
			$sql=<<<EOSQL
UPDATE forces SET armor_id = '{$mysql['armor_id']}' WHERE force_id = '{$mysql['force_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "{$forceinfo['name']} equipped with {$armor['name']}.";
        }
	}
    }
}
$sql=<<<EOSQL
SELECT ad.name, a.amount, ad.type, ad.armor_id from armordefs ad LEFT JOIN armor a ON (a.armor_id = ad.armor_id
AND a.nation_id = '{$_SESSION['nation_id']}')
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['amount']) {
		$rs['displayname'] = $rs['name'] . " (Have {$rs['amount']} extra)";
    } else {
		$rs['displayname'] = $rs['name'];
	}
    $armors[$rs['type']][] = $rs;
}
$sql=<<<EOSQL
SELECT wd.name, w.amount, wd.type, wd.weapon_id from weapondefs wd LEFT JOIN weapons w ON (w.weapon_id = wd.weapon_id
AND w.nation_id = '{$_SESSION['nation_id']}')
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['amount']) {
		$rs['displayname'] = $rs['name'] . " (Have {$rs['amount']} extra)";
    } else {
		$rs['displayname'] = $rs['name'];
	}
	$weapons[$rs['type']][] = $rs;
}
$sql=<<<EOSQL
SELECT fg.name AS groupname, f.* FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
WHERE fg.location_id = '{$_SESSION['nation_id']}' AND f.nation_id = '{$_SESSION['nation_id']}' AND fg.departuredate IS NULL
ORDER BY fg.forcegroup_id
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $forces[] = $rs;
}
?>