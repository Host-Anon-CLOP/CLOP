<?php
include_once("allfunctions.php");

echo <<<EOFORM
<script>
const groupFieldsBackend = document.getElementById('group-fields')
</script>

foreach ($group in $groupFieldsBackend) {
	
<tr><td>$group</td></tr>

	}
EOFORM;

?>