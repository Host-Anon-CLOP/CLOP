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
	} else if ($_POST['friendalliance']) {
		$sql=<<<EOSQL
		SELECT user_id FROM users WHERE alliance_id = '{$mysql['alliance_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			if ($rs['user_id'] != $_SESSION['user_id']) {
			$sql=<<<EOSQL
			INSERT INTO friends SET friender = '{$_SESSION['user_id']}', friendee = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			} else {
				$errors[] = "You can friend your own alliance if you like, but you can't friend yourself!";
			}
		}
		$infos[] = "Alliance friended.";
	} else if ($_POST['enemyalliance']) {
		$sql=<<<EOSQL
		SELECT user_id FROM users WHERE alliance_id = '{$mysql['alliance_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			if ($rs['user_id'] != $_SESSION['user_id']) {
			$sql=<<<EOSQL
			INSERT INTO enemies SET enemier = '{$_SESSION['user_id']}', enemiee = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			} else {
				$errors[] = "You can enemy your own alliance if you like, but you can't enemy yourself!";
			}
		}
		$infos[] = "Alliance enemied.";
	}	else if ($_POST['unembargoalliance']) {
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
	}	else if ($_POST['unfriendalliance']) {
		$sql=<<<EOSQL
		SELECT user_id FROM users WHERE alliance_id = '{$mysql['alliance_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			$sql=<<<EOSQL
			DELETE FROM friends WHERE friender = '{$_SESSION['user_id']}' AND friendee = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
		$infos[] = "Alliance unfriended.";
	} else if ($_POST['unenemyalliance']) {
		$sql=<<<EOSQL
		SELECT user_id FROM users WHERE alliance_id = '{$mysql['alliance_id']}'
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		while ($rs = mysqli_fetch_array($sth)) {
			$sql=<<<EOSQL
			DELETE FROM enemies WHERE enemier = '{$_SESSION['user_id']}' AND enemiee = '{$rs['user_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
		$infos[] = "Alliance unenemied.";
	}
	}
    $sql=<<<EOSQL
	SELECT user_id, flag, username FROM users WHERE alliance_id = '{$mysql['alliance_id']}' ORDER BY username
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$alliancemembers[] = $rs;
		$sql=<<<EOSQL
		SELECT nation_id, name, region FROM nations WHERE user_id = {$rs['user_id']} ORDER BY name
EOSQL;
		$sth2 = $GLOBALS['mysqli']->query($sql);
		while ($rs2 = mysqli_fetch_array($sth2)) {
			$nations[$rs['user_id']][] = $rs2;
		}
	}
    $displaypubdescription = nl2br(htmlentities($allianceinfo['public_description'], ENT_SUBSTITUTE, "UTF-8"));
    
# Get HideIcons Details
$sql = "SELECT n.hideicons, from nations WHERE n.nation_id = '{$mysql['nation_id']}'";
$nationinfo = onelinequery($sql);

# Alliance Resources
$allianceaffectedresources = array();
$alliancerequiredresources = array();
$allianceresources = array();

$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS affected
FROM resourceeffects rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
INNER JOIN nations n ON r.nation_id = n.nation_id
INNER JOIN users u ON n.user_id = u.user_id
WHERE u.alliance_id = '{$allianceinfo['alliance_id']}' AND u.stasismode = 0
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $allianceaffectedresources[$rs['name']] = $rs['affected'];
}

$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS required
FROM resourcerequirements rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
INNER JOIN nations n ON r.nation_id = n.nation_id
INNER JOIN users u ON n.user_id = u.user_id
WHERE u.alliance_id = '{$allianceinfo['alliance_id']}' AND u.stasismode = 0
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $alliancerequiredresources[$rs['name']] = $rs['required'];
}

# Add resources used by government type
$sql = "SELECT n.government, count(n.government) AS count
FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.alliance_id = '{$nationinfo['alliance_id']}' AND u.stasismode = 0
GROUP BY n.government";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['government'] == "Democracy") {
		$alliancerequiredresources["Gasoline"] += (20 * $rs['count']);
		$alliancerequiredresources["Vehicle Parts"] += (2 * $rs['count']);
	} else if ($rs['government'] == "Repression") {
		$alliancerequiredresources["Gasoline"] += (10 * $rs['count']);
	} else if ($rs['government'] == "Independence") {
		$alliancerequiredresources["Gasoline"] += (40 * $rs['count']);
		$alliancerequiredresources["Vehicle Parts"] += (4 * $rs['count']);
	} else if ($rs['government'] == "Decentralization") {
		$alliancerequiredresources["Gasoline"] += (50 * $rs['count']);
		$alliancerequiredresources["Vehicle Parts"] += (5 * $rs['count']);
	} else if ($rs['government'] == "Authoritarianism") {
		$alliancerequiredresources["Gasoline"] += (10 * $rs['count']);
		$alliancerequiredresources["Machinery Parts"] += (3 * $rs['count']);
	} else if ($rs['government'] == "Oppression") {
		$alliancerequiredresources["Gasoline"] += (10 * $rs['count']);
		$alliancerequiredresources["Machinery Parts"] += (5 * $rs['count']);
	}
}

# Add resources used by economy type
$sql = "SELECT n.economy, count(n.economy) AS count
FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.alliance_id = '{$nationinfo['alliance_id']}' AND u.stasismode = 0
GROUP BY n.economy";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['economy'] == "Free Market") {
		$alliancerequiredresources["Coffee"] += (6 * $rs['count']);
	} else if ($rs['economy'] == "State Controlled") {
		$alliancerequiredresources["Cider"] += (6 * $rs['count']);
	}
}

}
?>