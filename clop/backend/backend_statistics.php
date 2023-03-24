<?php
include_once("allfunctions.php");

# New Players
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations WHERE creationdate >= NOW() - INTERVAL 1 DAY;
EOSQL;
$nations_new_24h = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations WHERE creationdate >= NOW() - INTERVAL 7 DAY;
EOSQL;
$nations_new_week = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations WHERE creationdate >= NOW() - INTERVAL 30 DAY;
EOSQL;
$nations_new_month = onelinequery($sql)['COUNT(*)'];


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
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "3" AND u.stasismode = 0;
EOSQL;
$census_burrozil = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "2" AND u.stasismode = 0;
EOSQL;
$census_zebrica = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "1" AND u.stasismode = 0;
EOSQL;
$census_saddle = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "4" AND u.stasismode = 0;
EOSQL;
$census_prze = onelinequery($sql)['COUNT(*)'];


# Global Resources
$affectedresources = array();
$requiredresources = array();
$resources = array();

$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS affected
FROM resourceeffects rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
INNER JOIN nations n ON r.nation_id = n.nation_id
INNER JOIN users u ON n.user_id = u.user_id
WHERE u.stasismode = 0
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $affectedresources[$rs['name']] = $rs['affected'];
}

$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS required
FROM resourcerequirements rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
INNER JOIN nations n ON r.nation_id = n.nation_id
INNER JOIN users u ON n.user_id = u.user_id
WHERE u.stasismode = 0
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $requiredresources[$rs['name']] = $rs['required'];
}
?>