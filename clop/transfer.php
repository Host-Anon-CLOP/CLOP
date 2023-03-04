<?php
include("backend/backend_transfer.php");
$extratitle = "Empire Transfers - ";
include("header.php");
if (count($nations) == 1) {
	echo <<<EOFORM
<center>You only have one nation.</center>
EOFORM;
} else if ($nationinfo['government'] == "Decentralization") {
    echo <<<EOFORM
<center>Your Decentralized government cannot perform transfers.</center>
EOFORM;
} else {
if ($nationinfo['economy'] == "State Controlled") {
	$description = "With your State Controlled economy, you transfer both bits and items for free.";
} else if ($nationinfo['economy'] == "Free Market") {
	$description = "With your Free Market economy, you transfer items for 100 bits each and pay a 6% fee to transfer bits.";
} else {
	$description = "With your Poorly Defined economy, you transfer items for 50 bits each and pay a 3% fee to transfer bits.";
}
    echo <<<EOFORM
	<center><div class="well">Funds: <span class="text-success">{$displayfunds} Bits</span></div></center>
	<center>{$description}</center>
	<center><div style="width:400px;">
<div class="panel-footer">
Transfer to: <select name="determinenation_id" class="form-control"
onchange="document.getElementById('select1').value = this.value;
document.getElementById('select2').value = this.value;
document.getElementById('select3').value = this.value;
document.getElementById('select4').value = this.value;">
EOFORM;
    foreach ($nations as $nation_id => $nationname) {
		if ($nation_id != $_SESSION['nation_id']) {
        if (!$defaultnation_id) {
            $defaultnation_id = $nation_id;
        }
			echo <<<EOFORM
<option value="{$nation_id}"
EOFORM;
        if ($_POST['nation_id'] == $nation_id) {
            echo " selected ";
            $defaultnation_id = $nation_id;
        }
            echo <<<EOFORM
>{$nationname}</option>
EOFORM;
		}
    }
	 echo <<<EOFORM
		</select>
		</div>
        <div class="panel-footer">
        <form action="transfer.php" method="post">
<input type="hidden" name="token_transfer" value="{$_SESSION["token_transfer"]}"/>
<input type="hidden" id="select1" name="nation_id" value="{$defaultnation_id}"/>
            Transfer this resource: 
<select name="resource_id" class="form-control" style="width:210px;">
EOFORM;
foreach($resourceoptions as $option) {
    echo <<<EOFORM
<option value="{$option['resource_id']}"
EOFORM;
    if ($_POST['resource_id'] == $option['resource_id']) {
        echo " selected ";
    }
echo <<<EOFORM
>
{$option['optionslistname']}
</option>
EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="resourceamount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="transferresource" value="Transfer Resource" class="btn btn-primary"/>
               </span>
            </div>
        </form>
        </div>
        <div class="panel-footer">
        <form action="transfer.php" method="post">
<input type="hidden" name="token_transfer" value="{$_SESSION["token_transfer"]}"/>
<input type="hidden" id="select2" name="nation_id" value="{$defaultnation_id}"/>
            Transfer this weapon: 
            <select name="weapon_id" class="form-control" style="width:210px;">
      
EOFORM;
foreach($weaponoptions as $option) {
    echo <<<EOFORM
<option value="{$option['resource_id']}"
EOFORM;
    if ($_POST['weapon_id'] == $option['resource_id']) {
        echo " selected ";
    }
echo <<<EOFORM
>
{$option['optionslistname']}
</option>
EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="weaponamount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="transferweapon" value="Transfer Weapon" class="btn btn-primary"/>
               </span>
            </div>
        </form>
        </div>
        <div class="panel-footer">
        <form action="transfer.php" method="post">
<input type="hidden" name="token_transfer" value="{$_SESSION["token_transfer"]}"/>
<input type="hidden" id="select3" name="nation_id" value="{$defaultnation_id}"/>
            Transfer this armor: 
            <select name="armor_id" class="form-control" style="width:210px;">
      
EOFORM;
foreach($armoroptions as $option) {
    echo <<<EOFORM
<option value="{$option['resource_id']}"
EOFORM;
    if ($_POST['armor_id'] == $option['resource_id']) {
        echo " selected ";
    }
echo <<<EOFORM
>
{$option['optionslistname']}
</option>
EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="armoramount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="transferarmor" value="Transfer Armor" class="btn btn-primary"/>
               </span>
            </div>
          </form>
          </div>
		  <div class="panel-footer">
          <form action="transfer.php" method="post">
<input type="hidden" name="token_transfer" value="{$_SESSION["token_transfer"]}"/>
<input type="hidden" id="select4" name="nation_id" value="{$defaultnation_id}"/>
            <div class="input-group">
              <input type="text" class="form-control" name="money"/>
              <span class="input-group-btn">
                  <input type="submit" name="transfermoney" value="Transfer This Much Money" class="btn btn-primary"/>
               </span>
            </div>
			</div>
		</form>
		</div></center>
EOFORM;
}
include("footer.php");
?>