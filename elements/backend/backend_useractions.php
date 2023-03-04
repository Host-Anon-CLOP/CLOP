<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_useractions"] == "") || ($_POST["token_useractions"] != $_SESSION["token_useractions"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_useractions"] == "")) {
    $_SESSION["token_useractions"] = sha1(rand() . $_SESSION["token_useractions"]);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$userabilities = array("seerippedoff" => "See Ripped Off", "logmarketplace" => "Log Marketplace", "seespyattempts" => "See Spy Attempts", "seeattackattempts" => "See Attack Attempts");
$alliancemembers = alliancemembers($userinfo['alliance_id'], true);
$productioncost = ceil(pow($userinfo['production'], 1.5));
switch ($userinfo['tier']) {
	case 1:
	$requiredresource = 1;
	$requiredname = "Magic";
	break;
    case 2:
    $requiredresource = 5;
	$requiredname = "Optimism";
	break;
    case 3:
    $requiredresource = 52;
	$requiredname = "Growth";
	break;
    case 4:
    $requiredresource = 43;
	$requiredname = "Narcissism";
	break;
    case 5:
    $requiredresource = 62;
	$requiredname = "Drudgery";
	break;
    case 6:
    $requiredresource = 63;
    $requiredname = "Harmony";
	break;
    default:
    echo "Call the admin, something's broke.";
    break;
}
$focusarray = array(1 => "Magic", 2 => "Loyalty", 4 => "Laughter", 8 => "Kindness", 16 => "Honesty", 32 => "Generosity");
if (!$userinfo['focus']) {
	$focuscost = $constants['devotiontofocus'];
} else if ($userinfo['focusamount'] == 1) {
	$focuscost = $constants['devotiontofocus'] * 4;
}
if ($_POST['focus']) {
    if ($userinfo['focusamount'] == 0) {
		$mysql['focuson'] = (int)$_POST['focuson'];
        if (!hasamount(10, $_SESSION['user_id'], $constants['devotiontofocus'])) {
            $errors[] = "You do not have enough Devotion to do that.";
        }
		if (!$focusarray[$mysql['focuson']]) {
			$errors[] = "Select an element to focus on.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE users SET focusamount = 1, focus = '{$mysql['focuson']}' WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$userinfo['focusamount'] = 1;
            $userinfo['focus'] = $mysql['focuson'];
			addamount(10, $_SESSION['user_id'], $constants['devotiontofocus'] * -1);
			$infos[] = "You are now focusing on {$focusarray[$mysql['focuson']]}.";
			$focuscost = $constants['devotiontofocus'] * 4;
		}
    } else if ($userinfo['focusamount'] == 1) {
		if (!hasamount(10, $_SESSION['user_id'], $constants['devotiontofocus'] * 4)) {
            $errors[] = "You do not have enough Devotion to do that.";
        }
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE users SET focusamount = 2 WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$userinfo['focusamount'] = 2;
			addamount(10, $_SESSION['user_id'], $constants['devotiontofocus'] * -4);
			$infos[] = "You strengthened your focus.";
		}
	}
}
if ($_POST['unfocus']) {
	if ($userinfo['focusamount'] == 2) {
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE users SET focusamount = 1 WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$userinfo['focusamount'] = 1;
			$infos[] = "You weakened your focus.";
			$focuscost = $constants['devotiontofocus'] * 4;
		}
	} else if ($userinfo['focusamount'] == 1) {
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE users SET focusamount = 0, focus = 0 WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$userinfo['focusamount'] = 0;
			$userinfo['focus'] = 0;
			$infos[] = "You removed your focus.";
			$focuscost = $constants['devotiontofocus'];
		}
	}
}
if ($_POST['increaseproduction']) {
    if (!hasamount($requiredresource, $_SESSION['user_id'], $productioncost)) {
		$errors[] = "You do not have enough {$requiredname} to do that.";
    } else {
		addamount($requiredresource, $_SESSION['user_id'], $productioncost * -1);
		$sql=<<<EOSQL
		UPDATE users SET production = production + 1 WHERE user_id = {$_SESSION['user_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$userinfo['production']++;
		$infos[] = "You spent {$productioncost} {$requiredname} to raise your production by 1.";
		if ($userinfo['production'] == 6 || $userinfo['production'] == 21 || $userinfo['production'] == 41 || $userinfo['production'] == 71 || $userinfo['production'] == 101) {
			$sql=<<<EOSQL
			UPDATE users SET tier = tier + 1 WHERE user_id = {$_SESSION['user_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$userinfo['tier']++;
			$infos[] = "Your tier has increased!";
			switch ($userinfo['tier']) {
				case 1:
				$requiredresource = 1; $requiredname = "Magic"; break;
				case 2:
				$requiredresource = 5; $requiredname = "Optimism"; break;
				case 3:
				$requiredresource = 52;	$requiredname = "Growth"; break;
				case 4:
				$requiredresource = 43;	$requiredname = "Narcissism"; break;
				case 5:
				$requiredresource = 62;	$requiredname = "Drudgery";	break;
				case 6:
				$requiredresource = 63;	$requiredname = "Harmony"; break;
				default:
				echo "Call the admin, something's broke.";
				break;
			}
		}
		$productioncost = ceil(pow($userinfo['production'], 1.5));
	}
}
if ($_POST['increaseownsatisfaction']) {
	$mysql['amount'] = (int)$_POST['amount'];
	$satisfactionmax = 1000 + (100 * $userinfo['tier']);
	$maxaddedtimes = floor(($satisfactionmax - $userinfo['satisfaction']) / 10);
	if ($maxaddedtimes < ($mysql['amount'])) {
		$errors[] = "You can only add 10 to your satisfaction {$maxaddedtimes} times.";
	}
	if (!hasamount(12, $_SESSION['user_id'], $constants['happinessrequired'] * $mysql['amount'])) {
		$errors[] = "You do not have enough Happiness to raise your satisfaction by that much.";
	}
	if ($mysql['amount'] < 1) {
		$errors[] = "Enter an amount.";
	}
	if (!$errors) {
		addamount(12, $_SESSION['user_id'], $constants['happinessrequired'] * $mysql['amount'] * -1);
		$addingsat = $mysql['amount'] * 10;
		$sql=<<<EOSQL
		UPDATE users SET satisfaction = satisfaction + {$addingsat} WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Your satisfaction was raised by {$addingsat}.";
		addreport("You raised your satisfaction by {$addingsat}.", $_SESSION['user_id']);
	}
}
if ($_POST['increasemembersatisfaction']) {
	$mysql['user_id'] = (int)$_POST['user_id'];
	$mysql['amount'] = (int)$_POST['amount'];
	$sql=<<<EOSQL
	SELECT * FROM users WHERE user_id = '{$mysql['user_id']}'
EOSQL;
	$member = onelinequery($sql);
	if ($mysql['user_id'] == $_SESSION['user_id']) {
		$errors[] = "Nice try!";
	}
	if ($member['alliance_id'] != $userinfo['alliance_id']) {
		$errors[] = "Select an alliance member.";
	}
	if ($mysql['amount'] < 1) {
		$errors[] = "Enter an amount.";
	}
	if (!$errors) {
	$satisfactionmax = 1000 + (100 * $member['tier']);
	$maxaddedtimes = floor(($satisfactionmax - $member['satisfaction']) / 10);
	if ($maxaddedtimes < ($mysql['amount'])) {
		$errors[] = "You can only add 10 to that player's satisfaction {$maxaddedtimes} times.";
	}
	if (!hasamount(6, $_SESSION['user_id'], $constants['camaraderierequired'] * $mysql['amount'])) {
		$errors[] = "You do not have enough Camaraderie to raise that player's satisfaction by that much.";
	}
	if (!$errors) {
		addamount(6, $_SESSION['user_id'], $constants['camaraderierequired'] * $mysql['amount'] * -1);
		$addingsat = $mysql['amount'] * 10;
		$sql=<<<EOSQL
		UPDATE users SET satisfaction = satisfaction + {$addingsat} WHERE user_id = '{$mysql['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "{$member['username']}'s satisfaction was raised by {$addingsat}.";
		addreport("{$userinfo['username']} raised your satisfaction by {$addingsat}.", $mysql['user_id']);
	}
	}
}
if ($_POST['increasenonmembersatisfaction']) {
	$mysql['username'] = $GLOBALS['mysqli']->real_escape_string($_POST['username']);
	$mysql['amount'] = (int)$_POST['amount'];
	$sql=<<<EOSQL
	SELECT * FROM users WHERE username = '{$mysql['username']}'
EOSQL;
	$nonmember = onelinequery($sql);
	if (!$nonmember['user_id']) {
		$errors[] = "User not found.";
	} else if (!$nonmember['alliance_id']) {
		$errors[] = "That user is not part of any alliance.";
	}
	if ($nonmember['alliance_id'] == $userinfo['alliance_id']) {
		$errors[] = "You can't use Cheer on somebody in your alliance.";
	}
	if ($mysql['amount'] < 1) {
		$errors[] = "Enter an amount.";
	}
	if (!$errors) {
	$satisfactionmax = 1000 + (100 * $nonmember['tier']);
	$maxaddedtimes = floor(($satisfactionmax - $nonmember['satisfaction']) / 10);
	if ($maxaddedtimes < ($mysql['amount'])) {
		$errors[] = "You can't add to that player's satisfaction that many times.";
	}
	if (!hasamount(36, $_SESSION['user_id'], $constants['cheerrequired'] * $mysql['amount'])) {
		$errors[] = "You do not have enough Cheer to raise that player's satisfaction by that much.";
	}
	if (!$errors) {
		addamount(36, $_SESSION['user_id'], $constants['cheerrequired'] * $mysql['amount'] * -1);
		$addingsat = $mysql['amount'] * 10;
		$sql=<<<EOSQL
		UPDATE users SET satisfaction = satisfaction + {$addingsat} WHERE user_id = '{$nonmember['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "{$nonmember['username']}'s satisfaction was raised by {$addingsat}.";
		$message =<<<EOFORM
<a href="viewuser.php?user_id={$userinfo['user_id']}">{$userinfo['username']}</a> raised your satisfaction by {$addingsat}.
EOFORM;
		addreport($message, $nonmember['user_id']);
	}
	}
}
if ($_POST['purchaseability']) {
    $mysql['turns'] = (int)$_POST['turns'];
    if ($mysql['turns'] < 1) {
        $errors[] = "Enter a number of turns.";
    }
    $mysql['abilityname'] = $GLOBALS['mysqli']->real_escape_string($_POST['abilityname']);
    if (!$userabilities[$mysql['abilityname']]) {
        $errors[] = "Enter an ability name.";
    }
    if (!hasamount(48, $_SESSION['user_id'], $constants['beneficenceforabilities'] * $mysql['turns'])) {
        $errors[] = "You do not have enough Beneficence to gain an ability for that long.";
    }
    if (!$errors) {
        $sql=<<<EOSQL
        SELECT ability_id FROM abilities WHERE name = '{$mysql['abilityname']}'
EOSQL;
        $abilityid = onelinequery($sql);
        $sql=<<<EOSQL
        INSERT INTO user_abilities SET ability_id = '{$abilityid['ability_id']}', user_id = '{$_SESSION['user_id']}', turns = '{$mysql['turns']}'
        ON DUPLICATE KEY UPDATE turns = turns + {$mysql['turns']}
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Ability granted.";
        addamount(48, $_SESSION['user_id'], $constants['beneficenceforabilities'] * $mysql['turns'] * -1);
    }
}
if ($_POST['postmessage']) {
    if (!hasamount(20, $_SESSION['user_id'], $constants['humornecessary'])) {
        $errors[] = "You do not have enough Humor to change the top message.";
    }
    $mysql['message'] = $GLOBALS['mysqli']->real_escape_string($_POST['message']);
    if (!$mysql['message']) {
        $errors[] = "Enter a message.";
    }
    if (!$errors) {
        $sql=<<<EOSQL
		DELETE FROM topmessage WHERE 1 = 1;
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
INSERT INTO topmessage SET message = '{$mysql['message']}', user_id = '{$_SESSION['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		addamount(20, $_SESSION['user_id'], $constants['humornecessary'] * -1);
		$infos[] = "Message set.";
    }
}
if ($_POST['embezzle']) {
    if (!hasamount(29, $_SESSION['user_id'], $constants['embezzlementrequired'])) {
        $errors[] = "You do not have enough Embezzlement for that.";
    }
    $mysql['resource_id'] = (int)$_POST['resource_id'];
	$mysql['amount'] = (int)$_POST['amount'];
    if ($mysql['amount'] < 1) {
        $errors[] = "No amount entered.";
    }
    if (!isset($_POST['resource_id']) || $mysql['resource_id'] < 0 || $mysql['resource_id'] > 63) {
        $errors[] = "Enter a resource.";
    } else {
        $sql=<<<EOSQL
		SELECT name FROM resourcedefs
		WHERE resource_id = '{$mysql['resource_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!alliancehasamount($mysql['resource_id'], $userinfo['alliance_id'], $mysql['amount'])) {
			$errors[] = "The alliance does not have that much {$rs['name']}.";
		}
    }
	if (!$errors) {
		allianceaddamount($mysql['resource_id'], $userinfo['alliance_id'], $mysql['amount'] * -1);
        addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount']);
		addamount(29, $_SESSION['user_id'], $constants['embezzlementrequired'] * -1);
		$infos[] = "You have embezzled {$mysql['amount']} {$rs['name']} from your alliance.";
        if (alliancehasability("logbankactivity", $userinfo['alliance_id'])) {
			$message =<<<EOFORM
Someone has embezzled {$mysql['amount']} {$rs['name']} from the alliance bank!
EOFORM;
            allianceaddreport($message, $userinfo['alliance_id']);
        }
    }
}
if ($_POST['encouragemember']) {
	$mysql['turns'] = (int)$_POST['turns'];
	$mysql['user_id'] = (int)$_POST['user_id'];
	$sql=<<<EOSQL
	SELECT * FROM users WHERE user_id = '{$mysql['user_id']}'
EOSQL;
	$member = onelinequery($sql);
	if ($mysql['user_id'] == $_SESSION['user_id']) {
		$errors[] = "Nice try!";
	}
	if ($member['alliance_id'] != $userinfo['alliance_id']) {
		$errors[] = "Select an alliance member.";
	}
	if ($mysql['turns'] < 1) {
		$errors[] = "Enter a number of ticks.";
	}
	$sql=<<<EOSQL
	SELECT turns FROM user_abilities
	WHERE ability_id = 3 AND user_id = {$mysql['user_id']}
EOSQL;
	$turns = onelinequery($sql);
	if ($turns['turns'] + $mysql['turns'] > 12) {
		$errors[] = "That would cause that user to have more than 12 ticks of Encouragement, which is the maximum.";
	}
	if (!hasamount(22, $_SESSION['user_id'], $constants['encouragementrequired'] * $mysql['turns'])) {
        $errors[] = "You do not have enough Encouragement to encourage this user for this many ticks.";
    }
    if (!$errors) {
        $sql=<<<EOSQL
        INSERT INTO user_abilities SET ability_id = 3, user_id = '{$mysql['user_id']}', turns = '{$mysql['turns']}'
        ON DUPLICATE KEY UPDATE turns = turns + {$mysql['turns']}
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Alliance member encouraged.";
        addamount(22, $_SESSION['user_id'], $constants['encouragementrequired'] * $mysql['turns'] * -1);
    }
}
?>