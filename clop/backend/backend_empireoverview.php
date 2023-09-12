<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
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
    $resources += array($rs['nation_id'] => array());
}


# sum(n.se_relation) sum(n.nlr_relation)  sum(n.funds)


foreach ($empirenations as $nation_id => $nation_name) {
    # get satisfaction
    $sql=<<<EOSQL
    SELECT sum(n.satisfaction) AS amount FROM nations n WHERE n.nation_id = $nation_id
EOSQL;
    $rs = onelinequery($sql);
    $resources[$nation_id] += array('nlr' => $rs['amount']);

    # get resource stockpiles per nation owned
    $sql=<<<EOSQL
    SELECT resource_id, amount from resources WHERE nation_id = $nation_id
EOSQL;
        $sth = $GLOBALS['mysqli']->query($sql);

        while ($rs = mysqli_fetch_array($sth)) {
            $resources[$nation_id] += array($rs['resource_id'] => $rs['amount']);
            }
        }
?>