<?php
if ($_POST['ascend-calc']) {
    
    
    echo "ticks " . $_POST['ticks'] . "<br>";
    echo "rep " . abs($_POST['reputation']) . "<br>";
    echo "pegasi " . $_POST['pegasi'] . "<br>";

    $current_rep = abs($_POST['reputation']);
    $total_pegasi = $_POST['pegasi'];

    for ($x = 0; $x <= $_POST['ticks']; $x++) {
        $current_rep += 1;
        if ($current_rep >= 25) {
            $new_pegasi = (ceil($current_rep / 4) * 2);
        } 
        else {
            $new_pegasi = 0;
        }
        $total_pegasi = $total_pegasi + $new_pegasi;
        echo "<br>";
        echo "tick " . $x . "<br>";
        echo "rep: " . $current_rep . "<br>";
        echo "new_pegasi: " . $new_pegasi . "<br>";
        echo "total pegasi: " . $total_pegasi . "<br>";
      }
    
      # PEGASI ARMOR VS NAVAL 0.45
      $damage_to_alicorns = round((1.4 * .1 * pow(1.5, ((20 - $_POST['barracks']) / 20))), 3);
      # defender bonus
      $damage_to_alicorns = $damage_to_alicorns * .75;
      $alicorns_needed = ($total_pegasi * $damage_to_alicorns);

      echo "<br><br>calc finished<br>";
      echo "Damage Per Pegasi: " . $damage_to_alicorns . "<BR>";
      echo "Total Pegasi: " . $total_pegasi . "<br>";
      echo "Alicorns to Defend: " . $alicorns_needed;
}
?>