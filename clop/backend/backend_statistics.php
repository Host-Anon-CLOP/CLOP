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
WHERE n.region = "3" AND n.subregion = "1" AND u.stasismode = 0;
EOSQL;
$census_burrozil_north = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "2" AND n.subregion = "1" AND u.stasismode = 0;
EOSQL;
$census_zebrica_north = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "1" AND n.subregion = "1" AND u.stasismode = 0;
EOSQL;
$census_saddle_north = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "4" AND n.subregion = "1" AND u.stasismode = 0;
EOSQL;
$census_prze_north = onelinequery($sql)['COUNT(*)'];

$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "3" AND n.subregion = "2" AND u.stasismode = 0;
EOSQL;
$census_burrozil_central = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "2" AND n.subregion = "2" AND u.stasismode = 0;
EOSQL;
$census_zebrica_central = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "1" AND n.subregion = "2" AND u.stasismode = 0;
EOSQL;
$census_saddle_central = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "4" AND n.subregion = "2" AND u.stasismode = 0;
EOSQL;
$census_prze_central = onelinequery($sql)['COUNT(*)'];

$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "3" AND n.subregion = "3" AND u.stasismode = 0;
EOSQL;
$census_burrozil_south = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "2" AND n.subregion = "3" AND u.stasismode = 0;
EOSQL;
$census_zebrica_south = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "1" AND n.subregion = "3" AND u.stasismode = 0;
EOSQL;
$census_saddle_south = onelinequery($sql)['COUNT(*)'];
$sql=<<<EOSQL
SELECT COUNT(*) FROM nations n
INNER JOIN users u ON n.user_id = u.user_id
WHERE n.region = "4" AND n.subregion = "3" AND u.stasismode = 0;
EOSQL;
$census_prze_south = onelinequery($sql)['COUNT(*)'];

$census_burrozil_total = $census_burrozil_north + $census_burrozil_central + $census_burrozil_south;
$census_saddle_total = $census_saddle_north + $census_saddle_central + $census_saddle_south;
$census_zebrica_total = $census_zebrica_north + $census_zebrica_central + $census_zebrica_south;
$census_prze_total = $census_prze_north + $census_prze_central + $census_prze_south;


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

# Add resources used by government type
$sql = "SELECT n.government, count(n.government) AS count
FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.stasismode = 0
GROUP BY n.government";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['government'] == "Democracy") {
        $requiredresources["Gasoline"] += (20 * $rs['count']);
        $requiredresources["Vehicle Parts"] += (2 * $rs['count']);
    } else if ($rs['government'] == "Repression") {
        $requiredresources["Gasoline"] += (10 * $rs['count']);
    } else if ($rs['government'] == "Independence") {
        $requiredresources["Gasoline"] += (40 * $rs['count']);
        $requiredresources["Vehicle Parts"] += (4 * $rs['count']);
    } else if ($rs['government'] == "Decentralization") {
        $requiredresources["Gasoline"] += (50 * $rs['count']);
        $requiredresources["Vehicle Parts"] += (5 * $rs['count']);
    } else if ($rs['government'] == "Authoritarianism") {
        $requiredresources["Gasoline"] += (10 * $rs['count']);
        $requiredresources["Machinery Parts"] += (3 * $rs['count']);
    } else if ($rs['government'] == "Oppression") {
        $requiredresources["Gasoline"] += (10 * $rs['count']);
        $requiredresources["Machinery Parts"] += (5 * $rs['count']);
    }
}

# Add resources used by economy type
$sql = "SELECT n.economy, count(n.economy) AS count
FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.stasismode = 0
GROUP BY n.economy";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['economy'] == "Free Market") {
        $requiredresources["Coffee"] += (6 * $rs['count']);
    } else if ($rs['economy'] == "State Controlled") {
        $requiredresources["Cider"] += (6 * $rs['count']);
    }
}
?>