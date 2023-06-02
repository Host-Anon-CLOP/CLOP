<?php
require_once("backend/allfunctions.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $superpower_count = $_POST['weapon'][$count]

    echo "<h2>Attacker Data:</h2>";
    echo "<pre>";
    foreach ($attackerData as $attacker) {
        echo $attacker['unit'] . ' ' . $attacker['weapon'] . ' ' . $attacker['armor'] . ' size:' . $attacker['size'] . ' train:' . $attacker['training'] . '<br>';
    }
    echo "</pre>";
}
?>