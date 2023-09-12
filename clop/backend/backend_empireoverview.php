<?php
include_once("allfunctions.php");
$all_resources_list = array();
$empirenations = array();
$resources = array();

# get list of all resources that exist, excluding buildings
$sql=<<<EOSQL
select resource_id, name from resourcedefs where is_building = 0
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

while ($rs = mysqli_fetch_array($sth)) {
    $all_resources_list += array($rs['resource_id'] => $rs['name']);
}

# get list of nations owned by the user
$sql=<<<EOSQL
select nation_id, name from nations where user_id = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

while ($rs = mysqli_fetch_array($sth)) {
    $empirenations += array($rs['nation_id'] => $rs['name']);
}

# get resource stockpiles per nation owned
foreach ($empirenations as $key => $value) {
$sql=<<<EOSQL
SELECT resource_id, amount from resources WHERE nation_id = '{$key}'
EOSQL;
    $sth = $GLOBALS['mysqli']->query($sql);

    while ($rs = mysqli_fetch_array($sth)) {
        $resources[$key] += array($rs['resource_id'] => $rs['amount']);
        }
    }
?>