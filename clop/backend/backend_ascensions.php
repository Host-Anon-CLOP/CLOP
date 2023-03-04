<?php
include_once("allfunctions.php");
$mysql['page'] = (int)$_GET['page'];
if (!$mysql['page']) {
    $mysql['page'] = 1;
}
$nations = array();
$limit = ($mysql['page'] * 20) - 20;
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM ascendednations
EOSQL;
$sqlcount = onelinequery($sql);
    $sql=<<<EOSQL
SELECT an.user_id, an.name, an.date, u.username FROM ascendednations an INNER JOIN users u ON u.user_id = an.user_id
ORDER BY an.date DESC LIMIT {$limit}, 20
EOSQL;
$numnations = $sqlcount['count'];
$numpages = ceil($sqlcount['count'] / 20);
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $nations[] = $rs;
}
?>