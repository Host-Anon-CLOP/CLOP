<?php
include_once('allfunctions.php');

//caching stuff
/*
if (isset($_POST['nocache']) || $_SERVER['HTTP_IF_NONE_MATCH'] == 'abcdef') {
	header('HTTP/1.1 304 Not Modified');
}
header('Cache-Control: private; max-age=0; must-revalidate, post-check=0, pre-check=0');
header('Etag: abcdef');
header('Pragma:');
header('Last-Modified: Tue, 05 Jul 2015 19:15:59 GMT');
header('Expires: Tue, 05 Jul 2016 22:15:59 GMT');
?>
<form action="stats.php" method="post">
<input type="submit" name="nocache" value="submit">
</form>
<?php
*/

$totals = array('resources' => array(), 'buildings' => array());
$display = array('resources' => array(), 'buildings' => array());
$nationstats = array();
$subregionstats = array();
$averages = array();
$regiontypes = array(1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
$subregiontypes = array(1 => "North ", 2 => "Central ", 3 => "South ");

//this may or may not be faster than COUNT(*)
$sql = "SELECT MAX(nation_id) AS maxid FROM nations";
$sth = onelinequery($sql);
$totals['evernations'] = $sth['maxid'];
$sql = "SELECT MAX(user_id) AS maxuid FROM users";
$sth = onelinequery($sql);
$totals['everusers'] = $sth['maxuid'];
$sql = "SELECT MAX(alliance_id) AS maxaid FROM alliances";
$sth = onelinequery($sql);
$totals['everalliances'] = $sth['maxaid'];

$sql = <<<EOSQL
SELECT * FROM (SELECT n.region, COUNT(*) AS nation_count, COUNT(DISTINCT u.user_id) AS user_count FROM nations n INNER JOIN users u ON n.user_id = u.user_id WHERE u.stasismode = '0' GROUP BY n.region WITH ROLLUP) AS tab ORDER BY region ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
//rollup row is the first in set
$rs = $sth->fetch_array();
$totals['users'] = $rs['user_count'];
$totals['nations'] = $rs['nation_count'];
while ($rs = $sth->fetch_array()) {
	$nationstats[$rs['region']] = array();
	$nationstats[$rs['region']]['nations'] = $rs['nation_count'];
	$nationstats[$rs['region']]['users'] = $rs['user_count'];
}

//I hate to do this, but without the window functions this seems like the only option
//only option besides an if() inside the loop above
$sql = <<<EOSQL
SELECT n.subregion, COUNT(*) AS subregioncount, COUNT(DISTINCT u.user_id) AS usercount FROM nations n INNER JOIN users u ON n.user_id = u.user_id WHERE u.stasismode = '0' GROUP BY n.subregion
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = $sth->fetch_array()) {
	$subregionstats[$rs['subregion']]['nations'] = $rs['subregioncount'];
	$subregionstats[$rs['subregion']]['users'] = $rs['usercount'];
}

//$sql = "SELECT stasismode, COUNT(stasismode) AS stasiscount FROM users GROUP BY stasismode ASC";
$sql = "SELECT COUNT(*) AS stasiscount FROM users WHERE stasismode = '1'";
$sth = onelinequery($sql);
$totals['stasiscount'] = $sth['stasiscount'];

$sql = <<<EOSQL
SELECT SUM(alliance_id = 0) AS nonallied, COUNT(DISTINCT CASE WHEN alliance_id > 0 THEN alliance_id ELSE null END) AS alliancecount FROM users WHERE stasismode = '0'
EOSQL;
$sth = onelinequery($sql);
$totals['alliances'] = $sth['alliancecount'];
$totals['nonallied'] = $sth['nonallied'];

$averages['playernations'] = round($totals['nations'] / $totals['users'], 3);

$sql = <<<EOSQL
SELECT rd.resource_id, rd.name, IFNULL(SUM(r.amount), 0) AS total, 
IFNULL(SUM(r.disabled >= 1), 0) AS disabled, rd.is_building,
SUM(r.amount * e.amount) AS produced, e.affectedresource_id AS affected_rid
FROM resources r RIGHT JOIN resourcedefs rd ON r.resource_id = rd.resource_id
LEFT JOIN resourceeffects e ON rd.resource_id = e.resource_id
WHERE rd.resource_id <> '81'
GROUP BY rd.resource_id ORDER BY rd.is_building ASC, rd.name ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
//always 79 rows
//31 resources
//48 buildings
//if ($sth->num_rows != 79) die('Wrong number of rows');

for ($i = 0; $i < 31; ++$i) {
	$rs = $sth->fetch_array();
	//consistent index in both arrays
	$totals['resources'][$rs['resource_id']] = $rs;
	$totals['resources'][$rs['resource_id']]['produced'] = 0;
	$display['resources'][$rs['resource_id']] = commas($rs['total']);
}
for ($i = 0; $i < 48; ++$i) {
	$rs = $sth->fetch_array();
	$totals['buildings'][$rs['resource_id']] = $rs;
	$totals['resources'][$rs['affected_rid']]['produced'] += $rs['produced'];
	$display['buildings'][$rs['resource_id']] = array('total' => commas($rs['total']), 'disabled' => commas($rs['disabled']));
}

$sql = "SELECT SUM(funds) AS totalbits, SUM(gdp_last_turn) AS production FROM nations WHERE user_id <> '1'";
$sth = onelinequery($sql);
$totals['bits'] = $sth['totalbits'];
$totals['gdp'] = $sth['production'];
$display['totalbits'] = commas($totals['bits']);
$display['totalgdp'] = commas($totals['gdp']);
