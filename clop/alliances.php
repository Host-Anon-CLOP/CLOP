<?php
include("backend/backend_alliances.php");
$extratitle = "Alliances - ";
include("header.php");
if ($hasalliance) {
    echo <<<EOFORM
<center><h4><a href="myalliance.php">View My Alliance</a></h4></center>
EOFORM;
} else {
    echo <<<EOFORM
<center><form name="alliance" method="post" action="alliances.php">
<input type="hidden" name="token_alliances" value="{$_SESSION['token_alliances']}"/>
<table>
<tr><td>Alliance Name</td><td><input type="text" class="form-control" style="width:250px" name="alliancename" value="{$display['alliancename']}"/></td></tr>
<tr><td>Alliance Description</td><td><textarea class="form-control" name="alliancedescription">{$display['alliancedescription']}</textarea></td></tr>
</table>
<input type="submit" name="action" value="Make New Alliance" class="btn btn-success"/>
</form>
</center>
EOFORM;
}
echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><th>Alliance Name</th><th>Active<br/>Member Count</th><th>Active<br/>Nation Count</th><th>Leader</th>
EOFORM;
if (!$hasalliance) {
echo "<th></th>";
}
echo "</tr>";
foreach ($alliances AS $allianceinfo) {
    if ($allianceinfo['nations']) {
    if (!$hasalliance) {
    $request =<<<EOFORM
<td><form action="alliances.php" method="post"><input type="hidden" name="token_alliances" value="{$_SESSION['token_alliances']}"/>
<input type="hidden" name="alliance_id" value="{$allianceinfo['alliance_id']}"/>
EOFORM;
        if ($allianceinfo['alliancerequested']) {
        $request .=<<<EOFORM
<input type="submit" name="rescindrequest" value="Rescind Join Request" class="btn btn-danger btn-sm"/><br/>
EOFORM;
        } else {
        $request .=<<<EOFORM
<input type="submit" name="requestjoin" value="Request to Join" class="btn btn-success btn-sm"/><br/>
EOFORM;
        }
        $request .= "</form></td>";
    }
    echo <<<EOFORM
<tr><td><a href="viewalliance.php?alliance_id={$allianceinfo['alliance_id']}">{$allianceinfo['name']}</a></td>
<td>{$allianceinfo['players']}</td><td>{$allianceinfo['nations']}</td><td><a href="viewuser.php?user_id={$allianceinfo['owner_id']}">{$allianceinfo['leader']}</a></td>{$request}</tr>
EOFORM;
    }
}
echo <<<EOFORM
</table></center>
EOFORM;
include("footer.php");
?>