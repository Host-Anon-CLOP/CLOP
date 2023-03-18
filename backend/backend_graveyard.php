<?php
include_once("allfunctions.php");
$mysql['page'] = (int)$_GET['page'];
if (!$mysql['page']) {
    $mysql['page'] = 1;
}
$nations = array();
$limit = ($mysql['page'] * 20) - 20;
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM graveyard
EOSQL;
$sqlcount = onelinequery($sql);
    $mode = "gdp";
    $sql=<<<EOSQL
SELECT graveyard_id, name, killer, deathdate FROM graveyard ORDER BY graveyard_id DESC LIMIT {$limit}, 20
EOSQL;
$numnations = $sqlcount['count'];
$numpages = ceil($sqlcount['count'] / 20);
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $nations[] = $rs;
}
?>