<?php
include("backend/backend_makeequipment.php");
if ($mode == "weapons") {
    $extratitle = "Make Weapons - ";
} else {
    $extratitle = "Make Armor - ";
}
include("header.php");
$temptype = 0;
$first = true;
echo <<<EOFORM
<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
EOFORM;
foreach ($availablemakeequip as $action => $info) {
if ($temptype != $info['type']) {
    if (!$first) {
        echo "</table></center>";
    }
    echo <<<EOFORM
    <center><h3>{$forcetypes[$info['type']]}</h3></center>
    <center><table class="table table-striped table-bordered">
EOFORM;
    $first = false;
    $temptype = $info['type'];
}
foreach ($info['displayitems'] as $item) {
    if ($item['is_used_up']) {
        $info['description'] .= <<<EOFORM
{$item['amount']} {$item['name']}<br/>
EOFORM;
    } else {
        $info['description'] .= <<<EOFORM
(Requires {$item['amount']} {$item['name']})<br/>
EOFORM;
    }
}
echo <<<EOFORM
<tr><td><div class="row">
	<div class="col-md-6"><p><b>{$info['name']}</b><br/>{$info['description']}</p></div>
	<div class="col-md-1"><p class="text-danger">{$info['displaycost']}</p></div>
	<div class="col-md-5">
		<form action="makeequipment.php" method="post">
            <input type="hidden" name="token_make{$plural}" value="{$_SESSION["token_make{$plural}"]}"/>
            <input type="hidden" name="mode" value="{$mode}"/>
			<input type="hidden" name="{$singular}recipe_id" value="{$action}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" value="{$info['name']}" class="btn btn-success btn-block"/>
				</div>
				<div class="col-xs-5">
					<div class="input-group">
					    <input name="times" value="1" class="form-control" type="text"/>
					    <span class="input-group-addon">times</span>
					</div>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
EOFORM;
include("footer.php");
?>