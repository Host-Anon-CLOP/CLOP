<?php
include("backend/backend_viewascension.php");
$extratitle = "View Ascension - ";
include("header.php");
if ($username) {
    if ($nations) {
    echo <<<EOFORM
<center><h3>{$username['username']} has ascended!</h3></center>
<br/><br/>
<center><h4>Nations</h4></center>
<table class="table table-striped table-bordered">
<th>Nation</th><th>Ascended on</th>
EOFORM;
		foreach ($nations as $nation) {
			echo <<<EOFORM
<tr><td>{$nation['name']}</td><td>{$nation['date']}</td></tr>
EOFORM;
		}
        $displayfunds = commas($funds);
echo <<<EOFORM
</table>
<br/><br/>
<center><h4>Resources and Buildings</h4></center>
<table class="table table-striped table-bordered">
<tr><td>Bits</td><td>{$displayfunds}</td></tr>
EOFORM;
	foreach ($resources as $resource) {
    $displayamount = commas($resource['amount']);
		echo <<<EOFORM
<tr><td>{$resource['name']}</td><td>{$displayamount}</td></tr>
EOFORM;
	}
echo <<<EOFORM
</table>
EOFORM;
    } else {
        echo <<<EOFORM
<center>{$username['username']} has not ascended.</center> 
EOFORM;
    }
} else {
echo <<<EOFORM
<center>User not found.</center>
EOFORM;
}
include("footer.php");
?>