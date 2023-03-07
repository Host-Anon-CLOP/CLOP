<?php
include_once("allfunctions.php");

# Players Activity
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

# Census
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations WHERE region = "3"
EOSQL;
$census_burrozil = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations WHERE region = "2"
EOSQL;
$census_zebrica = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations WHERE region = "1"
EOSQL;
$census_saddle = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations WHERE region = "4"
EOSQL;
$census_prze = onelinequery($sql)['COUNT(*)'];
?>