<?php
include("backend/backend_search.php");
$extratitle = "Search - ";
include("header.php");
echo <<<EOFORM
<center><form action="search.php" method="post">
Search individual User or Nation:
<input name="name" class="form-control" placeholder="User or Nation Name" style="width:300px"/>
<input type="submit" name="search" value="Search" class="btn btn-success"/>
<br>
<br>
<br>

Search by Category:
<br>
<input type="submit" name="Allianceless" value="Allianceless" class="btn btn-success btn-sm"/>
<input type="submit" name="Burrozil" value="Burrozil" class="btn btn-success btn-sm"/>
<input type="submit" name="Saddle " value="Saddle " class="btn btn-success btn-sm"/>
<input type="submit" name="Zebrica" value="Zebrica" class="btn btn-success btn-sm"/>
<input type="submit" name="Przewalskia" value="Przewalskia" class="btn btn-success btn-sm"/>

</form></center>
EOFORM;
include("footer.php");
?>