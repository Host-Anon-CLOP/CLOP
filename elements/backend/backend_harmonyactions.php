<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_harmonyactions"] == "") || ($_POST["token_harmonyactions"] != $_SESSION["token_harmonyactions"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_harmonyactions"] == "")) {
    $_SESSION["token_harmonyactions"] = sha1(rand() . $_SESSION["token_harmonyactions"]);
}
$sql=<<<EOSQL
SELECT * FROM constants
ORDER BY name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constantgroups[$rs['type']][] = $rs;
	$constants[$rs['name']] = $rs;
}
$groupnames = array("clop" => "&gt;CLOP", "allianceactions" => "Alliance", "useractions" => "User", "philippy" => "Philippy", "marketplace" => "Marketplace",
"spying" => "Spying", "war" => "War", "alliancewar" => "War (Alliance)", "void" => "Void");
if ($_POST['raiseamount'] || $_POST['loweramount']) {
	$mysql['name'] = $GLOBALS['mysqli']->real_escape_string($_POST['name']);
    if (!hasamount(63, $_SESSION['user_id'], 6000)) {
        $errors[] = "You do not have the Harmony to alter fundamental constants.";
    }
	if (!$constants[$mysql['name']]) {
		$errors[] = "I hear Hell is great this time of year.";
	} else if ($constants[$mysql['name']]['value'] == 1 && $_POST['loweramount']) {
		$errors[] = "Even with the power of Harmony, nothing's free.";
	}
    if (!$errors) {
		if ($_POST['raiseamount']) {
			$word = "raised";
			$newamount = ceil($constants[$mysql['name']]['value'] * 1.25);
        } else {
			$word = "lowered";
			$newamount = floor($constants[$mysql['name']]['value'] * .8);
		}
		$sql=<<<EOSQL
		UPDATE constants SET value = {$newamount} WHERE name = '{$mysql['name']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$constants[$mysql['name']]['value'] = $newamount;
        addamount(63, $_SESSION['user_id'], -6000);
		$infos[] = "Reality has been altered.";
		$newsmessage = "{$userinfo['username']} has {$word} the cost of {$constants[$mysql['name']]['friendlyname']}!";
		$newsmessage = $GLOBALS['mysqli']->real_escape_string($newsmessage);
		$sql=<<<EOSQL
		INSERT INTO news SET message = '{$newsmessage}', posted = NOW()
EOSQL;
		$GLOBALS['mysqli']->query($sql);
    }
}
?>