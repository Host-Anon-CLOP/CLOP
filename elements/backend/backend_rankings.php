<?php
include_once("allfunctions.php");
$mysql['page'] = (int)$_GET['page'];
if (!$mysql['page']) {
    $mysql['page'] = 1;
}
$nations = array();
$limit = ($mysql['page'] * 20) - 20;
if ($_GET['mode'] == "production") {
    $sql=<<<EOSQL
SELECT COUNT(*) AS count FROM users u INNER JOIN alliances a ON u.alliance_id = a.alliance_id
WHERE u.stasismode = 0
EOSQL;
$sqlcount = onelinequery($sql);
    $mode = "production";
    $sql=<<<EOSQL
SELECT u.production, u.user_id, u.tier, a.name AS alliancename, a.alliance_id, u.username
FROM users u
INNER JOIN alliances a ON u.alliance_id = a.alliance_id
WHERE u.stasismode = 0
ORDER BY u.production DESC, u.user_id DESC
LIMIT {$limit}, 20
EOSQL;
} else {
	$sql=<<<EOSQL
SELECT COUNT(*) AS count FROM users u
WHERE u.stasismode = 0 AND u.alliance_id = 0
EOSQL;
$sqlcount = onelinequery($sql);
    $mode = "unallied";
    $sql=<<<EOSQL
    SELECT u.user_id, u.username
	FROM users u
	WHERE u.stasismode = 0 AND u.alliance_id = 0
	ORDER BY u.user_id DESC
	LIMIT {$limit}, 20
EOSQL;
}
$numpages = ceil($sqlcount['count'] / 20);
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $users[] = $rs;
}
?>