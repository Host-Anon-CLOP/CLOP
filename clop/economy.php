<?php
include("backend/backend_economy.php");
$extratitle = "Economy - ";
#include("header.php");
echo <<<EOFORM
<center>Players Active</center>
<table class="table table-striped table-bordered">
<tr><th>Last 24 Hours:</th><th>$players_active_24h</th></tr>
<tr><th>Last 7 Days:</th><th>$players_active_week</th></tr>
<tr><th>Last 30 Days:</th><th>$players_active_month</th></tr>
EOFORM;
echo "</table>";
#include("footer.php");
?>