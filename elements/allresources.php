<?php
include("backend/allfunctions.php");
for ($i = 0; $i <= 63; $i++) {
    $sql=<<<EOSQL
    INSERT INTO resources SET user_id = 1, amount = 1, resource_id = {$i}
EOSQL;
    $GLOBALS['mysqli']->query($sql);
}
?>