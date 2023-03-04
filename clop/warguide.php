<?php
include("backend/backend_warguide.php");
$extratitle = "War Guide - ";
include("header.php");
echo <<<EOFORM
<center><h4>Basic Guide to War, Empires, and Combat</h4></center>
You lose 1 satisfaction per turn for every 2 total size of forces you have over 20.<br/>
It takes a minimum of 12 hours for friendly forces to reach their destination in the same region, 24 hours for enemy forces to reach their destination in the same region,
36 hours for friendly forces to reach their destination from another region, and 48 hours for enemy forces to reach their destination from another region.<br/>
These forces only actually get there when fights occur: at midnight and noon server time. These are the "war ticks", and they are also when the upkeep for forces is paid.<br/>
<b>If you cannot pay a unit's upkeep, its ponies lose confidence in your leadership and it immediately evaporates.</b>
Don't make a bigger army than you can afford.<br/>
Free Markets lose 100 sat for attacking other Free Markets, State Controlled economies lose 100 sat for attacking
other State Controlled economies, and free countries lose 200 sat for attacking other free countries. These numbers are per attacking group.
You do not get your satisfaction back if you recall your forces.<br/>
Both attacking and defending units hit simultaneously.<br/>
Units with the highest damage will attack first, followed by the largest units. Unit types will be attacked in this order: Cavalry, tanks, pegasi, unicorns, naval.
The only exception is units with weapons good against a certain unit type, who will attack that unit type out of order.<br/>
Units with the best armor against the particular attacking unit type will defend first, followed by the largest units.<br/>
Weapon values are per-hit base damage, and armor values are multipliers to incoming damage (lower = better). Units without weapons do .25 base damage.
Defending units in their home country take only .75x damage unless the nation's owner is in stasis during the fight.<br/>
Training improves units on a slight curve. Units automatically get 1 training at midnight (after any combat). Units' training cancels out enemy units' training.
Units with the maximum amount of training, 20, do 1.5x damage to untrained units and take 0.667x damage from untrained units.<br/>
A unit hits for a number of times equal to its size. For example, a unit of size 6 hits six times (and may destroy multiple units in the process).
For each one full point of damage a unit takes, it loses 1 size.<br/>
If there are no defenders at the end of a fight, the nation becomes the property of the biggest attacker.<br/>
To travel between regions, cavalry, tanks, and unicorns must be transported, either by air or water, by being in groups with units with enough carrying capacity.<br/>
You cannot attack a nation that is younger than Age 21.<br/>
If you take over another nation, you lose 20 sat per turn per nation for having an empire of 2 nations. This loss increases to 80 for an empire of 3, 180 for an empire of 4, etc.
Freshly conquered nations will take on the government and economy of the conquerer's oldest nation.<br/>
If you have a multi-nation empire, you can transfer your forces from one nation to another. Move the forces to the receiving nation to enable transferring them on the Send Forces page.
EOFORM;
echo <<<EOFORM
<center><h3>Weapons</h3></center>
<center><table class="table table-striped table-bordered">
<tr><th>Name</th><th>Type</th><th>Vs. Cavalry</th><th>Vs. Tanks</th><th>Vs. Pegasi</th><th>Vs. Unicorns</th><th>Vs. Naval</th></tr>
EOFORM;
foreach ($weapons as $thisweapon) {
    echo <<<EOFORM
<tr><td>{$thisweapon['name']}</td><td>{$forcetypes[$thisweapon['type']]}</td><td>{$thisweapon['dmg_cavalry']}</td>
<td>{$thisweapon['dmg_tanks']}</td><td>{$thisweapon['dmg_pegasi']}</td><td>{$thisweapon['dmg_unicorns']}</td><td>{$thisweapon['dmg_naval']}</td></tr>
EOFORM;
}
echo <<<EOFORM
</table></center>
<center><h3>Armor</h3></center>
<center><table class="table table-striped table-bordered">
<tr><th>Name</th><th>Type</th><th>Vs. Cavalry</th><th>Vs. Tanks</th><th>Vs. Pegasi</th><th>Vs. Unicorns</th><th>Vs. Naval</th><th>Carrying Capacity</th></tr>
EOFORM;
foreach ($armor as $thisarmor) {
    echo <<<EOFORM
<tr><td>{$thisarmor['name']}</td><td>{$forcetypes[$thisarmor['type']]}</td><td>{$thisarmor['arm_cavalry']}</td>
<td>{$thisarmor['arm_tanks']}</td><td>{$thisarmor['arm_pegasi']}</td><td>{$thisarmor['arm_unicorns']}</td><td>{$thisarmor['arm_naval']}</td><td>{$thisarmor['carrying']}</td></tr>
EOFORM;
}

echo <<<EOFORM
</table></center>
EOFORM;
include("footer.php");
?>