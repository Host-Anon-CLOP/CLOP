<?php
include("backend/backend_makewar.php");
$extratitle = "Make War - ";
include("header.php");
$token = $_SESSION["token_makewar"];
$mercilessness2 = $constants['mercilessnessrequired'] * 4;
$mercilessness3 = $constants['mercilessnessrequired'] * 9;
$mercilessness4 = $constants['mercilessnessrequired'] * 16;
echo <<<EOFORM
<div class="row">
	<div class="col-md-4">
    <div class="panel panel-default">
      <div class="panel-heading">Attack Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['brutalityrequired']} Brutality</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['burdenrequired']} Burden</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['corruptionrequired']} Corruption</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['despairrequired']} Despair</div>
	  </div>
	  <div class="row">
      <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['robberyrequired']} Robbery</div>
	  </div>
    </div>
	</div>
	<div class="col-md-4">
	<div class="panel panel-default">
      <div class="panel-heading">Special Target Costs</div>
	  <div class="row">
	  <div class="col-md-6">Tier 4</div>
	  <div class="col-md-6">{$constants['mercilessnessrequired']} Mercilessness</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Tier 3</div>
	  <div class="col-md-6">{$mercilessness2} Mercilessness</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Tier 2</div>
	  <div class="col-md-6">{$mercilessness3} Mercilessness</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Tier 1</div>
	  <div class="col-md-6">{$mercilessness4} Mercilessness</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Alliance Member</div>
	  <div class="col-md-6">{$constants['treasonrequired']} Treason</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Violate Peace Treaty</div>
	  <div class="col-md-6">{$constants['perfidyrequired']} Perfidy</div>
	  </div>
    </div>
	</div>
	<div class="col-md-4">
	<div class="panel panel-default">
      <div class="panel-heading">Cancellation Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['compassionforbrutal']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['compassionforburden']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['compassionforcorrupt']} Compassion</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['compassionfordespair']} Compassion</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['compassionforrobbery']} Compassion</div>
	  </div>
    </div>
	</div>
	</div>
	<div class="row">
	<div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-heading">Banked Defense Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['securityforbrutal']} Security</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['securityforburden']} Security</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['serenityforcorrupt']} Serenity</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['serenityfordespair']} Serenity</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['securityforrobbery']} Security</div>
	  </div>
    </div>
	</div>
	<div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-heading">Attraction Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['heroismforbrutal']} Heroism</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['heroismforburden']} Heroism</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['heroismforcorrupt']} Heroism</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['heroismfordespair']} Heroism</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['heroismforrobbery']} Heroism</div>
	  </div>
    </div>
	</div>
	<div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-heading">Nullification Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['shelterforbrutal']} Shelter</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['shelterforburden']} Shelter</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['shelterforcorrupt']} Shelter</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['shelterfordespair']} Shelter</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['shelterforrobbery']} Shelter</div>
	  </div>
    </div>
	</div>
	<div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-heading">Redirection Costs</div>
	  <div class="row">
	  <div class="col-md-6">Brutality</div>
	  <div class="col-md-6">{$constants['maliceforbrutal']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Burden</div>
	  <div class="col-md-6">{$constants['maliceforburden']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Corruption</div>
	  <div class="col-md-6">{$constants['maliceforcorrupt']} Malice</div>
	  </div>
	  <div class="row">
	  <div class="col-md-6">Despair</div>
	  <div class="col-md-6">{$constants['malicefordespair']} Malice</div>
	  </div>
      <div class="row">
	  <div class="col-md-6">Robbery</div>
	  <div class="col-md-6">{$constants['maliceforrobbery']} Malice</div>
	  </div>
    </div>
	</div>
</div>
<form name="makewar" action="makewar.php" method="post">
<input type="hidden" name="token_makewar" value="{$token}"/>
<center><input name="username" placeholder="Target" class="form-control" type="text" style="width:200px;"/></center>
<div class="row">
	<div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Brutality</div>
<center>Reduce the target's production by 1.</center>
<center><input type="submit" name="brutal" value="Brutalize" class="btn btn-danger" onclick="return confirm('Really perform a Brutality attack?')"/></center>
	</div>
	</div>
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
</div></div>
	</div>
	</div>
</div>
<div class="row">
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
</div></div></div></div>
	<div class="col-md-6">
    <div class="panel panel-default">
		<div class="panel-heading">Despair</div>
	<center>Reduce the target's satisfaction by 200.</center>
	<center><input type="submit" name="despair" value="Despair" class="btn btn-danger" onclick="return confirm('Really perform a Despair attack?')"/></center>
	</div>
	</div>
</div>
<div class="row">
<div class="col-md-6">
<div class="panel panel-default">
<div class="panel-heading">Robbery</div>
	<center>Steal resources.</center>
<div class="row">
<div class="col-sm-6">
<select name="resourcetorob" class="form-control">
EOFORM;
echo elementsdropdown(true, true);
echo <<<EOFORM
</select>
</div>
<div class="col-sm-6">
<input type="submit" name="robbery" value="Rob" class="btn btn-danger" onclick="return confirm('Really perform a Robbery attack?')"/>
</div></div></div></div>
</form>
EOFORM;
include("footer.php");
?>