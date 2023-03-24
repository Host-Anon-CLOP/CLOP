<?php
include("allfunctions.php");
$sql=<<<EOSQL
SELECT alliance_id
FROM users
WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
$rs = onelinequery($sql);
if (!$rs['alliance_id']) {
    header("Location: overview.php");
    exit;
}
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
    $display[$key] = htmlentities($value);
}
$sql=<<<EOSQL
SELECT a.*, u.donator
FROM alliances a
LEFT JOIN users u ON a.owner_id = u.user_id
WHERE a.alliance_id = '{$rs['alliance_id']}'
EOSQL;
$allianceinfo = onelinequery($sql);
$displayeditpubdescription = htmlentities($allianceinfo['public_description'], ENT_SUBSTITUTE, "UTF-8");
$displayeditdescription = htmlentities($allianceinfo['description'], ENT_SUBSTITUTE, "UTF-8");
$displaydescription = nl2br(htmlentities($allianceinfo['description'], ENT_SUBSTITUTE, "UTF-8"));

if ($_POST && (($_POST['token_myalliance'] == "") || ($_POST['token_myalliance'] != $_SESSION['token_myalliance']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_myalliance'] == "")) {
    $_SESSION['token_myalliance'] = sha1(rand() . $_SESSION['token_myalliance']);
}
if ($_SESSION['user_id'] == $allianceinfo['owner_id']) {
    $owner = true;
}
if (!$errors) {
    # Alliance Leader Stuff
    if ($owner) {
    if ($_POST['updatedescription']) {
        $sql=<<<EOSQL
    UPDATE alliances SET description = '{$mysql['alliancedescription']}' WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $displayeditdescription = htmlentities($_POST['alliancedescription'], ENT_SUBSTITUTE, "UTF-8");
        $displaydescription = nl2br(htmlentities($_POST['alliancedescription'], ENT_SUBSTITUTE, "UTF-8"));
    }
    if ($_POST['updatepubdescription']) {
        $sql=<<<EOSQL
    UPDATE alliances SET public_description = '{$mysql['alliancepubdescription']}' WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $displayeditpubdescription = htmlentities($_POST['alliancepubdescription']);
    }
    if ($_POST['action'] == "Accept User") {
        $sql=<<<EOSQL
    SELECT * FROM alliance_requests WHERE user_id = '{$mysql['user_id']}' AND alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $rs = onelinequery($sql);
        if ($rs) {
            $sql=<<<EOSQL
    DELETE FROM alliance_requests WHERE user_id = '{$mysql['user_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $sql=<<<EOSQL
    UPDATE users SET alliance_id = {$allianceinfo['alliance_id']} WHERE user_id = '{$mysql['user_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $message = $GLOBALS['mysqli']->real_escape_string("Your application to {$allianceinfo['name']} was accepted.");
            $sql=<<<EOSQL
    INSERT INTO messages (fromuser, touser, fromdeleted, message, sent) VALUES (0, {$mysql['user_id']}, 1, '{$message}', NOW())
EOSQL;
            $GLOBALS['mysqli']->query($sql);
        } else {
            $errors[] = "The user disappeared before you could accept!";
        }
    }
    if ($_POST['action'] == "Reject User") {
        $sql=<<<EOSQL
    SELECT * FROM alliance_requests WHERE user_id = '{$mysql['user_id']}' AND alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        if ($rs) {
            $sql=<<<EOSQL
    DELETE FROM alliance_requests WHERE user_id = '{$mysql['user_id']}' AND alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $message = $GLOBALS['mysqli']->real_escape_string("Your application to {$allianceinfo['name']} was rejected.");
            $sql=<<<EOSQL
    INSERT INTO messages (fromuser, touser, fromdeleted, message, sent) VALUES (0, {$mysql['user_id']}, 1, '{$message}', NOW())
EOSQL;
            $GLOBALS['mysqli']->query($sql);
        } else {
            $errors[] = "The member disappeared before you could reject!";
        }
    }
    if ($_POST['action'] == "Eject User") {
        $sql=<<<EOSQL
    UPDATE users SET alliance_id = 0 WHERE user_id = '{$mysql['user_id']}' AND alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $message = $GLOBALS['mysqli']->real_escape_string("You were thrown out of {$allianceinfo['name']}!");
        $sql=<<<EOSQL
    INSERT INTO messages (fromuser, touser, fromdeleted, message, sent) VALUES (0, {$mysql['user_id']}, 1, '{$message}', NOW())
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    }
    if ($_POST['givealliance']) {
        $sql = "SELECT user_id, alliance_id FROM users WHERE username = '{$mysql['giveto']}'";
        $rs = onelinequery($sql);
        if (!$rs) {
            $errors[] = "User not found.";
        } else if ($rs['alliance_id'] != $allianceinfo['alliance_id']) {
            $errors[] = "That user is not in your alliance!";
        } else if ($rs['user_id'] == $_SESSION['user_id']) {
            $errors[] = "Silly.";
        } else if ($rs) {
            $givetoid = $rs['user_id'];
            $sql=<<<EOSQL
UPDATE alliances SET owner_id = '{$rs['user_id']}' WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $owner = false;
        }
    }
    if ($_POST['action'] == "Disband Alliance") {
        $sql=<<<EOSQL
    UPDATE users SET alliance_id = 0 WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
    DELETE FROM alliance_requests WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
    DELETE FROM alliances WHERE alliance_id = '{$allianceinfo['alliance_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    header("Location: overview.php");
    exit;
    }
    if ($_POST['bulkdelete']) {
	if (!ctype_digit($_POST['deletedays']) || $_POST['deletedays'] === "") {
        $errors[] = "Enter a number of days.";
    }
	if (!$errors) {
	$mysql['deletedays'] = (int)$_POST['deletedays'];
    $sql=<<<EOSQL
DELETE FROM alliance_messages WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND posted < DATE_SUB(NOW(), INTERVAL {$mysql['deletedays']} DAY)
EOSQL;
	$GLOBALS['mysqli']->query($sql);
   }
   }
   }
if ($_POST['action'] == "Leave Alliance") {
    $sql=<<<EOSQL
    UPDATE users SET alliance_id = 0 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    header("Location: overview.php");
    exit;
}
if ($_POST['sendmessage']) {
    $sql=<<<EOSQL
    INSERT INTO alliance_messages SET alliance_id = '{$allianceinfo['alliance_id']}', user_id = '{$_SESSION['user_id']}', posted = NOW(), message = '{$mysql['message']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
}
if ($_POST['deletemessage']) {
    if ($owner) {
        $sql=<<<EOSQL
        SELECT user_id FROM alliance_messages WHERE alliance_id = '{$allianceinfo['alliance_id']}' AND message_id = '{$mysql['message_id']}'
EOSQL;
    } else {
        $sql=<<<EOSQL
        SELECT user_id FROM alliance_messages WHERE user_id = '{$_SESSION['user_id']}' AND alliance_id = '{$allianceinfo['alliance_id']}' AND message_id = '{$mysql['message_id']}'
EOSQL;
    }
    $rs = onelinequery($sql);
    if ($rs['user_id']) {
        $sql=<<<EOSQL
        DELETE FROM alliance_messages WHERE message_id = '{$mysql['message_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
    } else {
        $errors[] = "You can't delete that message.";
    }
}
}

# Alliance Member Stuff
$alliancemembers = array();
$requestingmembers = array();
$sql=<<<EOSQL
SELECT username, user_id, stasismode FROM users WHERE alliance_id = '{$allianceinfo['alliance_id']}' ORDER BY username
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
$sql=<<<EOSQL
SELECT u.username, u.user_id FROM alliance_requests ar INNER JOIN users u ON ar.user_id = u.user_id WHERE ar.alliance_id = '{$allianceinfo['alliance_id']}' ORDER BY u.username
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
    while ($rs = mysqli_fetch_array($sth)) {
        $requestingmembers[] = $rs;
    }
}
$sql=<<<EOSQL
SELECT u.username, u.user_id, am.message, am.posted, am.message_id FROM alliance_messages am INNER JOIN users u ON am.user_id = u.user_id WHERE am.alliance_id = '{$allianceinfo['alliance_id']}'
ORDER BY am.posted DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
    while ($rs = mysqli_fetch_array($sth)) {
        $rs['displaymessage'] = nl2br(htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8"));
        $messages[] = $rs;
    }
}

# Set when Alliance Messages were last checked
$sql=<<<EOSQL
UPDATE users SET alliance_messages_last_checked = NOW() WHERE user_id = {$_SESSION['user_id']}
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
$_SESSION['alliance_messages_last_checked'] = date("Y-m-d H:i:s");

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
WHERE u.alliance_id = {$allianceinfo['alliance_id']} AND u.stasismode = 0
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
WHERE u.alliance_id = {$allianceinfo['alliance_id']} AND u.stasismode = 0
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $alliancerequiredresources[$rs['name']] = $rs['required'];
}
    
?>