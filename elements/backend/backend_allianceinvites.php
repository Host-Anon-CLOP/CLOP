<?php
include_once("allfunctions.php");
needsuser();
$clopmysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_clop");
if ($_POST && (($_POST["token_allianceinvites"] == "") || ($_POST["token_allianceinvites"] != $_SESSION["token_allianceinvites"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_allianceinvites"] == "")) {
    $_SESSION["token_allianceinvites"] = sha1(rand() . $_SESSION["token_allianceinvites"]);
}
if ($_POST['acceptinvitation']) {
	$mysql['alliance_id'] = (int)$_POST['alliance_id'];
	$sql=<<<EOSQL
	SELECT alliance_id FROM allianceinvitations
	WHERE alliance_id = '{$mysql['alliance_id']}' AND user_id = '{$_SESSION['user_id']}'
EOSQL;
	$thisalliance = onelinequery($sql);
	if (!$thisalliance['alliance_id']) {
		$errors[] = "That invitation doesn't exist.";
	}
	if ($userinfo['alliance_id']) {
		$sql=<<<EOSQL
SELECT owner_id FROM alliances WHERE alliance_id = {$userinfo['alliance_id']}
EOSQL;
		$rs = onelinequery($sql);
		if ($rs['owner_id'] == $_SESSION['user_id']) {
			$errors[] = "You are the owner of your alliance, so you cannot be invited away from it.";
		}
	}
	if (!$errors) {
	if (!$userinfo['alliance_id']) {
		$production = 3;
		$tier = 1;
		$sql=<<<EOSQL
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
        $sql=<<<EOSQL
        UPDATE users SET alliance_id = '{$mysql['alliance_id']}', production = '{$production}', tier = '{$tier}' WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
	} else {
        $sql=<<<EOSQL
        UPDATE users SET alliance_id = '{$mysql['alliance_id']}' WHERE user_id = '{$userinfo['user_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    }
	$sql=<<<EOSQL
	DELETE FROM allianceinvitations WHERE user_id = '{$_SESSION['user_id']}' AND alliance_id = '{$mysql['alliance_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	header("Location: overview.php");
	exit;
	}
} else if ($_POST['refuseinvitation']) {
	$mysql['alliance_id'] = (int)$_POST['alliance_id'];
    $sql=<<<EOSQL
	DELETE FROM allianceinvitations WHERE user_id = '{$_SESSION['user_id']}' AND alliance_id = '{$mysql['alliance_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
	$infos[] = "Invitation refused.";
}
$sql=<<<EOSQL
SELECT a.name, a.alliance_id FROM alliances a
INNER JOIN allianceinvitations ai ON a.alliance_id = ai.alliance_id
WHERE ai.user_id = '{$_SESSION['user_id']}'
ORDER BY ai.alliance_id DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $invitations[$rs['alliance_id']] = $rs['name'];
}
?>