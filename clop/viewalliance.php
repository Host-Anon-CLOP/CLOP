<?php
include("backend/backend_viewalliance.php");
$extratitle = "View Alliance - ";
include("header.php");
if ($allianceinfo['name']) {
if ($_SESSION['user_id']) {
    $buttons .=<<<EOFORM
<center><form action="viewalliance.php" method="post"><input type="hidden" name="token_viewalliance" value="{$_SESSION['token_viewalliance']}"/>
<input type="hidden" name="alliance_id" value="{$allianceinfo['alliance_id']}"/>
EOFORM;
    if (!$useralliance['alliance_id']) {
        if ($allianceinfo['alliancerequested']) {
        $buttons .=<<<EOFORM
<input type="submit" name="rescindrequest" value="Rescind Join Request" class="btn btn-danger btn-sm"/>
EOFORM;
        } else {
        $buttons .=<<<EOFORM
<input type="submit" name="requestjoin" value="Request to Join" class="btn btn-success btn-sm"/>
EOFORM;
        }
    }
    $buttons .=<<<EOFORM
 <input type="submit" onclick="return confirm('Really add everyone in {$allianceinfo['name']} to your embargo list? (Depending on the alliance, a large portion of the market may disappear for you!)')"
name="embargoalliance" value="Embargo Alliance" class="btn btn-danger btn-sm"/>
<input type="submit" name="unembargoalliance" value="Unembargo Alliance" class="btn btn-success btn-sm"/>
<input type="submit" name="enemyalliance" value="Enemy Alliance" class="btn btn-danger btn-sm"/>
<input type="submit" name="unenemyalliance" value="UnEnemy Alliance" class="btn btn-success btn-sm"/>
<input type="submit" name="friendalliance" value="Friend Alliance" class="btn btn-success btn-sm"/>
<input type="submit" name="unfriendalliance" value="UnFriend Alliance" class="btn btn-danger btn-sm"/>
</form>
</center>
</br>
EOFORM;
}
echo <<<EOFORM
<center><h3>{$allianceinfo['name']}</h3></center>
{$buttons}
<center>{$displaypubdescription}</center>
<center><table class="table table-striped table-bordered">
EOFORM;
foreach ($alliancemembers as $member) {
if ($member['flag'] && ($_SESSION['hideflags'] == 0)) {
$display['flag'] = htmlentities($member['flag'], ENT_SUBSTITUTE, "UTF-8");
$flaghtml =<<<EOFORM
<img src="{$display['flag']}" height="20" width="20">
EOFORM;
} else {
$flaghtml = "";
}
echo <<<EOFORM
<tr><td style="width:25px">{$flaghtml}</td><td><a href="viewuser.php?user_id={$member['user_id']}">{$member['username']}</a></td><td>
EOFORM;
    if ($nations[$member['user_id']]) {
        $displaynations = array();
        $regiontypes = array(0 => "The Heavily Fortified Island of Admin", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
        $icontypes = array(0 => "Drugs", 1 => "Oil", 2 => "Copper", 3 => "Apples", 4 => "Machinery Parts");
        foreach ($nations[$member['user_id']] as $nation) {
            $displaynations[] =<<<EOFORM
<a href="viewnation.php?nation_id={$nation['nation_id']}">{$nation['name']} (<img src="images/icons/{$icontypes[$nation['region']]}.png"/>{$regiontypes[$nation['region']]})</a>
EOFORM;
        }
        echo implode(", ", $displaynations);
    } else {
        echo "Nationless";
    }
echo <<<EOFORM
</td></tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
EOFORM;


# Alliance Resources
# uncentered: <div class="col-md-6">
# centered: <div class="col-md-6 col-md-offset-3">
echo <<<EOFORM
</tbody>
</table>
</div>
</div>
    <div class="col-md-6 col-md-offset-3">
    <div class="panel panel-default">
        <div class="panel-heading">Alliance Resources</div>
        <table class="table">
        <thead>
        <tr>
EOFORM;
        if (!$nationinfo['hideicons']) {
        echo <<<EOFORM
            <td></td>
EOFORM;
        }
        echo <<<EOFORM
            <td style="text-align: right;">Resource</td>
            <td>Generated</td>
            <td>Used</td>
            <td>Net</td>
        </tr>
        </thead>
        <tbody>

        <td style="text-align: right;">GDP</td>
        <td style="text-align: center;"><span class="text-success"></span></td>
        <td style="text-align: center;"><span class="text-danger"></span></td>
        <td style="text-align: center;"><span class="text-success">{$alliancegdp}</span></td>
        </tr>     
EOFORM;
foreach($allianceaffectedresources as $name => $amount) {
if (!$allianceresources[$name]) $allianceresources[$name] = 0;
}
foreach($alliancerequiredresources as $name => $amount) {
if (!$allianceresources[$name]) $allianceresources[$name] = 0;
}
ksort($allianceresources);
foreach($allianceresources as $name => $amount) {
    if (!$allianceaffectedresources[$name]) {
    $allianceaffectedresources[$name] = 0;
    }
    if (!$alliancerequiredresources[$name]) {
    $alliancerequiredresources[$name] = 0;
    }
    $amountNet = ($allianceaffectedresources[$name] - $alliancerequiredresources[$name]);

    if($amountNet > 0) $amountNetClass = "text-success";
    elseif($amountNet == 0) $amountNetClass = "text-warning";
    else $amountNetClass = "text-danger";
    $displayaffected = commas($allianceaffectedresources[$name]);
    $displayrequired = commas($alliancerequiredresources[$name]);
    if ($amountNet < 0) {
    $displaynet = "-" . commas(abs($amountNet));
    } else {
    $displaynet = commas($amountNet);
    }

    echo <<<EOFORM
    <tr>
EOFORM;
    if (!$nationinfo['hideicons']) {
    echo <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$name}.png"/></td>
EOFORM;
    }
    echo <<<EOFORM
    <td style="text-align: right;">{$name}</td>
    <td style="text-align: center;"><span class="text-success">{$displayaffected}</span></td>
    <td style="text-align: center;"><span class="text-danger">{$displayrequired}</span></td>
    <td style="text-align: center;"><span class="{$amountNetClass}">{$displaynet}</span></td>
    </tr>
EOFORM;
}

} else {
    echo <<<EOFORM
<center>Alliance not found.</center> 
EOFORM;
}
?>