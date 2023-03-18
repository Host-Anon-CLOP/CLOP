<?php
include_once("allfunctions.php");
needsuser();
$blockingyou = array();
$youblocking = array();
if ($_POST && (($_POST['token_blocklist'] == "") || ($_POST['token_blocklist'] != $_SESSION['token_blocklist']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_blocklist'] == "")) {
    $_SESSION['token_blocklist'] = sha1(rand() . $_SESSION['token_blocklist']);
}
if (!$errors) {
if ($_POST['action'] == "Block") {
    $mysql['block'] = $GLOBALS['mysqli']->real_escape_string($_POST['block']);
    $sql = "SELECT user_id FROM users WHERE username = '{$mysql['block']}'";
    $rs = onelinequery($sql);
    if ($rs) {
        $blockid = $rs['user_id'];
    } else {
        $sql = "SELECT u.user_id FROM users u INNER JOIN nations n ON u.user_id = n.user_id WHERE n.name = '{$mysql['block']}'";
        $rs = onelinequery($sql);
        if ($rs) {
            $blockid = $rs['user_id'];
        }
    }
    if ($blockid == $_SESSION['user_id']) {
        $errors[] = "Your senior advisors formally request that you put down the crack pipe.";
    } else if ($blockid) {
        $sql = "INSERT INTO blocklist (blockee, blocker) VALUES ({$blockid}, {$_SESSION['user_id']})";
        $GLOBALS['mysqli']->query($sql);
    } else {
        $errors[] = "Name not found.";
    }
}
if ($_POST['action'] == "Unblock") {
    $mysql['unblock'] = $GLOBALS['mysqli']->real_escape_string($_POST['unblock']);
    $sql = "DELETE FROM blocklist WHERE blockee = '{$mysql['unblock']}' AND blocker = '{$_SESSION['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
}
}
$sql = "SELECT u.user_id, u.username FROM blocklist e INNER JOIN users u ON e.blockee = u.user_id WHERE e.blocker = '{$_SESSION['user_id']}'";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$youblocking[$rs['user_id']] = $rs['username'];
}
$sql = "SELECT u.user_id, u.username FROM blocklist e INNER JOIN users u ON e.blocker = u.user_id WHERE e.blockee = '{$_SESSION['user_id']}'";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$blockingyou[$rs['user_id']] = $rs['username'];
}
?>