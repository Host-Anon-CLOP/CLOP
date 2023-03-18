<?php
include_once("allfunctions.php");
$mysql['user_id'] = (int)$_GET['user_id'];
$sql=<<<EOSQL
SELECT username FROM users WHERE user_id = {$mysql['user_id']}
EOSQL;
$username = onelinequery($sql);
$sql = "SELECT * FROM ascendednations WHERE user_id = {$mysql['user_id']} ORDER BY date DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $nations[] = $rs;
}
$sql =<<<EOSQL
SELECT ar.amount, rd.name FROM ascendedresources ar
INNER JOIN resourcedefs rd ON rd.resource_id = ar.resource_id
WHERE ar.user_id = {$mysql['user_id']}
ORDER BY rd.name
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $resources[] = $rs;
}
$sql=<<<EOSQL
SELECT amount FROM ascendedresources WHERE user_id = '{$mysql['user_id']}' AND resource_id = 0
EOSQL;
$rs = onelinequery($sql);
$funds = $rs['amount'];
?>