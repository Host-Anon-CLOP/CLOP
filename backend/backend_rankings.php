<?php
include_once("allfunctions.php");
$mysql['page'] = (int)$_GET['page'];
if (!$mysql['page']) {
    $mysql['page'] = 1;
}
$nations = array();
$limit = ($mysql['page'] * 20) - 20;
if ($_GET['mode'] == "statues") {
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM nations n INNER JOIN users u ON u.user_id = n.user_id
INNER JOIN resources r ON r.nation_id = n.nation_id WHERE r.resource_id = '38' AND u.stasismode = 0 AND u.user_id != 1
EOSQL;
$sqlcount = onelinequery($sql);
    $mode = "statues";
    $sql=<<<EOSQL
SELECT u.flag, r.amount, n.nation_id, n.name, n.government, n.economy FROM nations n INNER JOIN users u ON u.user_id = n.user_id
INNER JOIN resources r ON r.nation_id = n.nation_id WHERE r.resource_id = '38' AND u.stasismode = 0 AND u.user_id != 1
ORDER BY amount DESC, nation_id ASC
LIMIT {$limit}, 20
EOSQL;
} else if ($_GET['mode'] == "longevity") {
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE u.stasismode = 0 AND u.user_id != 1
EOSQL;
$sqlcount = onelinequery($sql);
    $mode = "longevity";
    $sql=<<<EOSQL
SELECT u.flag, n.name, n.nation_id, n.government, n.economy, n.creationdate, n.age FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.stasismode = 0 AND u.user_id != 1
ORDER BY age DESC, creationdate ASC, nation_id ASC
LIMIT {$limit}, 20
EOSQL;
} else {
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE u.stasismode = 0 AND u.user_id != 1
EOSQL;
$sqlcount = onelinequery($sql);
    $mode = "gdp";
    $sql=<<<EOSQL
SELECT u.flag, n.name, n.nation_id, n.government, n.economy, n.gdp_last_turn FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.stasismode = 0 AND u.user_id != 1
ORDER BY gdp_last_turn DESC, nation_id ASC
LIMIT {$limit}, 20
EOSQL;
}
$numpages = ceil($sqlcount['count'] / 20);
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $rs['gdp_last_turn'] = commas($rs['gdp_last_turn']);
    $nations[] = $rs;
}
?>