<?php
$sql=<<<EOSQL
SELECT message FROM topmessage ORDER BY message_id DESC LIMIT 1
EOSQL;
$rs = onelinequery($sql);
$topmessage = htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8");
if ($_SESSION['user_id']) {
$sql = <<<EOSQL
SELECT COUNT(*) AS number FROM messages WHERE touser = {$_SESSION['user_id']} AND todeleted = 0 AND (is_read = 0 OR message_id > (SELECT user_lastread FROM users WHERE user_id = '{$_SESSION['user_id']}') )
EOSQL;
$rs = onelinequery($sql);
if ($rs['number']) {
    $messagenumber = " ({$rs['number']})";
    $totalmsgcount = $rs['number'];
} else {
    $messagenumber = "";
}
$sql=<<<EOSQL
UPDATE users SET lastactive = NOW() WHERE user_id = {$_SESSION['user_id']}
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql = <<<EOSQL
SELECT COUNT(*) AS pollcount FROM requests WHERE visible = '1' AND voteable = '1' AND request_id NOT IN (SELECT poll_id FROM votes WHERE user_id = '{$_SESSION['user_id']}')
EOSQL;
$activepolls = onelinequery($sql);
$pollcount = ($activepolls['pollcount'] > 0) ? '('.$activepolls['pollcount'].')' : '';
$sql = <<<EOSQL
SELECT COUNT(*) AS amsgc FROM alliance_messages WHERE alliance_id = (SELECT alliance_id FROM users WHERE user_id = '{$_SESSION['user_id']}') AND message_id > (SELECT alliance_lastread FROM users WHERE user_id = '{$_SESSION['user_id']}')
EOSQL;
$rs2 = onelinequery($sql);
if ($rs2['amsgc']) {
	$alliancemsgcount = " (".$rs2['amsgc'].")";
	$totalmsgcount = " (".($totalmsgcount + $rs2['amsgc']).")";
} else {
	$alliancemsgcount = "";
	$totalmsgcount = ($totalmsgcount > 0) ? " (".($totalmsgcount + $rs2['amsgc']).")" : "";
}


if ($_SESSION['nation_id']) {
$sql = <<<EOSQL
SELECT COUNT(*) AS number FROM deals WHERE tonation = {$_SESSION['nation_id']} AND finalized = '1'
EOSQL;
$rs = onelinequery($sql);
if ($rs['number']) {
    $dealnumber = " ({$rs['number']})";
} else {
    $dealnumber = "";
}

$sql = <<<EOSQL
SELECT COUNT(*) AS number FROM forcegroups WHERE destination_id = {$_SESSION['nation_id']} AND attack_mission = 1 AND departuredate IS NOT NULL
EOSQL;
$rs = onelinequery($sql);
if ($rs['number']) {
    $incomingnumber = " ({$rs['number']})";
} else {
    $incomingnumber = "";
}
    $sql=<<<EOSQL
    SELECT nation_id, name FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);
    while ($rs = mysqli_fetch_array($sth)) {
	$headernationlist[$rs['nation_id']] = $rs['name'];
	}
	$nationname = $headernationlist[$_SESSION['nation_id']];
}
}
?>