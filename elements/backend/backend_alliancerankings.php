<?php
include_once("allfunctions.php");
$mysql['page'] = (int)$_GET['page'];
if (!$mysql['page']) {
    $mysql['page'] = 1;
}
$nations = array();
$limit = ($mysql['page'] * 20) - 20;
$_GET['mode'] = "production"; //for now
if ($_GET['mode'] == "production") {
    $mode = "production";
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM alliances a
EOSQL;
$sqlcount = onelinequery($sql);
    $sql=<<<EOSQL
SELECT a.name, a.alliance_id, COUNT(*) AS membercount, SUM(u.production) AS totalproduction
FROM alliances a
LEFT JOIN users u ON u.alliance_id = a.alliance_id AND u.stasismode = 0
GROUP BY a.alliance_id
ORDER BY totalproduction DESC, membercount DESC  
EOSQL;
}
$numpages = ceil($sqlcount['count'] / 20);
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $alliances[] = $rs;
}
?>