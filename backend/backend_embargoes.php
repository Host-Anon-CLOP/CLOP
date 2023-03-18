<?php
include_once("allfunctions.php");
needsnation();
$embargoingyou = array();
$youembargoing = array();
if ($_POST && (($_POST['token_embargoes'] == "") || ($_POST['token_embargoes'] != $_SESSION['token_embargoes']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_embargoes'] == "")) {
    $_SESSION['token_embargoes'] = sha1(rand() . $_SESSION['token_embargoes']);
}
if (!$errors) {
if ($_POST['action'] == "Embargo") {
    $mysql['embargo'] = $GLOBALS['mysqli']->real_escape_string($_POST['embargo']);
    $sql = "SELECT user_id FROM users WHERE username = '{$mysql['embargo']}'";
    $rs = onelinequery($sql);
    if ($rs) {
        $embargoid = $rs['user_id'];
    } else {
        $sql = "SELECT u.user_id FROM users u INNER JOIN nations n ON u.user_id = n.user_id WHERE n.name = '{$mysql['embargo']}'";
        $rs = onelinequery($sql);
        if ($rs) {
            $embargoid = $rs['user_id'];
        }
    }
    if ($embargoid == $_SESSION['user_id']) {
        $errors[] = "Your senior advisors formally request that you put down the crack pipe.";
    } else if ($embargoid) {
        $sql = "INSERT INTO embargoes (embargoee, embargoer) VALUES ({$embargoid}, {$_SESSION['user_id']})";
        $GLOBALS['mysqli']->query($sql);
    } else {
        $errors[] = "Name not found.";
    }
}
if ($_POST['action'] == "Unembargo") {
    $mysql['unembargo'] = $GLOBALS['mysqli']->real_escape_string($_POST['unembargo']);
    $sql = "DELETE FROM embargoes WHERE embargoee = '{$mysql['unembargo']}' AND embargoer = '{$_SESSION['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
}
}
$sql = "SELECT u.user_id, u.username FROM embargoes e INNER JOIN users u ON e.embargoee = u.user_id WHERE e.embargoer = '{$_SESSION['user_id']}'";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$youembargoing[$rs['user_id']] = $rs['username'];
}
$sql = "SELECT u.user_id, u.username FROM embargoes e INNER JOIN users u ON e.embargoer = u.user_id WHERE e.embargoee = '{$_SESSION['user_id']}'";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$embargoingyou[$rs['user_id']] = $rs['username'];
}
?>