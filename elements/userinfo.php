<?
include("backend/backend_userinfo.php");
$extratitle = "User Info - ";
include("header.php");
$cssoptions = array(0 => "White", 1 => "Black", 2 => "Gray");
if ($userinfo['donator']) {
    echo <<<EOFORM
<center><b>Thank you for your donation!</b></center>
EOFORM;
}
echo <<<EOFORM
<center>{$userinfo['username']}</center>
<form name="newpassword" method="post" action="userinfo.php">
<input type="hidden" name="token_userinfo" value="{$_SESSION['token_userinfo']}"/>
<input type="hidden" name="user_id" value="{$getpost['user_id']}"/>
<center><table>
<tr><td>Current Password</td><td><input type="password" class="form-control" name="currentpassword" class="width:200px;" autocomplete="off"/></td></tr>
<tr><td>New Password</td><td><input type="password" name="password" class="form-control" class="width:200px;" autocomplete="off"/></td></tr>
<tr><td>Confirm New Password</td><td><input type="password" class="form-control" class="width:200px;" name="confirm_password"/></td></tr>
</table></center>
<center><input type="submit" name="action" value="New Password" class="btn btn-sm btn-warning"/></center>
<center><table>
<tr><td>Email</td><td><input name="email" class="form-control" class="width:200px;" value="{$display['email']}"/></td></tr>
</table></center>
<center><input type="submit" name="action" value="Change Email" class="btn btn-sm btn-warning"/></center></form>
<br/>
<center><form name="description" method="post" action="userinfo.php" class="form" role="form">
<textarea name="description" class="form-control" style="width:400px;">{$display['description']}</textarea>
<input type="hidden" name="token_userinfo" value="{$_SESSION['token_userinfo']}"/>
</br>
<input name="changedescription" type="submit" value="Change Description" class="btn btn-success"/>
</form></center>
<br/>
<center><form name="cssoptions" method="post" action="userinfo.php">
<input type="hidden" name="token_userinfo" value="{$_SESSION['token_userinfo']}"/>
<select name="css" class="form-control" style="width:210px;"/>
EOFORM;
foreach($cssoptions as $css => $option) {
    echo <<<EOFORM
        <option value="{$css}"
EOFORM;
    if ($_SESSION['css'] == $css) {
        echo " selected ";
    }
    echo <<<EOFORM
>{$option}</option>
EOFORM;
}
echo <<<EOFORM
</select><input type="submit" name="changecolor" value="Change Background Color" class="btn btn-sm btn-success"/></form></center><br/>
<form name="banners" method="post" action="userinfo.php">
<input type="hidden" name="token_userinfo" value="{$_SESSION['token_userinfo']}"/>
EOFORM;
if (!$userinfo['hidebanners']) {
echo <<<EOFORM
<center><input type="submit" name="hidebanners" value="Hide Top Banners" class="btn btn-success"/></center>
EOFORM;
} else {
echo <<<EOFORM
<center><input type="submit" name="showbanners" value="Show Top Banners" class="btn btn-success"/></center>
EOFORM;
}
if (!$userinfo['hidereports']) {
echo <<<EOFORM
<center><input type="submit" name="hidereports" value="Hide Report Details" class="btn btn-success"/></center>
EOFORM;
} else {
echo <<<EOFORM
<center><input type="submit" name="showreports" value="Show Report Details" class="btn btn-success"/></center>
EOFORM;
}
if (!$userinfo['hideicons']) {
echo <<<EOFORM
<center><input type="submit" name="hideicons" value="Hide Overview Icons" class="btn btn-success"/></center>
EOFORM;
} else {
echo <<<EOFORM
<center><input type="submit" name="showicons" value="Show Overview Icons" class="btn btn-success"/></center>
EOFORM;
}
echo <<<EOFORM
</form>
<form name="stasis" method="post" action="userinfo.php">
<input type="hidden" name="token_userinfo" value="{$_SESSION['token_userinfo']}"/>
EOFORM;
if (!$userinfo['stasismode']) {
    echo <<<EOFORM
<center><h5>Stasis Mode</h5></center>
If you enter stasis mode, you will be unable to log in at all for 24 hours. Ticks will not happen for you, and you will not be able to be attacked.<br/>
<center><input type="submit" name="enterstasis" onclick="return confirm('Really enter stasis for at least 24 hours?')"  value="Enter Stasis" class="btn btn-danger"/></center>
EOFORM;
} else {
echo <<<EOFORM
<center><input type="submit" name="leavestasis" value="Leave Stasis" class="btn btn-success"/></center>
EOFORM;
}
echo <<<EOFORM
</form>
EOFORM;
include("footer.php");
?>