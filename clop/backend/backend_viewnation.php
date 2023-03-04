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
$sql = "SELECT n.*, u.user_id, u.username, u.donator from nations n INNER JOIN users u ON u.user_id = n.user_id WHERE n.nation_id = '{$mysql['nation_id']}'";
$nationinfo = onelinequery($sql);
if ($nationinfo) {
$nationinfo['regionname'] = $regiontypes[$nationinfo['region']];
$nationinfo['subregionname'] = $subregiontypes[$nationinfo['subregion']];
$display['description'] = nl2br(htmlentities($nationinfo['description'], ENT_SUBSTITUTE, "UTF-8"));

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
}
?>