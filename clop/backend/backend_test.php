<?php
include_once("allfunctions.php");

<script>
const groupFieldsBackend = document.getElementById('group-fields')
</script>

foreach ($group in $groupFieldsBackend) {
	echo <<<EOFORM
<tr><td>$group</td></tr>
EOFORM;
	}

?>