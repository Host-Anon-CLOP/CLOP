<?php
include_once("allfunctions.php");
function ReverseIPOctets($inputip){
    $ipoc = explode(".",$inputip);
    return $ipoc[3].".".$ipoc[2].".".$ipoc[1].".".$ipoc[0];
}
    # since run on docker this is getting the container ip 
    #$mysql['remote_addr'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['REMOTE_ADDR']);
    $mysql['remote_addr'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_X-Real-IP']);
    
    $mysql['forwarded'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_X_FORWARDED']);
    $mysql['forwarded_for'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_X_FORWARDED_FOR']);
    $sql = <<<EOSQL
    SELECT ip, reason FROM banlist WHERE ip = '{$mysql['remote_addr']}' OR ip = '{$mysql['forwarded']}' OR ip = '{$mysql['forwarded_for']}'
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        $rs = mysqli_fetch_array($sth);
        if ($rs['ip']) {
            die ("You're banned. " . $rs['reason']);
        }
    }
if (!empty($_POST['username'])) {
    if ($_POST['isopass']) {
        $_POST['password'] = mb_convert_encoding($_POST['password'], 'ISO-8859-1', 'UTF-8');
    }
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
    $passwordhash = sha1($mysql['password'] . "saltlick"); //SURE IS SHIT-HA1 IN HERE AIN'T IT
    $sql = "SELECT user_id, alliance_id, stasisdate, stasismode, css, hidebanners, hidereports, alliance_messages_last_checked FROM users WHERE username = '{$mysql['username']}' AND password = '{$passwordhash}'";
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
            $_SESSION['alliance_id'] = $rs['alliance_id'];
            $_SESSION['alliance_messages_last_checked'] = $rs['alliance_messages_last_checked'];
            $sql = "SELECT nation_id, name FROM nations WHERE user_id = '{$rs['user_id']}'"; // replace with multiple handling at some point
            $rs2 = onelinequery($sql);
            $_SESSION['nation_id'] = $rs2['nation_id'];
            $_SESSION['nation_name'] = $rs2['name'];
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
    }
}