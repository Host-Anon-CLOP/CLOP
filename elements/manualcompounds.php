<?php
include("backend/backend_manualcompounds.php");
$extratitle = "Manual Compounds - ";
include("header.php");
$token = $_SESSION["token_manualcompounds"];
if ($userinfo['tier'] < 2) {
echo <<<EOFORM
<center>At Tier 1, you can't really use this page yet.</center>
EOFORM;
}
$elementslist = elementsdropdown(true, false);
echo <<<EOFORM
This page manually compounds new elements out of elements you have. It takes multiples of 10.
If you compound two different elements, you get 9 of the combined element for each 10 of the elements you use; for three elements, you get 8 back, and so on.
You cannot create an element with a higher tier than your own. There is no un-compounding, so be careful.
<center>
<form name="newcompound" action="manualcompounds.php" method="post">
<input type="hidden" name="token_manualcompounds" value="{$token}"/>
<select name="compound1" class="form-control" style="width:210px;">
EOFORM;
echo $elementslist;
echo <<<EOFORM
</select>
<select name="compound2" class="form-control" style="width:210px;">
EOFORM;
echo $elementslist;
echo <<<EOFORM
</select>
<select name="compound3" class="form-control" style="width:210px;">
EOFORM;
echo $elementslist;
echo <<<EOFORM
</select>
<select name="compound4" class="form-control" style="width:210px;">
EOFORM;
echo $elementslist;
echo <<<EOFORM
</select>
<select name="compound5" class="form-control" style="width:210px;">
EOFORM;
echo $elementslist;
echo <<<EOFORM
</select>
<select name="compound6" class="form-control" style="width:210px;">
EOFORM;
echo $elementslist;
echo <<<EOFORM
</select>
<div class="input-group">
<input name="amount" placeholder="Amount" value="" class="form-control" style="width:100px;"> * 10
<input type="submit" name="compound" value="Compound" class="btn btn-success"/>
</div>
</form></center>
EOFORM;
echo displayresources($resourcelist, $userinfo['hideicons']);
include("footer.php");
?>