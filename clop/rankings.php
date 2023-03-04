<?php
include("backend/backend_rankings.php");
$extratitle = "Rankings - ";
include("header.php");
if ($mode == "statues") {
    $topline = <<<EOFORM
<center>Here, you can see who has the biggest ego and the most money to spend.</center>
EOFORM;
    $secondcolumn = "Statues";
} else if ($mode == "longevity") {
    $topline = <<<EOFORM
<center>A nation's age has nothing to do with how long its owner has been playing &gt;CLOP.</center>
EOFORM;
    $secondcolumn =<<<EOFORM
Age</th><th>Creation Date
EOFORM;
} else {
    $topline = <<<EOFORM
<center>These rankings are only for GDP made from factories and satisfaction! There are plenty of other ways to make money in &gt;CLOP. Take these rankings with a grain of salt.</center>
EOFORM;
    $secondcolumn = "GDP Last Turn";
}
echo <<<EOFORM
{$topline}
<table class="table table-striped table-bordered">
<tr><th style="width:25px"></th><th>Name</th><th>{$secondcolumn}</th><th>Government</th><th>Economy</th></tr>
EOFORM;
foreach ($nations as $nation) {
if ($nation['flag']) {
$display['flag'] = htmlentities($nation['flag'], ENT_SUBSTITUTE, "UTF-8");
$flaghtml =<<<EOFORM
<img src="{$display['flag']}" height="20" width="20">
EOFORM;
} else {
$flaghtml = "";
}
    if ($mode == "statues") {
        $sortby = $nation['amount'];
        if (!$sortby) {$sortby = 0;}
    } else if ($mode == "longevity") {
        $sortby =<<<EOFORM
{$nation['age']}</td><td>{$nation['creationdate']}
EOFORM;
    } else {
        $sortby = $nation['gdp_last_turn'];
    }
    echo <<<EOFORM
<tr><td style="width:25px">{$flaghtml}</td><td><a href="viewnation.php?nation_id={$nation['nation_id']}">{$nation['name']}</a></td><td>{$sortby}</td><td>{$nation['government']}</td><td>{$nation['economy']}</td></tr>
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