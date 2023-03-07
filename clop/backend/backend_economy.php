<?php
include_once("allfunctions.php");
#$players_active_24h = "";
$sql=<<<EOSQL
SELECT COUNT(*) FROM users WHERE lastactive >= NOW() - INTERVAL 1 DAY
EOSQL;
$players_active_24h = onelinequery($sql);
$players_active_24h = $players_active_24h['COUNT(*)']
?>