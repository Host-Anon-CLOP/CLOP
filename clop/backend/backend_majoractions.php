<?php
include_once("allfunctions.php");
$nationinfo = needsnation();
$compoundsmysqli = new mysqli("mariadb", "root", $_ENV["MYSQL_PASS"], "compounds");
$sql=<<<EOSQL
SELECT SUM(r.amount) AS buildings FROM resources r
INNER JOIN resourcedefs rd ON r.resource_id = rd.resource_id
WHERE r.nation_id = '{$nationinfo['nation_id']}'
AND rd.is_building = 1
AND rd.resource_id != 43
EOSQL;
$rs = onelinequery($sql);
$newnameprice = $rs['buildings'] * 250000;
$displaynewnameprice = commas($newnameprice);
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
if ($_POST && (($_POST['token_majoractions'] == "") || ($_POST['token_majoractions'] != $_SESSION['token_majoractions']))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION['token_majoractions'] == "")) {
    $_SESSION['token_majoractions'] = sha1(rand() . $_SESSION['token_majoractions']);
}
if (!$errors) {
    if ($_POST['topmessage']) {
		if ($nationinfo['funds'] < 5000000) {
			$errors[] = "You don't have the money!";
		}
		if (!$errors) {
        #$sql=<<<EOSQL
		#DELETE FROM topmessage WHERE 1 = 1;
#EOSQL;
		#$GLOBALS['mysqli']->query($sql);
		$sql=<<<EOSQL
INSERT INTO topmessage SET message = '{$mysql['message']}', user_id = '{$_SESSION['user_id']}', time = NOW()
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$sql = "UPDATE nations SET funds = funds - 5000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Message set.";
		}
    }
    if ($_POST['changename']) {
        if ($nationinfo['funds'] < $newnameprice) {
            $errors[] = "You don't have the money!";
        }
		if ($_POST['nationname'] != preg_replace('/[^0-9a-zA-Z_\ ]/' ,"", $_POST['nationname'])) {
			$errors[] = "Only English letters and numbers for the nation name.";
		}
		$newname = trim($mysql['nationname']);
		if ($newname == "") {
			$errors[] = "No nation name entered.";
		}
		if ($newname == $nationinfo['name']) {
			$errors[] = "Name's the same. Lame.";
		}
		$sql = "SELECT COUNT(*) AS count FROM users WHERE username = '{$newname}' AND user_id != '{$_SESSION['user_id']}'";
		$rs = onelinequery($sql);
		if ($rs['count'] > 0) {
			$errors[] = "Due to the potential for faggotry, we're not going to let you make your nation name someone else's username.";
		}
		$sql = "SELECT COUNT(*) AS count FROM nations WHERE name = '{$newname}' AND nation_id != '{$_SESSION['nation_id']}'";
		$rs = onelinequery($sql);
		if ($rs['count'] > 0) {
			$errors[] = "Name already taken.";
		}
        if (!$errors) {
			$sql = <<<EOSQL
INSERT INTO reports SET report = 'Nation name changed', nation_id = '{$_SESSION['nation_id']}', time = NOW()
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			$sql = "UPDATE nations SET funds = funds - {$newnameprice}, name = '{$newname}', satisfaction = satisfaction - 500 WHERE nation_id = '{$_SESSION['nation_id']}'";
			$GLOBALS['mysqli']->query($sql);
			header("Location: overview.php");
			exit;
		}
    }
    if ($_POST['alicornelite']) {
        if (!$nationinfo['seesecrets']) {
            $errors[] = "No.";
        } else {
            if ($nationinfo['funds'] < 3000000000) {
                $errors[] = "You don't have the money!";
            }
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 1000) {
				$errors[] = "You don't have the machinery parts!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '27'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 1000) {
				$errors[] = "You don't have the tungsten!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '29'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 1000) {
				$errors[] = "You don't have the precision parts!";
			}
            if (!$errors) {
                $sql = "UPDATE nations SET funds = funds - 3000000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE nations SET government = 'Alicorn Elite' WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
                $sql = "UPDATE users SET empiremax = -10 WHERE user_id = '{$_SESSION['user_id']}'";
                $GLOBALS['mysqli']->query($sql);
                $sql =<<<EOSQL
				UPDATE nations SET se_relation = -10 WHERE se_relation > -10 AND user_id = '{$_SESSION['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				$sql =<<<EOSQL
				UPDATE nations SET nlr_relation = -10 WHERE nlr_relation > -10 AND user_id = '{$_SESSION['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
                $sql = "UPDATE resources SET amount = amount - 1000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE resources SET amount = amount - 1000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '27'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE resources SET amount = amount - 1000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '29'";
				$GLOBALS['mysqli']->query($sql);
                $sql = <<<EOSQL
                INSERT INTO resources SET resource_id = 76, nation_id = '{$_SESSION['nation_id']}', amount = 1
EOSQL;
                $GLOBALS['mysqli']->query($sql);
				$sql = "DELETE FROM resources WHERE amount = 0";
				$GLOBALS['mysqli']->query($sql);
                $infos[] = "You have switched to Alicorn Elite. Check this page to see what you will need for the next step of ascension.";
                $nationinfo['government'] = "Alicorn Elite";
            }
        }
    } else if ($_POST['transponyism']) {
        if ($nationinfo['government'] != "Alicorn Elite") {
            $errors[] = "No.";
        } else {
            if ($nationinfo['funds'] < 5000000000) {
                $errors[] = "You don't have the money!";
            }
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 2000) {
				$errors[] = "You don't have the machinery parts!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '27'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 2000) {
				$errors[] = "You don't have the tungsten!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '29'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 2000) {
				$errors[] = "You don't have the precision parts!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '77'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 30) {
				$errors[] = "You don't have the Apotheosis Serum!";
			}
            if (!$errors) {
                $sql = "UPDATE nations SET funds = funds - 5000000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE nations SET government = 'Transponyism' WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
                $sql = "UPDATE resources SET amount = amount - 2000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE resources SET amount = amount - 2000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '27'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE resources SET amount = amount - 2000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '29'";
				$GLOBALS['mysqli']->query($sql);
                $sql = "UPDATE resources SET amount = amount - 30 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '77'";
				$GLOBALS['mysqli']->query($sql);
                $sql = "UPDATE resources SET amount = amount + 2 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '76'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "DELETE FROM resources WHERE amount = 0";
				$GLOBALS['mysqli']->query($sql);
                $infos[] = "You have switched to Transponyism. Check this page to see what you will need for the final step of ascension.";
				$nationinfo['government'] = "Transponyism";
            }
        }
    } else if ($_POST['ascend']) {
		if ($nationinfo['government'] != "Transponyism") {
            $errors[] = "No.";
		} else {
			if ($nationinfo['funds'] < 8000000000) {
                $errors[] = "You don't have the money!";
            }
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 5000) {
				$errors[] = "You don't have the machinery parts!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '30'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 10000) {
				$errors[] = "You don't have the composites!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '29'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 5000) {
				$errors[] = "You don't have the precision parts!";
			}
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '77'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 100) {
				$errors[] = "You don't have the Apotheosis Serum!";
			}
			if (!$errors) {
				$sql = "UPDATE resources SET amount = amount - 5000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '29'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE resources SET amount = amount - 5000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE resources SET amount = amount - 10000 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '30'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE resources SET amount = amount - 100 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '77'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "UPDATE nations SET funds = funds - 8000000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql=<<<EOSQL
				SELECT nation_id, name, funds FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
				$sth = $GLOBALS['mysqli']->query($sql);
				while ($rs = mysqli_fetch_array($sth)) {
                    $ascendnames[] = $rs['name'];
					$ascendfunds += $rs['funds'];
					$sql=<<<EOSQL
					SELECT resource_id, amount FROM resources WHERE nation_id = '{$rs['nation_id']}'
EOSQL;
					$sth2 = $GLOBALS['mysqli']->query($sql);
					while ($rs2 = mysqli_fetch_array($sth2)) {
						$ascendresources[$rs2['resource_id']] += $rs2['amount'];
					}
					$sql = "DELETE FROM resources WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = "DELETE FROM marketplace WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = "DELETE FROM nations WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = "DELETE FROM weapons WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = "DELETE FROM armor WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = "DELETE FROM forcegroups WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = "DELETE FROM forces WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = "DELETE FROM recipefavorites WHERE nation_id = '{$rs['nation_id']}'";
					$GLOBALS['mysqli']->query($sql);
					$sql = <<<EOSQL
					UPDATE forcegroups SET location_id = nation_id, departuredate = NULL, attack_mission = 0 WHERE destination_id = {$rs['nation_id']} OR location_id = {$rs['nation_id']}
EOSQL;
					$GLOBALS['mysqli']->query($sql);
					$sql = "SELECT deal_id FROM deals WHERE fromnation = '{$rs['nation_id']}'";
					$sth2 = $GLOBALS['mysqli']->query($sql);
					while ($rs2 = mysqli_fetch_array($sth2)) {
						$sql = "DELETE FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}'";
						$GLOBALS['mysqli']->query($sql);
						$sql = "DELETE FROM dealitems_requested WHERE deal_id = '{$rs2['deal_id']}'";
						$GLOBALS['mysqli']->query($sql);
						$sql = "DELETE FROM dealarmor_offered WHERE deal_id = '{$rs2['deal_id']}'";
						$GLOBALS['mysqli']->query($sql);
						$sql = "DELETE FROM dealarmor_requested WHERE deal_id = '{$rs2['deal_id']}'";
						$GLOBALS['mysqli']->query($sql);
						$sql = "DELETE FROM dealweapons_offered WHERE deal_id = '{$rs2['deal_id']}'";
						$GLOBALS['mysqli']->query($sql);
						$sql = "DELETE FROM dealweapons_requested WHERE deal_id = '{$rs2['deal_id']}'";
						$GLOBALS['mysqli']->query($sql);
					}
					$sql = "DELETE FROM deals WHERE fromnation = '{$rs['nation_id']}'";
				}
                foreach ($ascendnames as $name) {
					$mysqlname = $GLOBALS['mysqli']->real_escape_string($name);
                    $sql=<<<EOSQL
					INSERT INTO ascendednations SET user_id = '{$_SESSION['user_id']}', name = '{$mysqlname}', date = NOW()
EOSQL;
					$GLOBALS['mysqli']->query($sql);
                }
				$sql=<<<EOSQL
				INSERT INTO ascendedresources SET user_id = '{$_SESSION['user_id']}', resource_id = '0', amount = '{$ascendfunds}'
				ON DUPLICATE KEY UPDATE amount = amount + '{$ascendfunds}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
				foreach ($ascendresources AS $resource_id => $amount) {
					$sql=<<<EOSQL
					INSERT INTO ascendedresources SET user_id = '{$_SESSION['user_id']}', resource_id = '{$resource_id}', amount = '{$amount}'
					ON DUPLICATE KEY UPDATE amount = amount + '{$amount}'
EOSQL;
					$GLOBALS['mysqli']->query($sql);
				}
				$sql=<<<EOSQL
				UPDATE users SET empiremax = NULL, seesecrets = 0 WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
                $sql = "SELECT username FROM users WHERE user_id = '{$_SESSION['user_id']}'";
                $rs2 = onelinequery($sql);
                $sql =<<<EOSQL
                INSERT INTO news (message, posted)
                VALUES ('{$rs2['username']} has ascended!', NOW())
EOSQL;
                $GLOBALS['mysqli']->query($sql);
                $sql=<<<EOSQL
				UPDATE users SET ascended = 1 WHERE username = '{$rs2['username']}'
EOSQL;
                $GLOBALS['compoundsmysqli']->query($sql);
				header("Location: viewascension.php?user_id={$_SESSION['user_id']}");
				exit;
			}
		}
    } else if ($_POST['loosedespotism']) {
		if ($nationinfo['government'] == "Democracy") {
			if ($nationinfo['funds'] < 1000000) {
                $errors[] = "You don't have the money to revert!";
            } else {
				$sql=<<<EOSQL
UPDATE nations SET satisfaction = satisfaction - 700, government = 'Loose Despotism', funds = funds - 1000000 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
		} else if ($nationinfo['government'] == "Repression") {
			if ($nationinfo['funds'] < 5000000) {
                $errors[] = "You don't have the money to revert!";
            } else {
				$sql=<<<EOSQL
UPDATE nations SET government = 'Loose Despotism', funds = funds - 5000000 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
		}
		if (!$errors) {
			if ($nationinfo['satisfaction'] > 1000) {
				$sql=<<<EOSQL
UPDATE nations SET satisfaction = 1000 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
			header("Location: overview.php");
			exit;
		}
	} else if ($_POST['democracy']) {
		if ($nationinfo['government'] == "Loose Despotism" || $nationinfo['government'] == "Lunar Client" || $nationinfo['government'] == "Solar Vassal") {
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 300) {
				$errors[] = "You don't have the copper to switch!";
			}
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '25'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 60) {
				$errors[] = "You don't have the gasoline to switch!";
			}
                $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '9'
EOSQL;
                $rs = onelinequery($sql);
                if ($rs['amount'] < 30) {
                    $errors[] = "You don't have the vehicle parts to switch!";
                }
                if ($nationinfo['funds'] < 2000000) {
                    $errors[] = "You don't have the money to switch!";
                }
			if (empty($errors)) {
				$sql="UPDATE nations SET government = 'Democracy', funds = funds - 2000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 20 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '9'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 60 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '25'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 300 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "DELETE FROM resources WHERE amount = 0";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		} else if ($nationinfo['government'] == "Independence") {
			if ($nationinfo['funds'] < 5000000) {
				$errors[] = "You don't have the money to switch!";
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET funds = funds - 5000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql="UPDATE nations SET government = 'Democracy', satisfaction = satisfaction - 2000 WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		} else if ($nationinfo['government'] == "Decentralization") {
			if ($nationinfo['funds'] < 5000000) {
				$errors[] = "You don't have the money to switch!";
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET funds = funds - 5000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql="UPDATE nations SET government = 'Democracy', satisfaction = satisfaction - 1000 WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
				if ($nationinfo['satisfaction'] > 1500) {
					$sql=<<<EOSQL
UPDATE nations SET satisfaction = 1500 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
					$GLOBALS['mysqli']->query($sql);
				}
				header("Location: overview.php");
				exit;
			}
		}
	} else if ($_POST['repression']) {
		if ($nationinfo['government'] == "Loose Despotism" || $nationinfo['government'] == "Lunar Client" || $nationinfo['government'] == "Solar Vassal") {
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'
EOSQL;
            $rs = onelinequery($sql);
            if ($rs['amount'] < 200) {
                $errors[] = "You don't have the copper to switch!";
            }
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '25'
EOSQL;
            $rs = onelinequery($sql);
            if ($rs['amount'] < 20) {
                $errors[] = "You don't have the gasoline to switch!";
            }
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '9'
EOSQL;
			$rs = onelinequery($sql);
            if ($rs['amount'] < 10) {
                $errors[] = "You don't have the vehicle parts to switch!";
            }
            if ($nationinfo['funds'] < 1000000) {
                $errors[] = "You don't have the money to switch!";
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET government = 'Repression', funds = funds - 1000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 10 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '9'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 20 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '25'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 200 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "DELETE FROM resources WHERE amount = 0";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		} else if ($nationinfo['government'] == "Authoritarianism") {
			if ($nationinfo['funds'] < 10000000) {
                $errors[] = "You don't have the money to switch back!";
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET funds = funds - 10000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql="UPDATE nations SET government = 'Repression' WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		} else if ($nationinfo['government'] == "Oppression") {
			if ($nationinfo['funds'] < 20000000) {
                $errors[] = "You don't have the money to switch back!";
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET funds = funds - 20000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql="UPDATE nations SET government = 'Repression' WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		}
    } else if ($_POST['independence']) {
		if ($nationinfo['government'] == "Democracy") {
			if ($nationinfo['funds'] < 20000000) {
                $errors[] = "You don't have the money to switch!";
            }
			$sql=<<<EOSQL
			SELECT COUNT(*) AS number FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['number'] > 1) {
				$errors[] = "You cannot become independent; you have more than one nation.";
			}
			if (empty($errors)) {
				$sql="UPDATE nations SET government = 'Independence', funds = funds - 20000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		}
	} else if ($_POST['decentralization']) {
		if ($nationinfo['government'] == "Democracy") {
			if ($nationinfo['funds'] < 50000000) {
                $errors[] = "You don't have the money to switch!";
            }
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'
EOSQL;
            $rs = onelinequery($sql);
            if ($rs['amount'] < 500) {
                $errors[] = "You don't have the copper to switch!";
            }
            $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '30'
EOSQL;
            $rs = onelinequery($sql);
            if ($rs['amount'] < 20) {
                $errors[] = "You don't have the composites to switch!";
            }
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'
EOSQL;
			$rs = onelinequery($sql);
            if ($rs['amount'] < 50) {
                $errors[] = "You don't have the machinery parts to switch!";
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET funds = funds - 50000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql="UPDATE nations SET government = 'Decentralization' WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 50 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 20 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '30'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 500 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "DELETE FROM resources WHERE amount = 0";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		}
	} else if ($_POST['authoritarianism']) {
		if ($nationinfo['government'] == "Repression") {
			if ($nationinfo['funds'] < 20000000) {
                $errors[] = "You don't have the money to switch!";
            }
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] < 70) {
                $errors[] = "You don't have the machinery parts to switch!";
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET funds = funds - 20000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql="UPDATE nations SET government = 'Authoritarianism' WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 70 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "DELETE FROM resources WHERE amount = 0";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		}
	} else if ($_POST['oppression']) {
        $sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'
EOSQL;
		$rs = onelinequery($sql);
		if ($rs['amount'] < 100) {
            $errors[] = "You don't have the machinery parts to switch!";
        }
        if ($nationinfo['funds'] < 30000000) {
            $errors[] = "You don't have the money to switch!";
        }
		if ($nationinfo['government'] == "Repression") {
            $sql=<<<EOSQL
SELECT nation_id, name FROM nations WHERE user_id = '{$_SESSION['user_id']}'      
EOSQL;
            $sth = $GLOBALS['mysqli']->query($sql);
            while ($nations = mysqli_fetch_array($sth)) {
            $sql=<<<EOSQL
            SELECT amount FROM weaponsbuyermarketplace WHERE nation_id = '{$nations['nation_id']}'
EOSQL;
            $rs1 = onelinequery($sql);
            $sql=<<<EOSQL
            SELECT amount FROM armorbuyermarketplace WHERE nation_id = '{$nations['nation_id']}'
EOSQL;
            $rs2 = onelinequery($sql);
            $sql=<<<EOSQL
            SELECT amount FROM buyermarketplace WHERE nation_id = '{$nations['nation_id']}'
EOSQL;
            $rs3 = onelinequery($sql);
            $sql=<<<EOSQL
            SELECT amount FROM weaponsmarketplace WHERE nation_id = '{$nations['nation_id']}'
EOSQL;
            $rs4 = onelinequery($sql);
            $sql=<<<EOSQL
            SELECT amount FROM armormarketplace WHERE nation_id = '{$nations['nation_id']}'
EOSQL;
            $rs5 = onelinequery($sql);
            $sql=<<<EOSQL
            SELECT amount FROM marketplace WHERE nation_id = '{$nations['nation_id']}'
EOSQL;
            $rs6 = onelinequery($sql);
            if ($rs1['amount'] || $rs2['amount'] || $rs3['amount'] || $rs4['amount'] || $rs5['amount'] || $rs6['amount']) {
                $errors[] = "Remove everything from the marketplaces for {$nations['name']} before switching to Oppression.";
            }
            }
			if (empty($errors)) {
				$sql="UPDATE nations SET funds = funds - 30000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql="UPDATE nations SET government = 'Oppression' WHERE user_id = '{$_SESSION['user_id']}'";
				$GLOBALS['mysqli']->query($sql);
				$sql= "UPDATE resources SET amount = amount - 100 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '10'";
				$GLOBALS['mysqli']->query($sql);
				$sql = "DELETE FROM resources WHERE amount = 0";
				$GLOBALS['mysqli']->query($sql);
				header("Location: overview.php");
				exit;
			}
		}
	} else if ($_POST['poorlydefined']) {
		if ($nationinfo['economy'] == "Poorly Defined") {
			$errors[] = "Your economy is already Poorly Defined.";
		}
		if ($nationinfo['funds'] < 1000000) {
			$errors[] = "You don't have the money to revert!";
		}
		$sql=<<<EOSQL
		SELECT amount FROM weaponsbuyermarketplace WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$rs1 = onelinequery($sql);
		$sql=<<<EOSQL
		SELECT amount FROM armorbuyermarketplace WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$rs2 = onelinequery($sql);
		$sql=<<<EOSQL
		SELECT amount FROM buyermarketplace WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$rs3 = onelinequery($sql);
		if ($rs1['amount'] || $rs2['amount'] || $rs3['amount']) {
			$errors[] = "Remove everything from the buyer's marketplaces before altering your economy type.";
		}
		if (!$errors) {
			$sql=<<<EOSQL
UPDATE nations SET satisfaction = satisfaction - 500, economy = 'Poorly Defined', active_economy = 1, funds = funds - 1000000 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
			$GLOBALS['mysqli']->query($sql);
			header("Location: overview.php");
			exit;
		}
	} else if ($_POST['solarvassal']) {
		if ($nationinfo['government'] == "Oppression" || $nationinfo['government'] == "Decentralization" || $nationinfo['government'] == "Authoritarianism") {
			$sql =<<<EOSQL
			SELECT nation_id, name FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
			$sth = $GLOBALS['mysqli']->query($sql);
			while ($rs = mysqli_fetch_array($sth)) {
				$sql =<<<EOSQL
				SELECT amount FROM resources WHERE nation_id = '{$rs['nation_id']}' AND resource_id = (resource_id = 41 OR resource_id = 74)
EOSQL;
				$rs2 = onelinequery($sql);
				if ($rs2['amount'] > 0) {
					$errors[] = "Destroy your drug farms and/or forbidden research facilities in {$rs['name']} before attempting to become a Solar Vassal.";
				}
			}
		} else {
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND (resource_id = 41 OR resource_id = 74)
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] > 0) {
				$errors[] = "Destroy your drug farms and/or forbidden research facilities before attempting to become a Solar Vassal.";
			}
		}
        if ($nationinfo['government'] == "Solar Vassal") {
            $errors[] = "You already are a Solar Vassal.";
        }
        if ($nationinfo['se_relation'] < 1000) {
			$errors[] = "No.";
        }
		if (!$errors) {
			if ($nationinfo['government'] == "Oppression" || $nationinfo['government'] == "Decentralization" || $nationinfo['government'] == "Authoritarianism") {
			$sql=<<<EOSQL
UPDATE nations SET government = "Solar Vassal" WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
			} else {
			$sql=<<<EOSQL
UPDATE nations SET government = "Solar Vassal" WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
			}
			if ($nationinfo['satisfaction'] > 1250) {
				$sql=<<<EOSQL
UPDATE nations SET satisfaction = 1250 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
			$GLOBALS['mysqli']->query($sql);
			header("Location: overview.php");
			exit;
		}
    } else if ($_POST['lunarclient']) {
		if ($nationinfo['government'] == "Oppression" || $nationinfo['government'] == "Decentralization" || $nationinfo['government'] == "Authoritarianism") {
			$sql =<<<EOSQL
			SELECT nation_id, name FROM nations WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
			$sth = $GLOBALS['mysqli']->query($sql);
			while ($rs = mysqli_fetch_array($sth)) {
				$sql =<<<EOSQL
				SELECT amount FROM resources WHERE nation_id = '{$rs['nation_id']}' AND (resource_id = 41 OR resource_id = 74)
EOSQL;
				$rs2 = onelinequery($sql);
				if ($rs2['amount'] > 0) {
					$errors[] = "Destroy your drug farms and/or forbidden research facilities in {$rs['name']} before attempting to become a Lunar Client.";
				}
			}
		} else {
			$sql =<<<EOSQL
SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND (resource_id = 41 OR resource_id = 74)
EOSQL;
			$rs = onelinequery($sql);
			if ($rs['amount'] > 0) {
				$errors[] = "Destroy your drug farms and/or forbidden research facilities before attempting to become a Lunar Client.";
			}
		}
		if ($nationinfo['government'] == "Lunar Client") {
            $errors[] = "You already are a Lunar Client.";
        }
        if ($nationinfo['nlr_relation'] < 1000) {
			$errors[] = "No.";
        }
		if (!$errors) {
			if ($nationinfo['government'] == "Oppression" || $nationinfo['government'] == "Decentralization" || $nationinfo['government'] == "Authoritarianism") {
			$sql=<<<EOSQL
UPDATE nations SET government = "Lunar Client" WHERE user_id = '{$_SESSION['user_id']}'
EOSQL;
			} else {
			$sql=<<<EOSQL
UPDATE nations SET government = "Lunar Client" WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
			}
			if ($nationinfo['satisfaction'] > 1250) {
				$sql=<<<EOSQL
UPDATE nations SET satisfaction = 1250 WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
				$GLOBALS['mysqli']->query($sql);
			}
			$GLOBALS['mysqli']->query($sql);
			header("Location: overview.php");
			exit;
		}
    }
    if ($_POST['freemarket'] || $_POST['statecontrolled']) {
		$sql=<<<EOSQL
		SELECT amount FROM weaponsbuyermarketplace WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$rs1 = onelinequery($sql);
		$sql=<<<EOSQL
		SELECT amount FROM armorbuyermarketplace WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$rs2 = onelinequery($sql);
		$sql=<<<EOSQL
		SELECT amount FROM buyermarketplace WHERE nation_id = '{$_SESSION['nation_id']}'
EOSQL;
		$rs3 = onelinequery($sql);
		if ($rs1['amount'] || $rs2['amount'] || $rs3['amount']) {
			$errors[] = "Remove everything from the buyer's marketplaces before altering your economy type.";
		}
        if ($nationinfo['economy'] != "Poorly Defined") {
            $errors[] = "You already made your choice.";
        }
        if ($_POST['freemarket']) {
            $resource_id = 20; //coffee
            $name = "Free Market";
        }
        if ($_POST['statecontrolled']) {
            $resource_id = 18; //cider
            $name = "State Controlled";
        }
        $sql =<<<EOSQL
    SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'
EOSQL;
        $rs = onelinequery($sql);
        $copperamount = $rs['amount'];
        if ($rs['amount'] < 500) {
            $errors[] = "You don't have the copper to switch!";
        }
        if ($nationinfo['funds'] < 1000000) {
            $errors[] = "You don't have the money to switch!";
        }
        $sql=<<<EOSQL
    SELECT amount FROM resources WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$resource_id}'
EOSQL;
        $rs = onelinequery($sql);
        if (($rs['amount'] < 25) && $_POST['freemarket']) {
            $errors[] = "We're not doing this without enough coffee, boss!";
        } else if (($rs['amount'] < 25) && $_POST['statecontrolled']) {
            $errors[] = "You will need more cider for us to do this, comrade!";
        }
        if (empty($errors)) {
            $sql="UPDATE nations SET active_economy = 1, economy = '{$name}', funds = funds - 1000000 WHERE nation_id = '{$_SESSION['nation_id']}'";
            $GLOBALS['mysqli']->query($sql);
            $sql= "UPDATE resources SET amount = amount - 25 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '{$resource_id}'";
            $GLOBALS['mysqli']->query($sql);
            $sql= "UPDATE resources SET amount = amount - 500 WHERE nation_id = '{$_SESSION['nation_id']}' AND resource_id = '2'";
            $GLOBALS['mysqli']->query($sql);
            $sql = "DELETE FROM resources WHERE amount = 0";
            $GLOBALS['mysqli']->query($sql);
            header("Location: overview.php");
            exit;
		}
	}
}