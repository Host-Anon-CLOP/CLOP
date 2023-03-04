<?php
include_once("allfunctions.php");
needsuser();
$clopmysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_clop");
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
    $display[$key] = htmlentities($value);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE name = 'nobilityfornew'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
if ($_POST['makealliance']) {
	if (!$userinfo['ascended']) {
		$errors[] = "No.";
	} else {
		if ($_POST['alliancename'] != preg_replace('/[^0-9a-zA-Z_\s]/' ,"", $_POST['alliancename'])) {
			$errors[] = "Only English letters and numbers for the alliance name.";
		}
		if (!$_POST['alliancename']) {
			$errors[] = "No name entered.";
		}
		$sql = "SELECT COUNT(*) AS count FROM alliances WHERE name = '{$mysql['alliancename']}'";
		$rs = onelinequery($sql);
		if ($rs['count'] > 0) {
			$errors[] = "Alliance name already taken.";
		}
		if ($userinfo['alliance_id']) {
			$sql=<<<EOSQL
			SELECT owner_id FROM alliances WHERE alliance_id = {$userinfo['alliance_id']}
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['owner_id'] == $_SESSION['user_id']) {
				$errors[] = "Give your alliance to someone else before making a new one.";
			}
			if (!hasamount(35, $_SESSION['user_id'], $constants['nobilityfornew'])) {
				$errors[] = "You don't have the Nobility to make a new alliance.";
			}
		}
		if (!$errors) {
		if (!$userinfo['alliance_id']) {
		$production = 3;
		$tier = 1;
        /*$sql=<<<EOSQL
		SELECT COUNT(name) AS count FROM ascendednations an
		INNER JOIN users u ON u.user_id = an.user_id
		WHERE u.username = '{$userinfo['username']}'
EOSQL;
		$ascendednations = mysqli_fetch_array($GLOBALS['clopmysqli']->query($sql));
		if ($ascendednations['count']) {
			$nationamount = $ascendednations['count'] * 3;
			$production += $nationamount;
			$nationmessage = $GLOBALS['mysqli']->real_escape_string("Because you ascended from >CLOP with {$ascendednations['count']} nations, you got {$nationamount} extra production.");
			$sql = "INSERT INTO messages (fromuser, touser, message, fromdeleted, sent) VALUES(0, {$userinfo['user_id']}, '{$nationmessage}', 1, NOW())";
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
			SELECT amount FROM ascendedresources ar
			INNER JOIN users u ON u.user_id = ar.user_id
			WHERE u.username = '{$userinfo['username']}'
			AND resource_id = '38'
EOSQL;
			$ascendedstatues = mysqli_fetch_array($GLOBALS['clopmysqli']->query($sql));
			if ($ascendedstatues['amount'] >= 5) {
				$statueamount = ($ascendedstatues['amount'] / 5);
				$production += $statueamount;
				$statuemessage = $GLOBALS['mysqli']->real_escape_string("Because you ascended from >CLOP with {$ascendedstatues['amount']} statues, you got {$statueamount} extra production.");
				$sql = "INSERT INTO messages (fromuser, touser, message, fromdeleted, sent) VALUES(0, {$userinfo['user_id']}, '{$statuemessage}', 1, NOW())";
				$GLOBALS['mysqli']->query($sql);
			}
		}
		if ($production > 5) $tier++;
		if ($production > 20) $tier++;
		if ($production > 40) $tier++;
		if ($production > 70) {
			$production = 70;
            $capmessage = $GLOBALS['mysqli']->real_escape_string("Your additional production has been capped at 70.");
			$sql = "INSERT INTO messages (fromuser, touser, message, fromdeleted, sent) VALUES(0, {$userinfo['user_id']}, '{$capmessage}', 1, NOW())";
			$GLOBALS['mysqli']->query($sql);
        }
        */
		$sql=<<<EOSQL
		UPDATE users SET production = '{$production}', tier = '{$tier}' WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		} else {
			addamount(35, $_SESSION['user_id'], $constants['nobilityfornew'] * -1);
		}
		$sql=<<<EOSQL
		INSERT INTO alliances (name, description, owner_id) VALUES ('{$mysql['alliancename']}', '{$mysql['alliancedescription']}', {$_SESSION['user_id']});
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql = "SELECT alliance_id FROM alliances WHERE name = '{$mysql['alliancename']}'";
		$rs = onelinequery($sql);
		$sql=<<<EOSQL
		UPDATE users SET alliance_id = '{$rs['alliance_id']}' WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		header("Location: overview.php");
		}
    }
}
?>