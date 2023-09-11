<?php
#include("backend/backend_warguide.php");
$extratitle = "Top Message - ";
include("header.php");

$sql=<<<EOSQL
SELECT time, message FROM topmessage ORDER BY time DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

echo <<<EOFORM
$sth
EOFORM;


include("footer.php");
?>