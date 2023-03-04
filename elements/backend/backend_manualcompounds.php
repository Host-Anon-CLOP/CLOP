<?php
include_once("allfunctions.php");
include_once("listresources.php");
needsalliance();
if ($_POST && (($_POST["token_manualcompounds"] == "") || ($_POST["token_manualcompounds"] != $_SESSION["token_manualcompounds"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_manualcompounds"] == "")) {
    $_SESSION["token_manualcompounds"] = sha1(rand() . $_SESSION["token_manualcompounds"]);
}
if ($_POST['compound']) {
	$mysql['amount'] = (int)$_POST['amount'];
	$mysql['compound'][1] = (int)$_POST['compound1'];
	$mysql['compound'][2] = (int)$_POST['compound2'];
	$mysql['compound'][3] = (int)$_POST['compound3'];
	$mysql['compound'][4] = (int)$_POST['compound4'];
	$mysql['compound'][5] = (int)$_POST['compound5'];
	$mysql['compound'][6] = (int)$_POST['compound6'];
	$totalelement = 0;
	$elementcount = 0;
	foreach ($mysql['compound'] AS $element) {
		if ($element > 0 && $element < 64) {
			$elementcount++;
			if (shareselement($element, $totalelement)) {
				$sharingerror = true;
			}
			if (!hasamount($element, $_SESSION['user_id'], $mysql['amount'] * 10)) {
				$name = getelementname($element);
				$errors[] = "You do not have enough {$name} to do that.";
			}
			$totalelement += $element;
		}
	}
	if ($elementcount < 2) {
		$errors[] = "Compound two or more elements.";
	} else if ($sharingerror) {
		$errors[] = "Two of your chosen elements share a basic element.";
	} else {
		$sql=<<<EOSQL
		SELECT tier FROM resourcedefs WHERE resource_id = '{$totalelement}'
EOSQL;
		$rs = onelinequery($sql);
		if ($rs['tier'] > $userinfo['tier']) {
			$errors[] = "That would create an element with a higher tier than your own.";
		}
	}
	if ($mysql['amount'] <= 0) {
		$errors[] = "No amount entered.";
	}
	if (!$errors) {
		$multiplier = 11 - $elementcount;
		$totalamount = $mysql['amount'] * $multiplier;
		foreach ($mysql['compound'] AS $element) {
			if ($element) { // no Void!
				addamount($element, $_SESSION['user_id'], $mysql['amount'] * -10);
			}
		}
		addamount($totalelement, $_SESSION['user_id'], $totalamount);
		$name = getelementname($totalelement);
		$infos[] = "You have generated {$totalamount} {$name}.";
	}
}
$resourcelist = getresourcelist($_SESSION['user_id']);
?>