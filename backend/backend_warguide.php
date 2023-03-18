<?php
include_once("allfunctions.php");
$forcetypes = array(1 => "Cavalry", 2 => "Tanks", 3 => "Pegasi", 4 => "Unicorns", 5 => "Naval");
$sql = "SELECT * FROM weapondefs";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $weapons[] = $rs;
}
$sql = "SELECT * FROM armordefs";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $armor[] = $rs;
}
?>