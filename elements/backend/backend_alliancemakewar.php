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
if ($_POST && (($_POST["token_alliancemakewar"] == "") || ($_POST["token_alliancemakewar"] != $_SESSION["token_alliancemakewar"]))) {
	$errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_alliancemakewar"] == "")) {
	$_SESSION["token_alliancemakewar"] = sha1(rand() . $_SESSION["token_alliancemakewar"]);
}
if ($_POST && !hasability("alliancemakewar", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
    $errors[] = "You may not make war on behalf of your alliance.";
}
if ($_POST && !$errors) {
$mysql['name'] = $GLOBALS['mysqli']->real_escape_string($_POST['name']);
$sql=<<<EOSQL
SELECT * FROM alliances WHERE name = '{$mysql['name']}'
EOSQL;
$target = onelinequery($sql);
if (!$target['alliance_id']) {
	$errors[] = "Alliance not found.";
} else if ($target['alliance_id'] == $userinfo['alliance_id']) {
	$errors[] = "But.. but that's your own alliance...";
} else if ($target['alliance_id'] < 4 && $_SESSION['user_id'] >= 5) {
	$errors[] = "That would be a poor decision.";
}
//checking phase
if (!$errors) {
	$mysql['resource_id'] = 0;
	$mysql['amount'] = 0;
    $sql=<<<EOSQL
	SELECT turns FROM peacetreaties
	WHERE (alliance1 = {$target['alliance_id']} AND alliance2 = {$userinfo['alliance_id']})
	OR (alliance2 = {$target['alliance_id']} AND alliance1 = {$userinfo['alliance_id']})
EOSQL;
	$peace = onelinequery($sql);
	if ($peace['turns']) {
		if (!alliancehasamount(60, $userinfo['alliance_id'], $constants['backstabbingrequired'])) {
			$errors[] = "Your alliance doesn't have the Backstabbing to break the peace treaty.";
		}
	}
	if ($_POST['burden']) {
		$type = "burden";
		$mysql['resource_id'] = (int)$_POST['resource_id'];
		$mysql['amount'] = (int)$_POST['amount'];
        if ($mysql['amount'] < 1) {
            $errors[] = "Enter an amount.";
        }
		if (($mysql['resource_id'] < 0) || ($mysql['resource_id'] > 63) || ($_POST['resource_id'] === "")) {
			$errors[] = "Select a compound.";
		}
		if ($mysql['resource_id'] == 27) { //cheeky monkey
			if (!alliancehasamount(27, $userinfo['alliance_id'], $constants['burdenrequired'] + $mysql['amount'])) {
				$errors[] = "Your alliance doesn't have the Burden.";
			}
		} else {
			if (!alliancehasamount($mysql['resource_id'], $userinfo['alliance_id'], $mysql['amount'])) {
				$errors[] = "Your alliance doesn't have enough of the resource you're trying to burden the other alliance with.";
			}
			if (!alliancehasamount(27, $userinfo['alliance_id'], $constants['allianceburdenrequired'])) {
				$errors[] = "Your alliance doesn't have the Burden to attack.";
			}
		}
	} else if ($_POST['corrupt']) {
		$type = "corrupt";
		$focusarray = array(1 => "Magic", 2 => "Loyalty", 4 => "Laughter", 8 => "Kindness", 16 => "Honesty", 32 => "Generosity");
		$mysql['resource_id'] = (int)$_POST['focuson'];
		if (!$focusarray[$mysql['resource_id']]) {
			$errors[] = "Select a Focus to inflict on that player.";
		}
		if (!alliancehasamount(53, $userinfo['alliance_id'], $constants['alliancecorruptionrequired'])) {
			$errors[] = "Your alliance doesn't have the Corruption to attack.";
		}
	} else if ($_POST['sadness']) {
		$type = "sadness";
		if (!alliancehasamount(59, $userinfo['alliance_id'], $constants['alliancesadnessrequired'])) {
			$errors[] = "Your alliance doesn't have the Sadness to attack.";
		}
	} else if ($_POST['theft']) {
		$type = "theft";
		$mysql['resource_id'] = (int)$_POST['resourcetosteal'];
		if ($mysql['resource_id'] < 0 || $mysql['resource_id'] > 63 || $_POST['resourcetosteal'] === "") {
			$errors[] = "Select a compound.";
		}
		if (!alliancehasamount(31, $userinfo['alliance_id'], $constants['alliancetheftrequired'])) {
			$errors[] = "Your alliance doesn't have the Theft to attack.";
		}
	} else {
		$errors[] = "I just don't know what went wrong!";
	}
}
//there will be an attack
if (!$errors) {
    if ($_POST['burden']) {
        allianceaddamount(27, $userinfo['alliance_id'], $constants['allianceburdenrequired'] * -1);
        $ticks = 4;
    } else if ($_POST['corrupt']) {
		allianceaddamount(53, $userinfo['alliance_id'], $constants['alliancecorruptionrequired'] * -1);
		$ticks = 12;
	} else if ($_POST['sadness']) {
		allianceaddamount(59, $userinfo['alliance_id'], $constants['alliancesadnessrequired'] * -1);
		$ticks = 8;
	} else if ($_POST['theft']) {
		allianceaddamount(31, $userinfo['alliance_id'], $constants['alliancetheftrequired'] * -1);
		$ticks = 8;
	}
	if ($_POST['sadness'] || $_POST['corrupt']) {
		if (alliancehasbanked(56, $target['alliance_id'], $constants["allianceserenityfor{$type}"])) {
			$infos[] = "Your attack was blocked by the target's Serenity.";
			allianceaddbanked(56, $target['alliance_id'], $constants["allianceserenityfor{$type}"] * -1);
			$blocked = true;
            if (alliancehasability("seeallianceattacks", $target['alliance_id'])) {
                allianceaddreport("{$allianceinfo['name']} tried to attack your alliance, but its Serenity blocked it!", $target['alliance_id']);
            }
		}
	} else {
		if (alliancehasbanked(42, $target['alliance_id'], $constants["alliancesecurityfor{$type}"])) {
			$infos[] = "Your attack was blocked by the target's Security.";
			allianceaddbanked(42, $target['alliance_id'], $constants["alliancesecurityfor{$type}"] * -1);
			$blocked = true;
            if (alliancehasability("seeallianceattacks", $target['alliance_id'])) {
                allianceaddreport("{$allianceinfo['name']} tried to attack your alliance, but its Security blocked it!", $target['alliance_id']);
            }
		}
	}
	if (!$blocked) {
		if ($peace['turns']) {
			$sql=<<<EOSQL
			DELETE FROM peacetreaties
			WHERE (alliance1 = {$target['alliance_id']} AND alliance2 = {$userinfo['alliance_id']})
			OR (alliance2 = {$target['alliance_id']} AND alliance1 = {$userinfo['alliance_id']})
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "The peace treaty has been destroyed.";
			allianceaddamount(60, $userinfo['alliance_id'], $constants['backstabbingrequired'] * -1);
		}
		if ($_POST['burden']) {
			allianceaddamount($mysql['resource_id'], $userinfo['alliance_id'], $mysql['amount'] * -1);
		}
		$sql=<<<EOSQL
INSERT INTO allianceattacks SET attacker = {$userinfo['alliance_id']}, defender = {$target['alliance_id']}, type = '{$type}',
uncancelable = 0, sent = NOW(), ticks = {$ticks}, resource_id = '{$mysql['resource_id']}', amount = '{$mysql['amount']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Attack sent.";
        if (alliancehasability("seeallianceattacks", $target['alliance_id'])) {
            $message = $GLOBALS['mysqli']->real_escape_string("{$allianceinfo['name']} attacked the alliance!");
            $sql=<<<EOSQL
            INSERT INTO alliance_messages (alliance_id, user_id, message, posted)
            VALUES ({$target['alliance_id']}, 0, '{$message}', NOW())
EOSQL;
            $GLOBALS['mysqli']->query($sql);
        }
	}
}
}
?>