<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_myoffers"] == "") || ($_POST["token_myoffers"] != $_SESSION["token_myoffers"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_myoffers"] == "")) {
    $_SESSION["token_myoffers"] = sha1(rand() . $_SESSION["token_myoffers"]);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'marketplace'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
if (!$errors) {
	if ($_POST['remove']) {
    $mysql['marketplace_id'] = (int)$_POST['marketplace_id'];
    $sql=<<<EOSQL
	SELECT marketplace_id, offeredamount, multiplier, offereditem FROM marketplace
	WHERE user_id = {$_SESSION['user_id']} AND marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
	$item = onelinequery($sql);
		if (!$errors) {
        $plentyrecovered = $constants['plentynecessary'] * $item['multiplier'];
        addamount(33, $_SESSION['user_id'], $plentyrecovered);
		addamount($item['offereditem'], $_SESSION['user_id'], $item['offeredamount'] * $item['multiplier']);
		$sql=<<<EOSQL
		DELETE FROM marketplace WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Offer removed. You recovered your offered items and {$plentyrecovered} Plenty.";
		}
	}
}
$sql=<<<EOSQL
SELECT m.*, rd1.name AS offeredname, rd2.name AS apparentname, rd3.name AS requestedname,
		u1.username, u2.username AS apparentusername, u3.username AS unmasker
        FROM marketplace m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd2 ON rd2.resource_id = m.apparentitem
        INNER JOIN resourcedefs rd3 ON rd3.resource_id = m.requesteditem
		INNER JOIN users u1 ON u1.user_id = m.user_id
		LEFT JOIN users u2 ON u2.user_id = m.apparentuser_id
        LEFT JOIN users u3 ON u3.user_id = m.unmasker_id
		WHERE m.user_id = {$_SESSION['user_id']}
		ORDER BY m.priority DESC, m.multiplier DESC, m.apparentuser_id DESC

EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
if ($sth) {
	while ($rs = mysqli_fetch_array($sth)) {
		$offers[] = $rs;
	}
}
?>