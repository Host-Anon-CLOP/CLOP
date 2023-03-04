<?php
include ("backend/minimal.php");
if ($_GET['token'] == $_SESSION["token_chat"]) {
    $sql=<<<EOSQL
    SELECT MAX(message_id) AS lastmessage FROM chat
EOSQL;
    $lastmessage = onelinequery($sql);
    $getlast = (int)$_GET['lastmessage'];
    if ($getlast != $lastmessage['lastmessage']) {
$sql=<<<EOSQL
SELECT c.*, u.username FROM chat c
INNER JOIN users u ON u.user_id = c.user_id
WHERE c.message_id > {$getlast}
ORDER BY posted
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = mysqli_fetch_array($sth)) {
    $date = date("D H:i:s", strtotime($rs['posted']));
	$message = htmlentities($rs['message'], ENT_SUBSTITUTE, "UTF-8");
	echo <<<EOFORM
[{$date}] <a href="viewuser.php?user_id={$rs['user_id']}">{$rs['username']}</a>: {$message}<br/>
EOFORM;
}
	echo <<<EOFORM
<input type="hidden" id="lastmessage" value="{$lastmessage['lastmessage']}"/>
EOFORM;
    } else {
		http_response_code(304);
	}
} else {
    echo <<<EOFORM
Log in to chat, and only have one chat window open at a time!
EOFORM;
}
?>