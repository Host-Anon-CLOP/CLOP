<?php
if ($_POST['ascend-calc']) {
    
    
    echo "ticks " . $_POST['ticks'] . "<br>";
    echo "rep " . abs($_POST['reputation']) . "<br>";
    echo "pegasi " . $_POST['pegasi'] . "<br>";
    echo "barracks/training " . $_POST['barracks'] . "<br>";

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
      $pegasi_damage_to_alicorns = round((1.4 * .1 * pow(1.5, ((20 - $_POST['barracks']) / 20))), 3);
      # defender bonus
      $pegasi_damage_to_alicorns = $pegasi_damage_to_alicorns * .75;
      $alicorn_damage_to_pegasi = round((10 * .45 * pow(1.5, (($_POST['barracks'] - 20) / 20))), 3);

      $alicorns_needed_defend = ceil($total_pegasi * $pegasi_damage_to_alicorns);
      $alicorns_needed_kill = ceil($total_pegasi / $alicorn_damage_to_pegasi);

      echo "<br><br>SUMMARY<br>";
      echo "=======<br>";
      echo "Damage Per Pegasi: " . $pegasi_damage_to_alicorns . "<br>";
      echo "Total Pegasi: " . $total_pegasi . "<br>";
      echo "Pegasi Killed by Alicorns: " . floor($alicorn_damage_to_pegasi * $alicorns_needed_defend) . "<br>";
      echo "Pegasi Remaining: " . ($total_pegasi - floor($alicorn_damage_to_pegasi * $alicorns_needed_defend)) . "<br>";

      echo "<br>";
      echo "Damage to Pegasi per Alicorn: " . $alicorn_damage_to_pegasi . "<br>";

      echo "<br>";
      echo "Alicorns Required to Oneshot Pegasi: " . $alicorns_needed_kill . "<br>";
      echo "ALICORNS REQUIRED TO DEFEND: " . $alicorns_needed_defend . "<br>";
      
      echo "<br>";
      echo "Gems Upkeep for Alicorns: " . ($alicorns_needed_defend * 10) . "<br>";
      echo "Bits to Create Alicorns: " . (($alicorns_needed_defend * 2000000) / 1000000) . " (mil)";
}
?>