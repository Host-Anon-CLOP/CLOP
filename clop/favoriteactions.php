<?php
include("backend/backend_favoriteactions.php");
$extratitle = "Favorite Actions - ";
include("header.php");
$tempname = "";
$first = true;
if ($favorites) {
echo <<<EOFORM
<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
<center><div id="container" class="js-masonry" data-masonry-options='{ "itemSelector": ".masonryitem", "isFitWidth": true }'>
EOFORM;
foreach ($favorites as $info) {
if ($tempname != $info['name']) {
    if (!$first) {
        echo <<<EOFORM
<tr><td><center>
<form action="favoriteactions.php" method="post" class="form-horizontal">
<input type="hidden" name="token_favoriteactions" value="{$_SESSION['token_favoriteactions']}"/>
<input type="hidden" name="recipe_id" value="{$oldinfo['recipe_id']}"/>
<div class="form-inline"><input type="submit" name="perform" value="This many:" class="btn btn-success"/>
<span><input name="times" value="1" class="form-control" type="text" placeholder="Times" style="width:75px"/></span>
</div></form></td></tr>
</table></div></div>
EOFORM;
    }
    echo <<<EOFORM
    <div class="masonryitem" style="padding: 10px; width: 300px">
	<div class="panel panel-default">
	<div class="panel-heading h4">{$info['name']}</div>
<table class="table table-striped table-bordered">
EOFORM;
    $first = false;
    $tempname = $info['name'];
}
$oldinfo = $info;
echo <<<EOFORM
<tr><td><center>
<form action="favoriteactions.php" method="post">
<input type="hidden" name="token_favoriteactions" value="{$_SESSION['token_favoriteactions']}"/>
<input type="hidden" name="recipe_id" value="{$info['recipe_id']}"/>
<input type="hidden" name="times" value="{$info['times']}"/>
<input type="submit" name="perform" value="{$info['times']} times" class="btn btn-success"/>
<input type="submit" name="remove" value="Remove" class="btn btn-danger"/>
</form></center>
</td></tr>
EOFORM;
}
echo <<<EOFORM
<tr><td><center>
<form action="favoriteactions.php" method="post" class="form-horizontal">
<input type="hidden" name="token_favoriteactions" value="{$_SESSION['token_favoriteactions']}"/>
<input type="hidden" name="recipe_id" value="{$oldinfo['recipe_id']}"/>
<div class="form-inline"><input type="submit" name="perform" value="This many:" class="btn btn-success"/>
<span><input name="times" value="1" class="form-control" type="text" placeholder="Times" style="width:75px"/></span>
</div></form></td></tr>
</table></div></div></div></center>
EOFORM;
} else {
echo <<<EOFORM
<center>You have no actions listed as your favorites.</center>
EOFORM;
}
include("footer.php");
?>