<?php
include("backend/backend_alliancemakewar.php");
$extratitle = "Alliance Make War - ";
include("header.php");
$token = $_SESSION["token_alliancemakewar"];
echo <<<EOFORM
<center>It costs {$constants['backstabbingrequired']} Backstabbing to attack an alliance with which you have a peace treaty, thereby breaking the treaty.</center>
<div class="row">
	<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Attack Costs</div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['allianceburdenrequired']} Burden</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['alliancecorruptionrequired']} Corruption</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Sadness</div>
	  <div class="col-md-6">{$constants['alliancesadnessrequired']} Sadness</div>
	  </div>
	  <div class="row">
      <div class="col-md-6">Theft</div>
	  <div class="col-md-6">{$constants['alliancetheftrequired']} Theft</div>
	  </div>
    </div>
	</div>
	<div class="col-md-6">
	<div class="panel panel-default">
      <div class="panel-heading">Cancellation Costs</div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['alliancecompassionforburden']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['alliancecompassionforcorrupt']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Sadness</div>
	  <div class="col-md-6">{$constants['alliancecompassionforsadness']} Compassion</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Theft</div>
	  <div class="col-md-6">{$constants['alliancecompassionfortheft']} Compassion</div>
	  </div>
    </div>
	</div>
	</div>
	<div class="row">
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Banked Defense Costs</div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['alliancesecurityforburden']} Security</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['allianceserenityforcorrupt']} Serenity</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Sadness</div>
	  <div class="col-md-6">{$constants['allianceserenityforsadness']} Serenity</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Theft</div>
	  <div class="col-md-6">{$constants['alliancesecurityfortheft']} Security</div>
	  </div>
    </div>
	</div>
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Nullification Costs</div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['zealforburden']} Zeal</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['zealforcorrupt']} Zeal</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Sadness</div>
	  <div class="col-md-6">{$constants['zealforsadness']} Zeal</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Theft</div>
	  <div class="col-md-6">{$constants['zealfortheft']} Zeal</div>
	  </div>
    </div>
	</div>
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Redirection Costs</div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['alliancemaliceforburden']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['alliancemaliceforcorrupt']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Sadness</div>
	  <div class="col-md-6">{$constants['alliancemaliceforsadness']} Malice</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Theft</div>
	  <div class="col-md-6">{$constants['alliancemalicefortheft']} Malice</div>
	  </div>
    </div>
	</div>
</div>
<form name="makewar" action="alliancemakewar.php" method="post">
<input type="hidden" name="token_alliancemakewar" value="{$token}"/>
<center><input name="name" placeholder="Target Alliance" class="form-control" type="text" style="width:200px;"/></center>
<div class="row">
	<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Burden</div>
	  <center>Forcibly give the target resources.</center>
<div class="row">
<div class="col-sm-4">
<select name="resource_id" class="form-control">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select>
</div><div class="col-sm-4">
	<input name="amount" placeholder="Amount" class="form-control" type="text" style="width:200px;"/>
</div>
<div class="col-sm-4">
	<input type="submit" name="burden" value="Burden" class="btn btn-danger" onclick="return confirm('Really perform a Burden attack?')"/>
</div></div></div></div>
	<div class="col-md-6">
<div class="panel panel-default">
<div class="panel-heading">Corruption</div>
<center>Alter the target's focus.</center>
<div class="row">
<div class="col-sm-6">
<select name="focuson" class="form-control">
<option value=""/>
<option value="1">Magic</option>
<option value="2">Loyalty</option>
<option value="4">Laughter</option>
<option value="8">Kindness</option>
<option value="16">Honesty</option>
<option value="32">Generosity</option>
</select>
</div>
<div class="col-sm-6">
<input type="submit" name="corrupt" value="Corrupt" class="btn btn-danger" onclick="return confirm('Really perform a Corruption attack?')"/>
</div></div></div></div></div>
<div class="row">
	<div class="col-md-6">
    <div class="panel panel-default">
		<div class="panel-heading">Sadness</div>
	<center>Reduce the target's satisfaction by 500.</center>
	<center><input type="submit" name="sadness" value="Sadden" class="btn btn-danger" onclick="return confirm('Really perform a Sadness attack?')"/></center>
	</div>
	</div>
<div class="col-md-6">
<div class="panel panel-default">
<div class="panel-heading">Theft</div>
	<center>Steal resources; whoever has the most resources in the alliance gets hit.</center>
<div class="row">
<div class="col-sm-6">
<select name="resourcetosteal" class="form-control">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select>
</div>
<div class="col-sm-6">
<input type="submit" name="theft" value="Steal" class="btn btn-danger" onclick="return confirm('Really perform a Theft attack?')"/>
</div></div></div></div>
</form>
EOFORM;
include("footer.php");
?>