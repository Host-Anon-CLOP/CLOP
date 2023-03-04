<?php
include_once("allfunctions.php");
needsalliance();
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'war'
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
if ($_POST && (($_POST["token_incoming"] == "") || ($_POST["token_incoming"] != $_SESSION["token_incoming"]))) {
	$errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_incoming"] == "")) {
	$_SESSION["token_incoming"] = sha1(rand() . $_SESSION["token_incoming"]);
}
if ($_POST && !$errors) {
    $mysql['attack_id'] = (int)$_POST['attack_id'];
	$sql=<<<EOSQL
	SELECT * FROM attacks
	WHERE attack_id = {$mysql['attack_id']}
	AND defender = {$_SESSION['user_id']}
EOSQL;
	$attack = onelinequery($sql);
	if (!$attack['attack_id']) {
		$errors[] = "Attack not found.";
	} else {
	if ($_POST['nullify']) {
		if (!hasamount(34, $_SESSION['user_id'], $constants["shelterfor" . $attack['type']])) {
			$errors[] = "You do not have enough Shelter to protect against this kind of attack.";
		}
	}
	if ($_POST['redirect']) {
		if (!hasamount(51, $_SESSION['user_id'], $constants["malicefor" . $attack['type']])) {
			$errors[] = "You do not have enough Malice to redirect this kind of attack.";
		}
		$mysql['username'] = $GLOBALS['mysqli']->real_escape_string($_POST['target']);
		$sql=<<<EOSQL
		SELECT *
		FROM users
		WHERE username = '{$mysql['username']}'
EOSQL;
		$target = onelinequery($sql);
		$sql=<<<EOSQL
		SELECT alliance_id FROM users
		WHERE user_id = {$attack['attacker']}
EOSQL;
		$attacker = onelinequery($sql);
		if ($target['tier'] < 5) {
			$nomercy = $constants['mercilessnessrequired'] * pow((5 - $target['tier']), 2);
		}
		if (!$target['user_id']) {
			$errors[] = "User not found.";
		} else if (!$target['alliance_id']) {
			$errors[] = "Redirecting an attack to someone who can't even play? Really?";
		} else if ($target['alliance_id'] == $attacker['alliance_id']) {
			$errors[] = "You cannot redirect an attack to someone in the attacker's alliance.";
		} else if ($userinfo['alliance_id'] == $target['alliance_id']) {
			$errors[] = "You cannot redirect an attack to someone in your own alliance.";
		} else if ($target['user_id'] < 5 && $_SESSION['user_id'] >= 5) {
            $errors[] = "That would be a poor choice.";
        } else if ($target['stasismode']) {
			$errors[] = "That user is in stasis.";
		}
		if (!$errors) {
			if (!hasamount(55, $_SESSION['user_id'], $nomercy) && $target['tier'] < $userinfo['tier'] && $target['tier'] < 5) {
				$errors[] = "You do not have the Mercilessness to redirect this attack to someone under your tier.";
			}
		}
	}
	}
	if (!$errors) {
		if ($_POST['nullify']) {
            addamount(34, $_SESSION['user_id'], $constants["shelterfor" . $attack['type']] * -1);
			$sql=<<<EOSQL
			DELETE FROM attacks
			WHERE attack_id = '{$attack['attack_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Attack nullified.";
            $attackermessage = "Your attack was directly nullified by {$userinfo['username']}.";
		}
		if ($_POST['redirect']) {
            addamount(51, $_SESSION['user_id'], $constants["malicefor" . $attack['type']] * -1);
            if ($target['tier'] < $userinfo['tier'] && $target['tier'] < 5) {
				addamount(55, $_SESSION['user_id'], $nomercy * -1);
			}
			$heroism = $constants["heroismfor{$attack['type']}"];
			$sql=<<<EOSQL
			SELECT u.* 
			FROM bankedresources br
			INNER JOIN users u ON br.user_id = u.user_id
			WHERE u.alliance_id = {$target['alliance_id']}
			AND br.resource_id = 26
			AND br.amount >= {$heroism}
			ORDER BY br.amount DESC, u.user_id ASC
			LIMIT 1
EOSQL;
            $hero = onelinequery($sql);
            if ($hero['user_id'] == $target['user_id']) {
                $infos[] = "You redirected the attack to the hero of the enemy alliance.";
            } else if ($hero['user_id']) {
                $infos[] = "Your redirected attack was redirected to the alliance's hero, {$hero['username']}.";
                addbanked(26, $hero['user_id'], $heroism * -1);
                $target = $hero;
            }
			if ($attack['type'] == "despair" || $attack['type'] == "corrupt") {
				if (hasbanked(56, $target['user_id'], $constants["serenityfor{$attack['type']}"])) {
					$infos[] = "Your attack was blocked by the target's Serenity.";
					addbanked(56, $target['user_id'], $constants["serenityfor{$attack['type']}"] * -1);
					$blocked = true;
					if (hasability("seeattackattempts", $target['user_id'])) {
						addreport("{$userinfo['username']} tried to attack you, but your Serenity blocked it!", $target['user_id']);
					}
				}
			} else {
				if (hasbanked(42, $target['user_id'], $constants["securityfor{$attack['type']}"])) {
					$infos[] = "Your attack was blocked by the target's Security.";
					addbanked(42, $target['user_id'], $constants["securityfor{$attack['type']}"] * -1);
					$blocked = true;
					if (hasability("seeattackattempts", $target['user_id'])) {
						addreport("{$userinfo['username']} tried to attack you, but your Security blocked it!", $target['user_id']);
					}
				}
			}
			if ($blocked) {
				$sql=<<<EOSQL
DELETE from attacks WHERE attack_id = {$attack['attack_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			} else {
            switch ($attack['type']) {
				case "brutal": $ticks = 6; break;
				case "burden": $ticks = 4; break;
				case "corrupt": $ticks = 8; break;
				case "despair": $ticks = 6; break;
				case "robbery": $ticks = 4; break;
				default: die("Something went very wrong!"); break;
            }
			$sql=<<<EOSQL
UPDATE attacks SET attacker = {$_SESSION['user_id']}, defender = {$target['user_id']}, sent = NOW(), ticks = {$ticks}, uncancelable = 1
WHERE attack_id = {$attack['attack_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Attack redirected.";
			}
            $attackermessage = "{$userinfo['username']} redirected your attack.";
		}
        $attackermessage = $GLOBALS['mysqli']->real_escape_string($attackermessage);
        $sql=<<<EOSQL
INSERT INTO reports SET user_id = {$attack['attacker']}, report = '{$attackermessage}', time = NOW()
EOSQL;
        $GLOBALS['mysqli']->query($sql);
	}
}
$sql=<<<EOSQL
SELECT a.*, u.username AS attackername FROM attacks a
INNER JOIN users u ON a.attacker = u.user_id
WHERE a.defender = '{$_SESSION['user_id']}'
ORDER BY a.ticks ASC, a.type ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$attacks[] = $rs;
}
?>