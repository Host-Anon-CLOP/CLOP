<?php
include("backend/backend_topmessage.php");
$extratitle = "Top Message - ";
include("header.php");

$sql=<<<EOSQL
SELECT time, message FROM topmessage ORDER BY time DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

while ($rs = mysqli_fetch_array($sth)) {
    $time = strtotime($rs['time']);
	$message = htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8");
	echo <<<EOFORM
$message
EOFORM;
}


include("footer.php");
?>