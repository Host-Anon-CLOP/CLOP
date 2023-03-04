<?php
include("backend/backend_equipforces.php");
$extratitle = "Equip Forces - ";
include("header.php");
$tempname = "";
if ($forces) {
foreach ($forces AS $force) {
    if ($tempname != $force['forcegroup_id']) {
        if($tempname != "") echo "</table></div>";
        echo <<<EOFORM
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">{$force['groupname']}</h3>
    </div>
    <table class="table table-bordered">
EOFORM;
        $tempname = $force['forcegroup_id'];
    }
    echo <<<EOFORM
        <tr>
        <td>
            {$force['name']}</td><td>{$forcetypes[$force['type']]}<br/>
            Size: {$force['size']}<br/>
            Training: {$force['training']}
        </td>
        <td>
EOFORM;
    if ($force['type'] != 6) {
    echo <<<EOFORM
            <form action="equipforces.php" method="post">
                <input type="hidden" name="token_equipforces" value="{$_SESSION['token_equipforces']}"/>
                <input type="hidden" name="force_id" value="{$force['force_id']}"/>
                <div class="input-group">
                    <select name="weapon_id" class="form-control" style="width:200px;"/>
                        <option value="">Scrounged</option>
EOFORM;
    foreach ($weapons[$force['type']] as $weapon) {
        echo <<<EOFORM
                            <option value="{$weapon['weapon_id']}"
EOFORM;
    if ($force['weapon_id'] == $weapon['weapon_id']) {
        echo " selected ";
    }
    echo <<<EOFORM
>{$weapon['displayname']}</option>
EOFORM;
    }
    echo <<<EOFORM
                    </select>
                    <input type="submit" name="changeweapon" value="Change Weapon" class="btn btn-info"/>
                </div>
            </form><br/>
            <form action="equipforces.php" method="post">
                <input type="hidden" name="token_equipforces" value="{$_SESSION['token_equipforces']}"/>
                <input type="hidden" name="force_id" value="{$force['force_id']}"/>
                <div class="input-group">
                    <select name="armor_id" class="form-control" style="width:200px;"/>
                        <option value="">Scrounged</option>
EOFORM;
    foreach ($armors[$force['type']] as $armor) {
        echo <<<EOFORM
                        <option value="{$armor['armor_id']}"
EOFORM;
    if ($force['armor_id'] == $armor['armor_id']) {
        echo " selected ";
    }
    echo <<<EOFORM
>{$armor['displayname']}</option>
EOFORM;
    }
    echo <<<EOFORM
                    </select>
                    <input type="submit" name="changearmor" value="Change Armor" class="btn btn-info"/>
                </div>
            </form>
EOFORM;
}
	echo <<<EOFORM
        </td>
    </tr>
EOFORM;
}
} else {
    echo <<<EOFORM
<center>You have no forces at home to equip!</center>
EOFORM;
}
include("footer.php");
?>