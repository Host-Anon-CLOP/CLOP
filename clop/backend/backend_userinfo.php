<?php
include("allfunctions.php");
needsuser();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
if ($_POST && (($_POST['token_userinfo'] == "") || ($_POST['token_userinfo'] != $_SESSION['token_userinfo']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_userinfo'] == "")) {
    $_SESSION['token_userinfo'] = sha1(rand() . $_SESSION['token_userinfo']);
}
$sql =<<<EOSQL
SELECT u.username, u.user_id, u.email, u.flag, u.donator, u.description, u.stasismode, u.hidebanners, u.hideicons, u.hidereports, u.alliance_id, a.name AS alliancename
FROM users u LEFT JOIN alliances a ON u.alliance_id = a.alliance_id WHERE u.user_id = '{$_SESSION['user_id']}'
EOSQL;
$userinfo = onelinequery($sql);
$display['description'] = htmlentities($userinfo['description'], ENT_SUBSTITUTE, "UTF-8");
$display['email'] = htmlentities($userinfo['email'], ENT_SUBSTITUTE, "UTF-8");
$display['flag'] = htmlentities($userinfo['flag'], ENT_SUBSTITUTE, "UTF-8");
if (!$errors) {
if ($_POST['changedescription']) {
    $sql=<<<EOSQL
UPDATE users SET description = '{$mysql['description']}' WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
	$infos[] = "Description changed.";
	$display['description'] = htmlentities($_POST['description'], ENT_SUBSTITUTE, "UTF-8");
}
if ($_POST['action'] == "New Password") {
    $checkpasswordhash = sha1($mysql['currentpassword'] . "saltlick"); // I'm fully aware that this is shit, thanks
    $sql = "SELECT user_id FROM users WHERE user_id = '{$_SESSION['user_id']}' AND password = '{$checkpasswordhash}'";
    $rs = onelinequery($sql);
    if (!$rs) {
        $errors[] = "Incorrect current password.";
    }
    if ($_POST['password'] != $_POST['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($errors)) {
        $passwordhash = sha1($mysql['password'] . "saltlick");
        $sql=<<<EOSQL
UPDATE users SET password = '{$passwordhash}' WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Password changed.";
    }
}
if ($_POST['changeflag']) {
	if (!preg_match('/^(http:\/\/)?([\w\-\.]+)\:?([0-9]*)\/([^ \?&=\#\"\n\r\t<]*?(\.(jpg|jpeg|gif|png)))$/i', $_POST['flag']) && $_POST['flag'] != "") {
		$errors[] = "Heeeeyyy... are you SURE that's an image? (JPGs, GIFs, and PNGs only.)";
	}
    if (empty($errors)) {
        $sql=<<<EOSQL
UPDATE users SET flag = '{$mysql['flag']}' WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Flag changed.";
        $userinfo['flag'] = $_POST['flag'];
		$display['flag'] = htmlentities($userinfo['flag'], ENT_SUBSTITUTE, "UTF-8");
    }
}
if ($_POST['changeemail']) {
    if (empty($errors)) {
        $sql=<<<EOSQL
UPDATE users SET email = '{$mysql['email']}' WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $infos[] = "Email address changed.";
        $userinfo['email'] = $_POST['email'];
		$display['email'] = htmlentities($userinfo['email'], ENT_SUBSTITUTE, "UTF-8");
    }
}
if ($_POST['changecolor']) {
    $mysql['css'] = (int)$_POST['css'];
    if ($mysql['css'] > 2 || $mysql['css'] < 0) {
        $errors[] = "What do you think would even happen, smart guy?";
    }
    if (!$errors) {
    $sql=<<<EOSQL
    UPDATE users SET css = '{$mysql['css']}' WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $_SESSION['css'] = $mysql['css'];
    $userinfo['css'] = $mysql['css'];
    $GLOBALS['mysqli']->query($sql);
    }
}
if ($_POST['enterstasis']) {
    $sql=<<<EOSQL
    SELECT stasisdate FROM users WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $rs = onelinequery($sql);
    if (strtotime($rs['stasisdate']) > (time() - 86400)) {
        $errors[] = "You have left stasis less than 24 hours ago.";
    } else {
		$sql=<<<EOSQL
		SELECT nation_id FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			$sql=<<<EOSQL
			UPDATE forcegroups SET attack_mission = 0, oldmission = 0, departuredate = NULL, destination_id = 0
			WHERE nation_id = {$rs['nation_id']}
			AND departuredate IS NOT NULL
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
			UPDATE forcegroups SET attack_mission = 0, oldmission = 0, destination_id = {$rs['nation_id']}, departuredate = NOW()
			WHERE nation_id = {$rs['nation_id']}
			AND location_id != {$rs['nation_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
        $sql = "SELECT username FROM users WHERE user_id = {$_SESSION['user_id']}";
        $thisuser = onelinequery($sql);
        $message=<<<EOFORM
<a href="viewuser.php?user_id={$_SESSION['user_id']}">{$thisuser['username']}</a> has gone into stasis.
EOFORM;
        $mysqlmessage = $GLOBALS['mysqli']->real_escape_string($message);
        $sql =<<<EOSQL
        INSERT INTO news (message, posted)
        VALUES ('{$mysqlmessage}', NOW())
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
        UPDATE users SET stasisdate = NOW(), stasismode = 1 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        session_destroy();
        session_unset();
        header("Location: index.php");
        exit;
    }
} else if ($_POST['leavestasis']) {
	if (!$errors) {
	$sql=<<<EOSQL
    UPDATE users SET stasisdate = NOW(), stasismode = 0 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$infos[] = "You have left stasis mode.";
	$userinfo['stasismode'] = 0;
	$sql = "SELECT username FROM users WHERE user_id = {$_SESSION['user_id']}";
	$thisuser = onelinequery($sql);
	$message=<<<EOFORM
<a href="viewuser.php?user_id={$_SESSION['user_id']}">{$thisuser['username']}</a> has left stasis.
EOFORM;
	$mysqlmessage = $GLOBALS['mysqli']->real_escape_string($message);
    $sql =<<<EOSQL
    INSERT INTO news (message, posted)
    VALUES ('{$mysqlmessage}', NOW())
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    }
}
if ($_POST['hidebanners']) {
    $sql=<<<EOSQL
    UPDATE users SET hidebanners = 1 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $_SESSION['hidebanners'] = 1;
    $userinfo['hidebanners'] = 1;
} else if ($_POST['showbanners']) {
    $sql=<<<EOSQL
    UPDATE users SET hidebanners = 0 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $_SESSION['hidebanners'] = 0;
    $userinfo['hidebanners'] = 0;
}
if ($_POST['hidereports']) {
    $sql=<<<EOSQL
    UPDATE users SET hidereports = 1 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $_SESSION['hidereports'] = 1;
    $userinfo['hidereports'] = 1;
} else if ($_POST['showreports']) {
    $sql=<<<EOSQL
    UPDATE users SET hidereports = 0 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $_SESSION['hidereports'] = 0;
    $userinfo['hidereports'] = 0;
}
if ($_POST['hideicons']) {
    $sql=<<<EOSQL
    UPDATE users SET hideicons = 1 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $userinfo['hideicons'] = 1;
} else if ($_POST['showicons']) {
    $sql=<<<EOSQL
    UPDATE users SET hideicons = 0 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $userinfo['hideicons'] = 0;
}
}
?>