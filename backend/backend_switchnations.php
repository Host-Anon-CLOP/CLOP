<?php
include_once("allfunctions.php");
needsnation();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$mysql['nation_id'] = (int)$_POST['nation_id'];
if ($_POST && (($_POST['token_switchnations'] == "") || ($_POST['token_switchnations'] != $_SESSION['token_switchnations']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_switchnations'] == "")) {
    $_SESSION['token_switchnations'] = sha1(rand() . $_SESSION['token_switchnations']);
}
if (!$errors) {
    if ($_POST['delete']) {
		$sql=<<<EOSQL
        SELECT u.username, n.* FROM nations n
        INNER JOIN users u ON n.user_id = n.user_id
        WHERE u.user_id = '{$_SESSION['user_id']}' AND n.nation_id = '{$mysql['nation_id']}' AND n.nation_id != '{$_SESSION['nation_id']}'
EOSQL;
        $rs = onelinequery($sql);
        if ($rs['nation_id']) {
            if (strtotime($rs['creationdate']) < time() - 2419200) {
			$resourceslist = "";
			$buildingslist = "";
			$reportslist = "";
			$sql=<<<EOSQL
			SELECT rd.name, r.amount FROM resources r INNER JOIN resourcedefs rd ON rd.resource_id = r.resource_id WHERE rd.is_building = 0 AND r.nation_id = {$rs['nation_id']}
EOSQL;
			$sth2 = $GLOBALS['mysqli']->query($sql);
			while ($rs2 = mysqli_fetch_array($sth2)) {
				$resourceslist .=<<<EOFORM
<td>{$rs2['name']}</td><td>{$rs2['amount']}</td></tr>
EOFORM;
				}
			$sql=<<<EOSQL
			SELECT rd.name, r.amount FROM resources r INNER JOIN resourcedefs rd ON rd.resource_id = r.resource_id WHERE rd.is_building = 1 AND r.nation_id = {$rs['nation_id']}
EOSQL;
			$sth2 = $GLOBALS['mysqli']->query($sql);
			while ($rs2 = mysqli_fetch_array($sth2)) {
				$buildingslist .=<<<EOFORM
<td>{$rs2['name']}</td><td>{$rs2['amount']}</td></tr>
EOFORM;
			}
		$sql=<<<EOSQL
SELECT * FROM reports WHERE nation_id = '{$rs['nation_id']}' ORDER BY time DESC
EOSQL;
			$sth2 = $GLOBALS['mysqli']->query($sql);
			while ($rs2 = mysqli_fetch_array($sth2)) {
				$reportslist .=<<<EOFORM
<tr><td>{$rs2['report']}</td><td>{$rs2['time']}</td></tr>
EOFORM;
			}
			$commasfunds = commas($rs['funds']);
			$details=<<<EOFORM
<center>Deleted by Owner<br/>
{$commasfunds} Bits<br/>
{$rs['government']}<br/>
{$rs['economy']}</center>
<center><h4 class="graveyardresourcesheading">Resources</h4></center>
<table class="graveyardresourcestable table table-striped table-bordered">{$resourceslist}</table>
<center><h4 class="graveyardbuildingsheading">Buildings</h4></center>
<table class="graveyardbuildingstable table table-striped table-bordered">{$buildingslist}</table>
<center><h4 class="graveyardreportsheading">Reports</h4></center>
<table class="graveyardreportstable table table-striped table-bordered">{$reportslist}</table>
EOFORM;
			$mysqldetails = $GLOBALS['mysqli']->real_escape_string($details);
			$sql=<<<EOSQL
INSERT INTO graveyard SET name = '{$rs['name']}', details = '{$mysqldetails}', killer = 'Deleted', deathdate = NOW()
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			}
		$sql = "DELETE FROM resources WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM marketplace WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM nations WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM weapons WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM armor WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM recipefavorites WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM forcegroups WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = "DELETE FROM forces WHERE nation_id = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$sql = <<<EOSQL
		UPDATE forcegroups SET location_id = nation_id, departuredate = NULL, attack_mission = 0 WHERE destination_id = {$rs['nation_id']} OR location_id = {$rs['nation_id']}
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql = "SELECT deal_id FROM deals WHERE fromnation = '{$rs['nation_id']}'";
		$sth2 = $GLOBALS['mysqli']->query($sql);
		while ($rs2 = mysqli_fetch_array($sth2)) {
			$sql = "DELETE FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealitems_requested WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealarmor_offered WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealarmor_requested WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealweapons_offered WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
			$sql = "DELETE FROM dealweapons_requested WHERE deal_id = '{$rs2['deal_id']}'";
			$GLOBALS['mysqli']->query($sql);
		}
		$sql = "DELETE FROM deals WHERE fromnation = '{$rs['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
        $sql =<<<EOSQL
        INSERT INTO news (message, posted)
		VALUES ('The nation of {$rs['name']} has been deleted by its owner, {$rs['username']}!', NOW())
EOSQL;
		$GLOBALS['mysqli']->query($sql);
        } else {
            $errors[] = "No.";
        }
	}
}
$sql=<<<EOSQL
SELECT nation_id, name FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
$nations[$rs['nation_id']] = $rs['name'];
}
?>