<?php
include_once("allfunctions.php");
$elementsmysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_elements");
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
    $sql = "SELECT COUNT(*) AS count FROM users WHERE username = '{$mysql['realusername']}'";
	$rs = mysqli_fetch_array($GLOBALS['elementsmysqli']->query($sql));
	if ($rs['count'] > 0) {
		$errors[] = "Username already exists in the sequel.";
	}
    if (empty($errors)) {
        $passwordhash = sha1($mysql['password'] . "saltlick"); //I'm fully aware that this is shit, thanks
        $mysql['remote_addr2'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['REMOTE_ADDR']);        
        $mysql['forwarded'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_X_FORWARDED']);
        $mysql['forwarded_for'] = $GLOBALS['mysqli']->real_escape_string($_SERVER['HTTP_X_FORWARDED_FOR']);
        $sql = "INSERT INTO users (username, password, email, creation_ip, creation_ip2, creation_forwarded_ip, creation_forwarded_for_ip) VALUES ('{$mysql['realusername']}', '{$passwordhash}', '{$mysql['asdf']}', '{$_SERVER['REMOTE_ADDR']}', '{$mysql['remote_addr2']}', '{$mysql['forwarded']}', '{$mysql['forwarded_for']}')";
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
        $sql = "INSERT INTO logins(user_id, ip, logindate, failed) VALUES ({$rs['user_id']}, '{$_SERVER['REMOTE_ADDR']}', NOW(), false)";
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

        # MASSIVE RESOURCES FOR TEST SERVER USERS
        if(strpos($_ENV["DOMAIN_URL"], "test.4clop") !== false) {
            $sql = <<<EOSQL
            INSERT INTO resources (nation_id, resource_id, amount)
            VALUES 
            ('{$_SESSION['nation_id']}', '1', '1000000'),
            ('{$_SESSION['nation_id']}', '2', '1000000'),
            ('{$_SESSION['nation_id']}', '3', '1000000'),
            ('{$_SESSION['nation_id']}', '4', '1000000'),
            ('{$_SESSION['nation_id']}', '9', '1000000'),
            ('{$_SESSION['nation_id']}', '10', '1000000'),
            ('{$_SESSION['nation_id']}', '13', '1000000'),
            ('{$_SESSION['nation_id']}', '18', '1000000'),
            ('{$_SESSION['nation_id']}', '20', '1000000'),
            ('{$_SESSION['nation_id']}', '25', '1000000'),
            ('{$_SESSION['nation_id']}', '26', '1000000'),
            ('{$_SESSION['nation_id']}', '27', '1000000'),
            ('{$_SESSION['nation_id']}', '28', '1000000'),
            ('{$_SESSION['nation_id']}', '29', '1000000'),
            ('{$_SESSION['nation_id']}', '30', '1000000'),
            ('{$_SESSION['nation_id']}', '42', '1000000'),
            ('{$_SESSION['nation_id']}', '47', '1000000'),
            ('{$_SESSION['nation_id']}', '62', '1000000'),
            ('{$_SESSION['nation_id']}', '63', '1000000'),
            ('{$_SESSION['nation_id']}', '64', '1000000'),
            ('{$_SESSION['nation_id']}', '65', '1000000'),
            ('{$_SESSION['nation_id']}', '66', '1000000'),
            ('{$_SESSION['nation_id']}', '67', '1000000'),
            ('{$_SESSION['nation_id']}', '68', '1000000'),
            ('{$_SESSION['nation_id']}', '69', '1000000'),
            ('{$_SESSION['nation_id']}', '70', '1000000'),
            ('{$_SESSION['nation_id']}', '71', '1000000'),
            ('{$_SESSION['nation_id']}', '72', '1000000'),
            ('{$_SESSION['nation_id']}', '73', '1000000'),
            ('{$_SESSION['nation_id']}', '75', '1000000'),
            ('{$_SESSION['nation_id']}', '77', '1000000')
EOSQL;
        $GLOBALS['mysqli']->query($sql);

        $sql = <<<EOSQL
        UPDATE nations SET age = 30 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql = <<<EOSQL
        UPDATE nations SET funds = 100000000000000 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        }

        header("Location: overview.php");
    }
}
}
?>