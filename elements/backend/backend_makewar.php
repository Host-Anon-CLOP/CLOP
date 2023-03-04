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
if ($_POST && (($_POST["token_makewar"] == "") || ($_POST["token_makewar"] != $_SESSION["token_makewar"]))) {
	$errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_makewar"] == "")) {
	$_SESSION["token_makewar"] = sha1(rand() . $_SESSION["token_makewar"]);
}
if ($_POST && !$errors) {
$mysql['username'] = $GLOBALS['mysqli']->real_escape_string($_POST['username']);
$sql=<<<EOSQL
SELECT * FROM users WHERE username = '{$mysql['username']}'
EOSQL;
$targetuser = onelinequery($sql);
if (!$targetuser['user_id']) {
	$errors[] = "User not found.";
} else if ($targetuser['user_id'] == $_SESSION['user_id']) {
	$errors[] = "It's okay if you hate yourself. Everyone else hates you too.";
} else if (!$targetuser['alliance_id']) {
	$errors[] = "That user is not part of any alliance.";
} else if ($targetuser['stasismode']) {
	$errors[] = "That user is in stasis.";
} else if ($targetuser['user_id'] < 5 && $_SESSION['user_id'] >= 5) {
	$errors[] = "That would be a poor decision.";
}
//checking phase
if (!$errors && $_POST) {
	$mysql['resource_id'] = 0;
	$mysql['amount'] = 0;
	if ($targetuser['tier'] < 5) {
		$nomercy = $constants['mercilessnessrequired'] * pow((5 - $targetuser['tier']), 2);
		if (!hasamount(55, $_SESSION['user_id'], $nomercy)) {
			$errors[] = "You don't have the Mercilessness to attack that Tier {$targetuser['tier']} player.";
		}
	}
	if ($targetuser['alliance_id'] == $userinfo['alliance_id']) {
		if (!hasamount(45, $_SESSION['user_id'], $constants['treasonrequired'])) {
			$errors[] = "You don't have the Treason to attack an alliance member.";
		}
	}
    $sql=<<<EOSQL
	SELECT turns FROM peacetreaties
	WHERE (alliance1 = {$targetuser['alliance_id']} AND alliance2 = {$userinfo['alliance_id']})
	OR (alliance2 = {$targetuser['alliance_id']} AND alliance1 = {$userinfo['alliance_id']})
EOSQL;
	$peace = onelinequery($sql);
    if ($peace['turns']) {
		if (!hasamount(57, $_SESSION['user_id'], $constants['perfidyrequired'])) {
			$errors[] = "You don't have the Perfidy to violate the peace treaty.";
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
			if (!hasamount(27, $_SESSION['user_id'], $constants['burdenrequired'] + $mysql['amount'])) {
				$errors[] = "You don't have the Burden.";
			}
		} else {
			if (!hasamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'])) {
				$errors[] = "You don't have enough of the resource you're trying to burden the other player with.";
			}
			if (!hasamount(27, $_SESSION['user_id'], $constants['burdenrequired'])) {
				$errors[] = "You don't have the Burden to attack.";
			}
		}
	} else if ($_POST['corrupt']) {
		$type = "corrupt";
		$focusarray = array(1 => "Magic", 2 => "Loyalty", 4 => "Laughter", 8 => "Kindness", 16 => "Honesty", 32 => "Generosity");
		$mysql['resource_id'] = (int)$_POST['focuson'];
		if (!$focusarray[$mysql['resource_id']]) {
			$errors[] = "Select a Focus to inflict on that player.";
		}
		if (!hasamount(53, $_SESSION['user_id'], $constants['corruptionrequired'])) {
			$errors[] = "You don't have the Corruption to attack.";
		}
	} else if ($_POST['brutal']) {
		$type = "brutal";
		if (!hasamount(54, $_SESSION['user_id'], $constants['brutalityrequired'])) {
			$errors[] = "You don't have the Brutality to attack.";
		}
	} else if ($_POST['despair']) {
		$type = "despair";
		if (!hasamount(58, $_SESSION['user_id'], $constants['despairrequired'])) {
			$errors[] = "You don't have the Despair to attack.";
		}
	} else if ($_POST['robbery']) {
		$type = "robbery";
		$mysql['resource_id'] = (int)$_POST['resourcetorob'];
		if ($mysql['resource_id'] < 0 || $mysql['resource_id'] > 63 || $_POST['resourcetorob'] === "") {
			$errors[] = "Select a compound.";
		}
		if (!hasamount(23, $_SESSION['user_id'], $constants['robberyrequired'])) {
			$errors[] = "You don't have the Robbery to attack.";
		}
	} else {
		$errors[] = "I just don't know what went wrong!";
	}
}
//there will be an attack
if (!$errors) {
	$uncancelable = 0;
	if ($targetuser['tier'] < 5) {
		addamount(55, $_SESSION['user_id'], $nomercy * -1);
	}
	if ($targetuser['alliance_id'] == $userinfo['alliance_id']) {
		addamount(45, $_SESSION['user_id'], $constants['treasonrequired'] * -1);
	}
	if ($_POST['burden']) {
        addamount(27, $_SESSION['user_id'], $constants['burdenrequired'] * -1);
        $ticks = 4;
		$heroism = $constants["heroismforburden"];
    } else if ($_POST['corrupt']) {
		addamount(53, $_SESSION['user_id'], $constants['corruptionrequired'] * -1);
		$ticks = 8;
		$heroism = $constants["heroismforcorrupt"];
	} else if ($_POST['brutal']) {
		addamount(54, $_SESSION['user_id'], $constants['brutalityrequired'] * -1);
		$ticks = 6;
		$heroism = $constants["heroismforbrutal"];
	} else if ($_POST['despair']) {
		addamount(58, $_SESSION['user_id'], $constants['despairrequired'] * -1);
		$ticks = 6;
		$heroism = $constants["heroismfordespair"];
	} else if ($_POST['robbery']) {
		addamount(23, $_SESSION['user_id'], $constants['robberyrequired'] * -1);
		$ticks = 4;
		$heroism = $constants["heroismforrobbery"];
	}
	$sql=<<<EOSQL
	SELECT u.* 
	FROM bankedresources br
	INNER JOIN users u ON br.user_id = u.user_id
	WHERE u.alliance_id = {$targetuser['alliance_id']}
	AND br.resource_id = 26
	AND br.amount >= {$heroism}
	AND u.user_id != {$_SESSION['user_id']}
	ORDER BY br.amount DESC, u.user_id ASC
	LIMIT 1
EOSQL;
	$hero = onelinequery($sql);
	if ($hero['user_id'] == $targetuser['user_id']) {
		$infos[] = "You attacked the hero of the enemy alliance.";
	} else if ($hero['user_id']) {
		$infos[] = "Your attack was redirected to the alliance's hero, {$hero['username']}.";
		addbanked(26, $hero['user_id'], $heroism * -1);
		$targetuser = $hero;
		$uncancelable = 1;
	}
	if ($_POST['despair'] || $_POST['corrupt']) {
		if (hasbanked(56, $targetuser['user_id'], $constants["serenityfor{$type}"])) {
			$infos[] = "Your attack was blocked by the target's Serenity.";
			addbanked(56, $targetuser['user_id'], $constants["serenityfor{$type}"] * -1);
			$blocked = true;
            if (hasability("seeattackattempts", $targetuser['user_id'])) {
                addreport("{$userinfo['username']} tried to attack you, but your Serenity blocked it!", $targetuser['user_id']);
            }
		}
	} else {
		if (hasbanked(42, $targetuser['user_id'], $constants["securityfor{$type}"])) {
			$infos[] = "Your attack was blocked by the target's Security.";
			addbanked(42, $targetuser['user_id'], $constants["securityfor{$type}"] * -1);
			$blocked = true;
            if (hasability("seeattackattempts", $targetuser['user_id'])) {
                addreport("{$userinfo['username']} tried to attack you, but your Security blocked it!", $targetuser['user_id']);
            }
		}
	}
	if (!$blocked) {
        if ($peace['turns']) {
            addamount(57, $_SESSION['user_id'], $constants['perfidyrequired'] * -1);
            $infos[] = "You have violated the peace treaty.";
        }
		if ($_POST['burden']) {
			addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * -1);
		}
		$sql=<<<EOSQL
INSERT INTO attacks SET attacker = {$_SESSION['user_id']}, defender = {$targetuser['user_id']}, type = '{$type}',
uncancelable = {$uncancelable}, sent = NOW(), ticks = {$ticks}, resource_id = '{$mysql['resource_id']}', amount = '{$mysql['amount']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql) or die ($sql);
		$infos[] = "Attack sent.";
        if (alliancehasability("seeallianceattacks", $targetuser['alliance_id'])) {
            $message = $GLOBALS['mysqli']->real_escape_string("{$userinfo['username']} attacked the alliance member {$targetuser['username']}!");
            $sql=<<<EOSQL
            INSERT INTO alliance_messages (alliance_id, user_id, message, posted)
            VALUES ({$targetuser['alliance_id']}, 0, '{$message}', NOW())
EOSQL;
            $GLOBALS['mysqli']->query($sql);
        }
	}
}
}
?>