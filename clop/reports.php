<?php
include("backend/backend_reports.php");
$extratitle = "Reports - ";
include("header.php");
echo <<<EOFORM
<center><h3>Reports</h3></center>
<table class="table table-striped table-bordered">
EOFORM;
foreach ($reports as $datetime => $report) {
echo <<<EOFORM
<tr><td>{$report}</td><td>{$datetime}</td></tr>
EOFORM;
}
echo <<<EOFORM
</table>
EOFORM;
include("footer.php");
?>