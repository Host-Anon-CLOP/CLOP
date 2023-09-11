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
EOFORM;

while ($rs = mysqli_fetch_array($sth)) {
	$message = htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8");
	echo <<<EOFORM
<tr><td>$message</td></tr>
EOFORM;
}

echo <<<EOFORM
</table>
EOFORM;



include("footer.php");
?>