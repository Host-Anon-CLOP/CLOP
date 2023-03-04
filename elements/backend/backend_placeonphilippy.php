<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_placeonphilippy"] == "") || ($_POST["token_placeonphilippy"] != $_SESSION["token_placeonphilippy"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_placeonphilippy"] == "")) {
    $_SESSION["token_placeonphilippy"] = sha1(rand() . $_SESSION["token_placeonphilippy"]);
}
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'philippy'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
if (!$errors) {
	if ($_POST['place']) {
        $mysql['maxpertick'] = (int)$_POST['maxpertick'];
		$mysql['resource_id'] = (int)$_POST['resource_id'];
		$mysql['maxtier'] = (int)$_POST['maxtier'];
        if ($mysql['maxtier'] < 1 || $mysql['maxtier'] > 3) {
            $errors[] = "Tier must be between 1 and 3.";
        }
		$mysql['amount'] = (int)$_POST['amount'];
		$mysql['priority'] = (int)$_POST['priority'];
        if ($mysql['resource_id'] > 63 || $mysql['resource_id'] < 0 || $mysql['apparentresource_id'] === "") {
            $errors[] = "Select an item to offer.";
        }
		$sql=<<<EOSQL
		SELECT name FROM resourcedefs
		WHERE resource_id = '{$mysql['resource_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!hasamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'])) {
			$errors[] = "You do not have that much {$rs['name']}.";
		}
		if (!hasamount(41, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['philippydivisor']))) {
			$errors[] = "You do not have the Philippy to offer this many items to newbies.";
		}
		if ($_POST['resource_id'] === "") {
			$errors[] = "Enter an item to donate.";
		}
        if ($mysql['amount'] < 1) {
            $errors[] = "No amount entered to donate.";
        }
		if ($_POST['bullshit']) {
            $bullshit = 1;
            $mysql['apparentresource_id'] = (int)$_POST['apparentresource_id'];
            $mysql['apparentamount'] = (int)$_POST['apparentamount'];
			if (!hasamount(46, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['bullshitdivisor']))) {
				$errors[] = "You do not have enough Bullshit to lie about your offerings.";
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
            $bullshit = 0;
			$apparentitem = $mysql['resource_id'];
			$apparentamount = $mysql['amount'];
		}
		if ($_POST['libel'] == 2) {
			$mysql['username'] = $GLOBALS['mysqli']->real_escape_string($_POST['username']);
			if (!hasamount(39, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['libeldivisor']))) {
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
			if (!hasamount(21, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['witdivisor']))) {
				$errors[] = "You do not have enough Wit to make yourself anonymous.";
			}
			$apparentuser_id = 0;
		} else {
			$apparentuser_id = $_SESSION['user_id'];
		}
		if ($_POST['priority']) {
            $mysql['priority'] = (int)$_POST['priority'];
			if (!hasamount(44, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['delightdivisor']) * $mysql['priority'])) {
				$errors[] = "You do not have enough Delight to raise the priority of your Marketplace item by {$mysql['priority']}.";
			}
		} else {
			$mysql['priority'] = 0;
		}
		if (!$errors) {
			$sql=<<<EOSQL
			INSERT INTO philippy (offereditem, offeredamount, apparentitem, apparentamount, user_id, apparentuser_id, maxtier, maxpertick, priority, bullshit)
			VALUES ({$mysql['resource_id']}, {$mysql['amount']}, {$apparentitem}, {$apparentamount},
			{$_SESSION['user_id']}, {$apparentuser_id}, {$mysql['maxtier']}, {$mysql['maxpertick']}, {$mysql['priority']}, {$bullshit})
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			addamount($mysql['resource_id'], $_SESSION['user_id'], $mysql['amount'] * -1);
			addamount(41, $_SESSION['user_id'], (ceil($mysql['amount'] / $constants['philippydivisor']) * -1));
			if ($_POST['bullshit']) {
				addamount(46, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['bullshitdivisor']) * -1);
			}
			if ($_POST['libel'] == 2) {
				addamount(39, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['libeldivisor']) * -1);
			} else if ($_POST['libel'] == 1) {
				addamount(21, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['witdivisor']) * -1);
			}
			if ($_POST['priority']) {
				addamount(44, $_SESSION['user_id'], ceil($mysql['amount'] / $constants['delightdivisor']) * $mysql['priority'] * -1);
			}
			$infos[] = "Item added to Philippy.";
		}
	}
}
?>