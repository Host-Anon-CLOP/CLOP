<?php
include_once("allfunctions.php");
$mysql['page'] = (int)$_GET['page'];
if (!$mysql['page']) {
    $mysql['page'] = 1;
}
$users = array();
$limit = ($mysql['page'] * 20) - 20;
$sql=<<<EOSQL
SELECT COUNT(DISTINCT u.user_id) AS count FROM users u
INNER JOIN nations n ON u.user_id = n.user_id
WHERE u.stasismode = 0
EOSQL;
$sqlcount = onelinequery($sql);
$sql=<<<EOSQL
SELECT u.flag, u.user_id, u.username, COUNT(n.nation_id) AS nationcount, SUM(n.gdp_last_turn) as totalgdp
FROM users u
INNER JOIN nations n ON u.user_id = n.user_id
WHERE u.stasismode = 0
GROUP BY u.user_id
ORDER BY nationcount DESC, totalgdp DESC
LIMIT {$limit}, 20
EOSQL;
$numpages = ceil($sqlcount['count'] / 20);
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $rs['totalgdp'] = commas($rs['totalgdp']);
    $users[] = $rs;
}
?>