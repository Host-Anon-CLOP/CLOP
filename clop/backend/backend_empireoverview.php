<?php
include_once("allfunctions.php");
$affectedresources = array();
$requiredresources = array();
$resources = array();
$all_resources_list = array();

$empirenations = array();

$sql=<<<EOSQL
select resource_id, name from resourcedefs where is_building = 0
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

while ($rs = mysqli_fetch_array($sth)) {
    $all_resources_list += array($rs['resource_id'] => $rs['name']);
}

$sql=<<<EOSQL
select nation_id, name from nations where user_id = '{$_SESSION['user_id']}'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);

while ($rs = mysqli_fetch_array($sth)) {
    $empirenations += array($rs['nation_id'] => $rs['name']);
}


$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS affected
FROM resourceeffects rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
INNER JOIN nations n ON r.nation_id = n.nation_id
INNER JOIN users u ON n.user_id = u.user_id
WHERE u.stasismode = 0
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $affectedresources[$rs['name']] = $rs['affected'];
}

$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS required
FROM resourcerequirements rr
INNER JOIN resources r ON r.resource_id = rr.resource_id
INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
INNER JOIN nations n ON r.nation_id = n.nation_id
INNER JOIN users u ON n.user_id = u.user_id
WHERE u.stasismode = 0
GROUP BY rd.name";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $requiredresources[$rs['name']] = $rs['required'];
}

# Add resources used by government type
$sql = "SELECT n.government, count(n.government) AS count
FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.stasismode = 0
GROUP BY n.government";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['government'] == "Democracy") {
        $requiredresources["Gasoline"] += (20 * $rs['count']);
        $requiredresources["Vehicle Parts"] += (2 * $rs['count']);
    } else if ($rs['government'] == "Repression") {
        $requiredresources["Gasoline"] += (10 * $rs['count']);
    } else if ($rs['government'] == "Independence") {
        $requiredresources["Gasoline"] += (40 * $rs['count']);
        $requiredresources["Vehicle Parts"] += (4 * $rs['count']);
    } else if ($rs['government'] == "Decentralization") {
        $requiredresources["Gasoline"] += (50 * $rs['count']);
        $requiredresources["Vehicle Parts"] += (5 * $rs['count']);
    } else if ($rs['government'] == "Authoritarianism") {
        $requiredresources["Gasoline"] += (10 * $rs['count']);
        $requiredresources["Machinery Parts"] += (3 * $rs['count']);
    } else if ($rs['government'] == "Oppression") {
        $requiredresources["Gasoline"] += (10 * $rs['count']);
        $requiredresources["Machinery Parts"] += (5 * $rs['count']);
    }
}

# Add resources used by economy type
$sql = "SELECT n.economy, count(n.economy) AS count
FROM nations n
INNER JOIN users u ON u.user_id = n.user_id
WHERE u.stasismode = 0
GROUP BY n.economy";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    if ($rs['economy'] == "Free Market") {
        $requiredresources["Coffee"] += (6 * $rs['count']);
    } else if ($rs['economy'] == "State Controlled") {
        $requiredresources["Cider"] += (6 * $rs['count']);
    }
}





?>