<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_marketplace"] == "") || ($_POST["token_marketplace"] != $_SESSION["token_marketplace"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_marketplace"] == "")) {
    $_SESSION["token_marketplace"] = sha1(rand() . $_SESSION["token_marketplace"]);
}
$mysql['marketplace_id'] = (int)$_POST['marketplace_id'];
$sql=<<<EOSQL
SELECT name, value
FROM constants
WHERE type = 'marketplace'
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$constants[$rs['name']] = $rs['value'];
}
if (!$errors) {
	if ($_POST['loot']) {
		$mysql['multiplier'] = (int)$_POST['multiplier'];
		$sql=<<<EOSQL
		SELECT m.*, rd1.name AS name1, rd3.name AS name3 FROM marketplace m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd3 ON rd3.resource_id = m.apparentitem
		WHERE m.marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!$rs['marketplace_id']) {
			$errors[] = "Item not found.";
		} else {
			if ($mysql['multiplier'] < 1) {
				$errors[] = "Select the number of times you wish to loot this.";
			}
			if ($mysql['multiplier'] > $rs['multiplier']) {
				$errors[] = "You cannot loot this that many times.";
			}
			if (!hasamount(30, $_SESSION['user_id'], $mysql['multiplier'] * $constants['lootingformarketplace'])) {
				$errors[] = "You do not have enough Looting.";
			}
			if ($rs['user_id'] == $_SESSION['user_id']) {
				$errors[] = "What the fuck?";
			}
			if (!$errors) {
				addamount(30, $_SESSION['user_id'], $mysql['multiplier'] * $constants['lootingformarketplace'] * -1);
				if (hasbanked(42, $rs['user_id'], $mysql['multiplier'] * $constants['securityformarketplace'])) {
					addbanked(42, $rs['user_id'], $mysql['multiplier'] * $constants['securityformarketplace'] * -1);
					if (hasability("seeattackattempts", $targetuser['user_id'])) {
						$message =<<<EOFORM
<a href="viewuser.php?user_id={$_SESSION['user_id']}">{$userinfo['username']}</a> tried to steal your Marketplace item, but your Security blocked it!
EOFORM;
						addreport($message, $targetuser['user_id']);
					}
					$infos[] = "Your Looting attempt was blocked by the target's Security!";
				} else {
				if ($rs['multiplier'] == $mysql['multiplier']) {
				$sql=<<<EOSQL
				DELETE FROM marketplace WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
				} else {
				$sql=<<<EOSQL
				UPDATE marketplace SET multiplier = multiplier - {$mysql['multiplier']}
				WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
				}
				$GLOBALS['mysqli']->query($sql);
				addamount($rs['offereditem'], $_SESSION['user_id'], $rs['offeredamount'] * $mysql['multiplier']);
				if (hasability("seerippedoff", $_SESSION['user_id'])) {
					$message = "You stole {$rs['offeredamount']} {$rs['name1']} {$mysql['multiplier']} times.";
				} else {
					$message = "You apparently stole {$rs['apparentamount']} {$rs['name3']} {$mysql['multiplier']} times.";
				}
				$infos[] = $message;
				if (hasability("logmarketplace", $_SESSION['user_id'])) {
					addreport($message, $_SESSION['user_id']);
				}
                if (hasability("seerippedoff", $rs['user_id'])) {
                    $message =<<<EOFORM
<a href="viewuser.php?user_id={$_SESSION['user_id']}">{$userinfo['username']}</a> stole your {$rs['offeredamount']} {$rs['name1']} {$mysql['multiplier']} times!
EOFORM;
					addreport($message, $rs['user_id']);
				}
				}
			}
		}
	}
    if ($_POST['buy']) {
		$mysql['multiplier'] = (int)$_POST['multiplier'];
		$sql=<<<EOSQL
		SELECT m.*, rd1.name AS name1, rd2.name AS name2, rd3.name AS name3 FROM marketplace m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd2 ON rd2.resource_id = m.requesteditem
		INNER JOIN resourcedefs rd3 ON rd3.resource_id = m.apparentitem
		WHERE m.marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (!$rs['marketplace_id']) {
			$errors[] = "Item not found.";
		} else {
			if ($mysql['multiplier'] < 1) {
				$errors[] = "Select the number of times you wish to purchase this.";
			}
			if ($mysql['multiplier'] > $rs['multiplier']) {
				$errors[] = "You cannot do that deal that many times.";
			}
			if (!hasamount($rs['requesteditem'], $_SESSION['user_id'], $mysql['multiplier'] * $rs['requestedamount'])) {
				$errors[] = "You do not have that much {$rs['name2']}.";
			}
			if (!$errors) {
				if ($rs['multiplier'] == $mysql['multiplier']) {
				$sql=<<<EOSQL
				DELETE FROM marketplace WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
				} else {
				$sql=<<<EOSQL
				UPDATE marketplace SET multiplier = multiplier - {$mysql['multiplier']}
				WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
				}
				$GLOBALS['mysqli']->query($sql);
				addamount($rs['offereditem'], $_SESSION['user_id'], $rs['offeredamount'] * $mysql['multiplier']);
				addamount($rs['requesteditem'], $rs['user_id'], $rs['requestedamount'] * $mysql['multiplier']);
				addamount($rs['requesteditem'], $_SESSION['user_id'], $rs['requestedamount'] * $mysql['multiplier'] * -1);
				if (hasability("seerippedoff", $_SESSION['user_id'])) {
					$message = "You actually purchased {$rs['offeredamount']} {$rs['name1']} for {$rs['requestedamount']} {$rs['name2']} {$mysql['multiplier']} times.";
				} else {
					$message = "You apparently purchased {$rs['apparentamount']} {$rs['name3']} for {$rs['requestedamount']} {$rs['name2']} {$mysql['multiplier']} times.";
				}
				$infos[] = $message;
				if (hasability("logmarketplace", $_SESSION['user_id'])) {
					addreport($message, $_SESSION['user_id']);
				}
                if (hasability("logmarketplace", $rs['user_id'])) {
                    $message = "{$userinfo['username']} purchased your {$rs['offeredamount']} {$rs['name1']} for {$rs['requestedamount']} {$rs['name2']} {$mysql['multiplier']} times.";
					addreport($message, $rs['user_id']);
				}
			}
		}
    }
    if ($_POST['remove']) {
		$sql=<<<EOSQL
		SELECT user_id, offereditem, offeredamount, multiplier FROM marketplace WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
		$rs = onelinequery($sql);
        if ($rs['user_id'] != $_SESSION['user_id']) {
            $errors[] = "No.";
        } else {
            $plentyrecovered = $constants['plentynecessary'] * $rs['multiplier'];
			addamount($rs['offereditem'], $_SESSION['user_id'], $rs['offeredamount'] * $rs['multiplier']);
            addamount(33, $_SESSION['user_id'], $plentyrecovered);
            $sql=<<<EOSQL
			DELETE FROM marketplace WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$infos[] = "Offer removed. You recovered your offered items and {$plentyrecovered} Plenty.";
        }
    }
    if ($_POST['inspect']) {
		$sql=<<<EOSQL
		SELECT m.*, rd1.name AS name1, u1.username AS user1,
		rd2.name AS name2, u2.username AS user2
		FROM marketplace m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd2 ON rd2.resource_id = m.apparentitem
		INNER JOIN users u1 ON u1.user_id = m.user_id
		LEFT JOIN users u2 ON u2.user_id = m.apparentuser_id
		WHERE m.marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (amountof(17, $_SESSION['user_id']) < $constants['truthnecessary'] * $rs['multiplier']) {
			$errors[] = "You don't have the Truth to investigate this deal.";
		}
		if (!$rs['marketplace_id']) {
			$errors[] = "Marketplace item not found.";
		}
		if ($rs['unmasked']) {
			$errors[] = "There's no point; the item's already been unmasked.";
		}
		if (!$errors) {
			$spentamount = $constants['truthnecessary'] * $rs['multiplier'];
			addamount(17, $_SESSION['user_id'], $spentamount * -1);
			$infos[] = "You spent {$spentamount} Truth.";
			if (hasbanked(47, $rs['user_id'], $constants['liesabsorbed'] * $rs['multiplier'])) {
				addbanked(47, $rs['user_id'], $constants['liesabsorbed'] * $rs['multiplier'] * -1);
				$errors[] = "You attempt to investigate, but banked Lies prevent it!";
                if (hasability("logmarketplace", $rs['user_id'])) {
                    $message = "{$userinfo['username']} tried to investigate your deal, but your banked Lies stopped it!";
					addreport($message, $rs['user_id']);
				}
			} else {
				$liar = false;
				if ($rs['name1'] != $rs['name2']) {
					$liar = true;
					$infos[] = "The offer claims to be for {$rs['name2']} but is actually for {$rs['name1']}.";
				}
				if ($rs['offeredamount'] != $rs['apparentamount']) {
					$liar = true;
					$infos[] = "The offer claims to offer {$rs['apparentamount']} compounds but actually offers {$rs['offeredamount']}.";
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
		SELECT * FROM marketplace
		WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
		$rs = onelinequery($sql);
		if (amountof(28, $_SESSION['user_id']) < $constants['trustnecessary'] * $rs['multiplier']) {
			$errors[] = "You don't have the Trust to expose this deal.";
		}
		if (!$rs['marketplace_id']) {
			$errors[] = "Marketplace item not found.";
		}
		if ($rs['unmasked']) {
			$errors[] = "There's no point; the item's already been unmasked.";
		}
		if (!$errors) {
			addamount(28, $_SESSION['user_id'], $constants['trustnecessary'] * $rs['multiplier'] * -1);
			if (hasbanked(47, $rs['user_id'], $constants['liesabsorbed'] * $rs['multiplier'])) {
				addbanked(47, $rs['user_id'], $constants['liesabsorbed'] * $rs['multiplier'] * -1);
				$errors[] = "You attempt to expose, but banked Lies prevent it!";
                if (hasability("logmarketplace", $rs['user_id'])) {
                    $message = "{$userinfo['username']} tried to expose your deal, but your banked Lies stopped it!";
					addreport($message, $rs['user_id']);
				}
			} else {
				$sql=<<<EOSQL
UPDATE marketplace SET unmasked = 1, unmasker_id = {$_SESSION['user_id']} WHERE marketplace_id = '{$mysql['marketplace_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$infos[] = "Item exposed.";
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
		if ($_POST['buyingsearch']) {
			$extrasql = "WHERE apparentitem = '{$mysql['resource_id']}'";
		} else if ($_POST['sellingsearch']) {
			$extrasql = "WHERE requesteditem = '{$mysql['resource_id']}'";
		} else {
            $extrasql = " ";
        }
        if ($extrasql) {
        $sql=<<<EOSQL
		SELECT m.*, rd1.name AS offeredname, rd2.name AS apparentname, rd3.name AS requestedname,
		u1.username, u2.username AS apparentusername, u3.username AS unmasker
        FROM marketplace m
		INNER JOIN resourcedefs rd1 ON rd1.resource_id = m.offereditem
		INNER JOIN resourcedefs rd2 ON rd2.resource_id = m.apparentitem
        INNER JOIN resourcedefs rd3 ON rd3.resource_id = m.requesteditem
		INNER JOIN users u1 ON u1.user_id = m.user_id
		LEFT JOIN users u2 ON u2.user_id = m.apparentuser_id
        LEFT JOIN users u3 ON u3.user_id = m.unmasker_id
		{$extrasql}
		ORDER BY m.priority DESC, m.multiplier DESC, m.apparentuser_id DESC
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
			if ($_POST['buyingsearch']) {
				$infos[] = "No one is selling {$rs['name']}.";
			} else if ($_POST['sellingsearch']) {
				$infos[] = "No one is buying {$rs['name']}.";
			} else {
                $infos[] = "There is nothing on the Marketplace at all!";
            }
        }
    }
}
}
?>