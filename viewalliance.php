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
if ($member['flag']) {
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
        foreach ($nations[$member['user_id']] as $nation) {
            $displaynations[] =<<<EOFORM
<a href="viewnation.php?nation_id={$nation['nation_id']}">{$nation['name']}</a>
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
} else {
    echo <<<EOFORM
<center>Alliance not found.</center> 
EOFORM;
}
?>