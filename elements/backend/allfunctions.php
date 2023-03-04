<?php
$mysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_elements");
date_default_timezone_set("UTC");
/* ini_set("session.cookie_secure", 1); */
session_start();
if (!isset($_SESSION['SERVER_GENERATED_SID'])) {
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['SERVER_GENERATED_SID'] = true;
}
function commas($nm) {
    for ($done=strlen($nm); $done > 3;$done -= 3) {
        $returnNum = ",".substr($nm,$done-3,3).$returnNum;
    }
    return substr($nm,0,$done).$returnNum;
}
function onelinequery($sql) {
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        return mysqli_fetch_array($sth);
    } else {
        return false;
    }
}
if ($_SESSION['user_id']) {
	$sql=<<<EOSQL
	SELECT * FROM users WHERE user_id = {$_SESSION['user_id']} AND (stasismode = 0 OR (stasisdate < DATE_SUB(NOW(), INTERVAL 24 HOUR)) OR stasisdate IS NULL)
EOSQL;
    $userinfo = onelinequery($sql);
    if (!$userinfo) {
		session_destroy();
		session_unset();
		header("Location: index.php");
		exit;
    }
    if ($userinfo['alliance_id']) {
    $sql=<<<EOSQL
    SELECT * FROM alliances
    WHERE alliance_id = '{$userinfo['alliance_id']}'
EOSQL;
    $allianceinfo = onelinequery($sql);
    }
}
function needsuser() {
    if (!$GLOBALS['userinfo']['user_id']) {
		header("Location: index.php");
		exit;
    }
}
function needsalliance() {
    if ($GLOBALS['userinfo']['stasismode']) {
        header("Location: userinfo.php");
        exit;
    }
    if (!$GLOBALS['userinfo']['alliance_id']) {
		header("Location: index.php");
		exit;
    }
}
$sql=<<<EOSQL
SELECT * FROM elementpositions
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$positions[$rs['position']] = $rs['element'];
}
function withinsix($number) {
    if ($number > 6) {
        $number -= 6;
    }
    return $number;
}
function getcomplement($id) {
    $elementpositions = array_flip($positions);
    $newid = 0;
    if ($id & 32) $newid += $positions[withinsix($elementpositions['32'] + 3)];
    if ($id & 16) $newid += $positions[withinsix($elementpositions['16'] + 3)];
    if ($id & 8) $newid += $positions[withinsix($elementpositions['8'] + 3)];
    if ($id & 4) $newid += $positions[withinsix($elementpositions['4'] + 3)];
    if ($id & 2) $newid += $positions[withinsix($elementpositions['2'] + 3)];
    if ($id & 1) $newid += $positions[withinsix($elementpositions['1'] + 3)];
    return $newid;
}
function getelementname($id) {
	$sql=<<<EOSQL
	SELECT name FROM resourcedefs
	WHERE resource_id = '{$id}'
EOSQL;
	$rs = onelinequery($sql);
	return $rs['name'];
}
function getelements($id) {
$elementarray = array();
if ($id & 32) $elementarray[32] = "Generosity";
if ($id & 16) $elementarray[16] = "Honesty";
if ($id & 8) $elementarray[8] = "Kindness";
if ($id & 4) $elementarray[4] = "Laughter";
if ($id & 2) $elementarray[2] = "Loyalty";
if ($id & 1) $elementarray[1] = "Magic";
return $elementarray;
}
function elementimages($id) {
	$return = "";
	if ($id & 1) {
		$return .=<<<EOFORM
<img src="/images/magic.png"/>
EOFORM;
	}
	if ($id & 2) {
		$return .=<<<EOFORM
<img src="/images/loyalty.png"/>
EOFORM;
	}
	if ($id & 4) {
		$return .=<<<EOFORM
<img src="/images/laughter.png"/>
EOFORM;
	}
	if ($id & 8) {
		$return .=<<<EOFORM
<img src="/images/kindness.png"/>
EOFORM;
	}
	if ($id & 16) {
		$return .=<<<EOFORM
<img src="/images/honesty.png"/>
EOFORM;
	}
	if ($id & 32) {
		$return .=<<<EOFORM
<img src="/images/generosity.png"/>
EOFORM;
	}
	return $return;
}
function shareselement($id1, $id2) {
	if ($id1 & 1 && $id2 & 1) return true;
	if ($id1 & 2 && $id2 & 2) return true;
	if ($id1 & 4 && $id2 & 4) return true;
	if ($id1 & 8 && $id2 & 8) return true;
	if ($id1 & 16 && $id2 & 16) return true;
	if ($id1 & 32 && $id2 & 32) return true;
	return false;
}
function elementsdropdown($initialblank = 0, $includevoid = 0) {
$sql=<<<EOSQL
SELECT rd.*, r.amount
FROM resourcedefs rd
LEFT JOIN resources r ON r.resource_id = rd.resource_id AND r.user_id = '{$_SESSION['user_id']}'
ORDER BY resource_id ASC
EOSQL;
$return = "";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if (!$rs['amount']) $rs['amount'] = 0;
    $elementtiers[$rs['tier']][] = $rs;
}
if ($initialblank) {
$return .= <<<EOFORM
<option value=""></option>
EOFORM;
}
if ($includevoid) {
$return .= <<<EOFORM
<option value="0">Void (Have {$elementtiers[0][0]['amount']})</option>
EOFORM;
}
for ($i = 1; $i <= 5; $i++) {
	$return .= <<<EOFORM
<optgroup label="Tier {$i}">
EOFORM;
    foreach ($elementtiers[$i] as $rs) {
	$return .=<<<EOFORM
<option value="{$rs['resource_id']}">{$rs['name']} ({$rs['elements']}) (Have {$rs['amount']})</option>
EOFORM;
	}
}
$return .= <<<EOFORM
<optgroup label="Tier 6">
<option value="63">Harmony (Have {$elementtiers[6][0]['amount']})</option>
</optgroup>
EOFORM;
return $return;
}
function alliancehasamount($id, $alliance_id, $amount) {
//unsafe function, sanitize before calling
$sql=<<<EOSQL
SELECT amount FROM allianceresources
WHERE alliance_id = '{$alliance_id}' AND resource_id = '{$id}'
EOSQL;
$rs = onelinequery($sql);
if ($amount <= $rs['amount']) return true;
else return false;
}
function hasamount($id, $user, $amount) {
//unsafe function, sanitize before calling
$sql=<<<EOSQL
SELECT amount FROM resources
WHERE user_id = '{$user}' AND resource_id = '{$id}'
EOSQL;
$rs = onelinequery($sql);
if ($amount <= $rs['amount']) return true;
else return false;
}
function amountof($id, $user) {
//unsafe function, sanitize before calling
$sql=<<<EOSQL
SELECT amount FROM resources
WHERE user_id = '{$user}' AND resource_id = '{$id}'
EOSQL;
$rs = onelinequery($sql);
return $rs['amount'];
}
function addamount($id, $user, $amount) {
//unsafe
$sql=<<<EOSQL
INSERT INTO resources (user_id, resource_id, amount)
VALUES ({$user}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM resources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function allianceaddamount($id, $alliance_id, $amount) {
//unsafe
$sql=<<<EOSQL
INSERT INTO allianceresources (alliance_id, resource_id, amount)
VALUES ({$alliance_id}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM allianceresources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function amountbanked($id, $user) {
	$sql=<<<EOSQL
SELECT amount FROM bankedresources
WHERE user_id = '{$user}' AND resource_id = '{$id}'
EOSQL;
$rs = onelinequery($sql);
return $rs['amount'];
}
function hasbanked($id, $user, $amount) {
//unsafe function, sanitize before calling
$sql=<<<EOSQL
SELECT amount FROM bankedresources
WHERE user_id = '{$user}' AND resource_id = '{$id}'
EOSQL;
$rs = onelinequery($sql);
if ($amount <= $rs['amount']) return true;
else return false;
}
function addbanked($id, $user, $amount) {
//unsafe
$sql=<<<EOSQL
INSERT INTO bankedresources (user_id, resource_id, amount)
VALUES ({$user}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM bankedresources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function allianceamountbanked($id, $alliance) {
	$sql=<<<EOSQL
SELECT amount FROM alliancebankedresources
WHERE alliance_id = '{$alliance}' AND resource_id = '{$id}'
EOSQL;
$rs = onelinequery($sql);
return $rs['amount'];
}
function alliancehasbanked($id, $alliance, $amount) {
//unsafe function, sanitize before calling
$sql=<<<EOSQL
SELECT amount FROM alliancebankedresources
WHERE alliance_id = '{$alliance}' AND resource_id = '{$id}'
EOSQL;
$rs = onelinequery($sql);
if ($amount <= $rs['amount']) return true;
else return false;
}
function allianceaddbanked($id, $alliance, $amount) {
//unsafe
$sql=<<<EOSQL
INSERT INTO alliancebankedresources (alliance_id, resource_id, amount)
VALUES ({$alliance}, {$id}, {$amount})
ON DUPLICATE KEY UPDATE amount = amount + {$amount}
EOSQL;
$GLOBALS['mysqli']->query($sql);
$sql=<<<EOSQL
DELETE FROM alliancebankedresources WHERE amount = 0
EOSQL;
$GLOBALS['mysqli']->query($sql);
}
function hasability($name, $user) {
	//unsafe
	$sql=<<<EOSQL
	SELECT ua.turns FROM user_abilities ua
	INNER JOIN abilities a ON a.ability_id = ua.ability_id
	WHERE a.name = '{$name}' AND ua.user_id = {$user}
EOSQL;
	$rs = onelinequery($sql);
	if ($rs['turns']) return true;
	else return false;
}
function alliancehasability($name, $alliance) {
	//unsafe
	$sql=<<<EOSQL
	SELECT ua.turns FROM alliance_groupabilities ua
	INNER JOIN groupabilities a ON a.ability_id = ua.ability_id
	WHERE a.name = '{$name}' AND ua.alliance_id = {$alliance}
EOSQL;
	$rs = onelinequery($sql);
	if ($rs['turns']) return true;
	else return false;
}
function addreport($message, $user) {
	//safe
	$message = $GLOBALS['mysqli']->real_escape_string($message);
	$sql=<<<EOSQL
	INSERT INTO reports (report, user_id, time)
	VALUES ('{$message}', '{$user}', NOW())
EOSQL;
	$GLOBALS['mysqli']->query($sql);
}
function allianceaddreport($message, $alliance) {
	//safe
	$message = $GLOBALS['mysqli']->real_escape_string($message);
	$sql=<<<EOSQL
	INSERT INTO alliancereports (report, alliance_id, time)
	VALUES ('{$message}', '{$alliance}', NOW())
EOSQL;
	$GLOBALS['mysqli']->query($sql);
}
function getuserinfo($username) {
	//safe
	$username = $GLOBALS['mysqli']->real_escape_string($username);
    $sql=<<<EOSQL
    SELECT * FROM users WHERE username = '{$username}'
EOSQL;
	$rs = onelinequery($sql);
	return $rs;
}
function alliancemembers($alliance_id, $excludeself) {
	$memberarray = array();
	if ($excludeself) {
		$extrasql=<<<EOSQL
AND user_id != '{$_SESSION['user_id']}'
EOSQL;
	} else {
		$extrasql = "";
	}
	$sql=<<<EOSQL
	SELECT username, user_id FROM users
	WHERE alliance_id = '{$alliance_id}'
	{$extrasql}
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$memberarray[$rs['user_id']] = $rs['username'];
	}
    return $memberarray;
}
?>