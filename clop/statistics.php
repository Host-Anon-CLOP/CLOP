<?php
include("backend/backend_statistics.php");
$extratitle = "Statistics - ";
#include("header.php");

# New Players
echo <<<EOFORM
<center>New Nations</center>
<table class="table table-striped table-bordered">
<tr><th>Last 24 Hours:</th><th>$nations_new_24h</th></tr>
<tr><th>Last 7 Days:</th><th>$nations_new_week</th></tr>
<tr><th>Last 30 Days:</th><th>$nations_new_month</th></tr>
</table>
EOFORM;


# Players Activity
echo <<<EOFORM
<center>Players Active</center>
<table class="table table-striped table-bordered">
<tr><th>Last 24 Hours:</th><th>$players_active_24h</th></tr>
<tr><th>Last 7 Days:</th><th>$players_active_week</th></tr>
<tr><th>Last 30 Days:</th><th>$players_active_month</th></tr>
</table>
EOFORM;


# Census
echo <<<EOFORM
<center>Census</center>
<table class="table table-striped table-bordered">
<tr><th>Burrozil:</th><th>$census_burrozil</th></tr>
<tr><th>Zebrica:</th><th>$census_zebrica</th></tr>
<tr><th>Saddle Arabia:</th><th>$census_saddle</th></tr>
<tr><th>Przewalskia :</th><th>$census_prze</th></tr>
</table>
EOFORM;


# Global Resources


include("footer.php");
?>