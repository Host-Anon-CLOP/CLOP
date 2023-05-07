<?php
$extratitle = "War Calc - ";
echo <<<EOFORM
<center><h4>Create New Force</h4></center>
<center><form action="warcalc.php" method="post">
<input name="name" class="form-control" placeholder="Name" style="width:50px;"/>
<select name="forcetype" class="form-control" style="width:200px;"/>
    <option value="1">1</option>
    <option value="2">2</option>
</select>
<input name="size" class="form-control" placeholder="Size" style="width:50px;"/>
<input name="training" class="form-control" placeholder="Training" style="width:50px;"/>
<input type="submit" name="createforce" value="Create Force" class="btn btn-info"/>
</form>
EOFORM;
?>