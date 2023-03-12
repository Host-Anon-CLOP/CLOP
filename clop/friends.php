<?php
include("backend/backend_friends.php");
$extratitle = "friends - ";
include("header.php");
echo <<<EOFORM
<center><form action="friends.php" method="post">
<input type="hidden" name="token_friends" value="{$_SESSION['token_friends']}"/>
User or Nation Name to Friend: <input name="Friend" size="25" type="text" class="form-control" style="width:250px"/>
<input name="action" type="submit" value="Friend" class="btn btn-danger"/>
</form></center>
EOFORM;
if (!empty($youfriending)) {
    echo <<<EOFORM
    <center>Users you are Friending</center>
    <center><table class="table table-striped table-bordered" style="width:350px">
EOFORM;
    foreach ($youfriending as $id => $name) {
        echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$id}">{$name}</a></td><td><form action="friends.php" method="post">
<input type="hidden" name="token_friends" value="{$_SESSION['token_friends']}"/>
<input type="hidden" name="unfriend" value="{$id}"/><input name="action" type="submit" value="unfriend" class="btn btn-success btn-sm"/></form></td></tr>
EOFORM;
    }
    echo <<<EOFORM
</table></center>
EOFORM;
} else {
    echo <<<EOFORM
    <center>You have no friends.</center>
EOFORM;
}
include("footer.php");
?>