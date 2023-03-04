<?php
include("allfunctions.php");
if ($_POST['user_id']) {
	$mysql['user_id'] = (int)$_POST['user_id'];
} else {
	$mysql['user_id'] = (int)$_GET['user_id'];
}
$sql =<<<EOSQL
SELECT u.username, u.user_id, u.email, u.flag, u.donator, u.stasismode, u.description, u.hidebanners, u.hideicons, u.hidereports, u.alliance_id, a.name AS alliancename
FROM users u LEFT JOIN alliances a ON u.alliance_id = a.alliance_id WHERE u.user_id = '{$mysql['user_id']}'
EOSQL;
$userinfo = onelinequery($sql);
$display['description'] = $userinfo['description'];
$sql=<<<EOSQL
SELECT nation_id, name FROM nations WHERE user_id = '{$mysql['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
$nations = array();
if ($sth) {
    while ($rs = mysqli_fetch_array($sth)) {
        $nations[] = $rs;
    }
}
if ($_POST['action'] == "Send Message" && $_SESSION['user_id']) {
	$mysql['message'] = $GLOBALS['mysqli']->real_escape_string($_POST['message']);
	$sql=<<<EOSQL
	SELECT user_id from users WHERE user_id = '{$mysql['user_id']}'
EOSQL;
	if (!onelinequery($sql)) {
		$errors[] = "Something went badly wrong.";
	}
	if ($mysql['user_id'] == $_SESSION['user_id']) {
		$errors[] = "Use a piece of paper or something.";
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
			$sql = "INSERT INTO messages (touser, fromuser, message, sent) VALUES ('{$mysql['user_id']}', {$_SESSION['user_id']}, '{$mysql['message']}', NOW())";
			$GLOBALS['mysqli']->query($sql);
			$display['message'] = "";
			$infos[] = "Message sent.";
		}
	}
}