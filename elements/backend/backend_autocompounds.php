<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_autocompounds"] == "") || ($_POST["token_autocompounds"] != $_SESSION["token_autocompounds"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_autocompounds"] == "")) {
    $_SESSION["token_autocompounds"] = sha1(rand() . $_SESSION["token_autocompounds"]);
}
if (!$errors) {
if ($_POST['autocompound']) {
	$mysql['amount'] = (int)$_POST['amount'];
	$mysql['resource_id'] = (int)$_POST['resource_id'];
	if ($mysql['resource_id'] < 1 || $mysql['resource_id'] > 63) {
		$errors[] = "Select a compound.";
	} else {
        $sql=<<<EOSQL
SELECT tier FROM resourcedefs WHERE resource_id = '{$mysql['resource_id']}'
EOSQL;
		$compoundinfo = onelinequery($sql);
	if (!$mysql['amount']) {
		$errors[] = "Enter an amount.";
	}
	if ($compoundinfo['tier'] < 2) {
		$errors[] = "There's no point to compounding tiers below 2.";
	}
	if ($compoundinfo['tier'] > $userinfo['tier']) {
		$errors[] = "You do not have a high enough tier to automatically compound that element.";
    }
	}
	$sql=<<<EOSQL
	SELECT SUM(amount) AS totalamount FROM autocompounds WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
	$rs = onelinequery($sql);
	if ($rs['totalamount'] + $mysql['amount'] > $userinfo['production']) {
		$errors[] = "You cannot automatically compound more than your production each turn.";
	}
    if (!$errors) {
        $sql=<<<EOSQL
		INSERT INTO autocompounds SET amount = '{$mysql['amount']}', resource_id = '{$mysql['resource_id']}', user_id = '{$_SESSION['user_id']}'
		ON DUPLICATE KEY UPDATE amount = amount + '{$mysql['amount']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
    }
}
if ($_POST['remove']) {
	$mysql['resource_id'] = (int)$_POST['resource_id'];
	$sql=<<<EOSQL
	DELETE FROM autocompounds WHERE resource_id = '{$mysql['resource_id']}' AND user_id = '{$_SESSION['user_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
}
}
$sql=<<<EOSQL
SELECT rd.name, ac.resource_id, ac.amount FROM autocompounds ac
INNER JOIN resourcedefs rd ON rd.resource_id = ac.resource_id
WHERE ac.user_id = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$compounds[] = $rs;
}
?>