<?php
include("backend/backend_actions.php");
$extratitle = "Actions - ";
include("header.php");
$tempname = "";
$first = true;
echo <<<EOFORM
<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
<center><div id="container" class="js-masonry" data-masonry-options='{ "itemSelector": ".masonryitem", "isFitWidth": true }'>
EOFORM;
foreach ($availableactions as $action => $info) {
if ($tempname != $info['groupname']) {
    if (!$first) {
        echo "</table></div></div>";
    }
    echo <<<EOFORM
    <div class="masonryitem" style="padding: 10px; width: 400px">
	<div class="panel panel-default">
	<div class="panel-heading h4">{$info['groupname']}</div>
<table class="table table-striped table-bordered">
EOFORM;
    $first = false;
    $tempname = $info['groupname'];
}
if ($info['cost'] > 0) {
    $costline =<<<EOFORM
    <span class="text-danger">{$info['displaycost']}</span>
EOFORM;
} else if ($info['cost'] == 0) {
    $costline =<<<EOFORM
    <span class="text-warning">{$info['displaycost']}</span>
EOFORM;
} else {
    $costline =<<<EOFORM
    <span class="text-success">{$info['displaycost']}</span>
EOFORM;
}
echo <<<EOFORM
<tr><td>{$info['description']}<br/>
{$costline} bits<br/>
<form action="actions.php" method="post" class="form-horizontal"><input type="hidden" name="token_actions" value="{$_SESSION['token_actions']}"/>
<input type="hidden" name="recipe_id" value="{$action}"/>
<div class="form-inline"><input type="submit" value="{$info['name']}" class="btn btn-success"/>
<span class="pull-right"><input name="times" value="1" class="form-control" type="text" placeholder="Times" style="width:75px"/></span>
</div>
<input type="submit" name="favorite" value="Add to Favorites" class="btn btn-info"/>
</form>
</td></tr>
EOFORM;
}
echo <<<EOFORM
</table></div></div></div></center>
EOFORM;
include("footer.php");
?>