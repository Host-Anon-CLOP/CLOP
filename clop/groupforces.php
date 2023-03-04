<?php
include("backend/backend_groupforces.php");
$extratitle = "Group Forces - ";
include("header.php");
$tempid = "";
if ($forces) {
foreach ($forces AS $force) {
    if ($tempid != $force['forcegroup_id']) {
        if($tempid != "") echo "</table></div>";
        echo <<<EOFORM
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6">
                <form action="groupforces.php" method="post">
                    <input type="hidden" name="token_groupforces" value="{$_SESSION['token_groupforces']}"/>
                    <input type="hidden" name="forcegroup_id" value="{$force['forcegroup_id']}"/>
                    <div class="input-group">
                        <input name="name" class="form-control" placeholder="Name" value="{$force['groupname']}"/>
                        <span class="input-group-btn">
                            <input type="submit" name="renamegroup" value="Rename Group" class="btn btn-info"/>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form action="groupforces.php" method="post">
                    <input type="hidden" name="token_groupforces" value="{$_SESSION['token_groupforces']}"/>
                    <input type="hidden" name="forcegroup_id" value="{$force['forcegroup_id']}"/>
                    <div class="input-group">
                        <input type="submit" name="mergegroups" value="Merge With" class="btn btn-info"/>
                        <select name="targetgroup_id" class="form-control" style="width:200px;"/>
EOFORM;
    foreach ($eligiblegroups[$force['location_id']] as $eligiblegroup) {
        if ($eligiblegroup['forcegroup_id'] != $force['forcegroup_id']) {
        echo <<<EOFORM
                            <option value="{$eligiblegroup['forcegroup_id']}">{$eligiblegroup['groupname']}</option>
EOFORM;
        }
    }
    echo <<<EOFORM
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <table class="table table-bordered">

EOFORM;
        $tempid = $force['forcegroup_id'];
    }
    echo <<<EOFORM
        <tr>
            <td>
                <form action="groupforces.php" method="post">
                    <input type="hidden" name="token_groupforces" value="{$_SESSION['token_groupforces']}"/>
                    <input type="hidden" name="force_id" value="{$force['force_id']}"/>
                    <div class="input-group">
                        <input name="name" class="form-control" placeholder="Name" value="{$force['name']}"/>
                        <span class="input-group-btn">
                            <input type="submit" name="renameforce" value="Rename Force" class="btn btn-info"/>
                        </span>
                    </div>
                </form>
                <br/>
                {$forcetypes[$force['type']]}<br/>
EOFORM;
    if ($force['type'] != 6) {
    echo <<<EOFORM
                {$force['weaponname']}<br/>
                {$force['armorname']}<br/>
EOFORM;
    }
    echo <<<EOFORM
                Size: {$force['size']}<br/>
                Training: {$force['training']}</td><td>
                <form action="groupforces.php" method="post">
                    <input type="hidden" name="token_groupforces" value="{$_SESSION['token_groupforces']}"/>
                    <input type="hidden" name="force_id" value="{$force['force_id']}"/>
                    <div class="input-group">
                        <input type="submit" name="combineforces" value="Combine With" class="btn btn-info"/>
                        <select name="targetforce_id" class="form-control" style="width:200px;"/>
EOFORM;
    foreach ($eligibleforces[$force['forcegroup_id']] as $eligibleforce) {
        if ($eligibleforce['force_id'] != $force['force_id']) {
        echo <<<EOFORM
                            <option value="{$eligibleforce['force_id']}">{$eligibleforce['name']}</option>
EOFORM;
        }
    }
    if ($force['location_id'] != $_SESSION['nation_id']) {
        $extramessage = "You will lose that force\'s weapons and armor!";
    } else {
        $extramessage = "";
    }
    echo <<<EOFORM
                        </select>
                    </div>
                </form><br/>
                <form action="groupforces.php" method="post">
                    <input type="hidden" name="token_groupforces" value="{$_SESSION['token_groupforces']}"/>
                    <input type="hidden" name="force_id" value="{$force['force_id']}"/>
                    <div class="input-group">
                        <span class="input-group-btn">
                            <input type="submit" name="splitforce" value="Split Off:" class="btn btn-info"/>
                        </span>
                        <input name="size" class="form-control" placeholder="Size" style="width:60px;"/>
                    </div>
                </form><br/>
                <form action="groupforces.php" method="post">
                    <input type="hidden" name="token_groupforces" value="{$_SESSION['token_groupforces']}"/>
                    <input type="hidden" name="force_id" value="{$force['force_id']}"/>
                    <input type="submit" name="splitgroup" value="Make Own Group" class="btn btn-info"/>
                </form><br/>
                <form action="groupforces.php" method="post">
                    <input type="hidden" name="token_groupforces" value="{$_SESSION['token_groupforces']}"/>
                    <input type="hidden" name="force_id" value="{$force['force_id']}"/>
                    <input type="submit" onclick="return confirm('Really destroy {$force['name']}? {$extramessage}')" name="destroy" value="Destroy" class="btn btn-danger"/>
                </form>
            </td>
        </tr>
EOFORM;
}
} else {
    echo <<<EOFORM
<center>You have no forces to group!</center>
EOFORM;
}
include("footer.php");
?>