<?php
include("backend/allfunctions.php");
include("ascend-calc-backend.php");
$extratitle = "Create Forces - ";

echo <<<EOFORM
</select><br/>
<input name="size" class="form-control" placeholder="Size" style="width:200px;"/>
<input name="name" class="form-control" placeholder="Name" style="width:200px;"/><br/>
<input type="submit" name="createforce" value="Create Force" class="btn btn-info"/>
</form>
EOFORM;
?>