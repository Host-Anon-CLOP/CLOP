<?php
require_once("backend/allfunctions.php");
needsuser();
if ($_POST['async_token'] == $_SESSION['async_token']) {

if (isset($_POST['funtoggle'])) {
    if ($_POST['funstatus'] == "true") {
        user_error("1");
        $_SESSION['funmode'] = 1;
        $funstatus = "checked";
        $sql=<<<EOSQL
        UPDATE users SET funmode = 1 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    } else {
        user_error("0");
        $_SESSION['funmode'] = 0;
        $funstatus = "";
        $sql=<<<EOSQL
        UPDATE users SET funmode = 0 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
    }
    $GLOBALS['mysqli']->query($sql);
} else /*user_error("bad post") */;

} /* async_token endif */ else {
    /* user_error("bad token") */;
}
