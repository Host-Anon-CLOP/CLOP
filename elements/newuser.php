<?php
include("backend/backend_newuser.php");
include("header.php");
echo <<<EOFORM
<h3>One account per person.</h3>
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
    <input type="password" class="form-control" id="password" placeholder="Password" name="password" maxlength="25"/>
  </div>
  <div class="form-group">
    <label for="confirmpassword">Confirm Password</label>
    <input type="password" class="form-control" id="confirmpassword" placeholder="Confirm Password" name="confirmpassword" maxlength="25"/>
  </div>
  <div class="form-group">
    <label for="asdf">Email</label>
    <p class="help-block">This is completely optional; there's no email confirmation.<br/>If you're under 13, leave this blank.</p>
    <input type="email" class="form-control" id="asdf" placeholder="Email" name="asdf" maxlength="128" value="{$mysql['asdf']}"/>
  </div>
  <div class="form-group">
    <label for="userdescription">Description</label>
    <textarea class="form-control" id="description" name="userdescription">{$display['userdescription']}</textarea>
  </div>
<input type="submit" class="btn btn-success" name="allnew" value="Join The Compounds of Harmony"/>
</form>
EOFORM;
include("footer.php");
?>