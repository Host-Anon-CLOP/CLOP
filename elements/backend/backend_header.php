<?php
$sql=<<<EOSQL
SELECT message FROM topmessage
EOSQL;
$rs = onelinequery($sql);
$topmessage = htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8");
if ($_SESSION['user_id']) {
$sql = <<<EOSQL
SELECT COUNT(*) AS number FROM messages WHERE touser = {$_SESSION['user_id']} AND todeleted = '0' AND is_read = '0'
EOSQL;
$rs = onelinequery($sql);
if ($rs['number']) {
    $messagenumber = " ({$rs['number']})";
} else {
    $messagenumber = "";
}
$sql = <<<EOSQL
SELECT COUNT(*) AS number FROM allianceinvitations WHERE user_id = {$_SESSION['user_id']}
EOSQL;
$rs = onelinequery($sql);
if ($rs['number']) {
    $invitationnumber = " ({$rs['number']})";
} else {
    $invitationnumber = "";
}
if ($allianceinfo['alliance_id']) {
    $sql=<<<EOSQL
SELECT COUNT(*) AS number FROM alliance_messages am
LEFT JOIN markasread mr ON mr.user_id = {$_SESSION['user_id']} AND mr.message_id = am.message_id
WHERE am.alliance_id = {$allianceinfo['alliance_id']} AND mr.user_id IS NULL
EOSQL;
    $rs = onelinequery($sql);
    if ($rs['number']) {
        $alliancemessagenumber = " ({$rs['number']})";
    } else {
        $alliancemessagenumber = "";
    }
    $sql=<<<EOSQL
    SELECT COUNT(*) AS number FROM deals
	WHERE touser = {$_SESSION['user_id']} AND finalized = '1'
EOSQL;
	$rs = onelinequery($sql);
    if ($rs['number']) {
        $dealnumber = " ({$rs['number']})";
    } else {
        $dealnumber = "";
    }
    $sql=<<<EOSQL
	SELECT COUNT(attack_id) AS incoming FROM attacks
	WHERE defender = {$_SESSION['user_id']}
EOSQL;
    $userincoming = onelinequery($sql);
    $incomingnumber = $userincoming['incoming'];
    $sql=<<<EOSQL
	SELECT COUNT(attack_id) AS incoming FROM allianceattacks
	WHERE defender = {$userinfo['alliance_id']}
EOSQL;
    $allianceincoming = onelinequery($sql);
    $allianceincomingnumber = $allianceincoming['incoming'];
    $totalincomingnumber = $incomingnumber + $allianceincomingnumber;
    if (!$incomingnumber) $incomingnumber = "";
    if (!$allianceincomingnumber) $allianceincomingnumber = "";
    if (!$totalincomingnumber) $totalincomingnumber = "";
}
}
?>