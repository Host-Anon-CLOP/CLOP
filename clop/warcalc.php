<?php
$extratitle = "War Calc - ";
echo <<<EOFORM
<center><h4>Create New Force</h4></center>
EOFORM;

echo <<<EOFORM
<center><form action="createforces.php" method="post">
<input type="hidden" name="token_createforces" value="{$_SESSION['token_createforces']}"/>
<select name="forcetype" class="form-control" style="width:210px;"/>
<option value=""></option>
EOFORM;
foreach($forcetypes as $forcetype => $typename) {
    echo <<<EOFORM
        <option value="{$forcetype}"
EOFORM;
    if ($forcetype == $_POST['forcetype']) {
        echo " selected ";
    }
    echo <<<EOFORM
>{$typename}</option>
EOFORM;
}
echo <<<EOFORM
</select><br/>
<input name="size" class="form-control" placeholder="Size" style="width:200px;"/>
<input name="name" class="form-control" placeholder="Name" style="width:200px;"/><br/>
<input name="training" class="form-control" placeholder="Training" style="width:200px;"/><br/>
<input type="submit" name="createforce" value="Create Force" class="btn btn-info"/>
</form>
EOFORM;
?>