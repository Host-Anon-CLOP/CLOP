<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_clopactions"] == "") || ($_POST["token_clopactions"] != $_SESSION["token_clopactions"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_clopactions"] == "")) {
    $_SESSION["token_clopactions"] = sha1(rand() . $_SESSION["token_clopactions"]);
}
$clopmysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "clopus_clop");
$governmentsatisfactions = array(
"Lunar Client" => 1250,
"Solar Vassal" => 1250,
"Democracy" => 1500,
"Decentralization" => 2000,
"Independence" => 2500,
"Alicorn Elite" => 5000,
"Transponyism" => 7000);
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'clop'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
if ($_POST['satisfy']) {
	$mysql['amount'] = (int)$_POST['amount'];
	$mysql['nationname'] = $clopmysqli->real_escape_string($_POST['nationname']);
    $sql=<<<EOSQL
	SELECT name, nation_id, satisfaction, government FROM nations WHERE name = '{$mysql['nationname']}'
EOSQL;
	$sth = $clopmysqli->query($sql);
	$rs = mysqli_fetch_array($sth);
	if (!$rs['nation_id']) {
		$errors[] = "That nation is not in &gt;CLOP.";
	}
	if ($mysql['amount'] < 1) {
		$errors[] = "No amount entered.";
	}
	if (!hasamount(36, $_SESSION['user_id'], $mysql['amount'])) {
		$errors[] = "You do not have that much Cheer.";
	}
	if (!$errors) {
		if (!$governmentsatisfactions[$rs['government']]) $maxsat = 1000;
		else $maxsat = $governmentsatisfactions[$rs['government']];
		if ($rs['satisfaction'] >= $maxsat) {
			$errors[] = "{$rs['name']} is already at maximum satisfaction.";
		} else if ($rs['satisfaction'] + ($constants['satisfactionpercheer'] * $mysql['amount']) > $maxsat) {
			$infos[] = "You spent {$mysql['amount']} Cheer.";
			$infos[] = "{$rs['name']} is now at maximum satisfaction.";
			$message = "{$userinfo['name']} from Compounds has put you at maximum satisfaction.";
			$newsat = $maxsat;
		} else {
			$total = $constants['satisfactionpercheer'] * $mysql['amount'];
			$infos[] = "You spent {$mysql['amount']} Cheer.";
			$infos[] = "You have added {$total} satisfaction to {$rs['name']}.";
			$message = "{$userinfo['name']} from Compounds has given you {$total} satisfaction.";
			$newsat = $rs['satisfaction'] + $total;
		}
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE nations SET satisfaction = {$newsat} WHERE nation_id = {$rs['nation_id']}
EOSQL;
			$clopmysqli->query($sql);
			$message = $clopmysqli->real_escape_string($message);
			$sql=<<<EOSQL
INSERT INTO reports (nation_id, report, time) VALUES ({$rs['nation_id']}, {$message}, NOW())
EOSQL;
			$clopmysqli->query($sql);
			addamount(36, $_SESSION['user_id'], $mysql['amount'] * -1);
		}
	}
} else if ($_POST['spy']) {
	$mysql['nationname'] = $clopmysqli->real_escape_string($_POST['nationname']);
	$sql=<<<EOSQL
	SELECT n.*, u.username FROM nations n INNER JOIN users u ON u.user_id = n.user_id WHERE n.name = '{$mysql['nationname']}'
EOSQL;
	$sth = $clopmysqli->query($sql);
	if ($sth) {
		$nationinfo = mysqli_fetch_array($sth);
		$regiontypes = array(0 => "The Heavily Fortified Island of Admin", 1 => "Saddle Arabia", 2 => "Zebrica", 3 => "Burrozil", 4 => "Przewalskia");
		$subregiontypes = array(0 => "", 1 => "North ", 2 => "Central ", 3 => "South ");
		$nationinfo['regionname'] = $regiontypes[$nationinfo['region']];
		$nationinfo['subregionname'] = $subregiontypes[$nationinfo['subregion']];
	}
	if (!$nationinfo['nation_id']) {
		$errors[] = "That nation is not in &gt;CLOP.";
	}
	if (!hasamount(25, $_SESSION['user_id'], $constants['equalitytoclopspy'])) {
		$errors[] = "You do not have the Equality to spy on this &gt;CLOP nation.";
	}
	if ($nationinfo['username'] == $userinfo['username']) {
		$errors[] = "Spying on your own nation?";
	}
    if ($nationinfo['nation_id'] == 1 && $_SESSION['user_id'] > 4) {
        $errors[] = "Don't do that.";
    }
	if (!$errors) {
		$sql=<<<EOSQL
		SELECT user_id, username FROM users WHERE username = '{$nationinfo['username']}'
EOSQL;
		$thisuser = onelinequery($sql);
		if (hasbanked(7, $thisuser['user_id'], $constants['unitytoclopblock'])) {
			$infos[] = "Your spying attempt was blocked by the owner's Unity in Compounds.";
			addamount(25, $_SESSION['user_id'], $constants['equalitytoclopspy'] * -1);
			addbanked(7, $thisuser['user_id'], $constants['unitytoclopblock'] * -1);
			if (hasability("seespyattempts", $thisuser['user_id'])) {
				addreport("{$userinfo['username']} tried to spy on your &gt;CLOP nation, but your Unity blocked it!", $thisuser['user_id']);
			}
		} else {
			$affectedresources = array();
			$requiredresources = array();
			$resources = array();
			$weapons = array();
			$armor = array();
			$buildings = array();
			addamount(25, $_SESSION['user_id'], $constants['equalitytoclopspy'] * -1);
			$infos[] = "You spent {$constants['equalitytoclopspy']} Equality.";
            if ($nationinfo['funds'] > 500000000) {
				$nationtax = ceil(($nationinfo['funds'] - 500000000)/500);
				$displaytax = commas($nationtax);
			} else {
				$nationtax = 0;
				$displaytax = 0;
			}
			$sql = "SELECT SUM(rd.gdp * (r.amount - r.disabled)) AS totalgdp FROM resources r INNER JOIN resourcedefs rd ON (rd.resource_id = r.resource_id) WHERE r.nation_id = '{$nationinfo['nation_id']}'";
			$gdp2 = $clopmysqli->query($sql);
			$gdp3 = mysqli_fetch_array($gdp2);
			$gdp = $nationgdp['totalgdp'] + 50000 + $gdp3['totalgdp'];
			if ($nationinfo['government'] == "Authoritarianism") {
				$gdp += $gdp * 1.5;
			} else if ($nationinfo['government'] == "Oppression") {
				$gdp += $gdp * 2;
			} else if ($nationinfo['government'] == "Repression") {
				$gdp += $gdp;
			} else if ($nationinfo['government'] == "Alicorn Elite") {
				$gdp += $gdp * 5;
			} else if ($nationinfo['government'] == "Transponyism") {
				$gdp += $gdp * 7;
			} else {
				$gdpchange = $gdp * ($nationinfo['satisfaction']/1000);
				$gdp += $gdpchange;
			}
			$displaygdp = commas($gdp);
			$displayfunds = commas($nationinfo['funds']);
			$sql=<<<EOSQL
SELECT r.amount, rd.name, r.disabled FROM resources r INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id WHERE r.nation_id = {$nationinfo['nation_id']} AND rd.is_building = 1 ORDER BY rd.name
EOSQL;
			$sth = $clopmysqli->query($sql);
			if ($sth) {
			while ($rs2 = mysqli_fetch_array($sth)) {
				$buildings[] = $rs2;
			}
            }
			$sql=<<<EOSQL
SELECT r.amount, rd.name FROM resources r INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id WHERE r.nation_id = {$nationinfo['nation_id']} AND rd.is_building = 0 ORDER BY rd.name
EOSQL;
			$sth = $clopmysqli->query($sql);
			if ($sth) {
			while ($rs2 = mysqli_fetch_array($sth)) {
				$resources[$rs2['name']] = $rs2['amount'];
				if ($rs2['amount'] > 50000) {
					$taxes[$rs2['name']] = ceil(($rs2['amount'] - 50000)/500);
				} else {
					$taxes[$rs2['name']] = 0;
				}
			}
            }
            $sql=<<<EOSQL
SELECT r.amount, rd.name FROM weapons r INNER JOIN weapondefs rd ON r.weapon_id = rd.weapon_id WHERE r.nation_id = {$nationinfo['nation_id']} ORDER BY rd.name
EOSQL;
			$sth = $clopmysqli->query($sql);
			if ($sth) {
			while ($rs2 = mysqli_fetch_array($sth)) {
				$weapons[$rs2['name']] = $rs2['amount'];
				if ($rs2['amount'] > 1000) {
					$taxes[$rs2['name']] = ceil(($rs2['amount'] - 1000)/500);
				}
			}
			}
            $sql=<<<EOSQL
SELECT r.amount, rd.name FROM armor r INNER JOIN armordefs rd ON r.armor_id = rd.armor_id WHERE r.nation_id = {$nationinfo['nation_id']} ORDER BY rd.name
EOSQL;
			$sth = $clopmysqli->query($sql);
			if ($sth) {
			while ($rs2 = mysqli_fetch_array($sth)) {
				$armor[$rs2['name']] = $rs2['amount'];
				if ($rs2['amount'] > 1000) {
					$taxes[$rs2['name']] = ceil(($rs2['amount'] - 1000)/500);
				}
			}
			}
			$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS required
			FROM resourcerequirements rr
			INNER JOIN resources r ON r.resource_id = rr.resource_id
			INNER JOIN resourcedefs rd ON rd.resource_id = rr.requiredresource_id
			WHERE r.nation_id = '{$nationinfo['nation_id']}' GROUP BY rd.name";
			$sth = $clopmysqli->query($sql);
			while ($rs = mysqli_fetch_array($sth)) {
				$requiredresources[$rs['name']] = $rs['required'];
			}
			if ($nationinfo['government'] == "Democracy") {
				$requiredresources["Gasoline"] += 20;
				$requiredresources["Vehicle Parts"] += 2;
			} else if ($nationinfo['government'] == "Repression") {
				$requiredresources["Gasoline"] += 10;
			} else if ($nationinfo['government'] == "Independence") {
				$requiredresources["Gasoline"] += 40;
				$requiredresources["Vehicle Parts"] += 4;
			} else if ($nationinfo['government'] == "Decentralization") {
				$requiredresources["Gasoline"] += 50;
				$requiredresources["Vehicle Parts"] += 5;
			} else if ($nationinfo['government'] == "Authoritarianism") {
				$requiredresources["Gasoline"] += 10;
				$requiredresources["Machinery Parts"] += 3;
			} else if ($nationinfo['government'] == "Oppression") {
				$requiredresources["Gasoline"] += 10;
				$requiredresources["Machinery Parts"] += 5;
			}
			if ($nationinfo['economy'] == "Free Market") {
				$requiredresources["Coffee"] += 6;
			} else if ($nationinfo['economy'] == "State Controlled") {
				$requiredresources["Vodka"] += 6;
			}
			$milsugar = 0;
			$milgems = 0;
			$milgasoline = 0;
			$milcoffee = 0;
			$sql=<<<EOSQL
			SELECT SUM(size) AS totalsize, type FROM forces WHERE nation_id = '{$nationinfo['nation_id']}' GROUP BY type
EOSQL;
			$sth = $clopmysqli->query($sql);
			while ($rs = mysqli_fetch_array($sth)) {
				if ($rs['type'] == 1) {
					$milsugar += ($rs['totalsize'] * 5);
				} else if ($rs['type'] == 2 || $rs['type'] == 5) {
					$milgasoline += ($rs['totalsize'] * 5);
				} else if ($rs['type'] == 3) {
					$milcoffee += ($rs['totalsize'] * 5);
				} else if ($rs['type'] == 4) {
					$milgems += ($rs['totalsize'] * 5);
				} else if ($rs['type'] == 6) {
					$milgems += ($rs['totalsize'] * 10);
				}
			}
			$sql = "SELECT rd.name, SUM((r.amount - r.disabled) * rr.amount) AS affected
			FROM resourceeffects rr
			INNER JOIN resources r ON r.resource_id = rr.resource_id
			INNER JOIN resourcedefs rd ON rd.resource_id = rr.affectedresource_id
			WHERE r.nation_id = '{$nationinfo['nation_id']}' GROUP BY rd.name";
			$sth = $clopmysqli->query($sql);
			while ($rs = mysqli_fetch_array($sth)) {
				$affectedresources[$rs['name']] = $rs['affected'];
			}
			$envirodamage = 0;
			$satperturn = 0;
			$seperturn = 0;
			$nlrperturn = 0;
			$tempse = $nationinfo['se_relation'];
			$tempnlr = $nationinfo['nlr_relation'];
			if ($nationinfo['se_relation'] > 250) {
				$seperturn -= floor(($nationinfo['se_relation'] - 250) / 50);
				if ($nationinfo['se_relation'] > 400) {
					$seperturn -= floor(($nationinfo['se_relation'] - 400) / 50);
					if ($nationinfo['se_relation'] > 800) {
						$seperturn -= floor(($nationinfo['se_relation'] - 800) / 50);
					}
				}
			}
			if ($nationinfo['nlr_relation'] > 250) {
				$nlrperturn -= floor(($nationinfo['nlr_relation'] - 250) / 50);
				if ($nationinfo['nlr_relation'] > 400) {
					$nlrperturn -= floor(($nationinfo['nlr_relation'] - 400) / 50);
					if ($nationinfo['nlr_relation'] > 800) {
						$nlrperturn -= floor(($nationinfo['nlr_relation'] - 800) / 50);
					}
				}
			}
			if ($nationinfo['se_relation'] < -450) {
				$seperturn -= ceil(($nationinfo['se_relation'] + 450) / 50);
				if ($nationinfo['se_relation'] < -700) {
					$seperturn -= ceil(($nationinfo['se_relation'] + 700) / 50);
					if ($nationinfo['se_relation'] < -900) {
						$seperturn -= ceil(($nationinfo['se_relation'] + 900) / 50);
					}
				}
			}
			if ($nationinfo['nlr_relation'] < -450) {
				$nlrperturn -= ceil(($nationinfo['nlr_relation'] + 450) / 50);
				if ($nationinfo['nlr_relation'] < -700) {
					$nlrperturn -= ceil(($nationinfo['nlr_relation'] + 700) / 50);
					if ($nationinfo['nlr_relation'] < -900) {
						$nlrperturn -= ceil(($nationinfo['nlr_relation'] + 900) / 50);
					}
				}
			}
			$tempse += $seperturn;
			$tempnlr += $nlrperturn;
			if ($tempse > 0) {
				$nlrperturn -= floor($tempse / 50);
			}
			if ($tempnlr > 0) {
				$seperturn -= floor($tempnlr / 50);
			}
			if ($nationinfo['economy'] == "Free Market") {
				$seperturn -= 3;
				$nlrperturn += 1;
			} else if ($nationinfo['economy'] == "State Controlled") {
				$seperturn += 1;
				$nlrperturn -= 3;
			}
			switch ($nationinfo['government']) {
				case "Democracy":
				$seperturn -= 3;
				$nlrperturn += 2;
				break;
				case "Decentralization":
				$seperturn -= 3;
				$nlrperturn += 4;
				break;
				case "Independence":
				$seperturn -= 3;
				$nlrperturn += 6;
				break;
				case "Repression":
				$seperturn += 2;
				$nlrperturn -= 3;
				break;
				case "Authoritarianism":
				$seperturn += 4;
				$nlrperturn -= 3;
				break;
				case "Oppression":
				$seperturn += 6;
				$nlrperturn -= 3;
				break;
				case "Solar Vassal":
				$seperturn = "Fixed";
				break;
				case "Lunar Client":
				$nlrperturn = "Fixed";
				break;
				case "Alicorn Elite":
				case "Transponyism":
				$seperturn = "Ascending";
				$nlrperturn = "Ascending";
				break;
				default:
				break;
			}
			$sql=<<<EOSQL
			SELECT rd.*, r.amount, r.disabled, rd.se_relation, rd.nlr_relation
			FROM resourcedefs rd
			INNER JOIN resources r ON r.resource_id = rd.resource_id
			WHERE r.nation_id = '{$nationinfo['nation_id']}'
EOSQL;
			$sth = $clopmysqli->query($sql);
			if ($sth) {
				while ($rs = mysqli_fetch_array($sth)) {
					$satperturn += ($rs['satisfaction'] * ($rs['amount'] - $rs['disabled']));
					$satperturn -= $rs['disabled'];
					if ($rs['bad_min'] && (($rs['amount'] - $rs['disabled']) > $rs['bad_min'])) {
						$satloss = ceil(pow((($rs['amount'] - $rs['disabled']) - $rs['bad_min']), 2) / $rs['bad_div']);
						$satperturn -= $satloss;
						$envirodamage += $satloss;
					}
					if ($rs['resource_id'] == 44 || $rs['resource_id'] == 45) {
						$envirocleaners += ($rs['amount'] - $rs['disabled']);
					}
					if ($rs['se_relation'] && is_numeric($seperturn)) {
						$seperturn += (($rs['amount'] - $rs['disabled']) * $rs['se_relation']);
					}
					if ($rs['nlr_relation'] && is_numeric($nlrperturn)) {
						$nlrperturn += (($rs['amount'] - $rs['disabled']) * $rs['nlr_relation']);
					}
				}
				if ($envirocleaners) {
					$satperturn += $envirodamage - ceil($envirodamage * pow(.9, $envirocleaners));
				}
			}
			$sql=<<<EOSQL
			SELECT sum(size) AS totalsize FROM forces WHERE nation_id = '{$nationinfo['nation_id']}'
EOSQL;
			$sth = $clopmysqli->query($sql);
            if ($sth) {
			$rs = mysqli_fetch_array($sth);
			if ($rs['totalsize'] > 20) {
				$satperturn -= ceil(($rs['totalsize'] - 20)/2);
			}
            }
			$sql=<<<EOSQL
			SELECT COUNT(*) AS empiresize FROM nations WHERE user_id = '{$nationinfo['user_id']}'
EOSQL;
			$sth = $clopmysqli->query($sql);
			if ($sth) {
				$rs = mysqli_fetch_array($sth);
			if ($nationinfo['government'] == "Transponyism" || $nationinfo['government'] == "Alicorn Elite" || $nationinfo['government'] == "Oppression") {
				$satperturn -= ceil((pow(($rs['empiresize'] - 1), 2) * 20) / 3);
			} else {
				$satperturn -= pow(($rs['empiresize'] - 1), 2) * 20;
			}
			}
			if ($nationinfo['government'] == "Democracy") {
				$satperturn += 15;
			} else if ($nationinfo['government'] == "Decentralization") {
				$satperturn += 30;
			} else if ($nationinfo['government'] == "Independence") {
				$satperturn += 50;
			}
			if ($nationinfo['government'] == "Transponyism") {
				$satmultiplier = 7;
			} else if ($nationinfo['government'] == "Alicorn Elite") {
				$satmultiplier = 5;
			} else if ($nationinfo['government'] == "Independence") {
				$satmultiplier = 2.5;
			} else if ($nationinfo['government'] == "Decentralization") {
				$satmultiplier = 2;
			} else if ($nationinfo['government'] == "Democracy") {
				$satmultiplier = 1.5;
			} else if ($nationinfo['government'] == "Solar Vassal" || $nationinfo['government'] == "Lunar Client") {
				$satmultiplier = 1.25;
			} else {
				$satmultiplier = 1;
			}
			if ($nationinfo['satisfaction'] > (250 * $satmultiplier)) {
				$satperturn -= floor(($nationinfo['satisfaction'] - (250 * $satmultiplier)) / (50 * $satmultiplier));
				if ($nationinfo['satisfaction'] > (500 * $satmultiplier)) {
					$satperturn -= floor(($nationinfo['satisfaction'] - (500 * $satmultiplier)) / (50 * $satmultiplier));
					if ($nationinfo['satisfaction'] > (750 * $satmultiplier)) {
						$satperturn -= floor(($nationinfo['satisfaction'] - (750 * $satmultiplier)) / (50 * $satmultiplier));
					}
				}
			}
			$sql=<<<EOSQL
			SELECT fg.name AS groupname, fg.forcegroup_id, fg.attack_mission, fg.departuredate, fg.location_id,
			fg.nation_id AS ownernation_id, n.name AS ownername, n.region AS ownerregion,
			fg.location_id, f.*, rd1.name AS weaponname, rd2.name AS armorname FROM forces f
			INNER JOIN forcegroups fg ON f.forcegroup_id = fg.forcegroup_id
			LEFT JOIN weapondefs rd1 ON f.weapon_id = rd1.weapon_id
			LEFT JOIN armordefs rd2 ON f.armor_id = rd2.armor_id
			LEFT JOIN nations n ON fg.nation_id = n.nation_id
			WHERE fg.departuredate IS NULL AND fg.location_id = {$nationinfo['nation_id']}
			ORDER BY fg.forcegroup_id, f.name
EOSQL;
			$sth = $clopmysqli->query($sql);
			while ($rs = mysqli_fetch_array($sth)) {
				if ($rs['ownernation_id'] == -1) {
					$rs['ownername'] = "Solar Empire";
				} else if ($rs['ownernation_id'] == -2) {
					$rs['ownername'] = "New Lunar Republic";
				} else if ($rs['ownernation_id'] == -3) {
					$rs['ownername'] = "Occupy Equestria";
				} else {
					$rs['ownerregionname'] = $regiontypes[$rs['ownerregion']];
				}
				$rs['lowertype'] = strtolower($forcetypes[$rs['type']]);
				if ($rs['weaponname'] == "") {
					$rs['weapon_id'] = 0;
					$rs['weaponname'] = "Scrounged Weapons";
				}
				if ($rs['armorname'] == "") {
					$rs['armor_id'] = 0;
					$rs['armorname'] = "Scrounged Armor";
				}
				if ($rs['attack_mission']) {
					$attackers[] = $rs;
				} else {
					$defenders[] = $rs;
				}
			}
		}
	}
}