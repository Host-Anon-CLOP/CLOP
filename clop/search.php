<?php
include("backend/backend_search.php");
$extratitle = "Search - ";
include("header.php");
echo <<<EOFORM
<center><form action="search.php" method="post">
<input name="name" class="form-control" placeholder="User or Nation Name" style="width:300px"/>
<input type="submit" name="search" value="Search" class="btn btn-success"/>

<input type="submit" name="allianceless" value="Unallied Players" class="btn btn-success btn-sm"/>
\n\n
<input type="submit" name="prze" value="Prze" class="btn btn-success btn-sm"/>
<input type="submit" name="zeb" value="Zebs" class="btn btn-success btn-sm"/>
<input type="submit" name="saddle" value="Saddles" class="btn btn-success btn-sm"/>
<input type="submit" name="burro" value="Burros" class="btn btn-success btn-sm"/>

</form></center>
EOFORM;
include("footer.php");
?>