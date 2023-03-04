<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_placeonmarket"] == "") || ($_POST["token_placeonmarket"] != $_SESSION["token_placeonmarket"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_placeonmarket"] == "")) {
    $_SESSION["token_placeonmarket"] = sha1(rand() . $_SESSION["token_placeonmarket"]);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'marketplace'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
$maxtimes = $userinfo['production'] * 6;
$sql=<<<EOSQL
SELECT SUM(multiplier) as totaltimes FROM marketplace WHERE user_id = {$_SESSION['user_id']}
EOSQL;
$rs = onelinequery($sql);
$totaltimes = $rs['totaltimes'];
if (!$totaltimes) $totaltimes = 0;
$timesremaining = $maxtimes - $totaltimes;
if (!$errors) {
	if ($_POST['place']) {
		$mysql['resource_id'] = (int)$_POST['resource_id'];
		$mysql['requestedresource_id'] = (int)$_POST['requestedresource_id'];
		$mysql['multiplier'] = (int)$_POST['multiplier'];
		$mysql['amount'] = (int)$_POST['amount'];
		$mysql['requestedamount'] = (int)$_POST['requestedamount'];
		$mysql['priority'] = (int)$_POST['priority'];
        if ($mysql['resource_id'] > 63 || $mysql['resource_id'] < 0 || $mysql['apparentresource_id'] === "") {
            $errors[] = "Select an item to sell.";
        }
        if ($mysql['requestedresource_id'] > 63 || $mysql['requestedresource_id'] < 0 || $mysql['apparentresource_id'] === "") {
            $errors[] = "Select an item to buy.";
        }
		$sql=<<<EOSQL
		SELECT name FROM resourcedefs
		WHERE resource_id = '{$mysql['resource_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!hasamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * $mysql['multiplier'])) {
			$errors[] = "You do not have that much {$rs['name']}.";
		}
		if (amountof(33, $_SESSION['user_id']) < $constants['plentynecessary'] * $mysql['multiplier']) {
			$errors[] = "You do not have the Plenty to place this many items on the Marketplace.";
		}
		if ($_POST['resource_id'] === "") {
			$errors[] = "Enter an item to sell.";
		}
        if ($mysql['amount'] < 1) {
            $errors[] = "No amount entered to sell.";
        }
		if ($_POST['requestedresource_id'] === "") {
			$errors[] = "Enter an item to buy.";
		}
        if ($mysql['requestedamount'] < 1) {
            $errors[] = "No amount entered to buy.";
        }
		if ($mysql['multiplier'] < 1) {
			$errors[] = "Enter how many of this offer you plan to put on the market.";
		}
		if ($mysql['multiplier'] > $timesremaining) {
			$errors[] = "You may not place that many more items on the market.";
		}
		if ($_POST['fraud']) {
            $mysql['apparentresource_id'] = (int)$_POST['apparentresource_id'];
            $mysql['apparentamount'] = (int)$_POST['apparentamount'];
			$fraudamount = amountof(15, $_SESSION['user_id']);
			if ($fraudamount < ($mysql['multiplier'] * $constants['fraudnecessary'])) {
				$errors[] = "You do not have enough Fraud to lie about your offerings.";
			}
			if ($mysql['apparentresource_id'] > 63 || $mysql['apparentresource_id'] < 0 || $mysql['apparentresource_id'] === "") {
                $errors[] = "Select an item to fake.";
            }
            $apparentitem = $mysql['apparentresource_id'];
			if ($mysql['apparentamount'] < 1) {
				$errors[] = "Select an amount to fake.";
			}
			$apparentamount = $mysql['apparentamount'];
		} else {
			$apparentitem = $mysql['resource_id'];
			$apparentamount = $mysql['amount'];
		}
		if ($_POST['libel'] == 2) {
			$mysql['username'] = $GLOBALS['mysqli']->real_escape_string($_POST['username']);
			if (!hasamount(39, $_SESSION['user_id'], $mysql['multiplier'] * $constants['libelnecessary'])) {
				$errors[] = "You do not have enough Libel to make yourself appear to be someone else.";
			}
			$sql=<<<EOSQL
			SELECT user_id FROM users WHERE username = '{$mysql['username']}'
EOSQL;
			$rs = onelinequery($sql);
			if (!$rs['user_id']) {
				$errors[] = "The user you are trying to pretend to be does not exist.";
			} else if ($rs['user_id'] == $_SESSION['user_id']) {
				$errors[] = "Pretending to be yourself?";
			} else if ($rs['user_id'] < 5) {
				$errors[] = "Your choice of masks demonstrates exceptionally poor judgment.";
			}
			$apparentuser_id = $rs['user_id'];
		} else if ($_POST['libel'] == 1) {
			if (!hasamount(21, $_SESSION['user_id'], $mysql['multiplier'] * $constants['witnecessary'])) {
				$errors[] = "You do not have enough Wit to make yourself anonymous.";
			}
			$apparentuser_id = 0;
		} else {
			$apparentuser_id = $_SESSION['user_id'];
		}
		if ($_POST['priority']) {
			if (!hasamount(44, $_SESSION['user_id'], $constants['delightnecessary'] * $mysql['priority'])) {
				$errors[] = "You do not have enough Delight to raise the priority of your Marketplace item by {$mysql['priority']}.";
			}
		} else {
			$mysql['priority'] = 0;
		}
		if (!$errors) {
			$sql=<<<EOSQL
			INSERT INTO marketplace (offereditem, requesteditem, offeredamount, requestedamount, apparentitem, apparentamount, user_id, apparentuser_id, multiplier, priority)
			VALUES ({$mysql['resource_id']}, {$mysql['requestedresource_id']}, {$mysql['amount']}, {$mysql['requestedamount']}, {$apparentitem}, {$apparentamount},
			{$_SESSION['user_id']}, {$apparentuser_id}, {$mysql['multiplier']}, {$mysql['priority']})
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * $mysql['multiplier'] * -1);
			addamount(33, $_SESSION['user_id'], ($mysql['multiplier'] * $constants['plentynecessary'] * -1));
			if ($_POST['fraud']) {
				addamount(15, $_SESSION['user_id'], ($mysql['multiplier'] * $constants['fraudnecessary'] * -1));
			}
			if ($_POST['libel'] == 2) {
				addamount(39, $_SESSION['user_id'], ($mysql['multiplier'] * $constants['libelnecessary'] * -1));
			} else if ($_POST['libel'] == 1) {
				addamount(21, $_SESSION['user_id'], ($mysql['multiplier'] * $constants['witnecessary'] * -1));
			}
			if ($_POST['priority']) {
				addamount(44, $_SESSION['user_id'], ($mysql['priority'] * $mysql['multiplier'] * $constants['delightnecessary'] * -1));
			}
			$totaltimes += $mysql['multiplier'];
			$timesremaining = $maxtimes - $totaltimes;
			$infos[] = "Item added to marketplace.";
		}
	}
}
?>