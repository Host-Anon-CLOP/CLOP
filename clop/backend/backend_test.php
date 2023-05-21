<?php
include_once("allfunctions.php");

foreach ($attacks as $attack) {
	echo <<<EOFORM
<tr><td>{$attack['totalsize']}</td>
EOFORM;
	}

?>