<?php
include("allfunctions.php");
$sql =<<<EOSQL
SELECT alliance_id FROM users WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
$useralliance = onelinequery($sql);
foreach ($_POST as $key => $value) {
	$mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
	$display[$key] = htmlentities($value);
}
if (!$mysql['alliance_id']) {
	$mysql['alliance_id'] = (int)$_GET['alliance_id'];
}
if ($_POST && (($_POST['token_viewalliance'] == "") || ($_POST['token_viewalliance'] != $_SESSION['token_viewalliance']))) {
	$errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_viewalliance'] == "")) {
	$_SESSION['token_viewalliance'] = sha1(rand() . $_SESSION['token_viewalliance']);
}
$sql=<<<EOSQL
SELECT a.name, a.alliance_id, a.public_description, u.donator, ar.user_id AS alliancerequested
FROM alliances a
LEFT JOIN users u ON a.owner_id = u.user_id
LEFT JOIN alliance_requests ar ON (ar.alliance_id = a.alliance_id AND ar.user_id = '{$_SESSION['user_id']}')
WHERE a.alliance_id = '{$mysql['alliance_id']}'
EOSQL;
$allianceinfo = onelinequery($sql);
	if ($allianceinfo) {
	if ($_SESSION['user_id']) {
	if ($_POST['requestjoin']) {
		if ($useralliance['alliance_id']) {
			header("Location: myalliance.php");
			exit;
		}
		$infos[] = "You have requested to join {$allianceinfo['name']}.";
		$allianceinfo['alliancerequested'] = $_SESSION['user_id'];
		$sql =<<<EOSQL
	INSERT INTO alliance_requests (alliance_id, user_id) VALUES('{$mysql['alliance_id']}', '{$_SESSION['user_id']}')
EOSQL;
		$GLOBALS['mysqli']->query($sql);
	} else if ($_POST['rescindrequest']) {
		$sql =<<<EOSQL
	DELETE FROM alliance_requests WHERE alliance_id = '{$mysql['alliance_id']}' AND user_id = '{$_SESSION['user_id']}'
EOSQL;
		$allianceinfo['alliancerequested'] = "";
		$GLOBALS['mysqli']->query($sql);
	} else if ($_POST['embargoalliance']) {
		$sql=<<<EOSQL
		SELECT user_id FROM users WHERE alliance_id = '{$mysql['alliance_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			if ($rs['user_id'] != $_SESSION['user_id']) {
			$sql=<<<EOSQL
			INSERT INTO embargoes SET embargoer = '{$_SESSION['user_id']}', embargoee = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			} else {
				$errors[] = "You can embargo your own alliance if you like, but you can't embargo yourself!";
			}
		}
		$infos[] = "Alliance embargoed.";
	} else if ($_POST['unembargoalliance']) {
		$sql=<<<EOSQL
		SELECT user_id FROM users WHERE alliance_id = '{$mysql['alliance_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			$sql=<<<EOSQL
			DELETE FROM embargoes WHERE embargoer = '{$_SESSION['user_id']}' AND embargoee = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
		$infos[] = "Alliance unembargoed.";
	}
	}
    $sql=<<<EOSQL
	SELECT user_id, flag, username FROM users WHERE alliance_id = '{$mysql['alliance_id']}' ORDER BY username
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$alliancemembers[] = $rs;
		$sql=<<<EOSQL
		SELECT nation_id, name FROM nations WHERE user_id = {$rs['user_id']} ORDER BY name
EOSQL;
		$sth2 = $GLOBALS['mysqli']->query($sql);
		while ($rs2 = mysqli_fetch_array($sth2)) {
			$nations[$rs['user_id']][] = $rs2;
		}
	}
    $displaypubdescription = nl2br(htmlentities($allianceinfo['public_description'], ENT_SUBSTITUTE, "UTF-8"));
    
}
?>