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
$bankableresources[42] = "Security";
$bankableresources[56] = "Serenity";
if ($_POST && (($_POST["token_alliancebankresources"] == "") || ($_POST["token_alliancebankresources"] != $_SESSION["token_alliancebankresources"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_alliancebankresources"] == "")) {
    $_SESSION["token_alliancebankresources"] = sha1(rand() . $_SESSION["token_alliancebankresources"]);
}
if ($_POST) {
$mysql['resource_id'] = (int)$_POST['resource_id'];
$mysql['amount'] = (int)$_POST['amount'];
}
if ($_POST['bank']) {
    if (!hasability("alliancespendresources", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not bank your alliance's resources.";
	}
    if ($mysql['amount'] < 1) {
        $errors[] = "Enter an amount.";
    }
    if (!$bankableresources[$mysql['resource_id']]) { 
        $errors[] = "That resource is not bankable.";
    }
    if (!alliancehasamount($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount'])) {
        $errors[] = "Your alliance does not have that much {$bankableresources[$mysql['resource_id']]} to bank.";
    }
    if (!$errors) {
		allianceaddbanked($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount']);
        allianceaddamount($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount'] * -1);
        $infos[] = "Your alliance has banked {$mysql['amount']} {$bankableresources[$mysql['resource_id']]}.";
    }
}
if ($_POST['unbank'] || $_POST['unbankall']) {
    if (!hasability("alliancespendresources", $_SESSION['user_id']) && $allianceinfo['owner_id'] != $_SESSION['user_id']) {
		$errors[] = "You may not unbank your alliance's resources.";
	}
    if ($_POST['unbankall']) {
        $mysql['amount'] = allianceamountbanked($mysql['resource_id'], $allianceinfo['alliance_id']);
    } else if (!alliancehasbanked($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount'])) {
        $errors[] = "Your alliance does not have that much {$bankableresources[$mysql['resource_id']]} banked.";
    }
    if ($mysql['amount'] < 1) {
        $errors[] = "Enter an amount.";
    }
    if (!$errors) {
		allianceaddamount($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount']);
        allianceaddbanked($mysql['resource_id'], $allianceinfo['alliance_id'], $mysql['amount'] * -1);
        $infos[] = "Your alliance has unbanked {$mysql['amount']} {$bankableresources[$mysql['resource_id']]}.";
    }
}
$sql=<<<EOSQL
SELECT resource_id, amount FROM alliancebankedresources
WHERE alliance_id = {$allianceinfo['alliance_id']}
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $bankedresources[$rs2['resource_id']] = $rs2['amount'];
}
?>