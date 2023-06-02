<?php
include("ascend-calc-backend.php");
$extratitle = "Create Forces - ";

echo <<<EOFORM
<center><form action="createforces.php" method="post">
<input type="hidden" name="token_createforces" value="{$_SESSION['token_createforces']}"/>
<select name="forcetype" class="form-control" style="width:210px;"/>
<option value=""></option>
EOFORM;
?>