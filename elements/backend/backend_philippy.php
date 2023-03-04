<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_philippy"] == "") || ($_POST["token_philippy"] != $_SESSION["token_philippy"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_philippy"] == "")) {
    $_SESSION["token_philippy"] = sha1(rand() . $_SESSION["token_philippy"]);
}
$mysql['philippy_id'] = (int)$_POST['philippy_id'];
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
    if ($_POST['receive']) {
		$mysql['amount'] = (int)$_POST['amount'];
		$sql=<<<EOSQL
		SELECT m.*, rd1.name AS name1, rd3.name AS name3 FROM philippy m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd3 ON rd3.resource_id = m.apparentitem
		WHERE m.philippy_id = '{$mysql['philippy_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!$rs['philippy_id']) {
			$errors[] = "Item not found.";
		} else {
			if ($mysql['amount'] < 1) {
				$errors[] = "Select the amount you wish to receive.";
			}
			if ($mysql['amount'] > $rs['apparentamount']) {
				$errors[] = "There is not that much of that resource remaining.";
			}
			if ($rs['maxtier'] < $userinfo['tier']) {
				$errors[] = "Your tier is too high to receive this.";
			}
			if ($rs['maxpertick']) {
			$sql=<<<EOSQL
			SELECT amount FROM philippytaken
			WHERE user_id = {$_SESSION['user_id']}
			AND philippy_id = {$mysql['philippy_id']}
EOSQL;
			$takenamount = onelinequery($sql);
			if ($takenamount['amount'] + $mysql['amount'] > $rs['maxpertick']) {
				$errors[] = "You cannot receive that much of this item this tick.";
			}
			}
			if (!$errors) {
                if ($rs['bullshit']) {
                    $mysql['amount'] = $rs['offeredamount'];
                }
				if ($rs['offeredamount'] == $mysql['amount']) {
				$sql=<<<EOSQL
				DELETE FROM philippy WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
				DELETE FROM philippytaken WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				} else {
				$sql=<<<EOSQL
				UPDATE philippy SET offeredamount = offeredamount - {$mysql['amount']}, apparentamount = apparentamount - {$mysql['amount']}
				WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				}
				$sql=<<<EOSQL
				INSERT INTO philippytaken SET user_id = {$_SESSION['user_id']}, philippy_id = {$mysql['philippy_id']}, amount = {$mysql['amount']}
				ON DUPLICATE KEY UPDATE amount = amount + {$mysql['amount']}
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				addamount($rs['offereditem'], $_SESSION['user_id'], $mysql['amount']);
				if (hasability("seerippedoff", $_SESSION['user_id'])) {
                    if (!$rs['bullshit']) {
                        $message = "You received {$mysql['amount']} {$rs['name1']}.";
                    } else {
                        $message = "The offer was bullshit! You were forced to take {$rs['offeredamount']} {$rs['name1']}!";
                    }
				} else {
					$message = "You apparently received {$mysql['amount']} {$rs['name3']}.";
				}
				$infos[] = $message;
				if (hasability("logmarketplace", $_SESSION['user_id'])) {
					addreport($message, $_SESSION['user_id']);
				}
                if (hasability("logmarketplace", $rs['user_id'])) {
                    $message = "{$userinfo['username']} accepted your donation of {$mysql['amount']} {$rs['name1']}.";
					addreport($message, $rs['user_id']);
				}
			}
		}
    }
    if ($_POST['remove']) {
		$sql=<<<EOSQL
		SELECT user_id, offereditem, offeredamount FROM philippy WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
		$rs = onelinequery($sql);
        if ($rs['user_id'] != $_SESSION['user_id']) {
            $errors[] = "No.";
        } else {
			addamount($rs['offereditem'], $_SESSION['user_id'], $rs['offeredamount']);
            $sql=<<<EOSQL
			DELETE FROM philippy WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql=<<<EOSQL
			DELETE FROM philippytaken WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Offer removed.";
        }
    }
    if ($_POST['inspect']) {
		$sql=<<<EOSQL
		SELECT m.*, rd1.name AS name1, rd3.name AS name3 FROM philippy m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd3 ON rd3.resource_id = m.apparentitem
		WHERE m.philippy_id = '{$mysql['philippy_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!hasamount(17, $_SESSION['user_id'], ceil($rs['offeredamount'] / $constants['truthdivisor']))) {
			$errors[] = "You don't have the Truth to investigate this offer. (If you thought you did, the amount might be fake.)";
		}
		if (!$rs['philippy_id']) {
			$errors[] = "Philippy item not found.";
		}
		if ($rs['unmasked']) {
			$errors[] = "There's no point; the item's already been unmasked.";
		}
		if (!$errors) {
			$spentamount = ceil($rs['offeredamount'] / $constants['truthdivisor']);
			addamount(17, $_SESSION['user_id'], $spentamount * -1);
			$infos[] = "You spent {$spentamount} Truth.";
			if (amountbanked(47, $rs['user_id']) > ceil($rs['offeredamount'] / $constants['liesdivisor'])) {
				addbanked(47, $rs['user_id'], ceil($rs['offeredamount'] / $constants['liesdivisor']) * -1);
				$errors[] = "You attempt to inspect, but banked Lies prevent it!";
                if (hasability("logmarketplace", $rs['user_id'])) {
                    $message = "{$userinfo['username']} tried to inspect your Philippy offer, but your banked Lies stopped it!";
					addreport($message, $rs['user_id']);
				}
			} else {
				$sql=<<<EOSQL
				SELECT rd1.name AS name1, m.offeredamount, m.apparentamount, m.bullshit, u1.username AS user1,
				rd2.name AS name2, u2.username AS user2
				FROM philippy m
				INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
				INNER JOIN resourcedefs rd2 ON rd2.resource_id = m.apparentitem
				INNER JOIN users u1 ON u1.user_id = m.user_id
				LEFT JOIN users u2 ON u2.user_id = m.apparentuser_id
				WHERE m.philippy_id = '{$mysql['philippy_id']}'
EOSQL;
				$rs = onelinequery($sql);
                $liar = false;
				if ($rs['bullshit']) {
                    $liar = true;
					$infos[] = "The offer claims to be for {$rs['apparentamount']} {$rs['name2']} but is actually bullshit for {$rs['offeredamount']} {$rs['name1']}.";
				}
				if (!$rs['user2']) { 
					$liar = true;
					$infos[] = "The anonymous user offering this is {$rs['user1']}.";
				} else if ($rs['user1'] != $rs['user2']) {
                    $liar = true;
					$infos[] = "The offer claims to be offered by {$rs['user2']} but is actually offered by {$rs['user1']}.";
				}
				if (!$liar) {
					$infos[] = "The offer is what it claims to be.";
				}
			}
		}
    }
    if ($_POST['expose']) {
		$sql=<<<EOSQL
		SELECT * FROM philippy
		WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!hasamount(28, $_SESSION['user_id'], ceil($rs['offeredamount'] / $constants['trustdivisor']))) {
			$errors[] = "You don't have the Trust to expose this offer. (If you thought you did, the amount might be fake.)";
		}
		if (!$rs['philippy_id']) {
			$errors[] = "Philippy item not found.";
		}
		if ($rs['unmasked']) {
			$errors[] = "There's no point; the item's already been unmasked.";
		}
		if (!$errors) {
			addamount(28, $_SESSION['user_id'], ceil($rs['offeredamount'] / $constants['trustdivisor']) * -1);
			if (amountbanked(47, $rs['user_id']) > ceil($rs['offeredamount'] / $constants['liesdivisor'])) {
				addbanked(47, $rs['user_id'], ceil($rs['offeredamount'] / $constants['liesdivisor']) * -1);
				$errors[] = "You attempt to expose, but banked Lies prevent it!";
                if (hasability("logmarketplace", $rs['user_id'])) {
                    $message = "{$userinfo['username']} tried to expose your Philippy offer, but your banked Lies stopped it!";
					addreport($message, $rs['user_id']);
				}
			} else {
				$sql=<<<EOSQL
UPDATE philippy SET unmasked = 1, unmasker_id = {$_SESSION['user_id']} WHERE philippy_id = '{$mysql['philippy_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$infos[] = "Offer exposed.";
			}
		}
    }
	if (isset($_POST['resource_id']) || $_POST['everythingsearch']) {
        if (isset($_POST['resource_id'])) {
        $mysql['resource_id'] = (int)$_POST['resource_id'];
        if ($mysql['resource_id'] > 63 || $mysql['resource_id'] < 0) {
            $errors[] = "Nope.";
        }
        }
        $offers = array();
        $field = "";
		if ($_POST['search']) {
			$extrasql = "WHERE apparentitem = '{$mysql['resource_id']}'";
		} else if ($_POST['everythingsearch']) {
            $extrasql = " ";
        }
        if ($extrasql) {
        $sql=<<<EOSQL
		SELECT m.*, rd1.name AS offeredname, rd2.name AS apparentname,
		u1.username, u2.username AS apparentusername, u3.username AS unmasker
        FROM philippy m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd2 ON rd2.resource_id = m.apparentitem
		INNER JOIN users u1 ON u1.user_id = m.user_id
		LEFT JOIN users u2 ON u2.user_id = m.apparentuser_id
        LEFT JOIN users u3 ON u3.user_id = m.unmasker_id
		{$extrasql}
		ORDER BY m.priority DESC, m.apparentuser_id DESC
EOSQL;
		$sth = $GLOBALS['mysqli']->query($sql);
		if ($sth) {
			while ($rs = mysqli_fetch_array($sth)) {
				$offers[] = $rs;
			}
		}
		if (empty($offers)) {
            if (isset($_POST['resource_id'])) {
			$sql=<<<EOSQL
			SELECT name FROM resourcedefs WHERE resource_id = '{$mysql['resource_id']}'
EOSQL;
			$rs = onelinequery($sql);
            }
			if ($_POST['search']) {
				$infos[] = "No one is offering {$rs['name']}.";
			} else {
                $infos[] = "There is nothing offered in Philippy at all!";
            }
        }
    }
}
}
?>