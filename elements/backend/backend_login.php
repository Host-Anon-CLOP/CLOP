<?php
include_once("allfunctions.php");
$clopmysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_clop");
function ReverseIPOctets($inputip){
    $ipoc = explode(".",$inputip);
    return $ipoc[3].".".$ipoc[2].".".$ipoc[1].".".$ipoc[0];
}
    $mysql['remote_addr'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['REMOTE_ADDR']);
    $mysql['forwarded'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_X_FORWARDED']);
    $mysql['forwarded_for'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_X_FORWARDED_FOR']);
    $sql = <<<EOSQL
    SELECT ip, reason FROM banlist WHERE ip = '{$mysql['remote_addr']}' OR ip = '{$mysql['forwarded']}' OR ip = '{$mysql['forwarded_for']}'
EOSQL;
    $sth = $GLOBALS['clopmysqli']->query($sql);
    if ($sth) {
        $rs = mysqli_fetch_array($sth);
        if ($rs['ip']) {
            die ("You're banned. " . $rs['reason']);
        }
    }
if (!empty($_POST['username'])) {
    foreach ($_POST as $key => $value) {
        $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
    }
    $sql = "SELECT COUNT(*) AS count FROM logins WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND failed = true AND logindate > NOW() - 2 HOURS";
    $rs = onelinequery($sql);
    if ($rs['count'] > 20) {
        $errors[] = "Go bruteforce somewhere else.";
    }
if (gethostbyname(ReverseIPOctets($_SERVER['REMOTE_ADDR']).".".$_SERVER['SERVER_PORT'].".".ReverseIPOctets($_SERVER['SERVER_ADDR']).".ip-port.exitlist.torproject.org")=="127.0.0.2") {
    $errors[] = "No. No TOR.";
}
if (!$errors) {
$passwordhash = sha1($mysql['password'] . "saltlick");
if ($_POST['login']) {
$sql = "SELECT user_id, stasisdate, stasismode, css, hidebanners, hidereports FROM users WHERE username = '{$mysql['username']}' AND password = '{$passwordhash}'";
$rs = onelinequery($sql);
    if ($rs) {
        if ((strtotime($rs['stasisdate']) > (time() - 86400)) && $rs['stasismode']) {
        $errors[] = "You have entered stasis less than 24 hours ago.";
        } else {
        $sql =<<<EOSQL
        INSERT INTO logins(user_id, ip, forwarded, forwarded_for, logindate, failed)
        VALUES ({$rs['user_id']}, '{$mysql['remote_addr']}', '{$mysql['forwarded']}', '{$mysql['forwarded_for']}', NOW(), false)
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $_SESSION['css'] = $rs['css'];
            $_SESSION['hidebanners'] = $rs['hidebanners'];
            $_SESSION['hidereports'] = $rs['hidereports'];
            $_SESSION['user_id'] = $rs['user_id'];
            header("Location: overview.php");
        }
    } else {
        $sql =<<<EOSQL
        INSERT INTO logins(user_id, ip, forwarded, forwarded_for, logindate, failed)
        VALUES(0, '{$mysql['remote_addr']}', '{$mysql['forwarded']}', '{$mysql['forwarded_for']}', NOW(), true)
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $errors[] = "Login incorrect.";
    }
} else if ($_POST['cloplogin']) {
    $sql = <<<EOSQL
    SELECT user_id FROM users WHERE username = '{$mysql['username']}'
EOSQL;
    $exists = onelinequery($sql);
    if ($exists['user_id']) {
        $errors[] = "You have already migrated yourself to The Compounds of Harmony.";
    } else {
    $passwordhash = sha1($mysql['password'] . "saltlick");
    $sql = "SELECT user_id, username, css, hidebanners, hidereports, donator FROM users WHERE username = '{$mysql['username']}' AND password = '{$passwordhash}'";
    $rs = mysqli_fetch_array($GLOBALS['clopmysqli']->query($sql));
    if ($rs) {
        $passwordhash = sha1($mysql['password'] . "saltlick");
        $sql =<<<EOSQL
        INSERT INTO users (username, password, email, creation_ip, css, hidebanners, hidereports, donator)
        VALUES ('{$rs['username']}', '{$passwordhash}', '{$rs['email']}', '{$_SERVER['REMOTE_ADDR']}',
        {$rs['css']}, {$rs['hidebanners']}, {$rs['hidereports']}, {$rs['donator']})
EOSQL;
        $GLOBALS['mysqli']->query($sql);
		$_SESSION['css'] = $rs['css'];
        $sql = "SELECT user_id FROM users WHERE username = '{$mysql['username']}'";
        $rs2 = onelinequery($sql);
        $_SESSION['user_id'] = $rs2['user_id'];
        $sql = "INSERT INTO logins(user_id, ip, logindate, failed) VALUES ({$rs2['user_id']}, '{$_SERVER['REMOTE_ADDR']}', NOW(), false)";
        $GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
        SELECT COUNT(name) AS count FROM ascendednations WHERE user_id = '{$rs['user_id']}'
EOSQL;
        $ascendedcount = mysqli_fetch_array($GLOBALS['clopmysqli']->query($sql));
        if ($ascendedcount['count']) {
            $sql=<<<EOSQL
            UPDATE users SET ascended = 1 WHERE user_id = '{$rs2['user_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $sql =<<<EOSQL
            INSERT INTO news (message, posted)
            VALUES ('The ascended &gt;CLOP user {$rs['username']} has joined the game.', NOW())
EOSQL;
        } else {
            $sql =<<<EOSQL
            INSERT INTO news (message, posted)
            VALUES ('The &gt;CLOP user {$rs['username']} has joined the game.', NOW())
EOSQL;
        }
        $GLOBALS['mysqli']->query($sql);
        header("Location: overview.php");
    } else {
        $sql =<<<EOSQL
        INSERT INTO logins(user_id, ip, forwarded, forwarded_for, logindate, failed)
        VALUES(0, '{$mysql['remote_addr']}', '{$mysql['forwarded']}', '{$mysql['forwarded_for']}', NOW(), true)
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $errors[] = "&gt;CLOP user not found.";
    }
    }
}
}
}