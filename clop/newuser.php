<?php
include("backend/backend_newuser.php");
include("header.php");
foreach ($regions as $key => $value) {
if ($_POST['region'] == $key) {
    $selected = "selected";
} else {
    $selected = "";
}
$regionlist .=<<<EOFORM
<option value="{$key}" {$selected}>{$value}</option>
EOFORM;
}
foreach ($subregions as $key => $value) {
if ($_POST['subregion'] == $key) {
    $selected = "selected";
} else {
    $selected = "";
}
$subregionlist .=<<<EOFORM
<option value="{$key}" {$selected}>{$value}</option>
EOFORM;
}
echo <<<EOFORM
Hold it! Have you read the <a href="rules.php">Rules</a> and the <a href="https://docs.google.com/document/u/0/d/1jSinNyYJCHkoDvQgaJkD_z2g9SV5G6v9gsKI-nwmrYM/mobilebasic">Guide</a>?
<form name="newuser" method="post" action="newuser.php" role="form">
<input type="hidden" name="token_newuser" value="{$_SESSION['token_newuser']}"/>
Leave this field blank (stops crawler spambots) <input name="username" maxlength="25"/>
  <div class="form-group">
    <label for="realusername">Username</label>
    <p class="help-block">Other people can see this and it can't be changed. Choose wisely.</p>
    <input type="text" class="form-control" id="realusername" placeholder="Username" name="realusername" maxlength="25" value="{$display['realusername']}"/>
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" placeholder="Password" name="password" maxlength="250"/>
  </div>
  <div class="form-group">
    <label for="confirmpassword">Confirm Password</label>
    <input type="password" class="form-control" id="confirmpassword" placeholder="Confirm Password" name="confirmpassword" maxlength="250"/>
  </div>
  <div class="form-group">
    <label for="asdf">Email</label>
    <p class="help-block">This is completely optional; there's no email confirmation.<br/>If you're under 13, leave this blank.</p>
    <input type="email" class="form-control" id="asdf" placeholder="Email" name="asdf" maxlength="128" value="{$mysql['asdf']}"/>
  </div>
  <div class="form-group">
    <label for="nationname">Nation Name</label>
    <input type="text" class="form-control" id="nationname" placeholder="Nation Name" name="nationname" maxlength="40" value="{$display['nationname']}">
  </div>
  <div class="form-group">
    <label for="nationdescription">Nation Description</label>
    <textarea class="form-control" id="nationdescription" name="nationdescription">{$display['nationdescription']}</textarea>
  </div>
  <div class="form-group">
    <label for="region">Region</label>
    <select name="region">
			{$regionlist}
		</select>
  </div>
  <div class="form-group">
    <label for="subregion">Subregion</label>
    <select name="subregion">
			{$subregionlist}
		</select>
  </div>
<input type="submit" class="btn btn-success" value="Join >CLOP"/>
</form>
EOFORM;
include("footer.php");
?>