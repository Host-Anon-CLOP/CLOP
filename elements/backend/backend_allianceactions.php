<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_allianceactions"] == "") || ($_POST["token_allianceactions"] != $_SESSION["token_allianceactions"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_allianceactions"] == "")) {
    $_SESSION["token_allianceactions"] = sha1(rand() . $_SESSION["token_allianceactions"]);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$memberarray = alliancemembers($userinfo['alliance_id'], false);
$memberarraynoself = $memberarray;
unset($memberarraynoself[$_SESSION['user_id']]);
$sql=<<<EOSQL
SELECT COUNT(*) AS membercount FROM users WHERE alliance_id = '{$userinfo['alliance_id']}'
EOSQL;
$count = onelinequery($sql);
$membercount = $count['membercount'];
$sql=<<<EOSQL
	SELECT COUNT(*) as count FROM allianceinvitations WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
    $rs = onelinequery($sql);
	$totalcount = $membercount + $rs['count'];
	if ($totalcount > 4) $costtoinvite = ceil(pow(($totalcount - 4) * 8, 1.5));
	else $costtoinvite = 0;
$satisfactioncost = $constants['joyrequired'] * $membercount;
$groupabilities = array("alertproblems" => "Alert on Problems", "logbankactivity" => "Log Bank Activity", "seeallianceattacks" => "See Alliance Attacks",
"seespyattempts" => "See Spy Attempts");
$allianceabilities = array("alliancespendresources" => "Spend/Bank Resources", "allianceinviteusers" => "Invite Members",
"alliancegrantabilities" => "Grant Abilities", "alliancekickusers" => "Kick Members",
"alliancegiveresources" => "Give Resources", "alliancetakeresources" => "Take Resources",
"alliancemessaging" => "Control Messaging", "alliancemakedeals" => "Make Deals", "alliancemakewar" => "Make War");
$focusarray = array(1 => "Magic", 2 => "Loyalty", 4 => "Laughter", 8 => "Kindness", 16 => "Honesty", 32 => "Generosity");
if (!$allianceinfo['alliancefocus']) {
	$focuscost = $constants['faithtofocus'];
} else if ($allianceinfo['alliancefocusamount'] == 1) {
	$focuscost = $constants['faithtofocus'] * 4;
}
if ($_POST['focus']) {
	if ($allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "Only the alliance owner can do something this important.";
	} else {
    if ($allianceinfo['alliancefocusamount'] == 0) {
		$mysql['focuson'] = (int)$_POST['focuson'];
        if (!alliancehasamount(11, $allianceinfo['alliance_id'], $constants['faithtofocus'])) {
            $errors[] = "Your alliance does not have enough Faith to do that.";
        }
		if (!$focusarray[$mysql['focuson']]) {
			$errors[] = "Select an element to focus on.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE alliances SET alliancefocusamount = 1, alliancefocus = '{$mysql['focuson']}' WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$allianceinfo['alliancefocusamount'] = 1;
            $allianceinfo['alliancefocus'] = $mysql['focuson'];
			allianceaddamount(11, $allianceinfo['alliance_id'], $constants['faithtofocus'] * -1);
			$infos[] = "Your alliance is now focusing on {$focusarray[$mysql['focuson']]}.";
			$focuscost = $constants['faithtofocus'] * 4;
		}
    } else if ($allianceinfo['alliancefocusamount'] == 1) {
		if (!alliancehasamount(11, $allianceinfo['alliance_id'], $constants['faithtofocus'] * 4)) {
            $errors[] = "Your alliance does not have enough Faith to do that.";
        }
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE alliances SET alliancefocusamount = 2 WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$allianceinfo['alliancefocusamount'] = 2;
			allianceaddamount(11, $allianceinfo['alliance_id'], $constants['faithtofocus'] * -4);
			$infos[] = "You strengthened your alliance's focus.";
		}
	}
	}
}
if ($_POST['unfocus']) {
	if ($allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "Only the alliance owner can do something this important.";
	} else {
	if ($allianceinfo['alliancefocusamount'] == 2) {
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE alliances SET alliancefocusamount = 1 WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$allianceinfo['alliancefocusamount'] = 1;
			$infos[] = "You weakened your alliance's focus.";
			$focuscost = $constants['faithtofocus'] * 4;
		}
	} else if ($allianceinfo['alliancefocusamount'] == 1) {
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE alliances SET alliancefocusamount = 0, alliancefocus = 0 WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$allianceinfo['alliancefocusamount'] = 0;
			$allianceinfo['alliancefocus'] = 0;
			$infos[] = "You removed your alliance's focus.";
			$focuscost = $constants['faithtofocus'];
		}
	}
	}
}
if ($_POST['giveresource']) {
	if (!hasability("alliancegiveresources", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not give resources to your alliance.";
	} else {
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
		if (!hasamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'])) {
			$errors[] = "You do not have that much {$rs['name']}.";
		}
    }
	}
	if (!$errors) {
		allianceaddamount($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount']);
		addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * -1);
		$infos[] = "You have given {$mysql['amount']} {$rs['name']} to your alliance.";
        if (alliancehasability("logbankactivity", $allianceinfo['alliance_id'])) {
            $message =<<<EOFORM
<a href="viewuser.php?user_id={$_SESSION['user_id']}">{$userinfo['username']}</a> has given {$mysql['amount']} {$rs['name']} to the alliance bank.
EOFORM;
            allianceaddreport($message, $allianceinfo['alliance_id']);
        }
	}
}
if ($_POST['takeresource']) {
	if (!hasability("alliancetakeresources", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not take your alliance's resources.";
	} else {
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
		if (!alliancehasamount($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount'])) {
			$errors[] = "The alliance does not have that much {$rs['name']}.";
		}
    }
	}
	if (!$errors) {
		allianceaddamount($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount'] * -1);
		addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount']);
		$infos[] = "You have taken {$mysql['amount']} {$rs['name']} from your alliance.";
        if (alliancehasability("logbankactivity", $allianceinfo['alliance_id'])) {
			$message =<<<EOFORM
<a href="viewuser.php?user_id={$_SESSION['user_id']}">{$userinfo['username']}</a> has taken {$mysql['amount']} {$rs['name']} from the alliance bank.
EOFORM;
            allianceaddreport($message, $allianceinfo['alliance_id']);
        }
	}
}
if ($_POST['increasesatisfaction']) {
	if (!hasability("alliancespendresources", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not spend your alliance's resources.";
	} else {
	$mysql['amount'] = (int)$_POST['amount'];
	if ($mysql['amount'] < 1) {
		$errors[] = "Enter an amount.";
	}
	$satisfactionmax = 1000 + (100 * $membercount);
	$maxaddedtimes = floor(($satisfactionmax - $allianceinfo['alliancesatisfaction']) / 10);
	if ($maxaddedtimes < ($mysql['amount'])) {
		$errors[] = "You can only add 10 to your alliance's satisfaction {$maxaddedtimes} times.";
	}
	if (!alliancehasamount(13, $userinfo['alliance_id'], $constants['joyrequired'] * $mysql['amount'])) {
		$errors[] = "Your alliance does not have enough Joy to raise its satisfaction by that much.";
	}
	if (!$errors) {
		allianceaddamount(13, $userinfo['alliance_id'], $constants['joyrequired'] * $mysql['amount'] * -1);
		$addingsat = $mysql['amount'] * 10;
		$sql=<<<EOSQL
UPDATE alliances SET alliancesatisfaction = alliancesatisfaction + {$addingsat} WHERE alliance_id = {$userinfo['alliance_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Your alliance's satisfaction was raised by {$addingsat}.";
	}
	}
}
if ($_POST['increaseothersatisfaction']) {
	$mysql['alliancename'] = $GLOBALS['mysqli']->real_escape_string($_POST['alliancename']);
	if (!hasability("alliancespendresources", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not spend your alliance's resources.";
	} else {
	$mysql['amount'] = (int)$_POST['amount'];
	if ($mysql['amount'] < 1) {
		$errors[] = "Enter an amount.";
	}
	$sql=<<<EOSQL
	SELECT name, alliance_id FROM alliances WHERE name = '{$mysql['alliancename']}'
EOSQL;
	$thatalliance = onelinequery($sql);
	$sql=<<<EOSQL
	SELECT COUNT(*) AS membercount FROM alliances WHERE alliance_id = '{$thatalliance['alliance_id']}'
EOSQL;
	$thatmembercount = onelinequery($sql);
    if (!$thatmembercount['membercount']) {
		$errors[] = "Alliance not found.";
    }
	$satisfactionmax = 1000 + (100 * $thatmembercount['membercount']);
	$maxaddedtimes = floor(($satisfactionmax - $thatalliance['alliancesatisfaction']) / 10);
	if ($maxaddedtimes < ($mysql['amount'])) {
		$errors[] = "You cannot add 10 to that alliance's satisfaction that many times.";
	}
	if (!alliancehasamount(37, $userinfo['alliance_id'], $constants['hoperequired'] * $mysql['amount'])) {
		$errors[] = "Your alliance does not have enough Hope to raise that alliance's satisfaction by that much.";
	}
	if (!$errors) {
		allianceaddamount(37, $userinfo['alliance_id'], $constants['hoperequired'] * $mysql['amount'] * -1);
		$addingsat = $mysql['amount'] * 10;
		$sql=<<<EOSQL
UPDATE alliances SET alliancesatisfaction = alliancesatisfaction + {$addingsat} WHERE alliance_id = {$thatalliance['alliance_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "{$thatalliance['name']}'s satisfaction was raised by {$addingsat}.";
	}
	}
}
if ($_POST['inviteuser']) {
	$mysql['username'] = $GLOBALS['mysqli']->real_escape_string($_POST['username']);
	if (!hasability("allianceinviteusers", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not invite users to your alliance.";
	} else {
	if ($costtoinvite) {
		if (!alliancehasamount(14, $userinfo['alliance_id'], $costtoinvite)) {
			$errors[] = "Your alliance does not have the Love to invite another user.";
		}
	}
	$sql=<<<EOSQL
	SELECT username, user_id, alliance_id FROM users WHERE username = '{$mysql['username']}'
EOSQL;
	$thatuser = onelinequery($sql);
	if (!$thatuser['user_id']) {
		$errors[] = "User not found. (He must make an account first.)";
	}
	if ($thatuser['alliance_id'] == $allianceinfo['alliance_id']) {
		$errors[] = "This user is already part of this alliance.";
	}
	$sql=<<<EOSQL
	SELECT user_id FROM allianceinvitations WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND user_id = '{$thatuser['user_id']}'
EOSQL;
	$rs = onelinequery($sql);
	if ($rs['user_id']) {
		$errors[] = "You have already invited that user to your alliance.";
	}
	if (!$errors) {
		$sql=<<<EOSQL
		INSERT INTO allianceinvitations SET alliance_id = '{$allianceinfo['alliance_id']}', user_id = '{$thatuser['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		if ($totalcount > 4) {
			allianceaddamount(14, $userinfo['alliance_id'], $costtoinvite * -1);
			$infos[] = "You have invited {$thatuser['username']} at the cost of {$costtoinvite} Love.";
		} else {
			$infos[] = "You have invited {$thatuser['username']}.";
		}
		$totalcount++;
		if ($totalcount > 4) $costtoinvite = ceil(pow(($totalcount - 4) * 8, 1.5));
		else $costtoinvite = 0;
	}
	}
}
if ($_POST['rescindinvitation']) {
	$mysql['user_id'] = (int)$_POST['user_id'];
    $sql=<<<EOSQL
	SELECT alliance_id FROM allianceinvitations
	WHERE alliance_id = {$allianceinfo['alliance_id']}
	AND user_id = {$mysql['user_id']}
EOSQL;
	$rs = onelinequery($sql);
	if (!$rs['alliance_id']) {
		$errors[] = "Invitation not found.";
	}
	if (!hasability("alliancekickusers", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not rescind invitations.";
	}
	if (!$errors) {
		$sql=<<<EOSQL
		DELETE from allianceinvitations WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND user_id = '{$mysql['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$totalcount--;
		if ($totalcount > 4) $costtoinvite = ceil(pow(($totalcount - 4) * 8, 1.5));
		else $costtoinvite = 0;
	}
}
if ($_POST['grantability']) {
	if (!hasability("alliancegrantabilities", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not grant abilities.";
	} else {
		$mysql['user_id'] = (int)$_POST['user_id'];
		$mysql['turns'] = (int)$_POST['turns'];
		if ($mysql['turns'] < 1) {
			$errors[] = "Enter a number of turns.";
		}
		if ($mysql['user_id'] < 1) {
			$errors[] = "Select a user.";
		} else {
			$sql=<<<EOSQL
			SELECT * FROM users WHERE user_id = '{$mysql['user_id']}'
EOSQL;
			$rs = onelinequery($sql);
			if (!$memberarray[$mysql['user_id']]) {
				$errors[] = "That user is not in your alliance.";
			}
		}
		$mysql['abilityname'] = $GLOBALS['mysqli']->real_escape_string($_POST['abilityname']);
		if (!$allianceabilities[$mysql['abilityname']]) {
			$errors[] = "Enter an ability name.";
		} else if (!hasability($mysql['abilityname'], $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
			$errors[] = "You cannot grant an ability you don't have.";
		}
		if ($allianceinfo['owner_id'] == $mysql['user_id']) {
			$errors[] = "That's the owner; there'd be no point.";
		}
		if (!alliancehasamount(50, $allianceinfo['alliance_id'], $constants['favorforabilities'] * $mysql['turns'])) {
			$errors[] = "Your alliance does not have enough Favor to grant an ability for that long.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
			SELECT ability_id FROM abilities WHERE name = '{$mysql['abilityname']}'
EOSQL;
			$abilityid = onelinequery($sql);
			$sql=<<<EOSQL
			INSERT INTO user_abilities SET ability_id = '{$abilityid['ability_id']}', user_id = '{$mysql['user_id']}', turns = '{$mysql['turns']}'
			ON DUPLICATE KEY UPDATE turns = turns + {$mysql['turns']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Ability granted.";
			allianceaddamount(50, $allianceinfo['alliance_id'], $constants['favorforabilities'] * $mysql['turns'] * -1);
		}
	}
}
if ($_POST['uplift']) {
	if ($allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "Only the owner of an alliance may uplift.";
	} else {
	$mysql['user_id'] = (int)$_POST['user_id'];
	$sql=<<<EOSQL
	SELECT * FROM users WHERE user_id = '{$mysql['user_id']}'
EOSQL;
	$rs = onelinequery($sql);
	if ($rs['ascended']) {
		$errors[] = "This user can already make an alliance.";
	}
	if ($rs['alliance_id'] != $allianceinfo['alliance_id']) {
		$errors[] = "You may only uplift users in your alliance.";
	}
	if (!alliancehasamount(38, $allianceinfo['alliance_id'], $constants['magnanimitytouplift'])) {
		$errors[] = "Your alliance does not have the Magnanimity to uplift.";
	}
	if (!$errors) {
		allianceaddamount(38, $allianceinfo['alliance_id'], $constants['magnanimitytouplift'] * -1);
		$sql=<<<EOSQL
		UPDATE users SET ascended = 1 WHERE user_id = '{$mysql['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "{$rs['username']} has been uplifted!";
		$message =<<<EOFORM
<a href="viewuser.php?user_id={$rs['user_id']}">{$rs['username']}</a> has been uplifted by his alliance,
<a href="viewalliance.php?alliance_id={$allianceinfo['alliance_id']}">{$allianceinfo['name']}!</a>
EOFORM;
		$message = $GLOBALS['mysqli']->real_escape_string($message);
		$sql=<<<EOSQL
INSERT INTO news SET message = '{$message}', posted = NOW()
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$message = $GLOBALS['mysqli']->real_escape_string("Your alliance leader has just uplifted you!");
		$sql=<<<EOSQL
		INSERT INTO messages (fromuser, touser, fromdeleted, message, sent)
		VALUES (0, {$rs['user_id']}, 1, '{$message}', NOW())
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	}
	}
}
if ($_POST['transfercontrol']) {
	if ($allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "Only the owner of an alliance may transfer control.";
	} else {
	$mysql['user_id'] = (int)$_POST['user_id'];
	$sql=<<<EOSQL
	SELECT * FROM users WHERE user_id = '{$mysql['user_id']}'
EOSQL;
	$rs = onelinequery($sql);
	if (!$rs['ascended']) {
		$errors[] = "This user is not ascended and cannot control an alliance.";
	}
	if ($rs['alliance_id'] != $allianceinfo['alliance_id']) {
		$errors[] = "You may only transfer control to users in your alliance.";
	}
	if (!alliancehasamount(35, $allianceinfo['alliance_id'], $constants['nobilitytotransfer'])) {
		$errors[] = "Your alliance does not have the Nobility to transfer control.";
	}
	if (!$errors) {
		allianceaddamount(35, $allianceinfo['alliance_id'], $constants['nobilitytotransfer'] * -1);
		$sql=<<<EOSQL
		UPDATE alliances SET owner_id = {$rs['user_id']} WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "{$rs['username']} has been given control of the alliance!";
		$message =<<<EOFORM
<a href="viewuser.php?user_id={$rs['user_id']}">{$rs['username']}</a> is now in control of
<a href="viewalliance.php?alliance_id={$allianceinfo['alliance_id']}">{$allianceinfo['name']}!</a>
EOFORM;
		$message = $GLOBALS['mysqli']->real_escape_string($message);
		$sql=<<<EOSQL
INSERT INTO news SET message = '{$message}', posted = NOW()
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$message = $GLOBALS['mysqli']->real_escape_string("You now control your alliance!");
		$sql=<<<EOSQL
		INSERT INTO messages (fromuser, touser, fromdeleted, message, sent)
		VALUES (0, {$rs['user_id']}, 1, '{$message}', NOW())
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	}
	}
}
if ($_POST['kick']) {
	if (!hasability("alliancekickusers", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not kick alliance members.";
	} else {
        $mysql['user_id'] = (int)$_POST['user_id'];
		if (!$memberarray[$mysql['user_id']]) {
			$errors[] = "That user is not in your alliance. Asshole.";
		} else if ($allianceinfo['owner_id'] == $_SESSION['user_id'] && $mysql['user_id'] == $_SESSION['user_id']) {
			$errors[] = "You need to transfer control of the alliance to someone else first.";
		} else if ($allianceinfo['owner_id'] == $mysql['user_id']) {
			$errors[] = "It doesn't matter how much Backstabbing you've saved up, you can't do that.";
		} else if ($mysql['user_id'] == $_SESSION['user_id']) {
			$errors[] = "You have to get someone else's help to ragequit.";
		} else {
			$sql=<<<EOSQL
SELECT user_id FROM kickattempts WHERE user_id = {$mysql['user_id']}
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['user_id']) {
				$errors[] = "Someone already tried to kick this user this tick, and it didn't work. Try again next tick.";
			}
		}
		if (!$errors) {
			if (!hasbanked(61, $mysql['user_id'], $constants['treacheryabsorbed'])) {
                $infos[] = "Player kicked. Hope it was worth it.";
                unset($memberarray[$mysql['user_id']]);
                unset($memberarraynoself[$mysql['user_id']]);
				$sql=<<<EOSQL
UPDATE users SET production = 0, alliance_id = 0, satisfaction = 0 WHERE user_id = '{$mysql['user_id']}'		
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
DELETE FROM resources WHERE user_id = '{$mysql['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
DELETE FROM bankedresources WHERE user_id = '{$mysql['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
DELETE FROM user_abilities WHERE user_id = '{$mysql['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
DELETE FROM marketplace WHERE user_id = '{$mysql['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
                $sql=<<<EOSQL
DELETE FROM attacks WHERE attacker = '{$mysql['user_id']}' OR defender = '{$mysql['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
SELECT deal_id FROM deals WHERE fromuser = '{$mysql['user_id']}' OR touser = '{$mysql['user_id']}'
EOSQL;
				$sth = $GLOBALS['mysqli']->query($sql);
				while ($rs = mysqli_fetch_array($sth)) {
					$sql=<<<EOSQL
DELETE FROM dealitems WHERE deal_id = '{$rs['deal_id']}'
EOSQL;
					$GLOBALS['mysqli']->query($sql);
				}
			} else {
				addbanked(61, $mysql['user_id'], $constants['treacheryabsorbed'] * -1);
				$sql=<<<EOSQL
INSERT INTO kickattempts SET user_id = '{$mysql['user_id']}'	
EOSQL;
				$GLOBALS['mysqli']->query($sql);
                $infos[] = "That user's Treachery prevented the kick!";
			}
		}
	}
}
if ($_POST['purchaseability']) {
    if (!hasability("alliancespendresources", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not spend your alliance's resources.";
	} else {
    $mysql['turns'] = (int)$_POST['turns'];
    if ($mysql['turns'] < 1) {
        $errors[] = "Enter a number of turns.";
    }
    $mysql['abilityname'] = $GLOBALS['mysqli']->real_escape_string($_POST['abilityname']);
    if (!$groupabilities[$mysql['abilityname']]) {
        $errors[] = "Enter an ability name.";
    }
    if (!alliancehasamount(49, $allianceinfo['alliance_id'], $constants['benevolenceforabilities'] * $mysql['turns'])) {
        $errors[] = "Your alliance does not have enough Benevolence to gain an ability for that long.";
    }
    if (!$errors) {
        $sql=<<<EOSQL
        SELECT ability_id FROM groupabilities WHERE name = '{$mysql['abilityname']}'
EOSQL;
        $abilityid = onelinequery($sql);
        $sql=<<<EOSQL
        INSERT INTO alliance_groupabilities SET ability_id = '{$abilityid['ability_id']}', alliance_id = '{$allianceinfo['alliance_id']}', turns = '{$mysql['turns']}'
        ON DUPLICATE KEY UPDATE turns = turns + {$mysql['turns']}
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Ability granted.";
        allianceaddamount(49, $allianceinfo['alliance_id'], $constants['benevolenceforabilities'] * $mysql['turns'] * -1);
    }
    }
}
if ($_POST['disband']) {
    if ($allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "Only the owner may disband.";
	}
    if ($totalcount > 1) {
		$errors[] = "Rescind all invitations and kick everyone else out of your alliance first.";
	}
	if (!$errors) {
		$sql=<<<EOSQL
UPDATE users SET production = 0, alliance_id = 0, satisfaction = 0 WHERE user_id = '{$userinfo['user_id']}'		
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
DELETE FROM resources WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
DELETE FROM bankedresources WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
DELETE FROM user_abilities WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
DELETE FROM marketplace WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
DELETE FROM attacks WHERE attacker = '{$userinfo['user_id']}' OR defender = '{$userinfo['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
SELECT deal_id FROM deals WHERE fromuser = '{$userinfo['user_id']}' OR touser = '{$userinfo['user_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			$sql=<<<EOSQL
DELETE FROM dealitems WHERE deal_id = '{$rs['deal_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
		$sql=<<<EOSQL
DELETE FROM alliances WHERE alliance_id = '{$userinfo['alliance_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		header("Location: index.php");
		exit;
	}
}
$sql=<<<EOSQL
SELECT u.username, u.user_id FROM allianceinvitations ai
INNER JOIN users u ON u.user_id = ai.user_id
WHERE ai.alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$invitations[$rs['user_id']] = $rs['username'];
}
?>