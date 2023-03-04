<?php
include_once("allfunctions.php");
needsalliance();
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'void'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
if ($_POST && (($_POST["token_voidactions"] == "") || ($_POST["token_voidactions"] != $_SESSION["token_voidactions"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_voidactions"] == "")) {
    $_SESSION["token_voidactions"] = sha1(rand() . $_SESSION["token_voidactions"]);
}
if ($_POST && !$errors) {
$mysql['username'] = $GLOBALS['mysqli']->real_escape_string($_POST['username']);
$sql=<<<EOSQL
SELECT * FROM users WHERE username = '{$mysql['username']}'
EOSQL;
$targetuser = onelinequery($sql);
if (!$targetuser['user_id']) {
	$errors[] = "Your target is already of the void...";
} else if ($targetuser['user_id'] == $_SESSION['user_id']) {
	$errors[] = "Get help, while you still can...";
} else if (!$targetuser['alliance_id']) {
	$errors[] = "You cannot; that user is not truly here...";
} else if ($targetuser['stasismode']) {
	$errors[] = "That user has paused existence...";
} else if ($targetuser['user_id'] < 5 && $_SESSION['user_id'] >= 5) {
	$errors[] = "That would not be wise...";
}
if (!$errors) {
	$message = "";
    if ($_POST['destroy']) {
		$mysql['resource_id'] = (int)$_POST['resource_id'];
        if (!hasamount(0, $_SESSION['user_id'], $constants['voidfordestruction'])) {
            $errors[] = "You aren't in possession of enough Void to destroy anyone else...";
        }
        if ($mysql['resource_id'] < 1) {
			$errors[] = "You cannot destroy the Void...";
		}
		if ($mysql['resource_id'] > 63) {
			$errors[] = "...";
		}
        if (!$errors) {
            addamount(0, $_SESSION['user_id'], $constants['voidfordestruction'] * -1);
            $sql=<<<EOSQL
			DELETE FROM resources
			WHERE user_id = {$targetuser['user_id']}
			AND resource_id = {$mysql['resource_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
			DELETE FROM bankedresources
			WHERE user_id = {$targetuser['user_id']}
			AND resource_id = {$mysql['resource_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			addamount(0, $targetuser['user_id'], $constants['voidfordestruction']);
			$sql=<<<EOSQL
			SELECT name FROM resourcedefs
			WHERE resource_id = {$mysql['resource_id']}
EOSQL;
			$rs = onelinequery($sql);
			$message = "All of your {$rs['name']} has been destroyed by {$userinfo['username']}!";
			$infos[] = "Your target's {$rs['name']} has been destroyed. Was it worth it?";
        }
    } else if ($_POST['depress']) {
        if (!hasamount(0, $_SESSION['user_id'], $constants['voidfordepression'])) {
            $errors[] = "Attacking through depression requires more Void than you have; depressing yourself is free...";
        }
		if (!$errors) {
			addamount(0, $_SESSION['user_id'], $constants['voidfordepression'] * -1);
			$sql=<<<EOSQL
			UPDATE users SET satisfaction = 0
			WHERE user_id = {$targetuser['user_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$message = "Your satisfaction has been depressed to 0 by {$userinfo['username']}!";
			addamount(0, $targetuser['user_id'], $constants['voidfordepression']);
			$infos[] = "Your target has been depressed. Do you feel happy now?";
		}
    } else if ($_POST['pollute']) {
        if (!hasamount(0, $_SESSION['user_id'], $constants['voidforpollution'])) {
            $errors[] = "You haven't the Void to pollute any but yourself...";
        }
		if ($targetuser['production'] == 1) {
            $errors[] = "That user cannot be polluted any further...";
        }
        if (!$errors) {
            addamount(0, $_SESSION['user_id'], $constants['voidforpollution'] * -1);
            $sql=<<<EOSQL
			UPDATE users SET production = production - 1
			WHERE user_id = {$targetuser['user_id']}
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$message = "You have been polluted by {$userinfo['username']} and have lost 1 production!";
			addamount(0, $targetuser['user_id'], $constants['voidforpollution']);
			$infos[] = "Your target has been polluted, while you remain pure.";
        }
    }
	if (!$errors) {
	$mysql['message'] = $GLOBALS['mysqli']->real_escape_string($message);
	$sql=<<<EOSQL
	INSERT INTO reports SET time = NOW(), user_id = {$targetuser['user_id']}, report = '{$mysql['message']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	}
}
}
?>