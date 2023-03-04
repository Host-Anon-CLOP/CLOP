<?php
include("allfunctions.php");
include("listresources.php");
$getpost = array_merge($_GET, $_POST);
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$sql =<<<EOSQL
SELECT u.*, a.name AS alliancename, a.alliancesatisfaction,
a.alliancefocus, a.alliancefocusamount
FROM users u LEFT JOIN alliances a ON u.alliance_id = a.alliance_id WHERE u.user_id = '{$mysql['user_id']}'
EOSQL;
$thisuser = onelinequery($sql);
if ($_POST && (($_POST['token_viewuser'] == "") || ($_POST['token_viewuser'] != $_SESSION['token_viewuser']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_viewuser'] == "")) {
    $_SESSION['token_viewuser'] = sha1(rand() . $_SESSION['token_viewuser']);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$display['description'] = $thisuser['description'];
if ($_POST['action'] == "Send Message" && $_SESSION['user_id']) {
    $sql=<<<EOSQL
    SELECT user_id from users WHERE user_id = '{$mysql['user_id']}'
EOSQL;
    if (!onelinequery($sql)) {
        $errors[] = "Something went badly wrong.";
    }
    if (!$mysql['message']) {
        $errors[] = "No message entered.";
    }
    if (empty($errors)) {
        $sql=<<<EOSQL
SELECT * FROM blocklist WHERE blocker = '{$mysql['user_id']}' AND blockee = '{$_SESSION['user_id']}'      
EOSQL;
        $rs = onelinequery($sql);
        if ($rs['blocker']) {
            $errors[] = "That user has you blocked from sending messages.";
        } else {
            $sql = "INSERT INTO messages (touser, fromuser, message, sent) VALUES ({$mysql['user_id']}, {$_SESSION['user_id']}, '{$mysql['message']}', NOW())";
            $GLOBALS['mysqli']->query($sql);
            $display['message'] = "";
            $infos[] = "Message sent.";
        }
    }
}
if ($userinfo['alliance_id'] && $thisuser['alliance_id'] && $_POST['spy'] && !$errors) {
	if ($thisuser['user_id'] < 5 && $_SESSION['user_id'] >= 5) {
		$errors[] = "There's not a lot of point to that, really.";
	} else if ($thisuser['user_id'] == $_SESSION['user_id']) {
		$errors[] = "It's okay. Relax. We're all here for you.";
	} else if (!hasamount(25, $_SESSION['user_id'], $constants['equalitytospy'])) {
        $errors[] = "You do not have the Equality to spy on this user.";
    } else if (hasbanked(7, $thisuser['user_id'], $constants['unitytoblock'])) {
        $infos[] = "Your spying attempt was blocked by the target's Unity.";
        addamount(25, $_SESSION['user_id'], $constants['equalitytospy'] * -1);
        addbanked(7, $thisuser['user_id'], $constants['unitytoblock'] * -1);
        if (hasability("seespyattempts", $thisuser['user_id'])) {
            addreport("{$userinfo['username']} tried to spy on you, but your Unity blocked it!", $thisuser['user_id']);
        }
        $blocked = true;
    }
if (!$errors && !$blocked) {
	$sql=<<<EOSQL
	SELECT * FROM elementpositions
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$positions[$rs['position']] = $rs['resource_id'];
	}
	$elementpositions = array_flip($positions);
	foreach ($elementpositions as $value) {
		$production[$value] = $thisuser['production'];
	}
	if ($thisuser['alliancefocus'] == $thisuser['focus']) {
		$thisuser['focusamount'] += $thisuser['alliancefocusamount'];
	} else {
		switch ($thisuser['alliancefocusamount']) {
			case 1:
			$production[$elementpositions[$thisuser['alliancefocus']]] *= 2;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 5)] *= 1.25;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 1)] *= 1.25;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 4)] *= .8;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 2)] *= .8;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 3)] *= .5;
			break;
			case 2:
			$production[$elementpositions[$thisuser['alliancefocus']]] *= 3;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 5)] *= 2;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 1)] *= 2;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 4)] *= .5;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 2)] *= .5;
			$production[withinsix($elementpositions[$thisuser['alliancefocus']] + 3)] *= .25;
			break;
			default:
			break;
		}
	}
	switch ($thisuser['focusamount']) {
		case 1:
		$production[$elementpositions[$thisuser['focus']]] *= 2;
		$production[withinsix($elementpositions[$thisuser['focus']] + 5)] *= 1.25;
		$production[withinsix($elementpositions[$thisuser['focus']] + 1)] *= 1.25;
		$production[withinsix($elementpositions[$thisuser['focus']] + 4)] *= .8;
		$production[withinsix($elementpositions[$thisuser['focus']] + 2)] *= .8;
		$production[withinsix($elementpositions[$thisuser['focus']] + 3)] *= .5;
		break;
		case 2:
		$production[$elementpositions[$thisuser['focus']]] *= 3;
		$production[withinsix($elementpositions[$thisuser['focus']] + 5)] *= 2;
		$production[withinsix($elementpositions[$thisuser['focus']] + 1)] *= 2;
		$production[withinsix($elementpositions[$thisuser['focus']] + 4)] *= .5;
		$production[withinsix($elementpositions[$thisuser['focus']] + 2)] *= .5;
		$production[withinsix($elementpositions[$thisuser['focus']] + 3)] *= .25;
		break;
		case 3:
		$production[$elementpositions[$thisuser['focus']]] *= 4;
		$production[withinsix($elementpositions[$thisuser['focus']] + 5)] *= 2.5;
		$production[withinsix($elementpositions[$thisuser['focus']] + 1)] *= 2.5;
		$production[withinsix($elementpositions[$thisuser['focus']] + 4)] *= 0;
		$production[withinsix($elementpositions[$thisuser['focus']] + 2)] *= 0;
		$production[withinsix($elementpositions[$thisuser['focus']] + 3)] *= 0;
		break;
		case 4:
		$production[$elementpositions[$thisuser['focus']]] *= 15;
		$production[withinsix($elementpositions[$thisuser['focus']] + 5)] *= 0;
		$production[withinsix($elementpositions[$thisuser['focus']] + 1)] *= 0;
		$production[withinsix($elementpositions[$thisuser['focus']] + 4)] *= 0;
		$production[withinsix($elementpositions[$thisuser['focus']] + 2)] *= 0;
		$production[withinsix($elementpositions[$thisuser['focus']] + 3)] *= 0;
		break;
		default:
		break;
	}
	foreach ($production as $element => $amount) {
		$production[$element] = floor($amount);
	}
		$threshold = ($thisuser['production'] * 6) + 50;
		addamount(25, $_SESSION['user_id'], $constants['equalitytospy'] * -1);
		$resourcelist = getresourcelist($thisuser['user_id']);
	}
}
?>