<?php
include_once("allfunctions.php");

$topmessages = array();

$sql=<<<EOSQL
SELECT time, message FROM topmessage ORDER BY time DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

while ($rs = mysqli_fetch_array($sth)) {
    $topmessages[] = htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8");
}


?>