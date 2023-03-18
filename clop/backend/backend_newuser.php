<?php
include_once("allfunctions.php");
if ($_SESSION['user_id']) {
    $errors[] = "The \"multiple accounts will get you banned\" thing isn't a joke. Don't do it.";
}
function ReverseIPOctets($inputip){
    $ipoc = explode(".",$inputip);
    return $ipoc[3].".".$ipoc[2].".".$ipoc[1].".".$ipoc[0];
}
    $sql = <<<EOSQL
    SELECT ip, reason FROM banlist WHERE ip = '{$_SERVER['REMOTE_ADDR']}'
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        $rs = mysqli_fetch_array($sth);
        if ($rs['ip']) {
            die ("You're banned. " . $rs['reason']);
        }
    }
$sql = <<<EOSQL
    SELECT ip FROM creation_banlist WHERE ip = '{$_SERVER['REMOTE_ADDR']}'
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        $rs = mysqli_fetch_array($sth);
        if ($rs['ip']) {
            $errors[] = "This IP belongs to a public system, such as a school or a library. You can play from here, but to prevent easy multi abuse, new users have to make accounts at home.";
        }
    }
$baseregions = array();
$baseregions[1] = "Saddle Arabia";
$baseregions[2] = "Zebrica";
$baseregions[3] = "Burrozil";
$baseregions[4] = "Przewalskia";
$keys = array_keys($baseregions);
shuffle($keys);
$regions = array();
foreach ($keys as $key) {
$regions[$key] = $baseregions[$key];
}
$basesubregions = array();
$basesubregions[1] = "North";
$basesubregions[2] = "Central";
$basesubregions[3] = "South";
$keys = array_keys($basesubregions);
shuffle($keys);
$subregions = array();
foreach ($keys as $key) {
$subregions[$key] = $basesubregions[$key];
}
if ($_POST && (($_POST['token_newuser'] == "") || ($_POST['token_newuser'] != $_SESSION['token_newuser']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_newuser'] == "")) {
    $_SESSION['token_newuser'] = sha1(rand() . $_SESSION['token_newuser']);
}
if (!$errors) {
if (!empty($_POST)) {
        if (gethostbyname(ReverseIPOctets($_SERVER['REMOTE_ADDR']).".".$_SERVER['SERVER_PORT'].".".ReverseIPOctets($_SERVER['SERVER_ADDR']).".ip-port.exitlist.torproject.org")=="127.0.0.2") {
            $errors[] = "No. No TOR.";
        }
    if ($_POST['realusername'] != preg_replace('/[^0-9a-zA-Z_\ ]/' ,"", $_POST['realusername'])) {
        $errors[] = "Only English letters and numbers for the username.";
    }
    if (preg_match('/p\s*?[o0]\s*?[li]\s*?a\s*?n\s*?d\s*?b\s*?a\s*?[li]\s*?[li]/i', $_POST['realusername']) == 1) {
        //ain't no thing like overkill
        $errors[] = "O ty szczwana bestyjo.";
}
    if ($_POST['nationname'] != preg_replace('/[^0-9a-zA-Z_\ ]/' ,"", $_POST['nationname'])) {
        $errors[] = "Only English letters and numbers for the nation name.";
    }
    foreach ($_POST as $key => $value) {
        $mysql[$key] = trim($GLOBALS['mysqli']->real_escape_string($value));
        $display[$key] = trim(htmlentities($value, ENT_SUBSTITUTE, "UTF-8"));
    }
    if ($_POST['username'] != "") {
        $errors[] = "First field must be blank.";
    }
    if ($mysql['realusername'] == "") {
        $errors[] = "No username entered.";
    }
    if ($mysql['password'] == "") {
        $errors[] = "No password entered.";
    }
    if ($_POST['confirmpassword'] != $_POST['password']) {
        $errors[] = "Passwords do not match.";
    }
    if ($mysql['nationname'] == "") {
        $errors[] = "No nation name entered.";
    }
    if ($_POST['region'] < 1 || $_POST['region'] > 4 || !is_numeric($_POST['region'])) {
        $errors[] = "I'm on to your game, buster!";
    }
    $sql = "SELECT COUNT(*) AS count FROM users WHERE username = '{$mysql['realusername']}'";
    $rs = onelinequery($sql);
    if ($rs['count'] > 0) {
        $errors[] = "Username already taken.";
    }
    $sql = "SELECT COUNT(*) AS count FROM nations WHERE name = '{$mysql['nationname']}'";
    $rs = onelinequery($sql);
    if ($rs['count'] > 0) {
        $errors[] = "Nation name already taken.";
    }
    $sql = "SELECT COUNT(*) AS count FROM users WHERE username = '{$mysql['nationname']}'";
    $rs = onelinequery($sql);
    if ($rs['count'] > 0) {
        $errors[] = "Due to the potential for faggotry, we're not going to let you make your nation name someone else's username.";
    }
    $sql = "SELECT COUNT(*) AS count FROM nations WHERE name = '{$mysql['realusername']}'";
    $rs = onelinequery($sql);
    if ($rs['count'] > 0) {
        $errors[] = "Due to the potential for faggotry, we're not going to let you make your username someone else's nation name.";
    }
    if (empty($errors)) {
        $passwordhash = sha1($mysql['password'] . "saltlick"); //I'm fully aware that this is shit, thanks
        $sql = "INSERT INTO users (username, password, email, creation_ip) VALUES ('{$mysql['realusername']}', '{$passwordhash}', '{$mysql['asdf']}', '{$_SERVER['REMOTE_ADDR']}')";
        $GLOBALS['mysqli']->query($sql);
        $sql = "SELECT user_id FROM users WHERE username ='{$mysql['realusername']}'";
        $rs = onelinequery($sql);
        $_SESSION['user_id'] = $rs['user_id'];
        $sql =<<<EOFORM
        INSERT INTO nations (name, description, user_id, region, subregion, creationdate)
        VALUES ('{$mysql['nationname']}', '{$mysql['nationdescription']}', {$rs['user_id']}, '{$mysql['region']}', '{$mysql['subregion']}', NOW())
EOFORM;
        $GLOBALS['mysqli']->query($sql);
        $sql = "SELECT nation_id FROM nations WHERE user_id = '{$rs['user_id']}'";
        $rs2 = onelinequery($sql);
        $_SESSION['nation_id'] = $rs2['nation_id'];
        $mysql['user_agent'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_USER_AGENT']);
        $sql = "INSERT INTO logins(user_id, ip, logindate, failed, ua) VALUES ({$rs['user_id']}, '{$_SERVER['REMOTE_ADDR']}', NOW(), false, '{$mysql['user_agent']}')";
        $GLOBALS['mysqli']->query($sql);
        $message=<<<EOFORM
The user <a href="viewuser.php?user_id={$rs['user_id']}">{$mysql['realusername']}</a> has joined the game with the nation of <a href="viewnation.php?nation_id={$rs2['nation_id']}">{$mysql['nationname']}</a>.
EOFORM;
        $mysqlmessage = $GLOBALS['mysqli']->real_escape_string($message);
        $sql =<<<EOSQL
        INSERT INTO news (message, posted)
        VALUES ('{$mysqlmessage}', NOW())
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        header("Location: overview.php");
    }
}
}
?>
