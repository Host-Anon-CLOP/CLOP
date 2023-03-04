<?php
include_once("allfunctions.php");
needsnation();
$sql =<<<EOSQL
SELECT alliance_id FROM users WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
$rs = onelinequery($sql);
if ($rs['alliance_id']) {
    $hasalliance = true;
}
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
    $display[$key] = htmlentities($value);
}
if ($_POST && (($_POST['token_alliances'] == "") || ($_POST['token_alliances'] != $_SESSION['token_alliances']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_alliances'] == "")) {
    $_SESSION['token_alliances'] = sha1(rand() . $_SESSION['token_alliances']);
}
if (!$errors) {
if ($_POST['action'] == "Make New Alliance") {
    if ($hasalliance) {
        header("Location: myalliance.php");
        exit;
    }
    if ($_POST['alliancename'] != preg_replace('/[^0-9a-zA-Z_\s]/' ,"", $_POST['alliancename'])) {
        $errors[] = "Only English letters and numbers for the alliance name.";
    }
    if (!$_POST['alliancename']) {
        $errors[] = "No name entered.";
    }
    if (!$errors) {
    $sql =<<<EOFORM
    INSERT INTO alliances (name, description, owner_id, creationdate)
    VALUES ('{$mysql['alliancename']}', '{$mysql['alliancedescription']}', {$_SESSION['user_id']}, NOW())
EOFORM;
    $GLOBALS['mysqli']->query($sql);
    $sql=<<<EOSQL
    SELECT alliance_id FROM alliances WHERE owner_id = '{$_SESSION['user_id']}'
EOSQL;
    $rs = onelinequery($sql);
    $sql =<<<EOFORM
    UPDATE users SET alliance_id = '{$rs['alliance_id']}' WHERE user_id = '{$_SESSION['user_id']}'
EOFORM;
    $GLOBALS['mysqli']->query($sql);
    $sql =<<<EOFORM
    DELETE FROM alliance_requests WHERE user_id = '{$_SESSION['user_id']}'
EOFORM;
    $GLOBALS['mysqli']->query($sql);
    header("Location: myalliance.php");
    exit;
    }
} else if ($_POST['requestjoin']) {
    if ($hasalliance) {
        header("Location: myalliance.php");
        exit;
    }
    $sql =<<<EOSQL
    SELECT name FROM alliances WHERE alliance_id = '{$mysql['alliance_id']}'
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        $rs = mysqli_fetch_array($sth);
        $infos[] = "You have requested to join {$rs['name']}.";
    } else {
        $errors[] = "That alliance disappeared!";
    }
    $sql =<<<EOSQL
INSERT INTO alliance_requests (alliance_id, user_id) VALUES('{$mysql['alliance_id']}', '{$_SESSION['user_id']}')
EOSQL;
    $GLOBALS['mysqli']->query($sql);
} else if ($_POST['rescindrequest']) {
    $sql =<<<EOSQL
DELETE FROM alliance_requests WHERE alliance_id = '{$mysql['alliance_id']}' AND user_id = '{$_SESSION['user_id']}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
}
}
$sql=<<<EOSQL
SELECT a.* , count(DISTINCT u1.username) AS players, u2.username AS leader, COUNT(n.nation_id) AS nations,
ar.user_id AS alliancerequested
FROM alliances a
INNER JOIN users u1 ON u1.alliance_id = a.alliance_id AND u1.stasismode = 0
INNER JOIN users u2 ON a.owner_id = u2.user_id
INNER JOIN nations n ON u1.user_id = n.user_id
LEFT JOIN alliance_requests ar ON (ar.alliance_id = a.alliance_id AND ar.user_id = '{$_SESSION['user_id']}')
GROUP BY a.alliance_id
ORDER BY players DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$alliances[] = $rs;
}

?>