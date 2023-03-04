<?php
include("backend/backend_nonation.php");
include("header.php");
foreach ($regions as $key => $value) {
if ($_POST['region'] == $key) {
    $selected = "selected";
} else {
    $selected = "";
}
$regions .=<<<EOFORM
<option value="{$key}" {$selected}>{$value}</option>
EOFORM;
}
foreach ($subregions as $key => $value) {
if ($_POST['subregion'] == $key) {
    $selected = "selected";
} else {
    $selected = "";
}
$subregions .=<<<EOFORM
<option value="{$key}" {$selected}>{$value}</option>
EOFORM;
}
echo <<<EOFORM
<center><h4>You have lost your last nation! Try again!</h4></center><br/>
<center><form name="newuser" method="post" action="nonation.php">
<input type="hidden" name="token_nonation" value="{$_SESSION['token_nonation']}"/>
<table>
<tr><td>Nation Name</td><td><input name="nationname" class="form-control" maxlength="40" value="{$display['nationname']}"/></td></tr>
<tr><td>Nation Description</td><td><textarea name="nationdescription" class="form-control">{$display['nationdescription']}</textarea></td></tr>
<tr><td>Region</td><td><select name="region" class="form-control">
{$regions}
</select></td></tr>
<tr><td>Subregion</td><td><select name="subregion" class="form-control">
{$subregions}
</select></td></tr>
</table>
<input type="submit" class="btn btn-success" value="Rise From the Ashes"/>
</form></center>
EOFORM;
include("footer.php");
?>