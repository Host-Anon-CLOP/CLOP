<?php
include_once("allfunctions.php");

$sql=<<<EOSQL
SELECT COUNT(*) FROM users WHERE lastactive >= NOW() - INTERVAL 1 DAY
EOSQL;
$players_active_24h = onelinequery($sql)['COUNT(*)'];

$sql=<<<EOSQL
SELECT COUNT(*) FROM users WHERE lastactive >= NOW() - INTERVAL 7 DAY
EOSQL;
$players_active_week = onelinequery($sql)['COUNT(*)'];

$sql=<<<EOSQL
SELECT COUNT(*) FROM users WHERE lastactive >= NOW() - INTERVAL 30 DAY
EOSQL;
$players_active_month = onelinequery($sql)['COUNT(*)'];
?>