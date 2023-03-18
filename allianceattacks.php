<?php
include("backend/backend_allianceattacks.php");
$extratitle = "Alliance Attacks - ";
include("header.php");
if ($nationinfo['government'] != "Decentralization" && $nationinfo['government'] != "Alicorn Elite" && $nationinfo['government'] != "Transponyism") {
echo <<<EOFORM
<center>Attacks against alliance members can only be seen by Decentralized governments.</center>
EOFORM;
} else if (!$nationinfo['alliance_id']) {
echo <<<EOFORM
<center>You have no alliance.</center>
EOFORM;
} else {
    if (empty($attacks)){ 
    echo <<<EOFORM
<center>No one is attacking your alliance members.</center>
EOFORM;
    } else {
	echo <<<EOFORM
	<table class="table table-striped table-bordered">
	<tr><th>Total troops</th><th>Attacking<br/>Nation</th><th>Attacking<br/>User</th><th>Defending<br/>Nation</th><th>Defending<br/>User</th></tr>
EOFORM;
	foreach ($attacks as $attack) {
	echo <<<EOFORM
<tr><td>{$attack['totalsize']}</td>
<td><a href="viewnation.php?nation_id={$attack['attackerid']}">{$attack['attackername']}</a></td>
<td><a href="viewuser.php?user_id={$attack['attackeruserid']}">{$attack['attackeruser']}</a></td>
<td><a href="viewnation.php?nation_id={$attack['defenderid']}">{$attack['defendername']}</a></td>
<td><a href="viewuser.php?user_id={$attack['defenderuserid']}">{$attack['defenderuser']}</a></td>
</tr>
EOFORM;
	}
	echo <<<EOFORM
</table>
EOFORM;
	}
}
include("footer.php");
?>