<?php
include_once("allfunctions.php");
needsuser();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$inbox = array();
$sentbox = array();
$display['message'] = htmlentities($_POST['message'], ENT_SUBSTITUTE, "UTF-8");
if ($_POST && (($_POST['token_messages'] == "") || ($_POST['token_messages'] != $_SESSION['token_messages']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_messages'] == "")) {
    $_SESSION['token_messages'] = sha1(rand() . $_SESSION['token_messages']);
}
if (!$errors){
if ($_POST['action'] == "Send Message") {
    $sql = "SELECT user_id FROM users WHERE username = '{$mysql['sendto']}'";
    $rs = onelinequery($sql);
    if ($rs) {
        $sendtoid = $rs['user_id'];
    } else {
        $sql = "SELECT u.user_id FROM users u INNER JOIN nations n ON u.user_id = n.user_id WHERE n.name = '{$mysql['sendto']}'";
        $rs = onelinequery($sql);
        if ($rs) {
            $sendtoid = $rs['user_id'];
        }
    }
    if ($sendtoid == $_SESSION['user_id']) {
        $errors[] = "Just use a piece of paper or something.";
    } else if (!$mysql['message']) {
        $errors[] = "No message entered.";
    } else if ($sendtoid) {
        $sql=<<<EOSQL
SELECT * FROM blocklist WHERE blocker = '{$sendtoid}' AND blockee = '{$_SESSION['user_id']}'      
EOSQL;
		$rs = onelinequery($sql);
        if ($rs['blocker']) {
			$errors[] = "That user has you blocked from sending messages.";
        } else {
			$sql = "INSERT INTO messages (touser, fromuser, message, sent) VALUES ({$sendtoid}, {$_SESSION['user_id']}, '{$mysql['message']}', NOW())";
			$GLOBALS['mysqli']->query($sql);
			$display['message'] = "";
			$infos[] = "Message sent to {$mysql['sendto']}.";
		}
    } else {
        $errors[] = "Name not found.";
    }
}
if ($_POST['bulkdelete']) {
    if (!ctype_digit($_POST['deletedays']) || $_POST['deletedays'] === "") {
        $errors[] = "Enter a number of days.";
    }
	if ($_POST['bulkdeletebox'] == "inbox") {
        $fromto = "todeleted";
        $tofrom = "fromdeleted";
        $user = "touser";
    } else {
        $fromto = "fromdeleted";
        $tofrom = "todeleted";
        $user = "fromuser";
    }
    if (!$errors) {
        $mysql['deletedays'] = (int)$_POST['deletedays'];
        $sql=<<<EOSQL
SELECT message_id, {$tofrom} FROM messages WHERE {$user} = '{$_SESSION['user_id']}' AND sent < DATE_SUB(NOW(), INTERVAL {$mysql['deletedays']} DAY)
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
            if ($rs[$tofrom]) {
                $sql = "DELETE FROM messages WHERE message_id = '{$rs['message_id']}'";
                $GLOBALS['mysqli']->query($sql);
            } else {
                $sql = "UPDATE messages SET {$fromto} = '1' WHERE message_id = '{$rs['message_id']}'";
                $GLOBALS['mysqli']->query($sql);
            }
        }
    }
}
if ($_POST['action'] == "Delete Message") {
    if ($mysql['messagetype'] == "inbox") {
        $fromto = "todeleted";
        $tofrom = "fromdeleted";
        $user = "touser";
    } else {
        $fromto = "fromdeleted";
        $tofrom = "todeleted";
        $user = "fromuser";
    }
    $sql=<<<EOSQL
SELECT message_id, {$tofrom} FROM messages WHERE {$user} = '{$_SESSION['user_id']}' AND message_id = '{$mysql['message_id']}'
EOSQL;
    $rs = onelinequery($sql);
        if ($rs) {
            if ($rs[$tofrom]) {
                $sql = "DELETE FROM messages WHERE message_id = '{$mysql['message_id']}'";
                $GLOBALS['mysqli']->query($sql);
            } else {
                $sql = "UPDATE messages SET {$fromto} = '1' WHERE message_id = '{$mysql['message_id']}'";
                $GLOBALS['mysqli']->query($sql);
            }
        } else {
            $errors[] = "Something's gone wrong. You're not trying to delete someone else's message, are you?";
        }
}
if ($_POST['markread']) {
    $sql=<<<EOSQL
SELECT message_id FROM messages WHERE touser = '{$_SESSION['user_id']}' AND message_id = '{$mysql['message_id']}' AND is_read = 0
EOSQL;
    $rs = onelinequery($sql);
    if ($rs) {
        $sql = "UPDATE messages SET is_read = '1' WHERE message_id = '{$mysql['message_id']}'";
        $GLOBALS['mysqli']->query($sql);
    } else {
        $errors[] = "Message already marked as read.";
    }
}
if ($_POST['markunread']) {
    $sql=<<<EOSQL
SELECT message_id FROM messages WHERE touser = '{$_SESSION['user_id']}' AND message_id = '{$mysql['message_id']}' AND is_read = 1
EOSQL;
    $rs = onelinequery($sql);
    if ($rs) {
        $sql = "UPDATE messages SET is_read = '0' WHERE message_id = '{$mysql['message_id']}'";
        $GLOBALS['mysqli']->query($sql);
    } else {
        $errors[] = "Message already marked as unread.";
    }
}
}
$sql=<<<EOSQL
SELECT m.*, u.username, u.user_id FROM messages m LEFT JOIN users u ON u.user_id = m.fromuser WHERE m.touser = '{$_SESSION['user_id']}' AND m.todeleted = 0
ORDER BY sent DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if (($rs['fromuser']) == 0) {
        $rs['username'] = "<b>*Automated System Message*</b>";
    }
    $rs['message'] = nl2br(htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8"));
    $inbox[] = $rs;
}
$sql=<<<EOSQL
SELECT m.*, u.username, u.user_id FROM messages m LEFT JOIN users u ON u.user_id = m.touser WHERE m.fromuser = '{$_SESSION['user_id']}' AND m.fromdeleted = 0
ORDER BY sent DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $rs['message'] = nl2br(htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8"));
    $sentbox[] = $rs;
}
?>