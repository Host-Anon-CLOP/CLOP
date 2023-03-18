<?php
require_once("allfunctions.php");
needsuser();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
    $display[$key] = htmlentities($value, ENT_SUBSTITUTE, "UTF-8");
}
$old = array();
$waschecked = "";
$isbug = ($_POST['isbug'] == "on") ? 1 : 0;

if ($_POST['action'] == "Submit") {
	if ($_POST['token_featureform'] != $_SESSION['token_featureform']) {
		$errors[] = "Try Again.";
		$old['description'] = $display['description'];
		$old['title'] = $display['title'];
		$waschecked = ($_POST['isbug'] == "on") ? "checked" : "";
	} else if ($_POST['title'] == "") {
		$errors[] = "Must specify a title.";
	}
	$sql = <<<EOSQL
SELECT COUNT(*) AS count FROM requests WHERE submitter = {$_SESSION['user_id']} AND submitdate >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND isbug = FALSE
EOSQL;
	$count = onelinequery($sql);
	if ($count['count'] >= 5 && !$isbug && ($_SESSION['user_id'] != 1)) {
		$errors[] = "Hold on! You've exceeded current form submit limits.";
	}
	$sql = <<<EOSQL
SELECT COUNT(*) AS count FROM requests WHERE submitter = {$_SESSION['user_id']} AND submitdate >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND isbug = TRUE
EOSQL;
	$count = onelinequery($sql);
	if ($count['count'] >= 15 && $isbug && ($_SESSION['user_id'] != 1)) {
		$errors[] = "WaiWaiWait. Are you trying to DDOS or what?";
	}
	if (!$errors) {
		$sql =<<<EOSQL
INSERT INTO requests (submitter, title, description, isbug, submitdate) VALUES ('{$_SESSION['user_id']}', '{$mysql['title']}', '{$mysql['description']}', '{$isbug}', NOW())
EOSQL;
		$res = $GLOBALS['mysqli']->query($sql);
		if ($res == false) {
			$errors[] = "SQL Error.";
			$old['description'] = $display['description'];
			$old['title'] = $display['title'];
			$waschecked = ($_POST['isbug'] == "on") ? "checked" : "";
		} else {
			$infos[] = "Request submitted.";
		}
	}
}
