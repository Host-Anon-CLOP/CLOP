<?php
//let's stick the database shit in here for now
//AYYO YOU THOUGHT I WASN'T GOING TO TAKE THIS SHIT OUT DIDNTJA
$mysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_clop");
date_default_timezone_set("UTC");
session_start();
if (!isset($_SESSION['SERVER_GENERATED_SID'])) {
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['SERVER_GENERATED_SID'] = true;
}
if ($_POST['switchnation_id']) {
	$mysql['switchnation_id'] = (int)$_POST['switchnation_id'];
    $sql=<<<EOSQL
    SELECT nation_id FROM nations WHERE user_id = '{$_SESSION['user_id']}' AND nation_id = '{$mysql['switchnation_id']}'
EOSQL;
    $rs = onelinequery($sql);
    if ($rs['nation_id']) {
		$_SESSION['nation_id'] = $mysql['switchnation_id'];
	} else {
		$errors[] = "No.";
    }
	$_POST = array();
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
function needsnation() {
    if (!$_SESSION['nation_id']) {
    header("Location: nonation.php");
    exit;
    } else {
        $sql=<<<EOSQL
        SELECT n.*, u.alliance_id, u.seesecrets, u.stasismode, u.hideicons, u.hideflags FROM nations n INNER JOIN users u ON u.user_id = n.user_id
        WHERE u.user_id = {$_SESSION['user_id']} AND n.nation_id = {$_SESSION['nation_id']}
EOSQL;
        $rs = onelinequery($sql);
        if (!$rs) {
            $sql=<<<EOSQL
            SELECT nation_id FROM nations WHERE user_id = {$_SESSION['user_id']}
EOSQL;
            $rs2 = onelinequery($sql);
            if ($rs2['nation_id']) {
				$_SESSION['nation_id'] = $rs2['nation_id'];
                header("Location: test.php");
				exit;
            } else {
				unset($_SESSION['nation_id']);
                header("Location: nonation.php");
				exit;
            }
        } else if ($rs['stasismode']) {
            header("Location: userinfo.php");
            exit;
        } else {
			return $rs;
        }
    }
}
function needsuser() {
    if (!$_SESSION['user_id']) {
		header("Location: index.php");
		exit;
    } else {
        $sql=<<<EOSQL
        SELECT * FROM users WHERE user_id = {$_SESSION['user_id']} AND (stasismode = 0 OR (stasisdate < DATE_SUB(NOW(), INTERVAL 24 HOUR)) OR stasisdate IS NULL)
EOSQL;
        $rs = onelinequery($sql);
        if (!$rs) {
            session_destroy();
            session_unset();
            header("Location: index.php");
            exit;
        }
    }
}
function getgdp($nation_id) {
    $sql = "SELECT SUM(rd.gdp * (r.amount - r.disabled)) AS totalgdp FROM resources r INNER JOIN resourcedefs rd ON (rd.resource_id = r.resource_id) WHERE r.nation_id = '{$nation_id}'";
    $rs = onelinequery($sql);
    return $rs['totalgdp'] + 50000;
}
function chooseword($current, $alteration) {
    if ($current >= 0) {
        if ($alteration > 0) {
            return "improved";
        } else {
            return "dwindled";
        }
    } else {
        if ($alteration > 0) {
            return "recovered";
        } else {
            return "worsened";
        }
    }
}

function affectempirerelations($nation_id, $startingrelation, $relationeffect, $affector, $empire) {
    if ($empire == "Solar Empire") {
        $dbempire = "se_relation";
    } else if ($empire == "New Lunar Republic") {
        $dbempire = "nlr_relation";
    } else {
        return "Serious empire problem - Report this bug!";
    }
    $sql=<<<EOSQL
    SELECT u.empiremax FROM users u INNER JOIN nations n ON n.user_id = u.user_id WHERE n.nation_id = {$nation_id}
EOSQL;
    $rs = onelinequery($sql);
    if (!$rs['empiremax']) {
        $max = 1000;
    } else {
        $max = $rs['empiremax'];
    }
    if ($startingrelation + $relationeffect > $max) {
        $relationeffect = $max - $startingrelation;
        if ($relationeffect == 0) {
            return "Your relationship with the {$empire} can't get any better, despite the effects of your {$affector}.";
        } 
    } else if (($startingrelation + $relationeffect < -1000) && !$rs['empiremax']) {
        $relationeffect = -1000 - $startingrelation;
        if ($relationeffect == 0) {
            return "Your relationship with the {$empire} can't get any worse- your {$affector} sure tried, though!";
        }
    }
    $chosenword = chooseword($startingrelation, $relationeffect);
    $sql = "UPDATE nations SET {$dbempire} = {$dbempire} + {$relationeffect} WHERE nation_id = {$nation_id}";
    $GLOBALS['mysqli']->query($sql);
    return "Your relationship with the {$empire} has {$chosenword} due to your {$affector}. ({$relationeffect})";
}

function affectsatisfaction($nation_id, $startingrelation, $relationeffect, $affector, $government = "") {
    if ($government == "Transponyism") {
        $max = 7000;
	} else if ($government == "Alicorn Elite") {
		$max = 5000;
    } else if ($government == "Independence") {
        $max = 2500;
    } else if ($government == "Decentralization") {
        $max = 2000;
    } else if ($government == "Democracy") {
        $max = 1500;
    } else if ($government == "Solar Vassal" || $government == "Lunar Client") {
        $max = 1250;
    } else {
        $max = 1000;
    }
    if ($startingrelation + $relationeffect > $max) {
        $relationeffect = $max - $startingrelation;
        if ($relationeffect == 0) {
            return "Your population can't be any more satisfied, despite the effects of your {$affector}.";
        } 
    }
    $chosenword = chooseword($startingrelation, $relationeffect);
    $sql = "UPDATE nations SET satisfaction = satisfaction + {$relationeffect} WHERE nation_id = {$nation_id}";
    $GLOBALS['mysqli']->query($sql);
    return "Your population's satisfaction has {$chosenword} due to your {$affector}. ({$relationeffect})";
}

function affectsatisfaction_silent($nation_id, $startingrelation, $relationeffect, $government = "") {
    if ($government == "Transponyism") {
        $max = 7000;
	} else if ($government == "Alicorn Elite") {
		$max = 5000;
    } else if ($government == "Independence") {
        $max = 2500;
    } else if ($government == "Decentralization") {
        $max = 2000;
    } else if ($government == "Democracy") {
        $max = 1500;
    } else if ($government == "Solar Vassal" || $government == "Lunar Client") {
        $max = 1250;
    } else {
        $max = 1000;
    }
    if ($startingrelation + $relationeffect > $max) {
        $relationeffect = $max - $startingrelation;
    }
    $sql = "UPDATE nations SET satisfaction = satisfaction + {$relationeffect} WHERE nation_id = {$nation_id}";
    $GLOBALS['mysqli']->query($sql);
    return $relationeffect;
}

function getsellingmultiplier($nation_id) {
    $sql=<<<EOSQL
    SELECT economy, active_economy FROM nations WHERE nation_id = '{$nation_id}'
EOSQL;
    $rs = onelinequery($sql);
    if ($rs['economy'] == "State Controlled") {
    return .85;
    } else if ($rs['economy'] == "Free Market" && $rs['active_economy']) {
    return .97;
    } else {
    return .9;
    }
}

function getbuyingmultiplier($nation_id) {
    $sql=<<<EOSQL
    SELECT economy, active_economy FROM nations WHERE nation_id = '{$nation_id}'
EOSQL;
    $rs = onelinequery($sql);
    if ($rs['economy'] == "State Controlled") {
    return 1.2;
    } else if ($rs['economy'] == "Free Market" && $rs['active_economy']) {
    return 1.03;
    } else {
    return 1.1;
    }
}
?>