<?php
include_once("allfunctions.php");
needsalliance();
if ($_POST && (($_POST["token_changepositions"] == "") || ($_POST["token_changepositions"] != $_SESSION["token_changepositions"]))) {
    $errors[] = "Try again.";
}
if ($_POST || ($_SESSION["token_changepositions"] == "")) {
    $_SESSION["token_changepositions"] = sha1(rand() . $_SESSION["token_changepositions"]);
}
$validelements = array(1 => "Magic", 2 => "Loyalty", 4 => "Laughter", 8 => "Kindness", 16 => "Honesty", 32 => "Generosity");
if ($_POST['changepositions']) {
    $mysql['changeposition1'] = (int)$_POST['changeposition1'];
    $mysql['changeposition2'] = (int)$_POST['changeposition2'];
    if ($mysql['changeposition1'] == $mysql['changeposition2']) {
        $errors[] = "Same element was selected twice.";
    }
    if (!$validelements[$mysql['changeposition1']] || !$validelements[$mysql['changeposition2']]) {
        $errors[] = "Select two elements to swap.";
    }
    if (!hasamount(63, $_SESSION['user_id'], 10000)) {
		$errors[] = "You do not have the Harmony to change the balance of elements.";
	}
    if (!$errors) {
		$sql=<<<EOSQL
INSERT INTO positionswaps SET changeposition1 = {$mysql['changeposition1']}, changeposition2 = {$mysql['changeposition2']}, effectivedate = NOW()
EOSQL;
		$GLOBALS['mysqli']->query($sql);
        addamount(63, $_SESSION['user_id'], -10000);
		$infos[] = "Positions will change in 24 hours.";
		$newsmessage =<<<EOFORM
{$userinfo['username']} has decided to swap the positions of {$validelements[$mysql['changeposition1']]} and {$validelements[$mysql['changeposition2']]}!
The change will occur at the beginning of the tick after 24 hours.
EOFORM;
		$sql=<<<EOSQL
		INSERT INTO news SET message = '{$newsmessage}', posted = NOW()
EOSQL;
		$GLOBALS['mysqli']->query($sql);
    }
}
$sql=<<<EOSQL
SELECT * FROM elementpositions
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
	$positions[$rs['position']] = $rs['resource_id'];
}
?>