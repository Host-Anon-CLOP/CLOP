<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$displayfunds = commas($nationinfo['funds']);
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$mysql['nation_id'] = (int)$_POST['nation_id'];
if ($_POST && (($_POST['token_transfer'] == "") || ($_POST['token_transfer'] != $_SESSION['token_transfer']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_transfer'] == "")) {
    $_SESSION['token_transfer'] = sha1(rand() . $_SESSION['token_transfer']);
}
if ($mysql['nation_id'] == $_SESSION['nation_id']) {
    $errors[] = "You just tried to send something to the nation you're sending it from. Watch your tab switching!";
}
if (!$errors && $_POST) {
	$sql=<<<EOSQL
    SELECT nation_id, government, name FROM nations WHERE user_id = '{$_SESSION['user_id']}' AND nation_id = '{$mysql['nation_id']}'
EOSQL;
	$rs = onelinequery($sql);
	if (!$rs['nation_id']) {
		$errors[] = "No.";
	} else if ($rs['government'] == "Decentralization") {
		$errors[] = "Nope.";
    } else {
        $targetname = $rs['name'];
		if ($_POST['transfermoney']) {
			if ($mysql['money'] <= 0) {
				$errors[] = "No amount entered.";
			}
			if ($nationinfo['funds'] < $mysql['money']) {
				$errors[] = "You do not have that much money.";
			}
            if (!ctype_digit($mysql['money'])) {
                $errors[] = "Digits only- no commas, periods, or other markers.";
            }
			if (!$errors) {
				if ($nationinfo['economy'] == "State Controlled") {
					$multiplier = 1;
				} else if ($nationinfo['economy'] == "Free Market") {
					$multiplier = .94;
				} else {
					$multiplier = .97;
				}
				$amount = round($multiplier * $mysql['money']);
                $mysql['money'] = $mysql['money'] * 1; //leading zero bullshit
				$sql=<<<EOSQL
				UPDATE nations SET funds = funds + '{$amount}' WHERE nation_id = '{$mysql['nation_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
				UPDATE nations SET funds = funds - '{$mysql['money']}' WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$displayfunds = commas($nationinfo['funds'] - $mysql['money']);
				$displaymoney = commas($mysql['money']);
				$displayamount = commas($amount);
				$newinfo = "This nation paid {$displaymoney} bits and {$targetname} received {$displayamount} bits.";
				$infos[] = $newinfo;
				$mysqlnewinfo = $GLOBALS['mysqli']->real_escape_string($newinfo);
				$sql=<<<EOSQL
				INSERT INTO reports SET nation_id = {$_SESSION['nation_id']}, report = '{$mysqlnewinfo}', time = NOW()
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$message = $GLOBALS['mysqli']->real_escape_string("This nation received {$displayamount} bits from {$nationinfo['name']}.");
				$sql=<<<EOSQL
				INSERT INTO reports SET nation_id = {$mysql['nation_id']}, report = '{$message}', time = NOW()
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
		} else if ($_POST['transferresource'] || $_POST['transferweapon'] || $_POST['transferarmor']) {
			if ($_POST['transferweapon']) {
				$resource_id = "weapon_id";
				$resourcestable = "weapons";
				$resourcedefs = "weapondefs";
				$tradeable = "";
				$mysql['amount'] = $GLOBALS['mysqli']->real_escape_string($_POST['weaponamount']);
				$mysql['resource_id'] = $GLOBALS['mysqli']->real_escape_string($_POST['weapon_id']);
			} else if ($_POST['transferarmor']) {
				$resource_id = "armor_id";
				$resourcestable = "armor";
				$resourcedefs = "armordefs";
				$tradeable = "";
				$mysql['amount'] = $GLOBALS['mysqli']->real_escape_string($_POST['armoramount']);
				$mysql['resource_id'] = $GLOBALS['mysqli']->real_escape_string($_POST['armor_id']);
			} else {
				$resource_id = "resource_id";
				$resourcestable = "resources";
				$resourcedefs = "resourcedefs";
				$tradeable = "AND is_tradeable = 1";
				$mysql['amount'] = $GLOBALS['mysqli']->real_escape_string($_POST['resourceamount']);
				$mysql['resource_id'] = $GLOBALS['mysqli']->real_escape_string($_POST['resource_id']);
			}
		$sql =<<<EOSQL
		SELECT {$resource_id}, name FROM {$resourcedefs} WHERE {$resource_id} = '{$mysql['resource_id']}' {$tradeable}
EOSQL;
		$rs = onelinequery($sql);
		if (!$rs) {
			$errors[] = "Did you know that people like you only exist to make my life harder?";
		}
		$resourcename = $rs['name'];
		$sql =<<<EOSQL
		SELECT amount FROM {$resourcestable} WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = '{$mysql['resource_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if ($nationinfo['economy'] == "State Controlled") {
			$paythis = 0;
		} else if ($nationinfo['economy'] == "Free Market") {
			$paythis = $mysql['amount'] * 100;
		} else {
			$paythis = $mysql['amount'] * 50;
		}
		if ($nationinfo['funds'] < $paythis) {
			$errors[] = "You don't have the money to transfer that many items!";
		}
		if ($rs['amount'] < $mysql['amount']) {
			$errors[] = "You don't have enough to transfer!";
		} else if ($mysql['amount'] < 1) {
			$errors[] = "No amount entered.";
		}
        if (!ctype_digit($mysql['amount'])) {
            $errors[] = "Digits only- no commas, periods, or other markers.";
        }
		if (!$errors) {
            $mysql['amount'] = $mysql['amount'] * 1; //more leading zero bullshit
			$sql=<<<EOSQL
			INSERT INTO {$resourcestable} SET amount = '{$mysql['amount']}', nation_id = '{$mysql['nation_id']}', {$resource_id} = {$mysql['resource_id']}
			ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql) or die($sql);
			if ($rs['amount'] == $mysql['amount']) {
				$sql=<<<EOSQL
				DELETE FROM {$resourcestable} WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = {$mysql['resource_id']}
EOSQL;
			} else {
				$sql=<<<EOSQL
				UPDATE {$resourcestable} SET amount = amount - {$mysql['amount']} WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = {$mysql['resource_id']}
EOSQL;
			}
			$GLOBALS['mysqli']->query($sql);
			if ($paythis) {
				$sql=<<<EOSQL
				UPDATE nations SET funds = funds - {$paythis} WHERE nation_id = {$_SESSION['nation_id']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$displayfunds = commas($nationinfo['funds'] - $paythis);
			}
			$displaypay = commas($paythis);
			$newinfo = "You transferred {$mysql['amount']} {$resourcename} to {$targetname} for {$displaypay} bits.";
			$infos[] = $newinfo;
			$mysqlnewinfo = $GLOBALS['mysqli']->real_escape_string($newinfo);
			$sql=<<<EOSQL
			INSERT INTO reports SET nation_id = {$_SESSION['nation_id']}, report = '{$mysqlnewinfo}', time = NOW()
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$message = $GLOBALS['mysqli']->real_escape_string("This nation received {$mysql['amount']} {$resourcename} from {$nationinfo['name']}.");
			$sql=<<<EOSQL
			INSERT INTO reports SET nation_id = {$mysql['nation_id']}, report = '{$message}', time = NOW()
EOSQL;
			$GLOBALS['mysqli']->query($sql);
		}
		}
	}
}
$sql=<<<EOSQL
SELECT nation_id, name FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$nations[$rs['nation_id']] = $rs['name'];
}
$resourceoptions = array();
$weaponoptions = array();
$armoroptions = array();
$sql=<<<EOSQL
SELECT rd.resource_id, rd.name, r.amount from resourcedefs rd
INNER JOIN resources r ON r.resource_id = rd.resource_id AND r.nation_id = '{$_SESSION['nation_id']}'
WHERE rd.is_tradeable = 1 ORDER BY name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourceoptions[$rs['resource_id']]['resource_id'] = $rs['resource_id'];
    $resourceoptions[$rs['resource_id']]['name'] = $rs['name'];
    $resourceoptions[$rs['resource_id']]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
}
$sql = "SELECT rd.weapon_id, rd.name, r.amount from weapondefs rd INNER JOIN weapons r ON r.weapon_id = rd.weapon_id AND r.nation_id = '{$_SESSION['nation_id']}' ORDER BY name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $weaponoptions[$rs['weapon_id']]['resource_id'] = $rs['weapon_id'];
    $weaponoptions[$rs['weapon_id']]['name'] = $rs['name'];
    $weaponoptions[$rs['weapon_id']]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
}
$sql = "SELECT rd.armor_id, rd.name, r.amount from armordefs rd INNER JOIN armor r ON r.armor_id = rd.armor_id AND r.nation_id = '{$_SESSION['nation_id']}' ORDER BY name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $armoroptions[$rs['armor_id']]['resource_id'] = $rs['armor_id'];
    $armoroptions[$rs['armor_id']]['name'] = $rs['name'];
    $armoroptions[$rs['armor_id']]['optionslistname'] = $rs['name'] . " (Have {$rs['amount']})";
}
?>