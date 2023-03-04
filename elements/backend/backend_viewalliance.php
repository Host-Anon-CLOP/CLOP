<?php
include("allfunctions.php");
include("listresources.php");
$getpost = array_merge($_GET, $_POST);
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
if ($_POST && (($_POST['token_viewalliance'] == "") || ($_POST['token_viewalliance'] != $_SESSION['token_viewalliance']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_viewalliance'] == "")) {
    $_SESSION['token_viewalliance'] = sha1(rand() . $_SESSION['token_viewalliance']);
}
$sql=<<<EOSQL
SELECT a.*, u.donator
FROM alliances a
LEFT JOIN users u ON a.owner_id = u.user_id
WHERE a.alliance_id = '{$mysql['alliance_id']}'
EOSQL;
$thisallianceinfo = onelinequery($sql);
if ($thisallianceinfo) {
$sql=<<<EOSQL
SELECT user_id, username, production, tier FROM users WHERE alliance_id = '{$mysql['alliance_id']}' ORDER BY production desc, user_id asc
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	if ($rs['user_id'] == $thisallianceinfo['owner_id']) {
		$rs['username'] .= " (Owner)";
	}
	$alliancemembers[] = $rs;
}
$displaypubdescription = nl2br(htmlentities($thisallianceinfo['description'], ENT_SUBSTITUTE, "UTF-8"));
if ($userinfo['alliance_id'] && $_POST['spy'] && !$errors) {
	if ($thisallianceinfo['alliance_id'] < 4 && $_SESSION['user_id'] >= 5) {
		$errors[] = "There's not a lot of point to that, really.";
	} else if ($thisallianceinfo['alliance_id'] == $userinfo['alliance_id']) {
		$errors[] = "Get paranoid smarter, not harder.";
	} else if (!hasamount(25, $_SESSION['user_id'], $constants['allianceequalitytospy'])) {
        $errors[] = "You do not have the Equality to spy on this alliance.";
    } else if (alliancehasbanked(7, $thisallianceinfo['alliance_id'], $constants['allianceunitytoblock'])) {
        $infos[] = "Your spying attempt was blocked by the target alliance's Unity.";
        addamount(25, $_SESSION['user_id'], $constants['allianceequalitytospy'] * -1);
        allianceaddbanked(7, $thisallianceinfo['alliance_id'], $constants['allianceunitytoblock'] * -1);
        if (alliancehasability("seespyattempts", $thisallianceinfo['alliance_id'])) {
            allianceaddreport("{$userinfo['username']} tried to spy on your alliance, but banked Unity blocked it!", $thisallianceinfo['alliance_id']);
        }
        $blocked = true;
    }
if (!$errors && !$blocked) {
    if ($thisallianceinfo['alliancefocus']) {
	$sql=<<<EOSQL
	SELECT * FROM elementpositions
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$positions[$rs['position']] = $rs['resource_id'];
	}
	foreach ($positions as $key => $value) {
		if ($value == $thisallianceinfo['alliancefocus']) {
			$production[$key] = $thisallianceinfo['alliancefocusamount'];
		}
	}
	}
	$sql=<<<EOSQL
	SELECT * FROM elementpositions
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$positions[$rs['position']] = $rs['resource_id'];
	}
	$elementpositions = array_flip($positions);
    addamount(25, $_SESSION['user_id'], $constants['allianceequalitytospy'] * -1);
	$resourcelist = getallianceresources($thisallianceinfo['alliance_id']);
}
}
}
?>