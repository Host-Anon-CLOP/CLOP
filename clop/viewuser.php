<?
include("backend/backend_viewuser.php");
$extratitle = "View User - ";
include("header.php");
$cssoptions = array(0 => "White", 1 => "Black", 2 => "Gray");
if (!$userinfo) {
echo <<<EOFORM
<center><h3>User not found!</h3></center>
EOFORM;
} else {
if ($userinfo['donator']) {
    echo <<<EOFORM
<center><b>This user has donated to >CLOP!</b></center>
EOFORM;
}
if ($userinfo['stasismode']) {
    echo <<<EOFORM
<center><b>This user is in stasis.</b></center>
EOFORM;
}
echo <<<EOFORM
<center>{$userinfo['username']}</center>
EOFORM;
if ( ($userinfo['flag']) && ($_SESSION['hideflags'] == 0) ) {
    $display['flag'] = htmlentities($userinfo['flag'], ENT_SUBSTITUTE, "UTF-8");
    $flaghtml =<<<EOFORM
    <img src="{$display['flag']}" height="150" width="250">
EOFORM;
    echo <<<EOFORM
    <center>{$flaghtml}</center>
EOFORM;
    }
if ($userinfo['alliancename']) {
echo <<<EOFORM
<center><a href="viewalliance.php?alliance_id={$userinfo['alliance_id']}">{$userinfo['alliancename']}</a></center>
EOFORM;
}
echo <<<EOFORM
<center>{$display['description']}</center>
EOFORM;
if ($_SESSION['user_id'] && ($_SESSION['user_id'] != $mysql['user_id'])) {
echo <<<EOFORM
<center>Send Message</center>
<form name="newmessage" method="post" action="viewuser.php">
<input type="hidden" name="token_viewuser" value="{$_SESSION['token_viewuser']}"/>
<input type="hidden" name="user_id" value="{$mysql['user_id']}"/>
<center><textarea name="message">{$display['message']}</textarea></center>
<center><input type="submit" name="action" value="Send Message" class="btn btn-sm btn-success"/></center>
</form>
EOFORM;
}
if (!empty($nations)) {
echo <<<EOFORM
<center>Nations</center>
<center><table>
EOFORM;
$regiontypes = array(0 => "The Heavily Fortified Island of Admin", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
$icontypes = array(0 => "Drugs", 1 => "Oil", 2 => "Copper", 3 => "Apples", 4 => "Machinery Parts");
foreach ($nations as $nation) {
echo <<<EOFORM
<tr><td><a href="viewnation.php?nation_id={$nation['nation_id']}">{$nation['name']} (<img src="images/icons/{$icontypes[$nation['region']]}.png"/>{$regiontypes[$nation['region']]})</a></td></tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
EOFORM;
} else {
echo <<<EOFORM
<center>This user is nationless!</center>
EOFORM;
}
}
include("footer.php");
?>