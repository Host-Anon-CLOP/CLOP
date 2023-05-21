<?php
include_once("allfunctions.php");

const groupFieldsBackend = document.getElementById('group-fields');
foreach ($group in $groupFieldsBackend) {
	echo <<<EOFORM
<tr><td>$group</td></tr>
EOFORM;
	}

?>