<?php
include("backend/backend_alliancerankings.php");
$extratitle = "Alliance Rankings - ";
include("header.php");
if ($mode == "production") {
    $topline = <<<EOFORM
<center>Whoever controls the elements controls the universe.</center>
EOFORM;
    $secondcolumn = "Total Production";
    $thirdcolumn = "Members";
}
echo <<<EOFORM
{$topline}
<table class="table table-striped table-bordered">
<tr><th>Name</th><th>{$secondcolumn}</th><th>{$thirdcolumn}</th></tr>
EOFORM;
foreach ($alliances as $alliance) {
    if ($mode == "production") {
        $second = $alliance['totalproduction'];
        $third = $alliance['membercount'];
    }
    echo <<<EOFORM
<tr><td><a href="viewalliance.php?alliance_id={$alliance['alliance_id']}">{$alliance['name']}</td>
<td>{$second}</td><td>{$third}</td></tr>
EOFORM;
}
echo "</table>";
for ($i = 1; $i <= $numpages; $i++) {
    if ($i != $mysql['page']) {
        echo <<<EOFORM
<a href="rankings.php?page={$i}&mode={$mode}">{$i}</a> 
EOFORM;
    } else {
        echo "{$i} ";
    }
}
include("footer.php");
?>