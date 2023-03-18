<?php
include("backend/backend_search.php");
$extratitle = "Search - ";
include("header.php");
echo <<<EOFORM
<center><form action="search.php" method="post">
<input name="name" class="form-control" placeholder="User or Nation Name" style="width:300px"/>
<input type="submit" name="search" value="Search" class="btn btn-success"/>
</form></center>
EOFORM;
include("footer.php");
?>