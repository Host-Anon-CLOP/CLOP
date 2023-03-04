<?php
include("backend/backend_news.php");
$extratitle = "News - ";
include("header.php");
echo <<<EOFORM
<center><h3>News</h3></center>
EOFORM;
if ($allnews) {
echo <<<EOFORM
<table class="table table-striped table-bordered">
EOFORM;
foreach ($allnews as $news) {
    echo <<<EOFORM
<tr><td>{$news['message']}</td><td>{$news['posted']}</td></tr>
EOFORM;
}
echo "</table>";
for ($i = 1; $i * 20 < $numnews + 20; $i++) {
    if ($i != $mysql['page']) {
        echo <<<EOFORM
<a href="news.php?page={$i}">{$i}</a> 
EOFORM;
    } else {
        echo "{$i} ";
    }
}
} else {
    echo <<<EOFORM
<center>No news yet.</center>
EOFORM;
}
include("footer.php");
?>