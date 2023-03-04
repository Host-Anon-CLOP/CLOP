<?php
include("backend/backend_createforces.php");
$extratitle = "Create Forces - ";
include("header.php");
echo <<<EOFORM
<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
<center><h4>Create New Force</h4></center>
<center><table>
<tr><th>Force</th><th>Price per Unit</th><th>Upkeep</th></tr>
<tr><td>Cavalry</td><td>200,000</td><td>5 sugar/12 hours</td></tr>
<tr><td>Tanks</td><td>300,000</td><td>5 gasoline/12 hours</td></tr>
<tr><td>Pegasi</td><td>300,000</td><td>5 coffee/12 hours</td></tr>
<tr><td>Unicorns</td><td>400,000</td><td>5 gems/12 hours</td></tr>
<tr><td>Naval</td><td>250,000</td><td>5 gasoline/12 hours</td></tr>
EOFORM;
if ($nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Transponyism") {
    echo <<<EOFORM
<tr><td>Alicorns</td><td>2,000,000</td><td>10 gems/12 hours</td></tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
<center>Mercenaries start with training 10 but cost double to quadruple, depending on your relationship.</center>
EOFORM;
if ($nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Transponyism") {
    echo <<<EOFORM
<center>Alicorns need no weapons or armor; they always do 10 damage and have an armor value of .10.</center>
EOFORM;
}
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
<select name="mercenaries" class="form-control" style="width:210px;"/>
EOFORM;
foreach($mercenarytypes as $mercenarytype => $typename) {
    echo <<<EOFORM
        <option value="{$mercenarytype}"
EOFORM;
    if ($mercenarytype == $_POST['mercenaries']) {
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
<input type="submit" name="createforce" value="Create Force" class="btn btn-info"/>
</form>
EOFORM;
?>