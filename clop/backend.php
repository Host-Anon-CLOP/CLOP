<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attackerData = [];
    $defenderData = [];

    // Retrieve data for attackers
    if (!empty($_POST['type']) && is_array($_POST['type'])) {
        foreach ($_POST['type'] as $index => $type) {
            if (!empty($type)) {
                $attackerData[] = [
                    'type' => $type,
                    'weapon' => $_POST['weapon'][$index],
                    'armor' => $_POST['armor'][$index],
                    'size' => $_POST['size'][$index],
                    'training' => $_POST['training'][$index]
                ];
            }
        }
    }

    // Retrieve data for defenders
    // ...

    // Display the entered data
    echo "<h2>Attacker Data:</h2>";
    echo "<pre>";
    print_r($attackerData);
    echo "</pre>";

    echo "<h2>Defender Data:</h2>";
    // Display defender data
    // ...
}
?>