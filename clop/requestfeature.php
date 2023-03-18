<?php
require_once("backend/backend_requestfeature.php");
include("header.php");
$_SESSION['token_featureform'] = sha1(rand());
echo <<<EOTXT
<style>
#submitbtn {
padding-left: 20px;
padding-right: 20px;
}
#isbug {
margin-left: 10px;
margin-right: 10px;
}
</style>
<center>
<h3>People's Republic of >ReClop</h2>
<h5>Global rules 1 & 2 doesn't apply here!</h5>
</center>
<b>Instead, they are superseded by <a href="requestrules.php" target="_self">those two rules</a>. Make sure you familiarize with them!</b></br>
Your request will be reviewed, and if it's OK, it will be made into a poll, where users can vote if they would want to see your idea implemented in game or not.</br>
Submit your ideas about looks, game mechanics, rules, bugs, users, admins and <abbr title="within rules">everything</abbr>!
</br></br></br>
<div class="row">
<form name="feature-form" action="requestfeature.php" method="post" class="form">
<input type="hidden" name="token_featureform" value="{$_SESSION['token_featureform']}">
<div class="form-group">
<div class="form-group">
<label>Poll Title</label>
<input type="text" name="title" placeholder="General idea of your feature" class="form-control" value="{$old['title']}">
</div>
<div class="form-group">
<label>Description</label>
<textarea rows="5" name="description" placeholder="More elaborate description" class="form-control">{$old['description']}</textarea>
</div>
<div class="form-group">
<p class="pull-left">Poll voting options will be chosen accordingly. If you have a preference, outline them in description.</p>
<div class="pull-right">
<label for="isbug">Bug?</label>
<input type="checkbox" name="isbug" id="isbug" {$waschecked}>
<input type="submit" name="action" value="Submit" class="btn btn-success" id="submitbtn">
</div>
</div>
</form>
</div>
</center>
EOTXT;
