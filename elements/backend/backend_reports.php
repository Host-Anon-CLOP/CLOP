<?php
include_once("allfunctions.php");
needsalliance();
$reports = array();
$sql = "SELECT report, time FROM reports WHERE user_id = '{$_SESSION['user_id']}' ORDER BY time DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if (!$reports[$rs['time']]) {
        $reports[$rs['time']] = $rs['report'];
    } else {
        $reports[$rs['time']] .= "<br/>" . $rs['report'];
    }
}
?>