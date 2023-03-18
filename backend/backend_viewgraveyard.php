<?php
include_once("allfunctions.php");
$mysql['graveyard_id'] = (int)$_GET['graveyard_id'];
$sql = "SELECT * from graveyard WHERE graveyard_id = {$mysql['graveyard_id']}";
$nationinfo = onelinequery($sql);
?>