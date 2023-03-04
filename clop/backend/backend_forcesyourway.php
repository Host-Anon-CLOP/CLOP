<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$regiontypes = array(0 => "OH SHIT NIGGA", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval", 6 => "Alicorns");
$sql=<<<EOSQL
SELECT fg.name AS groupname, fg.forcegroup_id, fg.attack_mission, fg.departuredate, fg.location_id,
n.nation_id AS ownernation_id, n.name AS ownername, n.region AS ownerregion,
n2.name AS locationname, n2.region AS locationregion,
fg.location_id, f.*, rd1.name AS weaponname, rd2.name AS armorname FROM forces f
INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
LEFT JOIN weapondefs rd1 ON f.weapon_id = rd1.weapon_id
LEFT JOIN armordefs rd2 ON f.armor_id = rd2.armor_id
LEFT JOIN nations n ON fg.nation_id = n.nation_id
LEFT JOIN nations n2 ON fg.location_id = n2.nation_id
WHERE fg.departuredate IS NOT NULL AND fg.destination_id = {$_SESSION['nation_id']}
ORDER BY fg.forcegroup_id, f.name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if (date("G", strtotime($rs['departuredate'])) < 12) {
        $tickafter = date("Y-m-d", strtotime($rs['departuredate'])) . " 12:00:00";
    } else {
        $tickafter = date("Y-m-d", strtotime($rs['departuredate'] . " +1 day")) . " 0:00:00";
    }
    if (!$rs['attack_mission'] && ($nationinfo['region'] == $rs['locationregion'])) {
        $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +12 hours"));
    } else if ($rs['attack_mission'] && $nationinfo['region'] == $rs['locationregion']) {
        $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +24 hours"));
    } else if (!$rs['attack_mission'] && $nationinfo['region'] != $rs['locationregion']) {
        $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +36 hours"));
    } else {
        $rs['arrivaldate'] = date("Y-m-d H:i:s", strtotime($tickafter . " +48 hours"));
    }
    $rs['ownerregionname'] = $regiontypes[$rs['ownerregion']];
	$rs['locationregionname'] = $regiontypes[$rs['locationregion']];
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