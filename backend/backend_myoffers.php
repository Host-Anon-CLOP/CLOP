<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$getpost = array_merge($_GET, $_POST);
foreach ($getpost as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
if ($_POST && (($_POST["token_myoffers"] == "") || ($_POST["token_myoffers"] != $_SESSION["token_myoffers"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_myoffers"] == "")) {
    $_SESSION["token_myoffers"] = sha1(rand() . $_SESSION["token_myoffers"]);
}
$buyer = false;
if (($_POST['removesellitems'] || $_POST['removesellarmor'] || $_POST['removesellweapons'] || 
$_POST['removebuyitems'] || $_POST['removebuyarmor'] || $_POST['removebuyweapons']) && !$errors) {
	if ($_POST['removesellitems']) {
		$marketplace = "marketplace";
		$resource_id = "resource_id";
		$resourcedefs = "resourcedefs";
		$resourcesname = "resources";
	} else if ($_POST['removesellarmor']) {
		$marketplace = "armormarketplace";
		$resource_id = "armor_id";
		$resourcedefs = "armordefs";
		$resourcesname = "armor";
	} else if ($_POST['removesellweapons']) {
		$marketplace = "weaponsmarketplace";
		$resource_id = "weapon_id";
		$resourcedefs = "weapondefs";
		$resourcesname = "weapons";
	} else if ($_POST['removebuyitems']) {
		$buyer = true;
		$marketplace = "buyermarketplace";
		$resource_id = "resource_id";
		$resourcedefs = "resourcedefs";
		$resourcesname = "resources";
	} else if ($_POST['removebuyarmor']) {
		$buyer = true;
		$marketplace = "armorbuyermarketplace";
		$resource_id = "armor_id";
		$resourcedefs = "armordefs";
		$resourcesname = "armor";
	} else if ($_POST['removebuyweapons']) {
		$buyer = true;
		$marketplace = "weaponsbuyermarketplace";
		$resource_id = "weapon_id";
		$resourcedefs = "weapondefs";
		$resourcesname = "weapons";
	}
	$sql = "SELECT m.price, m.amount, rd.name FROM {$marketplace} m INNER JOIN {$resourcedefs} rd ON m.{$resource_id} = rd.{$resource_id}
	WHERE m.nation_id = '{$_SESSION['nation_id']}' AND m.{$resource_id} = '{$mysql['resource_id']}' AND m.price = '{$mysql['price']}'";
	$rs = onelinequery($sql);
	if (!$rs) {
		$errors[] = "Eeeeenope.";
	} else {
		$sql = "DELETE FROM {$marketplace} WHERE nation_id = '{$_SESSION['nation_id']}' AND {$resource_id} = '{$mysql['resource_id']}' AND price = '{$mysql['price']}'";
		$GLOBALS['mysqli']->query($sql);
		if ($buyer) {
            $returnfunds = floor(getbuyingmultiplier($_SESSION['nation_id']) * $rs['price'] * $rs['amount']);
			$nationinfo['funds'] += $returnfunds;
            $sql=<<<EOSQL
			UPDATE nations SET funds = funds + {$returnfunds} WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
            $GLOBALS['mysqli']->query($sql);
            $infos[] = "You have removed your request of {$rs['amount']} {$rs['name']} from the market.";
		} else {
            $sql = "INSERT INTO {$resourcesname} (nation_id, {$resource_id}, amount) VALUES ({$_SESSION['nation_id']}, {$mysql['resource_id']}, {$rs['amount']}) ON DUPLICATE KEY UPDATE amount = amount + {$rs['amount']}";
            $GLOBALS['mysqli']->query($sql);
            $infos[] = "You have removed {$rs['amount']} {$rs['name']} from the market.";
		}
	}
}
$buyeritems = array();
$selleritems = array();
$buyerarmor = array();
$sellerarmor = array();
$buyerweapons = array();
$sellerweapons = array();
$sql =<<<EOSQL
SELECT m.*, rd.name FROM buyermarketplace m
INNER JOIN nations n ON n.nation_id = m.nation_id
INNER JOIN resourcedefs rd ON rd.resource_id = m.resource_id
WHERE n.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$buyeritems[] = $rs;
	}
}
$sql =<<<EOSQL
SELECT m.*, rd.name FROM armorbuyermarketplace m
INNER JOIN nations n ON n.nation_id = m.nation_id
INNER JOIN armordefs rd ON rd.armor_id = m.armor_id
WHERE n.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$rs['resource_id'] = $rs['armor_id'];
		$buyerarmor[] = $rs;
	}
}
$sql =<<<EOSQL
SELECT m.*, rd.name FROM weaponsbuyermarketplace m
INNER JOIN nations n ON n.nation_id = m.nation_id
INNER JOIN weapondefs rd ON rd.weapon_id = m.weapon_id
WHERE n.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$rs['resource_id'] = $rs['weapon_id'];
		$buyerweapons[] = $rs;
	}
}
$sql =<<<EOSQL
SELECT m.*, rd.name FROM marketplace m
INNER JOIN nations n ON n.nation_id = m.nation_id
INNER JOIN resourcedefs rd ON rd.resource_id = m.resource_id
WHERE n.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$selleritems[] = $rs;
	}
}
$sql =<<<EOSQL
SELECT m.*, rd.name FROM armormarketplace m
INNER JOIN nations n ON n.nation_id = m.nation_id
INNER JOIN armordefs rd ON rd.armor_id = m.armor_id
WHERE n.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$rs['resource_id'] = $rs['armor_id'];
		$sellerarmor[] = $rs;
	}
}
$sql =<<<EOSQL
SELECT m.*, rd.name FROM weaponsmarketplace m
INNER JOIN nations n ON n.nation_id = m.nation_id
INNER JOIN weapondefs rd ON rd.weapon_id = m.weapon_id
WHERE n.nation_id = '{$_SESSION['nation_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$rs['resource_id'] = $rs['weapon_id'];
		$sellerweapons[] = $rs;
	}
}
?>