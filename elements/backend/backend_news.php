<?php
include_once("allfunctions.php");
$mysql['page'] = (int)$_GET['page'];
if (!$mysql['page']) {
    $mysql['page'] = 1;
}
$allnews = array();
$limit = ($mysql['page'] * 20) - 20;
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM news
EOSQL;
$sqlcount = onelinequery($sql);
    $mode = "gdp";
    $sql=<<<EOSQL
SELECT message, posted FROM news ORDER BY posted DESC LIMIT {$limit}, 20
EOSQL;
$numnews = $sqlcount['count'];
$numpages = ceil($sqlcount['count'] / 20);
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $allnews[] = $rs;
}
?>