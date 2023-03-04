<?php
include ("backend/minimal.php");
if ($_SESSION['user_id'] && ($_GET['message'] != "") && ($_GET['token'] == $_SESSION["token_chat"])) {
	$mysql['message'] = $GLOBALS['mysqli']->real_escape_string($_GET['message']);
    $sql=<<<EOSQL
    SELECT message_id FROM chat WHERE user_id = '{$_SESSION['user_id']}' AND posted > DATE_SUB(NOW(), INTERVAL 1 SECOND)
EOSQL;
    $rs = onelinequery($sql);
    if (!$rs['message_id']) {
	$sql=<<<EOSQL
	INSERT INTO chat SET message = '{$mysql['message']}', posted = NOW(), user_id = '{$_SESSION['user_id']}'
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    $sql=<<<EOSQL
	DELETE FROM chat WHERE posted <= DATE_SUB(NOW(), INTERVAL 1 WEEK)
EOSQL;
	$GLOBALS['mysqli']->query($sql);
    }
}
?>