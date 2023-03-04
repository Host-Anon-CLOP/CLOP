<?php
function getresourcelist($user_id) {
$user_id = (int)$user_id;
$sql=<<<EOSQL
SELECT a.* FROM alliances a
INNER JOIN users u ON a.alliance_id = u.alliance_id
WHERE u.user_id = {$user_id}
EOSQL;
$thisallianceinfo = onelinequery($sql);
$sql=<<<EOSQL
SELECT * FROM users
WHERE user_id = {$user_id}
EOSQL;
$thisuserinfo = onelinequery($sql);
$sql=<<<EOSQL
SELECT * FROM elementpositions
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$positions[$rs['position']] = $rs['resource_id'];
}
$elementpositions = array_flip($positions);
$effectivesat = $thisuserinfo['satisfaction'];
if ($effectivesat > 1000) {
	$effectivesat = 1000;
}
$effectivealliancesat = $thisallianceinfo['alliancesatisfaction'];
if ($effectivealliancesat > 1000) {
	$effectivealliancesat = 1000;
}
$personalsatmult = 1 - (($effectivesat * 1.5) / 2000);
$alliancesatmult = 1 - (($effectivealliancesat * 1.5) / 2000);
for ($id = 0; $id <= 63; $id++) {
    $newid = 0;
    if ($id & 32) $newid += $positions[withinsix($elementpositions['32'] + 3)];
    if ($id & 16) $newid += $positions[withinsix($elementpositions['16'] + 3)];
    if ($id & 8) $newid += $positions[withinsix($elementpositions['8'] + 3)];
    if ($id & 4) $newid += $positions[withinsix($elementpositions['4'] + 3)];
    if ($id & 2) $newid += $positions[withinsix($elementpositions['2'] + 3)];
    if ($id & 1) $newid += $positions[withinsix($elementpositions['1'] + 3)];
    $complements[$id] = $newid;
}
if (hasability("encouraged", $user_id)) {
    $extraproduction = true;
}
foreach ($elementpositions as $value) {
	$production[$value] = $thisuserinfo['production'];
    if ($extraproduction) {
        $production[$value] += 5;
    }
}
if ($thisallianceinfo['alliancefocus'] == $thisuserinfo['focus']) {
	$thisuserinfo['focusamount'] += $thisallianceinfo['alliancefocusamount'];
} else {
	switch ($thisallianceinfo['alliancefocusamount']) {
		case 1:
		$production[$elementpositions[$thisallianceinfo['alliancefocus']]] *= 2;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 5)] *= 1.25;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 1)] *= 1.25;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 4)] *= .8;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 2)] *= .8;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 3)] *= .5;
		break;
		case 2:
		$production[$elementpositions[$thisallianceinfo['alliancefocus']]] *= 3;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 5)] *= 2;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 1)] *= 2;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 4)] *= .5;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 2)] *= .5;
		$production[withinsix($elementpositions[$thisallianceinfo['alliancefocus']] + 3)] *= .25;
		break;
		default:
		break;
	}
}
switch ($thisuserinfo['focusamount']) {
	case 1:
	$production[$elementpositions[$thisuserinfo['focus']]] *= 2;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 5)] *= 1.25;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 1)] *= 1.25;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 4)] *= .8;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 2)] *= .8;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 3)] *= .5;
	break;
	case 2:
	$production[$elementpositions[$thisuserinfo['focus']]] *= 3;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 5)] *= 2;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 1)] *= 2;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 4)] *= .5;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 2)] *= .5;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 3)] *= .25;
	break;
	case 3:
	$production[$elementpositions[$thisuserinfo['focus']]] *= 4;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 5)] *= 2.5;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 1)] *= 2.5;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 4)] *= 0;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 2)] *= 0;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 3)] *= 0;
	break;
	case 4:
	$production[$elementpositions[$thisuserinfo['focus']]] *= 15;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 5)] *= 0;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 1)] *= 0;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 4)] *= 0;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 2)] *= 0;
	$production[withinsix($elementpositions[$thisuserinfo['focus']] + 3)] *= 0;
	break;
	default:
	break;
}
foreach ($production as $element => $amount) {
	$production[$element] = floor($amount);
    $pertick[$positions[$element]] = $production[$element];
}
$sql=<<<EOSQL
SELECT * FROM autocompounds
WHERE user_id = '{$user_id}'
ORDER BY amount DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $pertick[$rs['resource_id']] += $rs['amount'];
    $theseelements = getelements($rs['resource_id']);
    foreach ($theseelements as $element => $name) {
        $pertick[$element] -= $rs['amount'];
    }
}
$sql=<<<EOSQL
SELECT amount, resource_id
FROM bankedresources
WHERE user_id = {$user_id}
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $bankedresources[$rs2['resource_id']] += $rs2['amount'];
    $checkpay[$rs2['resource_id']] += $rs2['amount'];
}
$sql=<<<EOSQL
SELECT SUM(offeredamount * multiplier) AS total, offereditem
FROM marketplace
WHERE user_id = {$user_id}
GROUP BY offereditem
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $checkpay[$rs2['offereditem']] += $rs2['total'];
}
$sql=<<<EOSQL
SELECT SUM(offeredamount) AS total, offereditem
FROM philippy
WHERE user_id = {$user_id}
GROUP BY offereditem
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $checkpay[$rs2['offereditem']] += $rs2['total'];
}
$sql=<<<EOSQL
SELECT SUM(amount) AS total, resource_id
FROM attacks
WHERE attacker = {$user_id}
AND type = 'burden'
GROUP BY resource_id
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $checkpay[$rs2['resource_id']] += $rs2['total'];
}
$sql=<<<EOSQL
SELECT SUM(dio.amount) AS total, dio.resource_id
FROM dealitems_offered dio
INNER JOIN deals d ON d.deal_id = dio.deal_id
WHERE d.fromuser = {$user_id}
GROUP BY dio.resource_id
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $checkpay[$rs2['resource_id']] += $rs2['total'];
}
$sql=<<<EOSQL
SELECT r.amount, r.resource_id FROM resources r
INNER JOIN resourcedefs rd ON rd.resource_id = r.resource_id
WHERE r.user_id = {$user_id}
ORDER BY rd.tier ASC, rd.resource_id ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$resources[$rs['resource_id']] = $rs['amount'];
    $rs['amount'] += $pertick[$rs['resource_id']];
    $checkpay[$rs['resource_id']] += $rs['amount'];
	if ($checkpay[$rs['resource_id']] > ($thisuserinfo['production'] * 6) + 50) {
		$complementsrequired[$rs['resource_id']] += ceil($rs['amount'] * .4 * $personalsatmult * $alliancesatmult);
	}
}
if ($checkpay) {
foreach ($checkpay as $resource_id => $amount) {
    if ($amount <= ($thisuserinfo['production'] * 6) + 50) {
        $nopay[$resource_id] = true;
    }
}
}
if ($bankedresources) {
foreach ($bankedresources as $resource_id => $amount) {
    $other[$resource_id] += $amount;
    if (!$nopay[$resource_id]) {
		$complementsrequired[$resource_id] += ceil($amount * .4 * $personalsatmult * $alliancesatmult);
    }
}
}
$sql=<<<EOSQL
SELECT amount, resource_id
FROM attacks
WHERE attacker = {$user_id}
AND type = 'burden'
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $other[$rs2['resource_id']] += $rs2['amount'];
    if (!$nopay[$rs2['resource_id']]) {
		$complementsrequired[$rs2['resource_id']] += ceil($rs2['amount'] * .4 * $personalsatmult * $alliancesatmult);
    }
}
$sql=<<<EOSQL
SELECT m.offeredamount, m.multiplier, m.offereditem FROM marketplace m
INNER JOIN resourcedefs rd ON rd.resource_id = m.offereditem
WHERE m.user_id = '{$user_id}'
ORDER BY rd.tier ASC, rd.resource_id ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$other[$rs['offereditem']] += $rs['offeredamount'] * $rs['multiplier'];
    if (!$nopay[$rs['offereditem']]) {
		$complementsrequired[$rs['offereditem']] += ceil($rs['offeredamount'] * $rs['multiplier'] * .4 * $personalsatmult * $alliancesatmult);
    }
}
$sql=<<<EOSQL
SELECT m.offeredamount, m.offereditem FROM philippy m
INNER JOIN resourcedefs rd ON rd.resource_id = m.offereditem
WHERE m.user_id = '{$user_id}'
ORDER BY rd.tier ASC, rd.resource_id ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$other[$rs['offereditem']] += $rs['offeredamount'];
    if (!$nopay[$rs['offereditem']]) {
		$complementsrequired[$rs['offereditem']] += ceil($rs['offeredamount'] * .4 * $personalsatmult * $alliancesatmult);
    }
}
$sql=<<<EOSQL
SELECT deal_id FROM deals WHERE fromuser = '{$user_id}'
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $sql=<<<EOSQL
    SELECT resource_id, amount FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}'
EOSQL;
    $sth3 = $GLOBALS['mysqli']->query($sql);
    while ($rs3 = mysqli_fetch_array($sth3)) {
        $other[$rs3['resource_id']] += $rs3['amount'];
        if (!$nopay[$rs3['resource_id']]) {
        $complementsrequired[$rs3['resource_id']] += ceil($rs3['amount'] * .4 * $personalsatmult * $alliancesatmult);
        }
    }
}
$returnthis['resources'] = $resources;
$returnthis['other'] = $other;
$returnthis['pertick'] = $pertick;
$returnthis['complementsrequired'] = $complementsrequired;
$returnthis['complements'] = $complements;
return $returnthis;
}

/* GET ALLIANCE RESOURCES */

function getallianceresources($alliance_id) {
$alliance_id = (int)$alliance_id;
$sql=<<<EOSQL
SELECT * FROM elementpositions
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$positions[$rs['position']] = $rs['resource_id'];
}
$elementpositions = array_flip($positions);
for ($id = 0; $id <= 63; $id++) {
    $newid = 0;
    if ($id & 32) $newid += $positions[withinsix($elementpositions['32'] + 3)];
    if ($id & 16) $newid += $positions[withinsix($elementpositions['16'] + 3)];
    if ($id & 8) $newid += $positions[withinsix($elementpositions['8'] + 3)];
    if ($id & 4) $newid += $positions[withinsix($elementpositions['4'] + 3)];
    if ($id & 2) $newid += $positions[withinsix($elementpositions['2'] + 3)];
    if ($id & 1) $newid += $positions[withinsix($elementpositions['1'] + 3)];
    $complements[$id] = $newid;
}
$sql=<<<EOSQL
SELECT * FROM alliances
WHERE alliance_id = {$alliance_id}
EOSQL;
$thisallianceinfo = onelinequery($sql);
$sql=<<<EOSQL
SELECT name, resource_id FROM resourcedefs
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$resourcenames[$rs['resource_id']] = $rs['name'];
}
foreach ($resourcenames AS $id => $name) {
	$complementnames[$id] = $resourcenames[$complements[$id]];
}
$effectivealliancesat = $thisallianceinfo['alliancesatisfaction'];
if ($effectivealliancesat > 1000) {
	$effectivealliancesat = 1000;
}
$alliancesatmult = 1 - ($effectivealliancesat / 2000);
$sql=<<<EOSQL
   SELECT COUNT(*) AS count FROM users WHERE alliance_id = {$thisallianceinfo['alliance_id']}
EOSQL;
$usercounter = onelinequery($sql);
$usercount = $usercounter['count'];
$sql=<<<EOSQL
SELECT ar.amount, ar.resource_id FROM allianceresources ar
INNER JOIN resourcedefs rd ON rd.resource_id = ar.resource_id
WHERE ar.alliance_id = {$thisallianceinfo['alliance_id']}
ORDER BY rd.tier ASC, rd.resource_id ASC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$resources[$rs['resource_id']] = $rs['amount'];
    $checkpay[$rs['resource_id']] += $rs['amount'];
    if ($checkpay[$rs['resource_id']] > $usercount * 100) {
		$complementsrequired[$rs['resource_id']] += ceil($rs['amount'] * .1 * $alliancesatmult);
	}
}
$checkpay = $resources;
$sql=<<<EOSQL
SELECT amount, resource_id
FROM alliancebankedresources
WHERE alliance_id = {$thisallianceinfo['alliance_id']}
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $bankedresources[$rs['resource_id']] += $rs['amount'];
    $checkpay[$rs['resource_id']] += $rs['amount'];
}
$sql=<<<EOSQL
SELECT SUM(amount) AS total, resource_id
FROM allianceattacks
WHERE attacker = {$thisallianceinfo['alliance_id']}
AND type = 'burden'
GROUP BY resource_id
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $checkpay[$rs2['resource_id']] += $rs2['total'];
}
if ($checkpay) {
foreach ($checkpay as $resource_id => $amount) {
    if ($amount <= $usercount * 100) {
        $nopay[$resource_id] = true;
    }
}
}
if ($bankedresources) {
foreach ($bankedresources as $resource_id => $amount) {
    $other[$resource_id] += $amount;
    if (!$nopay[$resource_id]) {
		$complementsrequired[$resource_id] += ceil($amount * .1 * $alliancesatmult);
    }
}
}
$sql=<<<EOSQL
SELECT amount, resource_id
FROM allianceattacks
WHERE attacker = {$thisallianceinfo['alliance_id']}
AND type = 'burden'
EOSQL;
$sth2 = $GLOBALS['mysqli']->query($sql);
while ($rs2 = mysqli_fetch_array($sth2)) {
    $other[$rs2['resource_id']] += $rs2['amount'];
    if (!$nopay[$rs2['resource_id']]) {
		$complementsrequired[$rs2['resource_id']] += ceil($rs2['amount'] * .1 * $alliancesatmult);
    }
}
$returnthis['resources'] = $resources;
$returnthis['other'] = $other;
$returnthis['complementsrequired'] = $complementsrequired;
$returnthis['complements'] = $complements;
return $returnthis;
}

/* DISPLAY RESOURCES */

function displayresources($theseresources, $hideicons) {
$sql=<<<EOSQL
SELECT name, resource_id FROM resourcedefs
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$resourcenames[$rs['resource_id']] = $rs['name'];
}
foreach ($resourcenames AS $id => $name) {
	$complementnames[$id] = $resourcenames[$complements[$id]];
}
$other = $theseresources['other'];
$pertick = $theseresources['pertick'];
$resources = $theseresources['resources'];
$complementsrequired = $theseresources['complementsrequired'];
$complements = $theseresources['complements'];
$return .= <<<EOFORM
   <div class="panel panel-default">
     <div class="panel-heading">Resources</div>
     <table class="table">
      <thead>
        <tr>
EOFORM;
        if (!$hideicons) {
        $return .= <<<EOFORM
          <td></td>
EOFORM;
        }
        $return .= <<<EOFORM
          <td>Resource</td>
          <td>Stock</td>
		  <td>Other</td>
          <td>Per-Tick Generated</td>
EOFORM;
        if (!$hideicons) {
        $return .= <<<EOFORM
          <td></td>
EOFORM;
        }
        $return .= <<<EOFORM
          <td>Complement</td>
          <td>Complement Required Next Tick</td>
        </tr>
      </thead>
      <tbody>
EOFORM;
if ($other) {
    foreach ($other AS $resource_id => $amount) {
    if (!$complementsrequired[$resource_id]) {
        $complementsrequired[$resource_id] = 0;
    }
    if (!$resources[$resource_id] && !$pertick[$resource_id]) {
    if ($resources[$complements[$resource_id]] >= $complementsrequired[$resource_id]) $complementamountclass = "text-success";
	else $complementamountclass = "text-danger";
	$return .= <<<EOFORM
    <tr>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$resource_id}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$resource_id]}</td>
    <td>0</td>
	<td>{$amount}</td>
	<td>0</td>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$complements[$resource_id]}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$complements[$resource_id]]}</td>
    <td><span class="{$complementamountclass}">{$complementsrequired[$resource_id]}</span></td>
	</tr>
EOFORM;
    }
    }
}
foreach ($pertick AS $resource_id => $amount) {
    if (!$other[$resource_id]) $other[$resource_id] = 0;
    if (!$resources[$resource_id]) {
    if ($resources[$complements[$resource_id]] >= $complementsrequired[$resource_id]) $complementamountclass = "text-success";
	else $complementamountclass = "text-danger";
	$return .= <<<EOFORM
    <tr>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$resource_id}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$resource_id]}</td>
    <td>0</td>
	<td>{$other[$resource_id]}</td>
    <td>{$amount}</td>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$complements[$resource_id]}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$complements[$resource_id]]}</td>
    <td><span class="{$complementamountclass}">{$complementsrequired[$resource_id]}</span></td>
	</tr>
EOFORM;
    }
}
if ($resources) {
foreach ($resources AS $resource_id => $amount) {
    if (!$pertick[$resource_id]) $pertick[$resource_id] = 0;
    if (!$other[$resource_id]) $other[$resource_id] = 0;
    if (!$complementsrequired[$resource_id]) $complementsrequired[$resource_id] = 0;
	if ($resources[$complements[$resource_id]] >= $complementsrequired[$resource_id]) $complementamountclass = "text-success";
	else $complementamountclass = "text-danger";
	$return .= <<<EOFORM
    <tr>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$resource_id}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$resource_id]}</td>
    <td>{$amount}</td>
	<td>{$other[$resource_id]}</td>
	<td>{$pertick[$resource_id]}</td>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$complements[$resource_id]}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$complements[$resource_id]]}</td>
    <td><span class="{$complementamountclass}">{$complementsrequired[$resource_id]}</span></td>
	</tr>
EOFORM;
}
}
$return .=  <<<EOFORM
       </tbody>
     </table>
   </div>
EOFORM;
return $return;
}

/* DISPLAY ALLIANCE RESOURCES */

function displayallianceresources($theseresources, $hideicons) {
$sql=<<<EOSQL
SELECT name, resource_id FROM resourcedefs
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$resourcenames[$rs['resource_id']] = $rs['name'];
}
foreach ($resourcenames AS $id => $name) {
	$complementnames[$id] = $resourcenames[$complements[$id]];
}
$other = $theseresources['other'];
$resources = $theseresources['resources'];
$complementsrequired = $theseresources['complementsrequired'];
$complements = $theseresources['complements'];
$return .= <<<EOFORM
   <div class="panel panel-default">
     <div class="panel-heading">Resources</div>
     <table class="table">
      <thead>
        <tr>
EOFORM;
        if (!$hideicons) {
        $return .= <<<EOFORM
          <td></td>
EOFORM;
        }
        $return .= <<<EOFORM
          <td>Resource</td>
          <td>Stock</td>
		  <td>Other</td>
EOFORM;
        if (!$hideicons) {
        $return .= <<<EOFORM
          <td></td>
EOFORM;
        }
        $return .= <<<EOFORM
          <td>Complement</td>
          <td>Complement Required Next Tick</td>
        </tr>
      </thead>
      <tbody>
EOFORM;
if ($other) {
    foreach ($other AS $resource_id => $amount) {
    if (!$complementsrequired[$resource_id]) {
        $complementsrequired[$resource_id] = 0;
    }
    if (!$resources[$resource_id] && !$pertick[$resource_id]) {
    if ($resources[$complements[$resource_id]] >= $complementsrequired[$resource_id]) $complementamountclass = "text-success";
	else $complementamountclass = "text-danger";
	$return .= <<<EOFORM
    <tr>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$resource_id}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$resource_id]}</td>
    <td>0</td>
	<td>{$amount}</td>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$complements[$resource_id]}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$complements[$resource_id]]}</td>
    <td><span class="{$complementamountclass}">{$complementsrequired[$resource_id]}</span></td>
	</tr>
EOFORM;
    }
    }
}
if ($resources) {
foreach ($resources AS $resource_id => $amount) {
    if (!$other[$resource_id]) $other[$resource_id] = 0;
    if (!$complementsrequired[$resource_id]) $complementsrequired[$resource_id] = 0;
	if ($resources[$complements[$resource_id]] >= $complementsrequired[$resource_id]) $complementamountclass = "text-success";
	else $complementamountclass = "text-danger";
	$return .= <<<EOFORM
    <tr>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$resource_id}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$resource_id]}</td>
    <td>{$amount}</td>
	<td>{$other[$resource_id]}</td>
EOFORM;
    if (!$hideicons) {
    $return .= <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$complements[$resource_id]}.png"/></td>
EOFORM;
    }
    $return .= <<<EOFORM
    <td>{$resourcenames[$complements[$resource_id]]}</td>
    <td><span class="{$complementamountclass}">{$complementsrequired[$resource_id]}</span></td>
	</tr>
EOFORM;
}
}
$return .=  <<<EOFORM
       </tbody>
     </table>
   </div>
EOFORM;
return $return;
}

?>