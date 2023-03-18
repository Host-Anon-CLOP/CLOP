<?php
include("backend/backend_embargoes.php");
$extratitle = "Embargoes - ";
include("header.php");
echo <<<EOFORM
<center><form action="embargoes.php" method="post">
<input type="hidden" name="token_embargoes" value="{$_SESSION['token_embargoes']}"/>
User or Nation Name to Embargo: <input name="embargo" size="25" type="text" class="form-control" style="width:250px"/>
<input name="action" type="submit" value="Embargo" class="btn btn-danger"/>
</form></center>
EOFORM;
if (!empty($youembargoing)) {
    echo <<<EOFORM
    <center>Users you are embargoing</center>
    <center><table class="table table-striped table-bordered" style="width:350px">
EOFORM;
    foreach ($youembargoing as $id => $name) {
        echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$id}">{$name}</a></td><td><form action="embargoes.php" method="post">
<input type="hidden" name="token_embargoes" value="{$_SESSION['token_embargoes']}"/>
<input type="hidden" name="unembargo" value="{$id}"/><input name="action" type="submit" value="Unembargo" class="btn btn-success btn-sm"/></form></td></tr>
EOFORM;
    }
    echo <<<EOFORM
</table></center>
EOFORM;
} else {
    echo <<<EOFORM
    <center>You are not embargoing any users.</center>
EOFORM;
}
if (!empty($embargoingyou)) {
    echo <<<EOFORM
    <center>Users embargoing you:</center>
    <center><table class="table table-striped table-bordered" style="width:250px">
EOFORM;
    foreach ($embargoingyou as $id => $name) {
        echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$id}">{$name}</a></td></tr>
EOFORM;
}
    echo <<<EOFORM
    </table></center>
EOFORM;
} else {
    echo <<<EOFORM
    <center>No users are embargoing you.</center>
EOFORM;
}
include("footer.php");
?>