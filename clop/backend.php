<?php
require_once("backend/allfunctions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attackerData = [];
    $defenderData = [];

    $forcetypes = array("Cavalry" => 1, "Tanks" => 2, "Pegasi" => 3, "Unicorns" => 4, "Naval" => 5, "Alicorns" => 6);
    $weapontypes = array("Scrounged" => 0, "PRC-E6" => 1,"PRC-E7" => 2,"PRC-E8" => 3,"ACFU" => 4,"ATFU" => 5,"APFU" => 6,"AUFU" => 7,"K9P" => 8,"ELBO-GRS" => 9,"Chem-Light" => 10,"PropWash" => 11,"SteamBucket" => 12,"CanopyLights" => 13,"LongStand" => 14,"LongWeight" => 15,"GridSquares" => 16,"Shoreline" => 17,"WaterHammer" => 18,"WaterlineEraser" => 19);
    $armortypes = array("Scrounged" => 0,"Barding" => 1,"Bigdog" => 2,"Nope" => 3,"Trundle" => 4,"Shepherd" => 5,"Ohno" => 6,"Titan" => 7,"Cooler" => 8,"Wonder" => 9,"Griffin" => 10,"Dragon" => 11,"Hornshield" => 12,"Librarian" => 13,"Shining" => 14,"D2A" => 15,"C-PON3" => 16,"Esohes" => 17,"Shubidu" => 18);

    // Retrieve data for attackers
    if (!empty($_POST['type']) && is_array($_POST['type'])) {
# Create Forcegroup
$sql=<<<EOSQL
truncate forcegroups_calc
EOSQL;
$GLOBALS['mysqli']->query($sql);

$sql=<<<EOSQL
truncate forces_calc
EOSQL;
$GLOBALS['mysqli']->query($sql);


$sql=<<<EOSQL
INSERT INTO forcegroups_calc (nation_id, location_id, attack_mission, name) VALUES (1, 1, 1, 'attackers')
EOSQL;
$GLOBALS['mysqli']->query($sql);

# Get Forcegroup ID
$sql = "SELECT forcegroup_id FROM forcegroups_calc WHERE name = 'attackers'";
$sth = $GLOBALS['mysqli']->query($sql);
$forcegroup = mysqli_fetch_array($sth)['forcegroup_id'];

        foreach ($_POST['type'] as $index => $type) {
            if (!empty($type)) {
                $name = $type . '_' . $_POST['weapon'][$index] . '_' . $_POST['armor'][$index] . '_' . $_POST['training'][$index] . '_' . $_POST['size'][$index];

                $attackerData[] = [
                    'type' => $type,
                    'forcetype' => $forcetypes[$type],
                    'weapon' => $_POST['weapon'][$index],
                    'weapontype' => $weapontypes[$_POST['weapon'][$index]],
                    'armor' => $_POST['armor'][$index],
                    'armortype' => $armortypes[$_POST['armor'][$index]],
                    'size' => $_POST['size'][$index],
                    'training' => $_POST['training'][$index],
                    'name' => $name
                ];

# Create Force
$sql=<<<EOSQL
INSERT INTO forces_calc (nation_id, size, type, weapon_id, armor_id, training, name, forcegroup_id) VALUES (1, {$_POST['size'][$index]}, $type, '{$weapontypes[$_POST['weapon'][$index]]}', {$armortypes[$_POST['armor'][$index]]}, {$_POST['training'][$index]}, '{$name}', '1')
EOSQL;
$GLOBALS['mysqli']->query($sql);

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

    echo "<h2>MySQL Result</h2>";
    // Display defender data
    // ...

# DELETE AFTER DONE
#$sql=<<<EOSQL
#truncate forces_calc
#EOSQL;
#$GLOBALS['mysqli']->query($sql);
}
?>