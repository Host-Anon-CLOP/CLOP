<?php

$extratitle = "Top Message - ";
include("header.php");

echo <<<EOFORM
<table>
EOFORM;

include("backend/backend_topmessage.php");

echo <<<EOFORM
</table>
EOFORM;

include("footer.php");
?>