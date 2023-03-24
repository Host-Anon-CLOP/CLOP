<?php
include_once("allfunctions.php");
$getpost = array_merge($_GET, $_POST);
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$regiontypes = array(0 => "The Heavily Fortified Island of Admin", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
$subregiontypes = array(0 => "", 1 => "North ", 2 => "Central ", 3 => "South ");
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval", 6 => "Alicorns");
$buildings = array();
$sql = "SELECT n.*, u.user_id, u.username, u.donator, u.alliance_id, u.flag from nations n INNER JOIN users u ON u.user_id = n.user_id WHERE n.nation_id = '{$mysql['nation_id']}'";
$nationinfo = onelinequery($sql);
if ($nationinfo) {
$nationinfo['regionname'] = $regiontypes[$nationinfo['region']];
$nationinfo['subregionname'] = $subregiontypes[$nationinfo['subregion']];
$display['description'] = nl2br(htmlentities($nationinfo['description'], ENT_SUBSTITUTE, "UTF-8"));

$sql = "SELECT name FROM alliances WHERE alliance_id = '{$nationinfo['alliance_id']}'";
$nationinfo['alliance_name'] = onelinequery($sql)['name'];

$gdp = getgdp($mysql['nation_id']);
$sql = "SELECT r.amount, rd.name, rd.is_building FROM resources r INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id WHERE r.nation_id = '{$nationinfo['nation_id']}' ORDER BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['is_building']) {
        $buildings[$rs['name']] = $rs['amount'];
    }
}
$displaygdp = commas($gdp);
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

# Nation Resources
$affectedresources = array();
$requiredresources = array();
$rs = array();
$resources = array();

$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS affected
FROM resourceeffects rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
WHERE r.nation_id = '{$mysql['nation_id']}'
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $affectedresources[$rs['name']] = $rs['affected'];
}

$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS required
FROM resourcerequirements rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
WHERE r.nation_id = '{$mysql['nation_id']}'
GROUP BY rd.name";
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
	$requiredresources["Cider"] += 6;
}

# Alliance Resources
if ($nationinfo['alliance_id'] != 0) {
    $allianceaffectedresources = array();
    $alliancerequiredresources = array();
    $allianceresources = array();

    $sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS affected
    FROM resourceeffects rr
    INNER JOIN resources r ON r.resource_id = rr.resource_id
    INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
    INNER JOIN nations n ON r.nation_id = n.nation_id
    INNER JOIN users u ON n.user_id = u.user_id
    WHERE u.alliance_id = '{$nationinfo['alliance_id']}' AND u.stasismode = 0
    GROUP BY rd.name";
    $sth = $GLOBALS['mysqli']->query($sql);
    while ($rs = mysqli_fetch_array($sth)) {
        $allianceaffectedresources[$rs['name']] = $rs['affected'];
    }

    $sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS required
    FROM resourcerequirements rr
    INNER JOIN resources r ON r.resource_id = rr.resource_id
    INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
    INNER JOIN nations n ON r.nation_id = n.nation_id
    INNER JOIN users u ON n.user_id = u.user_id
    WHERE u.alliance_id = '{$nationinfo['alliance_id']}' AND u.stasismode = 0
    GROUP BY rd.name";
    $sth = $GLOBALS['mysqli']->query($sql);
    while ($rs = mysqli_fetch_array($sth)) {
        $alliancerequiredresources[$rs['name']] = $rs['required'];
    }

    # Add resources used by government/economy type
    /*
    $sql = "SELECT n.government, count(n.government) AS count
    FROM nations n
    INNER JOIN users u ON u.user_id = n.user_id
    WHERE u.alliance_id = '{nationinfo['alliance_id']}' AND u.stasismode = 0
	GROUP BY n.government";
    */
    $sql = "SELECT n.government, count(n.government) AS count
    FROM nations n
    INNER JOIN users u ON u.user_id = n.user_id
    WHERE u.alliance_id = '{nationinfo['alliance_id']}' AND u.stasismode = 0
	GROUP BY n.government";
    $sth = $GLOBALS['mysqli']->query($sql);
    while ($rs = mysqli_fetch_array($sth)) {
        if ($rs['government'] == "Democracy") {
            $alliancerequiredresources["Gasoline"] += (20 * $rs['count']);
            $alliancerequiredresources["Vehicle Parts"] += (2 * $rs['count']);
        } else if ($rs['government'] == "Repression") {
            $alliancerequiredresources["Gasoline"] += (10 * $rs['count']);
        } else if ($rs['government'] == "Independence") {
            $alliancerequiredresources["Gasoline"] += (40 * $rs['count']);
            $alliancerequiredresources["Vehicle Parts"] += (4 * $rs['count']);
        } else if ($rs['government'] == "Decentralization") {
            $alliancerequiredresources["Gasoline"] += (50 * $rs['count']);
            $alliancerequiredresources["Vehicle Parts"] += (5 * $rs['count']);
        } else if ($rs['government'] == "Authoritarianism") {
            $alliancerequiredresources["Gasoline"] += (10 * $rs['count']);
            $alliancerequiredresources["Machinery Parts"] += (3 * $rs['count']);
        } else if ($rs['government'] == "Oppression") {
            $alliancerequiredresources["Gasoline"] += (10 * $rs['count']);
            $alliancerequiredresources["Machinery Parts"] += (5 * $rs['count']);
        }
        #if ($rs['economy'] == "Free Market") {
        #    $alliancerequiredresources["Coffee"] += (6 * $rs['count']);
        #} else if ($rs['economy'] == "State Controlled") {
        #    $alliancerequiredresources["Cider"] += (6 * $rs['count']);
        #}
    }

    }
}
?>