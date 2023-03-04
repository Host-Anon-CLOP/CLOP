<?php
include("backend/backend_ascensions.php");
$extratitle = "Ascensions - ";
include("header.php");
if ($nations) {
echo <<<EOFORM
<center>These are the players whose nations have ascended into alicorns and gone on to pursue new pleasures.</center>
<table class="table table-striped table-bordered">
<tr><th>Nation</th><th>User</th><th>Ascended On</th></tr>
EOFORM;
foreach ($nations as $nation) {
    echo <<<EOFORM
<tr><td><a href="viewascension.php?user_id={$nation['user_id']}">{$nation['name']}</a></td><td>{$nation['username']}</td><td>{$nation['date']}</td></tr>
EOFORM;
}
echo "</table>";
for ($i = 1; $i * 20 < $numnations + 20; $i++) {
    if ($i != $mysql['page']) {
        echo <<<EOFORM
<a href="ascensions.php?page={$i}">{$i}</a> 
EOFORM;
    } else {
        echo "{$i} ";
    }
}
} else {
    echo <<<EOFORM
<center>No nations have yet ascended.</center>
EOFORM;
}
include("footer.php");
?>