<?php
include("allfunctions.php");
needsalliance();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
    $display[$key] = htmlentities($value);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE name = 'fealtyrequired'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$displayeditdescription = htmlentities($allianceinfo['description'], ENT_SUBSTITUTE, "UTF-8");
if ($_POST && (($_POST['token_alliancemessages'] == "") || ($_POST['token_alliancemessages'] != $_SESSION['token_alliancemessages']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_alliancemessages'] == "")) {
    $_SESSION['token_alliancemessages'] = sha1(rand() . $_SESSION['token_alliancemessages']);
}
if ($_SESSION['user_id'] == $allianceinfo['owner_id']) {
    $owner = true;
}
if (hasability("alliancemessaging", $_SESSION['user_id'])) $messagingpowers = true;
if (!$errors) {
if ($owner || $messagingpowers) {
	if ($_POST['updatedescription']) {
		$sql=<<<EOSQL
UPDATE alliances SET description = '{$mysql['alliancedescription']}' WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$displayeditdescription = htmlentities($_POST['alliancedescription'], ENT_SUBSTITUTE, "UTF-8");
	}
	if ($_POST['bulkdelete']) {
		if (!ctype_digit($_POST['deletedays']) || $_POST['deletedays'] === "") {
			$errors[] = "Enter a number of days.";
		}
	if (!$errors) {
		$mysql['deletedays'] = (int)$_POST['deletedays'];
		$sql=<<<EOSQL
		DELETE FROM alliance_messages WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND posted < DATE_SUB(NOW(), INTERVAL {$mysql['deletedays']} DAY)
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		}
	}
}
if ($_POST['sendmessage']) {
	if (!hasamount(3, $_SESSION['user_id'], $constants['fealtyrequired'])) {
		$errors[] = "You do not have the Fealty to send an alliance message.";
	}
	if (!$errors) {
	addamount(3, $_SESSION['user_id'], $constants['fealtyrequired'] * -1);
    $sql=<<<EOSQL
    INSERT INTO alliance_messages SET alliance_id = '{$allianceinfo['alliance_id']}', user_id = '{$_SESSION['user_id']}', posted = NOW(), message = '{$mysql['message']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
	}
}
if ($_POST['deletemessage']) {
    if ($owner || $messagingpowers) {
        $sql=<<<EOSQL
        SELECT message_id FROM alliance_messages WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND message_id = '{$mysql['message_id']}'
EOSQL;
    } else {
        $sql=<<<EOSQL
        SELECT message_id FROM alliance_messages WHERE user_id = '{$_SESSION['user_id']}' AND alliance_id = '{$allianceinfo['alliance_id']}' AND message_id = '{$mysql['message_id']}'
EOSQL;
    }
    $rs = onelinequery($sql);
    if ($rs['message_id']) {
        $sql=<<<EOSQL
        DELETE FROM alliance_messages WHERE message_id = '{$mysql['message_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
        DELETE FROM markasread WHERE message_id = '{$mysql['message_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    } else {
        $errors[] = "You can't delete that message.";
    }
}
if ($_POST['markall']) {
    $sql=<<<EOSQL
	SELECT message_id FROM alliance_messages WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
    $sql=<<<EOSQL
	INSERT INTO markasread SET message_id = '{$rs['message_id']}', user_id = '{$_SESSION['user_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    }
    $infos[] = "All messages marked as read.";
}
if ($_POST['markasread']) {
	$sql=<<<EOSQL
	SELECT message_id FROM alliance_messages WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND message_id = '{$mysql['message_id']}'
EOSQL;
	$rs = onelinequery($sql);
	if (!$rs['message_id']) {
		$errors[] = "What?";
	}
	if (!$errors) {
		$sql=<<<EOSQL
		INSERT INTO markasread SET message_id = '{$mysql['message_id']}', user_id = '{$_SESSION['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	}
}
if ($_POST['markasunread']) {
	$sql=<<<EOSQL
	SELECT message_id FROM alliance_messages WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND message_id = '{$mysql['message_id']}'
EOSQL;
	$rs = onelinequery($sql);
	if (!$rs['message_id']) {
		$errors[] = "What?";
	}
	if (!$errors) {
		$sql=<<<EOSQL
		DELETE FROM markasread WHERE message_id = '{$mysql['message_id']}' AND user_id = '{$_SESSION['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	}
}
}
$sql=<<<EOSQL
SELECT u.username, u.user_id, am.message, am.posted, am.message_id, mr.user_id AS isread
FROM alliance_messages am
LEFT JOIN users u ON am.user_id = u.user_id
LEFT JOIN markasread mr ON mr.message_id = am.message_id AND mr.user_id = '{$_SESSION['user_id']}'
WHERE am.alliance_id = '{$allianceinfo['alliance_id']}'
ORDER BY am.posted DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
    while ($rs = mysqli_fetch_array($sth)) {
        if (($rs['user_id']) == 0) {
			$rs['username'] = "<b>*Automated System Message*</b>";
        }
        $rs['displaymessage'] = nl2br(htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8"));
        $messages[] = $rs;
    }
}
?>