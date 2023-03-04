<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$attacks = array();
if (($nationinfo['government'] == "Decentralization" || $nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Transponyism") && $nationinfo['alliance_id']) {
	$sql=<<<EOSQL
SELECT SUM(f.size) AS totalsize, n.name AS attackername, n.nation_id AS attackerid, u.username AS attackeruser, u.user_id AS attackeruserid,
n2.name AS defendername, n2.nation_id AS defenderid, u2.username AS defenderuser, u2.user_id AS defenderuserid
FROM nations n
INNER JOIN forcegroups fg ON fg.nation_id = n.nation_id
INNER JOIN forces f ON fg.forcegroup_id = f.forcegroup_id
INNER JOIN users u ON u.user_id = n.user_id
INNER JOIN nations n2 ON (fg.destination_id = n2.nation_id AND departuredate IS NOT NULL)
INNER JOIN users u2 ON u2.user_id = n2.user_id
WHERE fg.attack_mission = 1
AND u2.alliance_id = '{$nationinfo['alliance_id']}'
GROUP BY n.nation_id, n2.nation_id
ORDER BY totalsize DESC
EOSQL;
	$sth = $GLOBALS['mysqli']->query($sql);
	while ($rs = mysqli_fetch_array($sth)) {
		$attacks[] = $rs;
    }
}
?>