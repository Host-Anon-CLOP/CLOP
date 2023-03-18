<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$displayfunds = commas($nationinfo['funds']);
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval");
if ($nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Transponyism") {
    $forcetypes[6] = "Alicorns";
}
$mercenarytypes = array("" => "National Recruits", "se" => "SE Mercenaries", "nlr" => "NLR Mercenaries");
if ($_POST && (($_POST['token_createforces'] == "") || ($_POST['token_createforces'] != $_SESSION['token_createforces']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_createforces'] == "")) {
    $_SESSION['token_createforces'] = sha1(rand() . $_SESSION['token_createforces']);
}
if (!$errors) {
if ($_POST['createforce']) {
    $mysql['size'] = (int)$_POST['size'];
    $mysql['name'] = $GLOBALS['mysqli']->real_escape_string($_POST['name']);
    if ($mysql['size'] < 1) $errors[] = "No size entered.";
    $type = (int)$_POST['forcetype'];
    switch ($type) {
        case 1:
        $cost = 200000;
        break;
        case 2:
        $cost = 300000;
        break;
        case 3:
        $cost = 300000;
        break;
        case 4:
        $cost = 400000;
        break;
        case 5:
        $cost = 250000;
        break;
        case 6:
        $cost = 2000000;
        break;
        default:
        $errors[] = "No type entered.";
        break;
    }
    if ($type == 6 && !($nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Transponyism")) {
        $errors[] = "Nope.";
    }
    $mercenarymult = 1;
    if ($_POST['mercenaries'] == "se") {
        $mercenarymult = 3 - ($nationinfo['se_relation']/1000);
    } else if ($_POST['mercenaries'] == "nlr") {
        $mercenarymult = 3 - ($nationinfo['nlr_relation']/1000);
    }
    $cost = $cost * $mercenarymult * $mysql['size'];
    if ($nationinfo['funds'] < $cost) {
        $errors[] = "You don't have the money!";
    }
    if ($mysql['name'] == "") {
        $errors[] = "No name entered.";
    }
    if ($mysql['name'] != preg_replace('/[^0-9a-zA-Z_\s]/' ,"", $mysql['name'])) {
        $errors[] = "Only English letters and numbers for the force name.";
    }
    if ($_POST['mercenaries'] != "") {
        $training = 10;
    } else {
        $sql=<<<EOSQL
        SELECT amount AS barracks FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '36'
EOSQL;
        $rs = onelinequery($sql);
        if (!$rs['barracks']) $rs['barracks'] = 0;
        $training = $rs['barracks'];
        if ($training > 20) $training = 20;
    }
    if ($_POST['mercenaries'] != "" && $type == 6) {
        $errors[] = "Alicorn mercenaries? Where the fuck are you going to find those?";
    }
    $sql=<<<EOSQL
    SELECT COUNT(*) AS totalcount FROM forces WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
    $totalcount = onelinequery($sql);
    if ($totalcount['totalcount'] >= 100) {
        $errors[] = "You have too many different forces.";
    }
    if (!$errors) {
        $sql=<<<EOSQL
        UPDATE nations SET funds = funds - {$cost} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $sql=<<<EOSQL
        INSERT INTO forcegroups (nation_id, location_id, name) VALUES ({$_SESSION['nation_id']}, {$_SESSION['nation_id']}, '{$mysql['name']}')
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $forcegroup_id = mysqli_insert_id($GLOBALS['mysqli']);
        $sql=<<<EOSQL
        INSERT INTO forces (nation_id, size, type, training, name, forcegroup_id) VALUES ({$_SESSION['nation_id']}, {$mysql['size']}, '{$type}', {$training}, '{$mysql['name']}', {$forcegroup_id})
EOSQL;
        $GLOBALS['mysqli']->query($sql);
        $messageslist = "You have created the military force {$mysql['name']}.";
		$sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$_SESSION['nation_id']}, '{$messageslist}', NOW())";
		$GLOBALS['mysqli']->query($sql);
		header("Location: equipforces.php");
        exit;
    }
}
}
?>