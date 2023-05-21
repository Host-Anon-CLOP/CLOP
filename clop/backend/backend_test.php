<?php
include_once("allfunctions.php");

echo <<<EOFORM
<script>
var element = document.getElementById('group-fields');
var children = element.children;
for(var i=0; i<children.length; i++){
    var child = children[i];
    echo "$child.outerHTML";
  }
</script>
EOFORM;



?>