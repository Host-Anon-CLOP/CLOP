<?php
include_once("allfunctions.php");
if ($_POST['search']) {
$mysql['name'] = $GLOBALS['mysqli']->real_escape_string($_POST['name']);
$sql=<<<EOSQL
SELECT nation_id FROM nations WHERE name = '{$mysql['name']}'
EOSQL;
$rs = onelinequery($sql);
if ($rs) {
	header("Location: viewnation.php?nation_id={$rs['nation_id']}");
	exit;
} else {
	$sql=<<<EOSQL
SELECT user_id FROM users WHERE username = '{$mysql['name']}'
EOSQL;
	$rs2 = onelinequery($sql);
	if ($rs2) {
		header("Location: viewuser.php?user_id={$rs2['user_id']}");
		exit;
	} else {
		$errors[] = "Name not found.";
	}
}
}
?>