<?php
include("backend/backend_topmessage.php");
$extratitle = "Top Message - ";
include("header.php");

echo <<<EOFORM
<table class="table table-striped table-bordered">
EOFORM;

foreach ($topmessages as $message) {
    echo <<<EOFORM
<tr><td>$message</tr></td>
EOFORM;
}

echo <<<EOFORM
</table>
EOFORM;

include("footer.php");
?>