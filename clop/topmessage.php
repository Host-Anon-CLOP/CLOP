<?php
include("backend/backend_topmessage.php");
$extratitle = "Top Message - ";
include("header.php");

$sql=<<<EOSQL
SELECT time, message FROM topmessage ORDER BY time DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

echo <<<EOFORM
<table>
<tr><td>DATE</td><td>  MESSAGE</td></tr>
EOFORM;

while ($rs = mysqli_fetch_array($sth)) {
    $time = $rs['time'];
	$message = htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8");
	echo <<<EOFORM
<tr><td>$time</td><td>  | $message</td></tr>
EOFORM;
}

echo <<<EOFORM
</table>
EOFORM;



include("footer.php");
?>