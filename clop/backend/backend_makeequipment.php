<?php
include_once("allfunctions.php");
needsnation();
$getpost = array_merge($_GET, $_POST);
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
if ($getpost['mode'] != "weapons" && $getpost['mode'] != "armor") {
	header("Location: overview.php");
	exit;
}
//SIEG GRAMMAR
if ($getpost['mode'] == "weapons") {
	$mode = "weapons";
	$plural = "weapons";
	$singular = "weapon";
} else if ($getpost['mode'] == "armor") {
	$mode = "armor";
	$plural = "armor";
	$singular = "armor";
}
$times = (int)$mysql['times'];
$thingstodo = array();
$nationinfo = onelinequery("SELECT * FROM nations WHERE nation_id = '{$_SESSION['nation_id']}'");
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval");
$displayfunds = commas($nationinfo['funds']);
$sql = "SELECT wr.name, wr.{$singular}recipe_id, wr.description, wr.cost, wd.type FROM {$singular}recipes wr
LEFT JOIN {$singular}defs wd ON wd.{$singular}_id = wr.{$singular}_id ORDER BY wd.type, wr.cost, wr.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $availablemakeequip[$rs["{$singular}recipe_id"]] = $rs;
    $availablemakeequip[$rs["{$singular}recipe_id"]]['displaycost'] = commas($availablemakeequip[$rs["{$singular}recipe_id"]]['cost']);
	$thisrecipe_id = $rs["{$singular}recipe_id"];
    $sql =<<<EOSQL
	SELECT rd.name, wri.is_used_up, wri.amount FROM {$singular}recipeitems wri
	INNER JOIN resourcedefs rd ON rd.resource_id = wri.resource_id WHERE wri.{$singular}recipe_id = '{$thisrecipe_id}' ORDER BY is_used_up DESC
EOSQL;
	$sth2 = $GLOBALS['mysqli']->query($sql);
	while ($rs2 = mysqli_fetch_array($sth2)) {
		$availablemakeequip[$rs["{$singular}recipe_id"]]['displayitems'][] = $rs2;
	}
}
if ($_POST && (($_POST["token_make{$plural}"] == "") || ($_POST["token_make{$plural}"] != $_SESSION["token_make{$plural}"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_make{$plural}"] == "")) {
    $_SESSION["token_make{$plural}"] = sha1(rand() . $_SESSION["token_make{$plural}"]);
}
if (!$errors) {
if($_POST["{$singular}recipe_id"]) {
	$rightrecipe_id = $mysql["{$singular}recipe_id"];
	if ($times < 1) {
		$errors[] = "Whole numbers greater than 0.";
	}
	$rs3 = onelinequery("SELECT w.*, wd.name AS {$singular}name FROM {$singular}recipes w
	LEFT JOIN {$singular}defs wd ON w.{$singular}_id = wd.{$singular}_id WHERE w.{$singular}recipe_id = {$rightrecipe_id}");
	$cost = $rs3['cost'];
	$rs3['amount'] = $rs3['amount'] * $times;
	$cost = $cost * $times;
	if ($nationinfo['funds'] < $cost) {
		$errors[] = "You don't have enough money to build that.";
	}
	$infostoadd = array();
	if (empty($errors)) {
		$sql = "SELECT rd.is_building, ri.resource_id, ri.is_used_up, ri.amount, rd.name FROM {$singular}recipeitems ri
		LEFT JOIN resourcedefs rd ON (rd.resource_id = ri.resource_id) WHERE ri.{$singular}recipe_id = {$rightrecipe_id}";
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
			$rightid = $rs3["{$singular}_id"];
			if ($rightid) {
				$sql = "INSERT INTO {$plural}(nation_id, {$singular}_id, amount) VALUES ({$_SESSION['nation_id']},
				{$rightid}, {$rs3['amount']}) ON DUPLICATE KEY UPDATE amount = amount + {$rs3['amount']}";
				$GLOBALS['mysqli']->query($sql);
				$thisname = $rs3["{$singular}name"];
				$infos[] = "You gained {$rs3['amount']} {$thisname}.";
			}
			if ($cost) {
				$formatcost = commas($cost);
				$sql = "UPDATE nations SET funds = funds - {$cost} WHERE nation_id = {$_SESSION['nation_id']}";
				$GLOBALS['mysqli']->query($sql);
				$infos[] = "You paid {$formatcost} bits.";
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
?>