<?php
include_once("allfunctions.php");
needsnation();
$friendingyou = array();
$youfriending = array();
if ($_POST && (($_POST['token_friends'] == "") || ($_POST['token_friends'] != $_SESSION['token_friends']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_friends'] == "")) {
    $_SESSION['token_friends'] = sha1(rand() . $_SESSION['token_friends']);
}
if (!$errors) {
if ($_POST['action'] == "Friend") {
    $mysql['Friend'] = $GLOBALS['mysqli']->real_escape_string($_POST['Friend']);
    $sql = "SELECT user_id FROM users WHERE username = '{$mysql['Friend']}'";
    $rs = onelinequery($sql);
    if ($rs) {
        $Friendid = $rs['user_id'];
    } else {
        $sql = "SELECT u.user_id FROM users u INNER JOIN nations n ON u.user_id = n.user_id WHERE n.name = '{$mysql['Friend']}'";
        $rs = onelinequery($sql);
        if ($rs) {
            $Friendid = $rs['user_id'];
        }
    }
    if ($Friendid == $_SESSION['user_id']) {
        $errors[] = "Your senior advisors formally request that you put down the crack pipe.";
    } else if ($Friendid) {
        $sql = "INSERT INTO friends (friendee, friender) VALUES ({$Friendid}, {$_SESSION['user_id']})";
        $GLOBALS['mysqli']->query($sql);
    } else {
        $errors[] = "Name not found.";
    }
}
if ($_POST['action'] == "unfriend") {
    $mysql['unfriend'] = $GLOBALS['mysqli']->real_escape_string($_POST['unfriend']);
    $sql = "DELETE FROM friends WHERE friendee = '{$mysql['unfriend']}' AND friender = '{$_SESSION['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
}
}
$sql = "SELECT u.user_id, u.username FROM friends e INNER JOIN users u ON e.friendee = u.user_id WHERE e.friender = '{$_SESSION['user_id']}'";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$youfriending[$rs['user_id']] = $rs['username'];
}
$sql = "SELECT u.user_id, u.username FROM friends e INNER JOIN users u ON e.friender = u.user_id WHERE e.friendee = '{$_SESSION['user_id']}'";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$friendingyou[$rs['user_id']] = $rs['username'];
}
?>