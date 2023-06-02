<?php
if ($_POST['ascend-calc']) {
    
    
    echo "ticks " . $_POST['ticks'] . "<br>";
    echo "rep " . $_POST['reputation'] . "<br>";
    echo "pegasi " . $_POST['pegasi'] . "<br>";

    for ($x = 0; $x <= $_POST['ticks']; $x++) {
        echo "The number is: $x <br>";
      }
    
}
?>