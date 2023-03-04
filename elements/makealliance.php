<?php
include("backend/backend_makealliance.php");
$extratitle = "Make Alliance - ";
include("header.php");
if (!$userinfo['ascended']) {
    echo <<<EOFORM
<center>You have not ascended in &gt;CLOP, nor have you been granted the power to make an alliance by an alliance leader in this game.</center>
EOFORM;
} else {
    if ($userinfo['alliance_id']) {
        echo <<<EOFORM
<center>Because you already have an alliance, it costs {$constants['nobilityfornew']} Nobility to create a new one.</center>
EOFORM;
    }
    echo <<<EOFORM
<center><form name="alliance" method="post" action="makealliance.php">
<input type="hidden" name="token_makealliance" value="{$_SESSION['token_makealliance']}"/>
<table>
<tr><td>Alliance Name</td><td><input type="text" class="form-control" style="width:250px" name="alliancename" value="{$display['alliancename']}"/></td></tr>
<tr><td>Alliance Description</td><td><textarea class="form-control" name="alliancedescription">{$display['alliancedescription']}</textarea></td></tr>
</table>
<input type="submit" name="makealliance" value="Make New Alliance" class="btn btn-success"/>
</form>
</center>
EOFORM;
}
?>