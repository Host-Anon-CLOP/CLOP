<?php
include_once("allfunctions.php");
$all_resources_list = array();
$empirenations = array();
$resources = array();

# get list of all resources that exist, excluding buildings
$sql=<<<EOSQL
select resource_id, name from resourcedefs where is_building = 0
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $all_resources_list += array($rs['resource_id'] => $rs['name']);
}

# get list of nations owned by the user
$sql=<<<EOSQL
select nation_id, name from nations where user_id = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $empirenations += array($rs['nation_id'] => $rs['name']);
    $resources += array($rs['nation_id'] => array());
}

# ALL NETS
foreach ($empirenations as $nation_id => $nation_name) {
    $nationinfo = needsspecificnation($nation_id);

    # Get GDP
    $sql=<<<EOSQL
    SELECT sum(n.gdp_last_turn) AS amount FROM nations n WHERE n.nation_id = $nation_id
EOSQL;
    $rs = onelinequery($sql);
    $resources[$nation_id] += array('funds' => $rs['amount']);


    # Nation Resources - Net
    $affectedresources = array();
    $requiredresources = array();
    $rs = array();

    $sql = "SELECT rd.name, rd.resource_id, SUM((r.amount - r.disabled) * rr.amount) AS affected
    FROM resourceeffects rr
    INNER JOIN resources r ON r.resource_id = rr.resource_id
    INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
    WHERE r.nation_id = $nation_id
    GROUP BY rd.name";
    $sth = $GLOBALS['mysqli']->query($sql);
    while ($rs = mysqli_fetch_array($sth)) {
        $affectedresources[$rs['resource_id']] = $rs['affected'];
    }

    $sql = "SELECT rd.name, rd.resource_id, SUM((r.amount - r.disabled) * rr.amount) AS required
    FROM resourcerequirements rr
    INNER JOIN resources r ON r.resource_id = rr.resource_id
    INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
    WHERE r.nation_id = $nation_id
    GROUP BY rd.name";
    $sth = $GLOBALS['mysqli']->query($sql);
    while ($rs = mysqli_fetch_array($sth)) {
        $requiredresources[$rs['resource_id']] = $rs['required'];
    }

    if ($nationinfo['government'] == "Democracy") {
        $requiredresources["25"] += 20; // gas
        $requiredresources["9"] += 2; // vp
    } else if ($nationinfo['government'] == "Repression") {
        $requiredresources["25"] += 10;
    } else if ($nationinfo['government'] == "Independence") {
        $requiredresources["25"] += 40;
        $requiredresources["9"] += 4;
    } else if ($nationinfo['government'] == "Decentralization") {
        $requiredresources["25"] += 50;
        $requiredresources["9"] += 5;
    } else if ($nationinfo['government'] == "Authoritarianism") {
        $requiredresources["25"] += 10;
        $requiredresources["10"] += 3; // mp
    } else if ($nationinfo['government'] == "Oppression") {
        $requiredresources["25"] += 10;
        $requiredresources["10"] += 5;
    }
    if ($nationinfo['economy'] == "Free Market") {
        $requiredresources["20"] += 6; // coffee
    } else if ($nationinfo['economy'] == "State Controlled") {
        $requiredresources["18"] += 6; // cider
    }


    # Update each nation with net resources
    foreach ($all_resources_list as $resource_id => $resource_name) {
        $resources[$nation_id] += array($resource_id => ($affectedresources[$resource_id] - $requiredresources[$resource_id]) );
    }


    # Net NLR / SE / SAT
    $envirodamage = 0;
    $satperturn = 0;
    $seperturn = 0;
    $nlrperturn = 0;
    $tempse = $nationinfo['se_relation'];
    $tempnlr = $nationinfo['nlr_relation'];
    if ($nationinfo['se_relation'] > 250) {
        $seperturn -= floor(($nationinfo['se_relation'] - 250) / 50);
        if ($nationinfo['se_relation'] > 400) {
            $seperturn -= floor(($nationinfo['se_relation'] - 400) / 50);
            if ($nationinfo['se_relation'] > 800) {
                $seperturn -= floor(($nationinfo['se_relation'] - 800) / 50);
            }
        }
    }
    if ($nationinfo['nlr_relation'] > 250) {
        $nlrperturn -= floor(($nationinfo['nlr_relation'] - 250) / 50);
        if ($nationinfo['nlr_relation'] > 400) {
            $nlrperturn -= floor(($nationinfo['nlr_relation'] - 400) / 50);
            if ($nationinfo['nlr_relation'] > 800) {
                $nlrperturn -= floor(($nationinfo['nlr_relation'] - 800) / 50);
            }
        }
    }
    if ($nationinfo['se_relation'] < -450) {
        $seperturn -= ceil(($nationinfo['se_relation'] + 450) / 50);
        if ($nationinfo['se_relation'] < -700) {
            $seperturn -= ceil(($nationinfo['se_relation'] + 700) / 50);
            if ($nationinfo['se_relation'] < -900) {
                $seperturn -= ceil(($nationinfo['se_relation'] + 900) / 50);
            }
        }
    }
    if ($nationinfo['nlr_relation'] < -450) {
        $nlrperturn -= ceil(($nationinfo['nlr_relation'] + 450) / 50);
        if ($nationinfo['nlr_relation'] < -700) {
            $nlrperturn -= ceil(($nationinfo['nlr_relation'] + 700) / 50);
            if ($nationinfo['nlr_relation'] < -900) {
                $nlrperturn -= ceil(($nationinfo['nlr_relation'] + 900) / 50);
            }
        }
    }
    $tempse += $seperturn;
    $tempnlr += $nlrperturn;
    if ($tempse > 0) {
        $nlrperturn -= floor($tempse / 50);
    }
    if ($tempnlr > 0) {
        $seperturn -= floor($tempnlr / 50);
    }
    if ($nationinfo['economy'] == "Free Market") {
        $seperturn -= 3;
        $nlrperturn += 1;
    } else if ($nationinfo['economy'] == "State Controlled") {
        $seperturn += 1;
        $nlrperturn -= 3;
    }
    switch ($nationinfo['government']) {
        case "Democracy":
        $seperturn -= 3;
        $nlrperturn += 2;
        break;
        case "Decentralization":
        $seperturn -= 3;
        $nlrperturn += 4;
        break;
        case "Independence":
        $seperturn -= 3;
        $nlrperturn += 6;
        break;
        case "Repression":
        $seperturn += 2;
        $nlrperturn -= 3;
        break;
        case "Authoritarianism":
        $seperturn += 4;
        $nlrperturn -= 3;
        break;
        case "Oppression":
        $seperturn += 6;
        $nlrperturn -= 3;
        break;
        case "Solar Vassal":
        $seperturn = "Fixed";
        break;
        case "Lunar Client":
        $nlrperturn = "Fixed";
        break;
        case "Alicorn Elite":
        case "Transponyism":
        $seperturn = "Ascending";
        $nlrperturn = "Ascending";
        break;
        default:
        break;
    }
    $sql=<<<EOSQL
    SELECT rd.*, r.amount, r.disabled, rd.se_relation, rd.nlr_relation
    FROM resourcedefs rd
    INNER JOIN resources r ON r.resource_id = rd.resource_id
    WHERE r.nation_id = $nation_id
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        while ($rs = mysqli_fetch_array($sth)) {
            $satperturn += ($rs['satisfaction'] * ($rs['amount'] - $rs['disabled']));
            $satperturn -= $rs['disabled'];
            if ($rs['bad_min'] && (($rs['amount'] - $rs['disabled']) > $rs['bad_min'])) {
                $satloss = ceil(pow((($rs['amount'] - $rs['disabled']) - $rs['bad_min']), 2) / $rs['bad_div']);
                $satperturn -= $satloss;
                $envirodamage += $satloss;
            }
            if ($rs['resource_id'] == 44 || $rs['resource_id'] == 45) {
                $envirocleaners += ($rs['amount'] - $rs['disabled']);
            }
            if ($rs['se_relation'] && is_numeric($seperturn)) {
                $seperturn += (($rs['amount'] - $rs['disabled']) * $rs['se_relation']);
            }
            if ($rs['nlr_relation'] && is_numeric($nlrperturn)) {
                $nlrperturn += (($rs['amount'] - $rs['disabled']) * $rs['nlr_relation']);
            }
        }
        if ($envirocleaners) {
            $satperturn += $envirodamage - ceil($envirodamage * pow(.9, $envirocleaners));
        }
    }
    $sql=<<<EOSQL
    SELECT sum(size) AS totalsize FROM forces WHERE nation_id = $nation_id
EOSQL;
    $rs = onelinequery($sql);
    if ($rs['totalsize'] > 20) {
        $satperturn -= ceil(($rs['totalsize'] - 20)/2);
    }
    $sql=<<<EOSQL
    SELECT COUNT(*) AS empiresize FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $rs = onelinequery($sql);
    if ($nationinfo['government'] == "Transponyism" || $nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Oppression") {
        $satperturn -= ceil((pow(($rs['empiresize'] - 1), 2) * 20) / 3);
    } else {
        $satperturn -= pow(($rs['empiresize'] - 1), 2) * 20;
    }
    if ($nationinfo['government'] == "Democracy") {
        $satperturn += 15;
    } else if ($nationinfo['government'] == "Decentralization") {
        $satperturn += 30;
    } else if ($nationinfo['government'] == "Independence") {
        $satperturn += 50;
    }
    if ($nationinfo['government'] == "Transponyism") {
        $satmultiplier = 7;
    } else if ($nationinfo['government'] == "Alicorn Elite") {
        $satmultiplier = 5;
    } else if ($nationinfo['government'] == "Independence") {
        $satmultiplier = 2.5;
    } else if ($nationinfo['government'] == "Decentralization") {
        $satmultiplier = 2;
    } else if ($nationinfo['government'] == "Democracy") {
        $satmultiplier = 1.5;
    } else if ($nationinfo['government'] == "Solar Vassal" || $nationinfo['government'] == "Lunar Client") {
        $satmultiplier = 1.25;
    } else {
        $satmultiplier = 1;
    }
    if ($nationinfo['satisfaction'] > (250 * $satmultiplier)) {
        $satperturn -= floor(($nationinfo['satisfaction'] - (250 * $satmultiplier)) / (50 * $satmultiplier));
        if ($nationinfo['satisfaction'] > (500 * $satmultiplier)) {
            $satperturn -= floor(($nationinfo['satisfaction'] - (500 * $satmultiplier)) / (50 * $satmultiplier));
            if ($nationinfo['satisfaction'] > (750 * $satmultiplier)) {
                $satperturn -= floor(($nationinfo['satisfaction'] - (750 * $satmultiplier)) / (50 * $satmultiplier));
            }
        }
    }

    $resources[$nation_id] += array('satisfaction' => $satperturn);
    $resources[$nation_id] += array('se' => $seperturn);
    $resources[$nation_id] += array('nlr' => $nlrperturn);

}
?>