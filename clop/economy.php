<?php
include("backend/backend_economy.php");
$extratitle = "Economy - ";
include("header.php");

echo <<<EOFORM
<center>New Nations</center>
<table class="table table-striped table-bordered">
<tr><th>Last 24 Hours:</th><th>$nations_new_24h</th></tr>
<tr><th>Last 7 Days:</th><th>$nations_new_week</th></tr>
<tr><th>Last 30 Days:</th><th>$nations_new_month</th></tr>
</table>
EOFORM;

echo <<<EOFORM
<center>Players Active</center>
<table class="table table-striped table-bordered">
<tr><th>Last 24 Hours:</th><th>$players_active_24h</th></tr>
<tr><th>Last 7 Days:</th><th>$players_active_week</th></tr>
<tr><th>Last 30 Days:</th><th>$players_active_month</th></tr>
</table>
EOFORM;

echo <<<EOFORM
<center>Census</center>
<table class="table table-striped table-bordered">
<tr><th>Burrozil:</th><th>$census_burrozil</th></tr>
<tr><th>Zebrica:</th><th>$census_zebrica</th></tr>
<tr><th>Saddle Arabia:</th><th>$census_saddle</th></tr>
<tr><th>Przewalskia :</th><th>$census_prze</th></tr>
</table>
EOFORM;

echo <<<EOFORM
<center>Global Production</center>
EOFORM;

include("footer.php");
?>