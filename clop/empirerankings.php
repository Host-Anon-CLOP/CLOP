<?php
include("backend/backend_empirerankings.php");
$extratitle = "Empire Rankings - ";
include("header.php");
echo <<<EOFORM
<center>Empires are ranked by number of nations first and total GDP second.</center>
<table class="table table-striped table-bordered">
<tr><th style="width:25px"></th><th>User</th><th>Nations</th><th>Total GDP</th></tr>
EOFORM;
foreach ($users as $user) {
if ($user['flag']) {
$display['flag'] = htmlentities($user['flag'], ENT_SUBSTITUTE, "UTF-8");
$flaghtml =<<<EOFORM
<img src="{$display['flag']}" height="20" width="20">
EOFORM;
} else {
$flaghtml = "";
}
    echo <<<EOFORM
<tr><td style="width:25px">{$flaghtml}</td><td><a href="viewuser.php?user_id={$user['user_id']}">{$user['username']}</a></td><td>{$user['nationcount']}</td><td>{$user['totalgdp']}</td></tr>
EOFORM;
}
echo "</table>";
for ($i = 1; $i <= $numpages; $i++) {
    if ($i != $mysql['page']) {
        echo <<<EOFORM
<a href="empirerankings.php?page={$i}">{$i}</a> 
EOFORM;
    } else {
        echo "{$i} ";
    }
}
include("footer.php");
?>