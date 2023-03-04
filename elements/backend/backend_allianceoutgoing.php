<?php
include_once("allfunctions.php");
needsalliance();
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'alliancewar'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$sql = "SELECT * FROM resourcedefs";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourcename[$rs['resource_id']] = $rs['name'];
}
if ($_POST && (($_POST["token_allianceoutgoing"] == "") || ($_POST["token_allianceoutgoing"] != $_SESSION["token_allianceoutgoing"]))) {
	$errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_allianceoutgoing"] == "")) {
	$_SESSION["token_allianceoutgoing"] = sha1(rand() . $_SESSION["token_allianceoutgoing"]);
}
if ($_POST && !hasability("alliancemakewar", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
    $errors[] = "You may not make war on behalf of your alliance.";
}
if ($_POST['cancel'] && !$errors) {
    $mysql['attack_id'] = (int)$_POST['attack_id'];
    $sql=<<<EOSQL
	SELECT * FROM allianceattacks
	WHERE attack_id = '{$mysql['attack_id']}'
	AND attacker = '{$userinfo['alliance_id']}'
EOSQL;
	$thisattack = onelinequery($sql);
	if (!$thisattack['attacker']) {
		$errors[] = "This attack no longer exists.";
	} else if ($thisattack['uncancelable']) {
		$errors[] = "This attack has been redirected through Malice and cannot be canceled.";
	}
	if (!alliancehasamount(9, $userinfo['alliance_id'], $constants["alliancecompassionfor{$thisattack['type']}"])) {
		$errors[] = "Your alliance doesn't have the Compassion to cancel this attack.";
	}
	if (!$errors) {
		allianceaddamount(9, $userinfo['alliance_id'], $constants["alliancecompassionfor{$thisattack['type']}"] * -1);
		if ($thisattack['type'] == "burden") {
			allianceaddamount($thisattack['resource_id'], $_SESSION['user_id'], $thisattack['amount']);
			allianceaddamount(27, $userinfo['alliance_id'], floor($constants['allianceburdenrequired'] / 2));
		} else if ($thisattack['type'] == "corrupt") {
			allianceaddamount(53, $userinfo['alliance_id'], floor($constants['alliancecorruptionrequired'] / 2));
		} else if ($thisattack['type'] == "sadness") {
			allianceaddamount(59, $userinfo['alliance_id'], floor($constants['alliancesadnessrequired'] / 2));
		} else if ($thisattack['type'] == "theft") {
			allianceaddamount(31, $userinfo['alliance_id'], floor($constants['alliancetheftrequired'] / 2));
		}
		$sql=<<<EOSQL
		DELETE FROM allianceattacks
		WHERE attack_id = '{$mysql['attack_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Attack canceled.";
	}
}
$sql=<<<EOSQL
SELECT a.*, al.name AS defendername FROM allianceattacks a
INNER JOIN alliances al ON a.defender = al.alliance_id
WHERE a.attacker = '{$userinfo['alliance_id']}'
ORDER BY a.ticks ASC, a.type ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$attacks[] = $rs;
}
?>