<?php
include("backend/backend_blocklist.php");
$extratitle = "blocklist - ";
include("header.php");
echo <<<EOFORM
<center><form action="blocklist.php" method="post">
<input type="hidden" name="token_blocklist" value="{$_SESSION['token_blocklist']}"/>
User or Nation Name to block: <input name="block" size="25" type="text" class="form-control" style="width:250px"/>
<input name="action" type="submit" value="Block" class="btn btn-danger"/>
</form></center>
EOFORM;
if (!empty($youblocking)) {
    echo <<<EOFORM
    <center>Users you are blocking</center>
    <center><table class="table table-striped table-bordered" style="width:350px">
EOFORM;
    foreach ($youblocking as $id => $name) {
        echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$id}">{$name}</a></td><td><form action="blocklist.php" method="post">
<input type="hidden" name="token_blocklist" value="{$_SESSION['token_blocklist']}"/>
<input type="hidden" name="unblock" value="{$id}"/><input name="action" type="submit" value="Unblock" class="btn btn-success btn-sm"/></form></td></tr>
EOFORM;
    }
    echo <<<EOFORM
</table></center>
EOFORM;
} else {
    echo <<<EOFORM
    <center>You are not blocking any users.</center>
EOFORM;
}
if (!empty($blockingyou)) {
    echo <<<EOFORM
    <center>Users blocking you:</center>
    <center><table class="table table-striped table-bordered" style="width:250px">
EOFORM;
    foreach ($blockingyou as $id => $name) {
        echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$id}">{$name}</a></td></tr>
EOFORM;
}
    echo <<<EOFORM
    </table></center>
EOFORM;
} else {
    echo <<<EOFORM
    <center>No users are blocking you.</center>
EOFORM;
}
include("footer.php");
?>