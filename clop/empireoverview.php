<?php
include("backend/backend_empireoverview.php");
$extratitle = "Empire Overview - ";
include("header.php");

echo <<<EOFORM
<table>
EOFORM;

foreach ($empirenations as $nation_id) {
    echo <<<EOFORM
<tr><td>$nation_id</tr></td>
EOFORM;
}

echo <<<EOFORM
</table>
EOFORM;

include("footer.php");
?>