<?php
include("backend/backend_viewgraveyard.php");
$extratitle = "View Graveyard - ";
include("header.php");
if ($nationinfo) {
echo <<<EOFORM
<center><h2>{$nationinfo['name']}</h2></center>
{$nationinfo['details']}
EOFORM;
} else {
echo <<<EOFORM
<center>Nation not found.</center>
EOFORM;
}
include("footer.php");
?>