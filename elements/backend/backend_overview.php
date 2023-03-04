<?php
include_once("allfunctions.php");
include_once("listresources.php");
needsalliance();
if ($_POST && (($_POST["token_overview"] == "") || ($_POST["token_overview"] != $_SESSION["token_overview"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_overview"] == "")) {
    $_SESSION["token_overview"] = sha1(rand() . $_SESSION["token_overview"]);
}
$sql=<<<EOSQL
SELECT a.friendlyname, ua.turns FROM user_abilities ua
INNER JOIN abilities a ON a.ability_id = ua.ability_id
WHERE ua.user_id = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$abilities[$rs['friendlyname']] = $rs['turns'];
}
$sql=<<<EOSQL
SELECT * FROM alliances
WHERE alliance_id = '{$userinfo['alliance_id']}'
EOSQL;
$allianceinfo = onelinequery($sql);
$sql=<<<EOSQL
SELECT * FROM elementpositions
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$positions[$rs['position']] = $rs['resource_id'];
}
$elementpositions = array_flip($positions);
$threshold = ($userinfo['production'] * 6) + 50;
foreach ($elementpositions as $value) {
	$production[$value] = $userinfo['production'];
    if ($abilities['Encouraged']) {
        $production[$value] += 5;
    }
}
if ($allianceinfo['alliancefocus'] == $userinfo['focus']) {
	$userinfo['focusamount'] += $allianceinfo['alliancefocusamount'];
} else {
	switch ($allianceinfo['alliancefocusamount']) {
		case 1:
		$production[$elementpositions[$allianceinfo['alliancefocus']]] *= 2;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 5)] *= 1.25;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 1)] *= 1.25;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 4)] *= .8;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 2)] *= .8;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 3)] *= .5;
		break;
		case 2:
		$production[$elementpositions[$allianceinfo['alliancefocus']]] *= 3;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 5)] *= 2;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 1)] *= 2;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 4)] *= .5;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 2)] *= .5;
		$production[withinsix($elementpositions[$allianceinfo['alliancefocus']] + 3)] *= .25;
		break;
		default:
		break;
	}
}
switch ($userinfo['focusamount']) {
	case 1:
	$production[$elementpositions[$userinfo['focus']]] *= 2;
	$production[withinsix($elementpositions[$userinfo['focus']] + 5)] *= 1.25;
	$production[withinsix($elementpositions[$userinfo['focus']] + 1)] *= 1.25;
	$production[withinsix($elementpositions[$userinfo['focus']] + 4)] *= .8;
	$production[withinsix($elementpositions[$userinfo['focus']] + 2)] *= .8;
	$production[withinsix($elementpositions[$userinfo['focus']] + 3)] *= .5;
	break;
	case 2:
	$production[$elementpositions[$userinfo['focus']]] *= 3;
	$production[withinsix($elementpositions[$userinfo['focus']] + 5)] *= 2;
	$production[withinsix($elementpositions[$userinfo['focus']] + 1)] *= 2;
	$production[withinsix($elementpositions[$userinfo['focus']] + 4)] *= .5;
	$production[withinsix($elementpositions[$userinfo['focus']] + 2)] *= .5;
	$production[withinsix($elementpositions[$userinfo['focus']] + 3)] *= .25;
	break;
	case 3:
	$production[$elementpositions[$userinfo['focus']]] *= 4;
	$production[withinsix($elementpositions[$userinfo['focus']] + 5)] *= 2.5;
	$production[withinsix($elementpositions[$userinfo['focus']] + 1)] *= 2.5;
	$production[withinsix($elementpositions[$userinfo['focus']] + 4)] *= 0;
	$production[withinsix($elementpositions[$userinfo['focus']] + 2)] *= 0;
	$production[withinsix($elementpositions[$userinfo['focus']] + 3)] *= 0;
	break;
	case 4:
	$production[$elementpositions[$userinfo['focus']]] *= 15;
	$production[withinsix($elementpositions[$userinfo['focus']] + 5)] *= 0;
	$production[withinsix($elementpositions[$userinfo['focus']] + 1)] *= 0;
	$production[withinsix($elementpositions[$userinfo['focus']] + 4)] *= 0;
	$production[withinsix($elementpositions[$userinfo['focus']] + 2)] *= 0;
	$production[withinsix($elementpositions[$userinfo['focus']] + 3)] *= 0;
	break;
	default:
	break;
}
foreach ($production as $element => $amount) {
	$production[$element] = floor($amount);
    $pertick[$positions[$element]] = $production[$element];
}
$effectivesat = $userinfo['satisfaction'];
if ($effectivesat > 1000) {
	$effectivesat = 1000;
}
$effectivealliancesat = $allianceinfo['alliancesatisfaction'];
if ($effectivealliancesat > 1000) {
	$effectivealliancesat = 1000;
}
$personalsatmult = 1 - ($effectivesat / 2000);
$alliancesatmult = 1 - ($effectivealliancesat / 2000);
$resourcelist = getresourcelist($_SESSION['user_id']);
?>