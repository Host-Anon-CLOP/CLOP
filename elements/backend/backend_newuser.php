<?php
include_once("allfunctions.php");
$clopmysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_clop");
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
$sth = $GLOBALS['clopmysqli']->query($sql);
if ($sth) {
	$rs = mysqli_fetch_array($sth);
	if ($rs['ip']) {
		die ("You're banned. " . $rs['reason']);
	}
}
$sql = <<<EOSQL
SELECT ip FROM creation_banlist WHERE ip = '{$_SERVER['REMOTE_ADDR']}'
EOSQL;
$sth = $GLOBALS['clopmysqli']->query($sql);
if ($sth) {
	$rs = mysqli_fetch_array($sth);
	if ($rs['ip']) {
		$errors[] = "This IP belongs to a public system, such as a school or a library. You can play from here, but to prevent easy multi abuse, new users have to make accounts at home.";
	}
}
if ($_POST && (($_POST["token_newuser"] == "") || ($_POST["token_newuser"] != $_SESSION["token_newuser"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_newuser"] == "")) {
    $_SESSION["token_newuser"] = sha1(rand() . $_SESSION["token_newuser"]);
}
if (!$errors) {
    if ($_POST['allnew']) {
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
		$sql = "SELECT COUNT(*) AS count FROM users WHERE username = '{$mysql['realusername']}'";
		$rs = onelinequery($sql);
		if ($rs['count'] > 0) {
			$errors[] = "Username already taken.";
		}
		$sql = "SELECT COUNT(*) AS count FROM users WHERE username = '{$mysql['realusername']}'";
		$rs = mysqli_fetch_array($GLOBALS['clopmysqli']->query($sql));
		if ($rs['count'] > 0) {
			$errors[] = "Username already exists in the first game.";
		}
		if (!$errors) {
			$passwordhash = sha1($mysql['password'] . "saltlick");
			$sql = "INSERT INTO users (username, password, description, email, creation_ip)
            VALUES ('{$mysql['realusername']}', '{$passwordhash}', '{$mysql['userdescription']}', '{$mysql['asdf']}', '{$_SERVER['REMOTE_ADDR']}')";
			$GLOBALS['mysqli']->query($sql);
			$sql = "SELECT user_id FROM users WHERE username = '{$mysql['realusername']}'";
			$rs = onelinequery($sql);
			$_SESSION['user_id'] = $rs['user_id'];
			$sql = "INSERT INTO logins(user_id, ip, logindate, failed) VALUES ({$rs['user_id']}, '{$_SERVER['REMOTE_ADDR']}', NOW(), false)";
			$GLOBALS['mysqli']->query($sql);
			$joinmessage =<<<EOFORM
Welcome to Compounds! To get started, message alliance leaders or make a post on the board asking to join. There are plenty of alliances waiting for new players; all you have to do is ask. Don't be shy!
EOFORM;
			$mysqlmessage = $GLOBALS['mysqli']->real_escape_string($joinmessage);
			$sql=<<<EOSQL
			INSERT INTO messages (message, sent, fromuser, touser, fromdeleted)
			VALUES ('{$mysqlmessage}', NOW(), 0, {$rs['user_id']}, 1)
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql =<<<EOSQL
			INSERT INTO news (message, posted)
			VALUES ('The user {$mysql['realusername']} has joined the game.', NOW())
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			header("Location: overview.php");
		}
    }
}
?>