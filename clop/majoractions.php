<?php
include("backend/backend_majoractions.php");
$extratitle = "Major Actions - ";
include("header.php");
echo <<<EOFORM
<center>Major, nation-altering actions are here.</center>
<center>Don't click on anything here unless you are very, very sure that it's what you want to do!</center>
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Change the top message. This message overwrites the current one and can be overwritten by anyone else with 5 million bits to spend.
    </p></div>
	<div class="col-md-1"><p class="text-danger">5,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
                    <input type="text" name="message" value="" class="form-control"/><br/>
					<input type="submit" name="topmessage" value="Change Top Message" class="btn btn-warning btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Change your nation's name. The price of this is dependent on how large your nation is; due to the immense complications involved, it costs 250,000 bits per building you own.<br/>
    You also lose 500 satisfaction as your ponies react to the loss of their former nationality.
    </p></div>
	<div class="col-md-1"><p class="text-danger">{$displaynewnameprice}</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
                    <input type="text" name="nationname" value="{$nationinfo['name']}" class="form-control"/><br/>
					<input type="submit" onclick="return confirm('Really change your nation\'s name?')" name="changename" value="Change Name" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
if ($nationinfo['seesecrets'] && $nationinfo['government'] != "Alicorn Elite" && $nationinfo['government'] != "Transponyism") {
    echo <<<EOFORM
    <center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Build an Alicornification Facility, and deify the elite of your society in all your nations.<br/><br/>
    Such powerful elites may trade with anyone, and their empire satisfaction loss is a third of what it would be otherwise.
    Additionally, your nations will have a satisfaction cap of 5,000, although they always produce bits at six times their ordinary GDP.<br/>
    Building the Alicornification Facility requires 1,000 tungsten, 1,000 precision parts, and 1,000 machinery parts.
    It produces one vital Apotheosis Serum per turn and requires 100 energy and 50 sugar per turn.<br/>
    However, the moment you go down this road, the <b>maximum</b> of your reputations with both the Solar Empire and the New Lunar Republic will be -10,
    and this maximum will decrease by 1 every turn. Therefore, you <b>will</b> be attacked and eventually destroyed unless you ascend. A new military option will present itself to you.<br/>
    <b>There is no going back from this point, there are no brakes on the ascension train (can't enter stasis mode), and future steps only get worse.</b><br/>
    Of course, once one of your nations goes Alicorn, they all do.
    </p></div>
	<div class="col-md-1"><p class="text-danger">3,000,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really begin the one-way path to ascension?')" name="alicornelite" value="Alicornify" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
}
if ($nationinfo['government'] == "Alicorn Elite") {
echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    With 30 Apotheosis Serum, you begin alicornifying your middle classes in all your nations, and with 2,000 tungsten, 2,000 precision parts, and 2,000 machinery parts, you
    build two more Alicornification Facilities.<br/><br/>
    As before, each Alicornification Facility requires 100 energy and 50 sugar a turn; therefore, the total requirement is 300 energy and 150 sugar.<br/>
    Your nations will have a satisfaction cap of 7,000, although they will always produce bits at eight times their ordinary GDP.<br/>
    This is the next step to ascension.<br/>
    Of course, once one of your nations goes Transpony, they all do.
    </p></div>
	<div class="col-md-1"><p class="text-danger">5,000,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really take the next step to ascension?')" name="transponyism" value="Convert to Transponyism" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['government'] == "Transponyism") {
    echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    With 100 Apotheosis Serum, you convert your entire population of all your nations to alicorns, and
    with 10,000 composites, 5,000 precision parts, and 5,000 machinery parts, you build a spaceship large enough to send them all to live forever among the stars, leaving
    the world to lesser mortals.<br/>
    When you ascend, you take it all with you- even the statues- and the totals of everything you have owned are recorded for all time.
    </p></div>
	<div class="col-md-1"><p class="text-danger">8,000,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really ascend?')" name="ascend" value="Ascend" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['government'] == "Independence") {
    echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Revert your government to be a Democracy.<br/><br/>
    Removing your ponies' independence costs only bits and no resources, but it will result in a devastating satisfaction loss of 2,000.
    </p></div>
	<div class="col-md-1"><p class="text-danger">5,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to Democracy?')" name="democracy" value="Revert to Democracy" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['government'] == "Decentralization") {
    echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Revert your government to be a Democracy.<br/><br/>
    Removing your ponies' decentralization costs only bits and no resources, but it will result in a devastating satisfaction loss of 1,000.
	Once you revert one Decentralized government in your empire, you revert them all.
    </p></div>
	<div class="col-md-1"><p class="text-danger">5,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to Democracy?')" name="democracy" value="Revert to Democracy" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['government'] == "Authoritarianism") {
	echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
<div class="col-md-6"><p>
Revert your government to be a Repression.<br/><br/>
    It costs 10,000,000 bits to relax the authoritarian strictures and restore some freedoms.<br/>
	Once you revert one Authoritarian government in your empire, you revert them all.
    </p></div>
	<div class="col-md-1"><p class="text-danger">10,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to Repression?')" name="repression" value="Revert to Repression" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['government'] == "Oppression") {
	echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
<div class="col-md-6"><p>
Revert your government to be a Repression.<br/><br/>
    It costs 20,000,000 bits to relax the oppressive strictures and restore some freedoms.<br/>
	Once you revert one Oppressive government in your empire, you revert them all.
    </p></div>
	<div class="col-md-1"><p class="text-danger">20,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to Repression?')" name="repression" value="Revert to Repression" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['government'] == "Democracy") {
    echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Alter your government to be one based on Independence.<br/><br/>
    This costs nothing except bits to promote and announce the new government type.<br/>
    The independence in this government type is only for your ponies; by choosing this government, you will always be dependent on other nations for resources, because you cannot have an empire.<br/>
    If you attack and conquer another nation, that nation is destroyed instead.<br/>
    However, it offers a maximum satisfaction of 2500.<br/>
    Independent ponies travel even more than their Democratic counterparts, using 40 gasoline and 4 vehicle parts a turn. If you cannot pay for these items, you lose 100 satisfaction a turn; however,
    a functioning Independence nets you 50 satisfaction a turn.<br/>
    Like with Democracy, going below 0 satisfaction ruins the pretense of independence and rebels appear.<br/>
    An Independent government loses you 3 relationship with the Solar Empire and gives you 6 relationship with the New Lunar Republic every turn.
    </p></div>
	<div class="col-md-1"><p class="text-danger">20,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really convert to Independence?')" name="independence" value="Convert to Independence" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Alter your government to be one based on Decentralization.<br/><br/>
    Building the complicated mechanics of Decentralization requires 500 copper, 50 mechanical parts, and 20 composites.<br/>
    The guiding principle of Decentralization is that the empire allows its subjects personal autonomy and self-governance; each nation presumably acts for its own benefit.
    Therefore, under a Decentralized government, you cannot transfer, sell, or deal between nations in your empire.<br/>
    However, it offers a maximum satisfaction of 2000.<br/>
    Decentralization facilitates and requires a great deal of travel, using 50 gasoline and 5 vehicle parts a turn. If you cannot pay for these items, you lose 100 satisfaction a turn; however,
    a functioning Decentralized government nets you 30 satisfaction a turn.<br/>
    Like with Democracy, going below 0 satisfaction ruins the pretense of self-governance and rebels appear.<br/>
    A Decentralized government loses you 3 relationship with the Solar Empire and gives you 4 relationship with the New Lunar Republic every turn.<br/>
    Once one of your nations switches to Decentralization, all of them switch to Decentralization.<br/>
    Due to its focus on embassies, Decentralization is the only government type that automatically knows in advance when its allies are under attack.
    </p></div>
	<div class="col-md-1"><p class="text-danger">50,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really convert to Decentralization?')" name="decentralization" value="Convert to Decentralization" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Revert your government to be a Loose Despotism.<br/><br/>
    Dismantling the democratic state costs 1,000,000 bits. It costs no resources, but it will result in a devastating satisfaction loss of 700.
    </p></div>
	<div class="col-md-1"><p class="text-danger">1,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to Loose Despotism?')" name="loosedespotism" value="Revert to Loose Despotism" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['government'] == "Repression") {
    echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Alter your government to be one based on Authoritarianism.<br/><br/>
    Creating the machinery of Authoritarianism requires 20 million bits and 70 machinery parts.<br/>
    An Authoritarian government conducts commerce with no one except its allies; therefore, you may not conduct any sort of trading with a nation owned by a player not in your alliance.<br/>
    However, your GDP is always 2.5 times its base value, as if you had a satisfaction of 1500.<br/>
    Your satisfaction can dip all the way to -400 before your ponies revolt. If you cannot pay for these items, you lose 400 satisfaction a turn once your ponies realize revolution is possible.<br/>
    An Authoritarian government uses 10 gasoline and 3 machinery parts a turn.<br/>
    An Authoritarian government loses you 3 relationship with the New Lunar Republic and gives you 4 relationship with the Solar Empire every turn.<br/>
    <b>Once one of your nations switches to Authoritarianism, all of them switch to Authoritarianism.</b>
    </p></div>
	<div class="col-md-1"><p class="text-danger">20,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really convert to Authoritarianism?')" name="authoritarianism" value="Convert to Authoritarianism" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Alter your government to be one based on Oppression.<br/><br/>
    Building the machinery of Oppression requires 30 million bits and 100 machinery parts.<br/>
    An Oppressive empire may not trade or deal with anyone. The only method of moving resources back and forth is transfers.<br/>
    <b>Therefore, if you do not have three, perhaps four, different regions in your empire, choosing an Oppressive government is suicide.</b></br>
    However, the trains run on time; your GDP is always triple its base value, as if you had a satisfaction of 2000.<br/>
    Your satisfaction can dip all the way to -500 before your ponies revolt, and your satisfaction penalty for having a large empire is a third of what it otherwise would be.</br>
    The machinery of Oppression uses 10 gasoline and 5 machinery parts a turn. If you cannot pay for these items, you lose 500 satisfaction a turn once your ponies realize revolution is possible.<br/>
    An Oppressive government loses you 3 relationship with the New Lunar Republic and gives you 6 relationship with the Solar Empire every turn.<br/>
    <b>Once one of your nations switches to Oppression, all of them switch to Oppression.</b>
    </p></div>
	<div class="col-md-1"><p class="text-danger">30,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really convert to Oppression?')" name="oppression" value="Convert to Oppression" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Revert your government to be a Loose Despotism.<br/><br/>
    Dismantling the repressive state costs 5,000,000 bits to bribe away all of the ponies that you placed in charge.
    </p></div>
	<div class="col-md-1"><p class="text-danger">5,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to Loose Despotism?')" name="loosedespotism" value="Revert to Loose Despotism" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
} else {
    echo <<<EOFORM
    <table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Shift your government to be a Democracy.<br/>
    <br/>
    Through the free movement permitted by widespread vehicle use, you alter your society to be a partial democracy in which ponies are capable of making local decisions on their own.
    (All real decisions are still made by you.)<br/>
    This open society raises the cap of satisfaction to 1500 and decreases the losses you get from high satisfaction accordingly; under a Democracy, you lose as much sat at 1500 as you lose at
    1000 under a Loose Despotism.<br/>
    Performing this action requires 300 copper, 60 gasoline, and 30 vehicle parts to get started along with the 2,000,000 bit cost.<br/>
    If you cannot provide your ponies with 20 gasoline and 2 vehicle parts each turn, they will lose 20 satisfaction every turn. However, if their vehicles are able to run,
    they gain 15 satisfaction a turn.<br/>
    If your satisfaction goes below 0, your pretense of Democracy goes out the window and rebels appear.<br/>
    Having this government type raises your relations with the New Lunar Republic by 2 and lowers them with the Solar Empire by 3 every turn.<br/>
    </p></div>
	<div class="col-md-1"><p class="text-danger">2,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really switch to a government of Democracy?')" name="democracy" value="Switch to Democracy" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Shift your government to be one based on Repression.<br/>
    <br/>
    You hire the most brutal and oppressive ponies you can find to patrol the streets and make the rest of them work at a steady rate.<br/>
    Your GDP is always twice its base value, as if you had a satisfaction of 1000. Your ponies' actual satisfaction no longer has an effect on GDP. Furthermore, your ponies'
    satisfaction can dip down to -300 before they revolt.<br/>
    Performing this action requires 200 copper, 20 gasoline, 10 vehicle parts, and 1,000,000 bits.<br/>
    If you cannot provide the machinery of oppression with at least 10 gasoline a turn, you lose 50 sat per turn as revolutionary feeling spreads.<br/>
    Having this government type raises your relations with the Solar Empire by 2 and lowers them with the New Lunar Republic by 3 every turn.<br/>
    </p></div>
	<div class="col-md-1"><p class="text-danger">1,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really switch to a government of Repression?')" name="repression" value="Switch to Repression" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
}
if ($nationinfo['se_relation'] == 1000 && $nationinfo['government'] != "Solar Vassal") {
 echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Become a vassal state of the Solar Empire.<br/>
    <br/>
    The Solar Empire requires surprisingly little of its vassals; as your entire government is devoted to its worship, it no longer requests that you build facilities for it,
    nor is there any upkeep that requires payment every turn. However, your satisfaction is capped at a mere 1,250, as your ponies accept stability but not independence.<br/>
    You will no longer be able to sell or produce drugs, and your relationship with the Solar Empire will be kept at 1000.<br/>
    </p></div>
	<div class="col-md-1"><p class="text-danger">0</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really become a vassal state of the Solar Empire?')" name="solarvassal" value="Become Vassal" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
}
if ($nationinfo['nlr_relation'] == 1000 && $nationinfo['government'] != "Lunar Client") {
 echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Become a client state of the New Lunar Republic.<br/>
    <br/>
    The New Lunar Republic requires surprisingly little of its client states; as your entire government is devoted to its worship, it no longer requests that you build facilities for it,
    nor is there any upkeep that requires payment every turn. However, your satisfaction is capped at a mere 1,250, as your ponies accept stability but not independence.<br/>
    You will no longer be able to sell or produce drugs, and your relationship with the New Lunar Republic will be kept at 1000.<br/>
    </p></div>
	<div class="col-md-1"><p class="text-danger">0</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really become a client state of the New Lunar Republic?')" name="lunarclient" value="Become Client" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
EOFORM;
}
if ($nationinfo['economy'] == "Free Market") {
echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Revert your economy to be Poorly Defined.<br/>
    <br/>
    Dismantling the Free Market costs 1,000,000 bits and results in economic chaos, causing a brutal 500 satisfaction loss.
    </p></div>
	<div class="col-md-1"><p class="text-danger">1,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to a Poorly Defined economy?')" name="poorlydefined" value="Revert to Poorly Defined"
					class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div></td></tr>
</table></center>
EOFORM;
} else if ($nationinfo['economy'] == "State Controlled") {
echo <<<EOFORM
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Revert your economy to be Poorly Defined.<br/>
    <br/>
    Dismantling your State Controlled economy costs 1,000,000 bits and results in economic chaos, causing a brutal 500 satisfaction loss.
    </p></div>
	<div class="col-md-1"><p class="text-danger">1,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really revert to a Poorly Defined economy?')" name="poorlydefined" value="Revert to Poorly Defined"
					class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div></td></tr>
</table></center>
EOFORM;
} else {
echo <<<EOFORM
<center>With the default poorly defined economy, you can <b>accept, but not initiate, deals.</b></center>
<center>If you have an empire, you can currently transfer items between your empire's nations for 200 bits and money at a 3% fee.</center>
<center><table class="table table-striped table-bordered">
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Shift your economy to be a Free Market.<br/>
    <br/>
    By hiring caffeinated ponies to help the rapid and economical shift of goods, you pay only 3% when buying or selling on the marketplace.<br/>
    However, you will not be able to even accept deals at all, as such state control is against everything your new economists believe in.<br/>
    Performing this action requires a whopping 500 copper to build the infrastructure and 25 coffee to get started along with the 1,000,000 bit cost.<br/>
    If you cannot provide your ponies with 6 coffee each turn, they will fall asleep at their desks; the resulting confusion will lower your nation's satisfaction
    by a seriously threatening 25 every turn that they are asleep. Furthermore, your marketplace advantage will be nullified back to 10%/10%.<br/>
    Your costs of transferring between empires will rise to 400 per item and 6% for bits.<br/>
    Having this government type raises your relations with the New Lunar Republic by 1 and lowers them with the Solar Empire by 3 every turn.<br/>
    </p></div>
	<div class="col-md-1"><p class="text-danger">1,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really switch to a Free Market economy?')" name="freemarket" value="Switch to Free Market" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
<tr><td><div class="row">
	<div class="col-md-6"><p>
    Shift your economy to be State Controlled.<br/>
    <br/>
    By hiring drunken ponies to make friends and grease palms, you become able to initiate deals. Making a deal costs you, the originator, 100 bits per item transferred each way, no matter what
    item it is. Additionally, you can transfer money through deals for free.<br/>
    However, you will pay 20% when buying and 15% when selling on the marketplace, as the corrupt bureaucrats skim a little off the top.<br/>
    Performing this action requires a whopping 500 copper to build the infrastructure and 25 vodka to get started along with the 1,000,000 bit cost.<br/>
    If you cannot provide your ponies with 6 vodka each turn, they will become sober and surly; the resulting confusion will lower your nation's satisfaction
    by a seriously threatening 25 every turn that they are sober. Furthermore, you will not be able to make deals.<br/>
    Transferring bits and items between empires is free with this economy.<br/>
    Having this government type raises your relations with the Solar Empire by 1 and lowers them with the New Lunar Republic by 3 every turn.<br/>
    </p></div>
	<div class="col-md-1"><p class="text-danger">1,000,000</p></div>
	<div class="col-md-5">
		<form action="majoractions.php" method="post">
            <input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
			<div class="row">
				<div class="col-xs-7">
					<input type="submit" onclick="return confirm('Really switch to a State Controlled economy?')" name="statecontrolled" value="Switch to State Controlled" class="btn btn-danger btn-sm btn-block"/>
				</div>
			</div>
		</form>
	</div>
</div></td></tr>
</table></center>
<form name="selecteconomy" action="majoractions.php">
<input type="hidden" name="token_majoractions" value="{$_SESSION['token_majoractions']}"/>
</form>
EOFORM;
}
include("footer.php");
?>