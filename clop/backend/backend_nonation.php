<?php
include_once("allfunctions.php");
needsuser();
$sql=<<<EOSQL
SELECT nation_id FROM nations
WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
$rs = onelinequery($sql);
if ($rs['nation_id']) {
    $_SESSION['nation_id'] = $rs['nation_id'];
    header("Location: overview.php");
    exit;
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
if ($_POST && (($_POST['token_nonation'] == "") || ($_POST['token_nonation'] != $_SESSION['token_nonation']))) {
    $errors[] = "Try again at trying again.";
}
if ($_POST || ($_SESSION['token_nonation'] == "")) {
    $_SESSION['token_nonation'] = sha1(rand() . $_SESSION['token_nonation']);
}
if (!$errors) {
if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        $mysql[$key] = trim($GLOBALS['mysqli']->real_escape_string($value));
        $display[$key] = htmlentities($value, ENT_SUBSTITUTE, "UTF-8");
    }
    if ($_POST['nationname'] != preg_replace('/[^0-9a-zA-Z_\s]/' ,"", $_POST['nationname'])) {
        $errors[] = "Only English letters and numbers for the nation name.";
    }
    $sql = "SELECT COUNT(*) AS count FROM nations WHERE name = '{$mysql['nationname']}'";
    $rs = onelinequery($sql);
    if ($rs['count'] > 0) {
        $errors[] = "Nation name already taken.";
    }
    $sql = "SELECT COUNT(*) AS count FROM users WHERE username = '{$mysql['nationname']}' AND user_id != '{$_SESSION['user_id']}'";
    $rs = onelinequery($sql);
    if ($rs['count'] > 0) {
        $errors[] = "Due to the potential for faggotry, we're not going to let you make your nation name someone else's username.";
    }
    if ($mysql['nationname'] == "") {
        $errors[] = "No nation name entered.";
    }
    if ($_POST['region'] < 1 || $_POST['region'] > 4 || !is_numeric($_POST['region'])) {
        $errors[] = "I'm on to your game, buster!";
    }
    if (empty($errors)) {
    $sql =<<<EOFORM
    INSERT INTO nations (name, description, user_id, region, subregion, creationdate)
    VALUES ('{$mysql['nationname']}', '{$mysql['nationdescription']}', {$_SESSION['user_id']}, '{$mysql['region']}', '{$mysql['subregion']}', NOW())
EOFORM;
    $GLOBALS['mysqli']->query($sql);
    $sql = "SELECT u.username, n.nation_id FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE u.user_id = '{$_SESSION['user_id']}'";
    $rs2 = onelinequery($sql);
    $_SESSION['nation_id'] = $rs2['nation_id'];
    $rawnews=<<<EOFORM
The user <a href="viewuser.php?user_id={$_SESSION['user_id']}">{$rs2['username']}</a> has created the new nation of <a href="viewnation.php?nation_id={$rs2['nation_id']}">{$mysql['nationname']}</a>.
EOFORM;
	$news = $GLOBALS['mysqli']->real_escape_string($rawnews);
    $sql =<<<EOSQL
    INSERT INTO news (message, posted)
    VALUES ('{$news}', NOW())
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    header("Location: overview.php");
    exit;
    }
}
}
?>