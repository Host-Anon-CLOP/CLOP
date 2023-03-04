<?php
include_once("allfunctions.php");
include("listresources.php");
needsalliance();
if ($_POST && (($_POST["token_allianceoverview"] == "") || ($_POST["token_allianceoverview"] != $_SESSION["token_allianceoverview"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_allianceoverview"] == "")) {
    $_SESSION["token_allianceoverview"] = sha1(rand() . $_SESSION["token_allianceoverview"]);
}
$sql=<<<EOSQL
SELECT * FROM elementpositions
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$positions[$rs['position']] = $rs['resource_id'];
}
$elementpositions = array_flip($positions);
if ($allianceinfo['alliancefocus']) {
foreach ($positions as $key => $value) {
	if ($value == $allianceinfo['alliancefocus']) {
		$production[$key] = $allianceinfo['alliancefocusamount'];
	}
}
}
$allianceabilities = array("alliancespendresources" => "Spend/Bank Resources", "allianceinviteusers" => "Invite Members",
"alliancegrantabilities" => "Grant Abilities", "alliancekickusers" => "Kick Members",
"alliancegiveresources" => "Give Resources", "alliancetakeresources" => "Take Resources",
"alliancemessaging" => "Control Messaging", "alliancemakedeals" => "Make Deals", "alliancemakewar" => "Make War",
"encouraged" => "Encouraged", "owner" => "Owner");
for ($id = 0; $id <= 63; $id++) {
    $newid = 0;
    if ($id & 32) $newid += $positions[withinsix($elementpositions['32'] + 3)];
    if ($id & 16) $newid += $positions[withinsix($elementpositions['16'] + 3)];
    if ($id & 8) $newid += $positions[withinsix($elementpositions['8'] + 3)];
    if ($id & 4) $newid += $positions[withinsix($elementpositions['4'] + 3)];
    if ($id & 2) $newid += $positions[withinsix($elementpositions['2'] + 3)];
    if ($id & 1) $newid += $positions[withinsix($elementpositions['1'] + 3)];
    $complements[$id] = $newid;
}
$resourcelist = getallianceresources($allianceinfo['alliance_id']);
$sql=<<<EOSQL
SELECT user_id, username, production, satisfaction, tier FROM users
WHERE alliance_id = '{$allianceinfo['alliance_id']}'
ORDER BY production DESC, user_id ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $sql=<<<EOSQL
SELECT a.name, ua.turns
FROM user_abilities ua
INNER JOIN abilities a ON a.ability_id = ua.ability_id
WHERE ua.user_id = {$rs['user_id']}
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		if ($allianceabilities[$rs2['name']]) {
			$rs['abilities'][$rs2['name']] = $rs2['turns'];
		}
	}
$members[] = $rs;
}
$sql=<<<EOSQL
SELECT ga.friendlyname, ag.turns FROM alliance_groupabilities ag
INNER JOIN groupabilities ga ON ag.ability_id = ga.ability_id
WHERE ag.alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$abilities[$rs['friendlyname']] = $rs['turns'];
}
$sql=<<<EOSQL
SELECT a.alliance_id, a.name, p.turns
FROM peacetreaties p
INNER JOIN alliances a ON p.alliance1 = a.alliance_id
WHERE p.alliance2 = {$allianceinfo['alliance_id']}
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $treaties[] = $rs;
}
$sql=<<<EOSQL
SELECT a.alliance_id, a.name, p.turns
FROM peacetreaties p
INNER JOIN alliances a ON p.alliance2 = a.alliance_id
WHERE p.alliance1 = {$allianceinfo['alliance_id']}
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $treaties[] = $rs;
}
?>