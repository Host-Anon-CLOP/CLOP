<?php
include_once("allfunctions.php");
needsalliance();
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'war'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$sql = "SELECT * FROM resourcedefs";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resourcename[$rs['resource_id']] = $rs['name'];
}
if ($_POST && (($_POST["token_outgoing"] == "") || ($_POST["token_outgoing"] != $_SESSION["token_outgoing"]))) {
	$errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_outgoing"] == "")) {
	$_SESSION["token_outgoing"] = sha1(rand() . $_SESSION["token_outgoing"]);
}
if ($_POST['cancel'] && !$errors) {
    $mysql['attack_id'] = (int)$_POST['attack_id'];
    $sql=<<<EOSQL
	SELECT * FROM attacks
	WHERE attack_id = '{$mysql['attack_id']}'
	AND attacker = '{$_SESSION['user_id']}'
EOSQL;
	$thisattack = onelinequery($sql);
	if (!$thisattack['attacker']) {
		$errors[] = "This attack no longer exists.";
	} else if ($thisattack['uncancelable']) {
		$errors[] = "This attack has been redirected through Heroism or Malice and cannot be canceled.";
	}
	if (!hasamount(9, $_SESSION['user_id'], $constants["compassionfor{$thisattack['type']}"])) {
		$errors[] = "You don't have the Compassion to cancel this attack.";
	}
	if (!$errors) {
		addamount(9, $_SESSION['user_id'], $constants["compassionfor{$thisattack['type']}"] * -1);
		if ($thisattack['type'] == "burden") {
			addamount($thisattack['resource_id'], $_SESSION['user_id'], $thisattack['amount']);
			addamount(27, $_SESSION['user_id'], floor($constants['burdenrequired'] / 2));
		} else if ($thisattack['type'] == "corrupt") {
			addamount(53, $_SESSION['user_id'], floor($constants['corruptionrequired'] / 2));
		} else if ($thisattack['type'] == "brutal") {
			addamount(54, $_SESSION['user_id'], floor($constants['brutalityrequired'] / 2));
		} else if ($thisattack['type'] == "despair") {
			addamount(58, $_SESSION['user_id'], floor($constants['despairrequired'] / 2));
		} else if ($thisattack['type'] == "robbery") {
			addamount(23, $_SESSION['user_id'], floor($constants['robberyrequired'] / 2));
		}
		$sql=<<<EOSQL
		DELETE FROM attacks
		WHERE attack_id = '{$mysql['attack_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Attack canceled.";
	}
}
$sql=<<<EOSQL
SELECT a.*, u.username AS defendername FROM attacks a
INNER JOIN users u ON a.defender = u.user_id
WHERE a.attacker = '{$_SESSION['user_id']}'
ORDER BY a.ticks ASC, a.type ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$attacks[] = $rs;
}
?>