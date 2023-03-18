<?php
require_once('sql_data.php');
$mysqli = new mysqli($dbhost, $username, $password, $database);
$sql = "SET time_zone = '+00:00'";
$GLOBALS['mysqli']->query($sql);
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
