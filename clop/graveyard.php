<?php
include("backend/backend_graveyard.php");
$extratitle = "Graveyard - ";
include("header.php");
echo <<<EOFORM
<center>The Graveyard only holds nations which have been in the game for a month before ceasing to exist.</center>
EOFORM;
if ($nations) {
echo <<<EOFORM
<table class="table table-striped table-bordered">
<tr><th>Name</th><th>Killer</th><th>Died On</th></tr>
EOFORM;
foreach ($nations as $nation) {
    echo <<<EOFORM
<tr><td><a href="viewgraveyard.php?graveyard_id={$nation['graveyard_id']}">{$nation['name']}</a></td><td>{$nation['killer']}</td><td>{$nation['deathdate']}</td></tr>
EOFORM;
}
echo "</table>";
for ($i = 1; $i * 20 < $numnations + 20; $i++) {
    if ($i != $mysql['page']) {
        echo <<<EOFORM
<a href="graveyard.php?page={$i}">{$i}</a> 
EOFORM;
    } else {
        echo "{$i} ";
    }
}
} else {
    echo <<<EOFORM
<center>No nations are in the graveyard yet. (Don't worry- there will be!)</center>
EOFORM;
}
include("footer.php");
?>