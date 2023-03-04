<?php
include("backend/allfunctions.php");
$sql=<<<EOSQL
SELECT * FROM positionswaps WHERE effectivedate < DATE_SUB(CONCAT(CURDATE(), ' {$hour}:00:00'), INTERVAL 24 HOUR)
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$sql=<<<EOSQL
SELECT position FROM elementpositions WHERE resource_id = {$rs['changeposition1']}
EOSQL;
	$position1 = onelinequery($sql);
    echo $sql;
	$sql=<<<EOSQL
SELECT position FROM elementpositions WHERE resource_id = {$rs['changeposition2']}
EOSQL;
	$position2 = onelinequery($sql);
    echo $sql;
	$sql=<<<EOSQL
UPDATE elementpositions SET position = {$position2['position']} WHERE resource_id = {$rs['changeposition1']}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    echo $sql;
$sql=<<<EOSQL
UPDATE elementpositions SET position = {$position1['position']} WHERE resource_id = {$rs['changeposition2']}
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    echo $sql;
	//$newsitem = $GLOBALS['mysqli']->real_escape_string("The positions of {$resourcename[$rs['changeposition1']]} and {$resourcename[$rs['changeposition2']]} have been swapped. (A bit late.)");
	//$sql = "INSERT INTO news (message, posted) VALUES ('{$newsitem}', NOW())";
	//$GLOBALS['mysqli']->query($sql);
}
?>