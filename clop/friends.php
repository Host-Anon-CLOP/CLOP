<?php
include("backend/backend_friends.php");
$extratitle = "friends - ";
include("header.php");

# FRIENDS
echo <<<EOFORM
<center><form action="friends.php" method="post">
<input type="hidden" name="token_friends" value="{$_SESSION['token_friends']}"/>
User or Nation Name to Friend: <input name="Friend" size="25" type="text" class="form-control" style="width:250px"/>
<input name="action" type="submit" value="Friend" class="btn btn-danger"/>
</form></center>
EOFORM;
if (!empty($youfriending)) {
    echo <<<EOFORM
    <center>Users you are Friends with</center>
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

# ENEMIES
echo <<<EOFORM
<center><form action="friends.php" method="post">
<input type="hidden" name="token_enemies" value="{$_SESSION['token_enemies']}"/>
User or Nation Name to Enemy: <input name="Enemy" size="25" type="text" class="form-control" style="width:250px"/>
<input name="action" type="submit" value="Enemy" class="btn btn-danger"/>
</form></center>
EOFORM;
if (!empty($youenemying)) {
    echo <<<EOFORM
    <center>Users you are Enemies with</center>
    <center><table class="table table-striped table-bordered" style="width:350px">
EOFORM;
    foreach ($youenemying as $id => $name) {
        echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$id}">{$name}</a></td><td><form action="friends.php" method="post">
<input type="hidden" name="token_enemies" value="{$_SESSION['token_enemies']}"/>
<input type="hidden" name="unenemy" value="{$id}"/><input name="action" type="submit" value="unenemy" class="btn btn-success btn-sm"/></form></td></tr>
EOFORM;
    }
    echo <<<EOFORM
</table></center>
EOFORM;
} else {
    echo <<<EOFORM
    <center>You have no enemies.</center>
EOFORM;
}
include("footer.php");
?>