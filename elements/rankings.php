<?php
include("backend/backend_rankings.php");
if ($mode == "production") {
	$extratitle = "Rankings - ";
    $topline = <<<EOFORM
<center>Even alicorns are not above comparing certain measurements.</center>
EOFORM;
	$firstcolumn = "Alliance";
    $secondcolumn = "Production";
    $thirdcolumn = "Tier";
} else if ($mode == "unallied") {
	$extratitle = "Unallied Players - ";
    $topline = <<<EOFORM
<center>Won't you give one of these poor orphans a home?</center>
EOFORM;
	$firstcolumn = "";
    $secondcolumn = "";
    $thirdcolumn = "";
}
include("header.php");
echo <<<EOFORM
{$topline}
<table class="table table-striped table-bordered">
<tr><th>Name</th><th>{$firstcolumn}</th><th>{$secondcolumn}</th><th>{$thirdcolumn}</th></tr>
EOFORM;
if ($users) {
foreach ($users as $user) {
    if ($mode == "production") {
        $first = <<<EOFORM
        <a href="viewalliance.php?alliance_id={$user['alliance_id']}">{$user['alliancename']}</a>
EOFORM;
        $second = $user['production'];
        $third = $user['tier'];
    } else if ($mode == "unallied") {
		$first = "";
        $second = "";
        $third = "";
    }
    echo <<<EOFORM
<tr><td><a href="viewuser.php?user_id={$user['user_id']}">{$user['username']}</a></td>
<td>{$first}</td>
<td>{$second}</td><td>{$third}</td></tr>
EOFORM;
}
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