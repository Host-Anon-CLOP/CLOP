<?php
include_once("allfunctions.php");
$players_active_24h = "";
$sql=<<<EOSQL
SELECT COUNT(*) FROM users WHERE lastactive >= NOW() - INTERVAL 1 DAY
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
#$rs = onelinequery($sth);
#$players_active_24h = $rs['COUNT(*)'];
$players_active_24h = "test"
?>