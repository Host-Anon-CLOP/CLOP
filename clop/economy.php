<?php
include("backend/backend_economy.php");
$extratitle = "Economy - ";
#include("header.php");
echo <<<EOFORM
<center>Players Active Last 24 Hours</center>
<table class="table table-striped table-bordered">
<tr><th>Count:</th><th>User</th><th>$players_active_24h</th></tr>
EOFORM;
echo "</table>";
#include("footer.php");
?>