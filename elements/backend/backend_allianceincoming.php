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
if ($_POST && (($_POST["token_allianceincoming"] == "") || ($_POST["token_allianceincoming"] != $_SESSION["token_allianceincoming"]))) {
	$errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_allianceincoming"] == "")) {
	$_SESSION["token_allianceincoming"] = sha1(rand() . $_SESSION["token_allianceincoming"]);
}
if ($_POST && !hasability("alliancemakewar", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
    $errors[] = "You may not make war on behalf of your alliance.";
}
if ($_POST && !$errors) {
    $mysql['attack_id'] = (int)$_POST['attack_id'];
	$sql=<<<EOSQL
	SELECT * FROM allianceattacks
	WHERE attack_id = {$mysql['attack_id']}
	AND defender = {$allianceinfo['alliance_id']}
EOSQL;
	$attack = onelinequery($sql);
	if (!$attack['attack_id']) {
		$errors[] = "Attack not found.";
	} else {
	if ($_POST['nullify']) {
		if (!alliancehasamount(19, $allianceinfo['alliance_id'], $constants["zealfor" . $attack['type']])) {
			$errors[] = "Your alliance does not have enough Zeal to protect against this kind of attack.";
		}
	}
	if ($_POST['redirect']) {
		if (!alliancehasamount(51, $allianceinfo['alliance_id'], $constants["alliancemalicefor" . $attack['type']])) {
			$errors[] = "Your alliance does not have enough Malice to redirect this kind of attack.";
		}
		$mysql['name'] = $GLOBALS['mysqli']->real_escape_string($_POST['target']);
		$sql=<<<EOSQL
		SELECT alliance_id
		FROM alliances
		WHERE name = '{$mysql['name']}'
EOSQL;
		$target = onelinequery($sql);
		if (!$target['alliance_id']) {
			$errors[] = "Alliance not found.";
		} else if ($target['alliance_id'] == $attack['attacker']) {
            $errors[] = "You cannot redirect an attack directly back to the attacker.";
        } else if ($target['alliance_id'] < 4 && $_SESSION['user_id'] >= 5) {
            $errors[] = "That would be a poor choice.";
        }
	}
	}
	if (!$errors) {
		if ($_POST['nullify']) {
            allianceaddamount(19, $allianceinfo['alliance_id'], $constants["zealfor" . $attack['type']] * -1);
			$sql=<<<EOSQL
			DELETE FROM allianceattacks
			WHERE attack_id = '{$attack['attack_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Attack nullified.";
            $attackermessage = "Your attack was directly nullified by {$allianceinfo['name']}.";
		}
		if ($_POST['redirect']) {
            allianceaddamount(51, $allianceinfo['alliance_id'], $constants["alliancemalicefor" . $attack['type']] * -1);
            if ($attack['type'] == "sadness" || $attack['type'] == "corrupt") {
				if (alliancehasbanked(56, $target['alliance_id'], $constants["allianceserenityfor{$attack['type']}"])) {
					$infos[] = "Your attack was blocked by the target's Serenity.";
					allianceaddbanked(56, $target['alliance_id'], $constants["allianceserenityfor{$attack['type']}"] * -1);
					$blocked = true;
					if (alliancehasability("seeallianceattacks", $target['alliance_id'])) {
						allianceaddreport("{$allianceinfo['name']} tried to attack your alliance, but its Serenity blocked it!", $target['alliance_id']);
					}
				}
			} else {
				if (alliancehasbanked(42, $target['alliance_id'], $constants["alliancesecurityfor{$attack['type']}"])) {
					$infos[] = "Your attack was blocked by the target's Security.";
					allianceaddbanked(42, $target['alliance_id'], $constants["alliancesecurityfor{$attack['type']}"] * -1);
					$blocked = true;
					if (alliancehasability("seeallianceattacks", $target['alliance_id'])) {
						allianceaddreport("{$allianceinfo['name']} tried to attack your alliance, but its Security blocked it!", $target['alliance_id']);
					}
				}
			}
			if ($blocked) {
				$sql=<<<EOSQL
DELETE from allianceattacks WHERE attack_id = {$attack['attack_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			} else {
            switch ($attack['type']) {
                case "burden": $ticks = 4; break;
                case "corrupt": $ticks = 12; break;
                case "sadness": $ticks = 8; break;
                case "theft": $ticks = 8; break;
                default: die("Something went very wrong!"); break;
            }
			$sql=<<<EOSQL
UPDATE allianceattacks SET attacker = {$allianceinfo['alliance_id']}, defender = {$target['alliance_id']}, sent = NOW(), ticks = {$ticks}, uncancelable = 1
WHERE attack_id = {$attack['attack_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Attack redirected.";
			}
            $attackermessage = "{$allianceinfo['name']} redirected your alliance's attack.";
		}
        $attackermessage = $GLOBALS['mysqli']->real_escape_string($attackermessage);
        $sql=<<<EOSQL
INSERT INTO alliancereports SET alliance_id = {$attack['attacker']}, report = '{$attackermessage}', time = NOW()
EOSQL;
        $GLOBALS['mysqli']->query($sql);
	}
}
$sql=<<<EOSQL
SELECT a.*, al.name AS attackername FROM allianceattacks a
INNER JOIN alliances al ON a.attacker = al.alliance_id
WHERE a.defender = '{$allianceinfo['alliance_id']}'
ORDER BY a.ticks ASC, a.type ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$attacks[] = $rs;
}
?>