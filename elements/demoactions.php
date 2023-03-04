<?php
include("backend/allfunctions.php");
$extratitle = "User Actions (Demo) - ";
include("header.php");
echo <<<EOFORM
<center><h3>This page is a nonfunctional sample!</h3></center>
<center><h4>Join an alliance to actually play!</h4></center>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Own Satisfaction by 10</div>
Cost: 5 Happiness
<div class="row input-group">
<div class="col-sm-6">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-6">
<input type="submit" name="increaseownsatisfaction" value="Increase" class="btn btn-success"/>
</div></div>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Alliance Member's Satisfaction by 10</div>
Cost: 5 Camaraderie<div class="row input-group">
<div class="col-sm-4">
<select name="user_id" class="form-control">
<option value="0">Applehorse</option>
<option value="0">Fasthorse</option>
<option value="0">Princesshorse</option>
</select>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="increasemembersatisfaction" value="Increase" class="btn btn-success"/>
</div>
</div></div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Unallied Player's Satisfaction by 10</div>
Cost: 5 Cheer
<div class="row input-group">
<div class="col-sm-4">
<input name="username" placeholder="Player" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input name="amount" placeholder="Times" value="" class="form-control"/>
</div>
<div class="col-sm-4">
<input type="submit" name="increasenonmembersatisfaction" value="Increase" class="btn btn-success"/>
</div></div>
</div></div>
</div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Increase Own Production by 1</div>
Cost: 32 Optimism
<center><input type="submit" name="increaseproduction" value="Increase" class="btn btn-success"/></center>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Focus Production</div>
Cost to Focus: 200 Devotion
<div class="row input-group">
<div class="col-sm-4">
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
<div class="col-sm-2">
</div>
<div class="col-sm-4">
<input type="submit" name="focus" value="Focus" class="btn btn-success"/>
</div>
</div>
</div></div>
</div>
<div class="row">
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Purchase Ability</div>
Cost: 7 Beneficence per tick
<div class="row input-group">
<div class="col-sm-6">
<select name="abilityname" class="form-control">
<option value=""></option>
<option value="logproduction">Log Marketplace</option>
<option value="seerippedoff">See Ripped Off</option>
</select>
</div>
<div class="col-sm-6">
<div class="input-group">
<input name="turns" placeholder="Ticks" type="text" class="form-control"/>
<span class="input-group-btn">
<input type="submit" name="purchaseability" value="Purchase" class="btn btn-success"/>
</span>
</div></div></div>
</div></div>
<div class="col-md-4">
<div class="panel panel-default">
<div class="panel-heading">Change Top Message</div>
Cost: 20 Humor
<input name="message" placeholder="Message" type="text" class="form-control" maxlength="128"/>
<input type="submit" name="postmessage" value="Post Message" class="btn btn-success"/>
</div></div>
EOFORM;
include("footer.php");
?>