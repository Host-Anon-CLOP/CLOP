<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$times = (int)$mysql['times'];
$thingstodo = array();
$favorites = array();
$displayfunds = commas($nationinfo['funds']);
if ($_POST && (($_POST['token_favoriteactions'] == "") || ($_POST['token_favoriteactions'] != $_SESSION['token_favoriteactions']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_favoriteactions'] == "")) {
    $_SESSION['token_favoriteactions'] = sha1(rand() . $_SESSION['token_favoriteactions']);
}
if (!$errors) {
if ($_POST['remove']) {
    $sql=<<<EOSQL
    DELETE FROM recipefavorites WHERE nation_id = '{$_SESSION['nation_id']}' AND recipe_id = '{$mysql['recipe_id']}' AND times = '{$times}'
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $infos[] = "Favorite removed.";
} else if ($_POST['recipe_id']) {
    if ($times < 1) {
        $errors[] = "Whole numbers greater than 0.";
    }
    //special handling, I hate having to do this
    if ($mysql['recipe_id'] == 57 && $nationinfo['seesecrets']) {
        $errors[] = "You don't need to build another one of those again.";
    }
    if ($mysql['recipe_id'] == 36) {
        if ($nationinfo['government'] == "Lunar Client" || $nationinfo['government'] == "Solar Vassal") {
            $errors[] = "Your patron forbids the manufacture of drugs.";
        }
    }
    if ($mysql['recipe_id'] == 37) {
        if ($nationinfo['government'] == "Lunar Client" || $nationinfo['government'] == "Solar Vassal") {
            $errors[] = "Your patron forbids the selling of drugs.";
        } else if ($nationinfo['se_relation'] <= -500) {
            $errors[] = "The Solar Empire has blocked all traffic from your nation.";
        } else if (($nationinfo['se_relation'] - $times) < -500) {
            $times = 500 + $nationinfo['se_relation'];
        }
    }
    if ($mysql['recipe_id'] == 38) {
        if ($nationinfo['government'] == "Lunar Client" || $nationinfo['government'] == "Solar Vassal") {
            $errors[] = "Your patron forbids the selling of drugs.";
        } else if ($nationinfo['nlr_relation'] <= -500) {
            $errors[] = "The New Lunar Republic has blocked all traffic from your nation.";
        } else if (($nationinfo['nlr_relation'] - $times) < -500) {
            $times = 500 + $nationinfo['nlr_relation'];
        }
    }
    if ($mysql['recipe_id'] == 40) {
        if ($nationinfo['se_relation'] < 900) {
            $errors[] = "You don't have enough relation with the Solar Empire to build this.";
        } else {
            $sql=<<<EOSQL
            SELECT amount FROM resources WHERE resource_id = 44 AND nation_id = {$nationinfo['nation_id']}
EOSQL;
            $rs = onelinequery($sql);
            if ($rs['amount'] == 5) {
                $errors[] = "You already have 5 of those.";
            } else if ($times > 5 - $rs['amount']) {
                $times = 5 - $rs['amount'];
            }
        }
    }
    if ($mysql['recipe_id'] == 41) {
        if ($nationinfo['nlr_relation'] < 900) {
            $errors[] = "You don't have enough relation with the New Lunar Republic to build this.";
        } else {
            $sql=<<<EOSQL
            SELECT amount FROM resources WHERE resource_id = 45 AND nation_id = {$nationinfo['nation_id']}
EOSQL;
            $rs = onelinequery($sql);
            if ($rs['amount'] == 5) {
                $errors[] = "You already have 5 of those.";
            } else if ($times > 5 - $rs['amount']) {
                $times = 5 - $rs['amount'];
            }
        }
    }
    $rs3 = onelinequery("SELECT r.*, rd.name AS resourcename FROM recipes r
    LEFT JOIN resourcedefs rd ON r.resource_id = rd.resource_id WHERE r.recipe_id = '{$mysql['recipe_id']}'");
    if (($rs3['region'] != $nationinfo['region'] && $rs3['region']) || ($rs3['subregion'] != $nationinfo['subregion'] && $rs3['subregion'])) {
        $errors[] = "Nice try!";
    }
    if ($nationinfo['region'] == 4) { //Przewalskia
        $cost = $rs3['cost_przewalskia'];
    } else {
        $cost = $rs3['cost'];
    }
    $rs3['amount'] = $rs3['amount'] * $times;
    $cost = $cost * $times;
    $rs3['se_relation'] = $rs3['se_relation'] * $times;
    $rs3['nlr_relation'] = $rs3['nlr_relation'] * $times;
    $rs3['satisfaction'] = $rs3['satisfaction'] * $times;
    if ($nationinfo['funds'] < $cost) {
        $errors[] = "You don't have enough money to build that.";
    }
    $infostoadd = array();
    if (empty($errors)) {
        $sql = "SELECT rd.is_building, ri.resource_id, ri.is_used_up, ri.amount, rd.name FROM recipeitems ri
        LEFT JOIN resourcedefs rd ON (rd.resource_id = ri.resource_id) WHERE ri.recipe_id = {$mysql['recipe_id']}";
        $sth = $GLOBALS['mysqli']->query($sql);
        while ($rs = mysqli_fetch_array($sth)) {
            if ($rs['is_used_up']) {
                $rs['amount'] = $rs['amount'] * $times;
                $thingstodo[] = "UPDATE resources SET amount = amount - {$rs['amount']} WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$rs['resource_id']}'";
                $infostoadd[] = "You spent {$rs['amount']} {$rs['name']}.";
            }
            $rs2 = onelinequery("SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$rs['resource_id']}'");
            if ($rs['is_building'] == "true") {
                if (!$rs2) {
                    $errors[] = "You haven't built any {$rs['name']}.";
                } else if ($rs['amount'] > $rs2['amount']) {
                    $errors[] = "You haven't built enough {$rs['name']} to do that.";
                }
            } else {
                if (!$rs2) {
                    $errors[] = "You don't have any {$rs['name']}.";
                } else if ($rs['amount'] > $rs2['amount']) {
                    $errors[] = "You don't have enough {$rs['name']} to do that.";
                }
            }
        }
        if (empty($errors)) {
            $infos = array();
            $infos = array_merge($infos, $infostoadd);
            foreach ($thingstodo as $dothing) {
                $GLOBALS['mysqli']->query($dothing);
            }
            if ($rs3['resource_id']) {
                $sql = "INSERT INTO resources(nation_id, resource_id, amount) VALUES ({$_SESSION['nation_id']},
                {$rs3['resource_id']}, {$rs3['amount']}) ON DUPLICATE KEY UPDATE amount = amount + {$rs3['amount']}";
                $GLOBALS['mysqli']->query($sql);
                $infos[] = "You gained {$rs3['amount']} {$rs3['resourcename']}.";
            }
            if ($rs3['se_relation']) {
                $infos[] = affectempirerelations($_SESSION['nation_id'], $nationinfo['se_relation'], $rs3['se_relation'], "{$times} {$rs3['name']}", "Solar Empire");
            }
            if ($rs3['nlr_relation']) {
                $infos[] = affectempirerelations($_SESSION['nation_id'], $nationinfo['nlr_relation'], $rs3['nlr_relation'], "{$times} {$rs3['name']}", "New Lunar Republic");
            }
            if ($rs3['satisfaction']) {
                $infos[] = affectsatisfaction($_SESSION['nation_id'], $nationinfo['satisfaction'], $rs3['satisfaction'], "{$times} {$rs3['name']}", $nationinfo['government']);
            }
            if ($cost) {
                $sql = "UPDATE nations SET funds = funds - {$cost} WHERE nation_id = {$_SESSION['nation_id']}";
                $GLOBALS['mysqli']->query($sql);
                if ($cost > 0) {
                    $formatcost = commas($cost);
                    $infos[] = "You paid {$formatcost} bits.";
                } else {
                    $formatcost = commas(0 - $cost);
                    $infos[] = "You gained {$formatcost} bits.";
                }
                $nationinfo['funds'] -= $cost;
                $displayfunds = commas($nationinfo['funds']);
            }
            $infos[] = "{$rs3['name']} completed successfully.";
            //these are quick hacks to make things work, I'll return to this sometime
            $sql = "UPDATE resources SET disabled = amount WHERE disabled > amount AND nation_id = {$_SESSION['nation_id']}";
            $GLOBALS['mysqli']->query($sql);
            $sql = "DELETE FROM resources WHERE amount = 0 AND nation_id = {$_SESSION['nation_id']}";
            $GLOBALS['mysqli']->query($sql);
            $messageslist = $GLOBALS['mysqli']->real_escape_string(implode("<br/>", $infos));
            $sql = "INSERT INTO reports (nation_id, report, time) VALUES ({$_SESSION['nation_id']}, '{$messageslist}', NOW())";
            $GLOBALS['mysqli']->query($sql);
        }
    }
}
}
$sql =<<<EOSQL
SELECT r.name, rf.times, r.recipe_id FROM recipes r
INNER JOIN recipefavorites rf ON rf.recipe_id = r.recipe_id WHERE rf.nation_id = '{$_SESSION['nation_id']}' ORDER BY r.name, rf.times
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $favorites[] = $rs;
}
?>