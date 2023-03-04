<?php
include_once("allfunctions.php");
needsalliance();
$sql=<<<EOSQL
SELECT name, value
FROM constants
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$bankableresources[7] = "Unity";
$bankableresources[26] = "Heroism";
$bankableresources[42] = "Security";
$bankableresources[47] = "Lies";
$bankableresources[56] = "Serenity";
$bankableresources[61] = "Treachery";
if ($_POST && (($_POST["token_bankresources"] == "") || ($_POST["token_bankresources"] != $_SESSION["token_bankresources"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_bankresources"] == "")) {
    $_SESSION["token_bankresources"] = sha1(rand() . $_SESSION["token_bankresources"]);
}
if ($_POST) {
$mysql['resource_id'] = (int)$_POST['resource_id'];
$mysql['amount'] = (int)$_POST['amount'];
}
if ($_POST['bank']) {
    if ($mysql['amount'] < 1) {
        $errors[] = "Enter an amount.";
    }
    if (!$bankableresources[$mysql['resource_id']]) { 
        $errors[] = "That resource is not bankable.";
    }
    if (!hasamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'])) {
        $errors[] = "You do not have that much {$bankableresources[$mysql['resource_id']]} to bank.";
    }
    if (!$errors) {
		addbanked($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount']);
        addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * -1);
        $infos[] = "You have banked {$mysql['amount']} {$bankableresources[$mysql['resource_id']]}.";
    }
}
if ($_POST['unbank'] || $_POST['unbankall']) {
    if ($_POST['unbankall']) {
        $mysql['amount'] = amountbanked($mysql['resource_id'], $_SESSION['user_id']);
    } else if (!hasbanked($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'])) {
        $errors[] = "You do not have that much {$bankableresources[$mysql['resource_id']]} banked.";
    }
    if ($mysql['amount'] < 1) {
        $errors[] = "Enter an amount.";
    }
    if (!$errors) {
		addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount']);
        addbanked($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * -1);
        $infos[] = "You have unbanked {$mysql['amount']} {$bankableresources[$mysql['resource_id']]}.";
    }
}
$sql=<<<EOSQL
SELECT resource_id, amount FROM bankedresources
WHERE user_id = {$_SESSION['user_id']}
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $bankedresources[$rs2['resource_id']] = $rs2['amount'];
}
?>