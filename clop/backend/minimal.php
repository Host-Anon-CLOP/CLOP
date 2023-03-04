<?php
$mysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_elements");
date_default_timezone_set("UTC");
session_start();
if (!isset($_SESSION['SERVER_GENERATED_SID'])) {
    session_destroy();
    session_start();
    session_regenerate_id(true);
    $_SESSION['SERVER_GENERATED_SID'] = true;
}
function onelinequery($sql) {
    $sth = $GLOBALS['mysqli']->query($sql);
    if ($sth) {
        return mysqli_fetch_array($sth);
    } else {
        return false;
    }
}
?>