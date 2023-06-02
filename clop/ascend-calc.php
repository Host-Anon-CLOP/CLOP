<?php
include("ascend-calc-backend.php");
$extratitle = "Ascend Calc - ";

echo <<<EOFORM
<center><form action="ascend-calc.php" method="post">
<input name="reputation" class="form-control" placeholder="Reputation" style="width:200px;"/>
<input name="pegasi" class="form-control" placeholder="Superpower Pegasi" style="width:200px;"/>
<input name="ticks" class="form-control" placeholder="Ticks To War" style="width:200px;"/>
<input type="submit" name="ascend-calc" value="Calculate" class="btn btn-info"/>
</form>
EOFORM;
?>