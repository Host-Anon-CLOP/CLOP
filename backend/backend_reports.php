<?php
include_once("allfunctions.php");
needsnation();
$reports = array();
$sql = "SELECT report, time FROM reports WHERE nation_id = '{$_SESSION['nation_id']}' ORDER BY time DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if (!$reports[$rs['time']]) {
        $reports[$rs['time']] = $rs['report'];
    } else {
        $reports[$rs['time']] .= "<br/>" . $rs['report'];
    }
}
?>