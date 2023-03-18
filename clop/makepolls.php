<?php
require_once("backend/allfunctions.php");
if ($_SESSION['user_id'] != 1) {
	die("Get out of here, stalker");
}

if (empty($_GET) && empty($_POST)) {
$sql = "SELECT r.*, u.username FROM requests AS r INNER JOIN users AS u ON r.submitter = u.user_id ORDER BY r.request_id DESC";
$sth = $GLOBALS['mysqli']->query($sql);
while ($rs = $sth->fetch_array()) {
	foreach ($rs as $key => $value) {
		$display[$key] = htmlentities($value, ENT_SUBSTITUTE, "UTF-8");
}
	echo <<<EOTXT
<div><hr>
By: <a href="viewuser.php?user_id={$rs['submitter']}">{$display['username']}</a></br>
On: {$display['submitdate']}</br>
Title: {$display['title']}</br>
Desc:
<p style="white-space: pre;">{$display['description']}</p>
Isbug: {$rs['isbug']}</br>
<form action="makepolls.php" method="get">
<input type="hidden" name="poll_id" value="{$rs['request_id']}">
<input type="submit" name="edit" value="Pollify">
EOTXT;
if ($rs['visible'] == 1) {
	echo '<input type="submit" name="takedown" value="Deactivate">';
}
if ($rs['voteable'] == 1) {
	echo '<input type="submit" name="close" value="Close Voting">';
}
echo <<<EOTXT
</form>
<hr></div></br>
EOTXT;
}
} /* endif empty($_GET) */ else if ($_GET['edit'] == "Pollify") {
$id = $GLOBALS['mysqli']->real_escape_string($_GET['poll_id']);
$sql = "SELECT * FROM requests WHERE request_id = '".$id."'";
$rs = onelinequery($sql);
foreach ($rs as $key => $value) {
	$display[$key] = htmlentities($value, ENT_SUBSTITUTE, "UTF-8");
}
echo <<<EOTXT
<div><hr><form action="makepolls.php" method="post">
By: {$rs['submitter']}<input type="hidden" name="submitter" value="{$rs['submitter']}"></br>
On: <input type="text" name="" value="{$display['submitdate']}" placeholder="leave empty for NOW()"> <label for="preservedate">Preserve: </label><input type="checkbox" name="preservedate" checked id="preservedate"></br>
Title: <input type="text" name="title" value="{$display['title']}"></br>
Desc:
<textarea name="desc">{$display['description']}</textarea></br>
Options:
<div id="options-container">
<input type="text" name="options[]"></br>
<button onclick="addoption(event)">Add Option</button></br>
</div>
<input type="hidden" name="poll_id" value="{$rs['request_id']}">
<label for=unchanged">Unchanged: </label><input type="checkbox" name="unchanged" checked id="unchanged">
<input type="submit" name="newpoll" value="New Poll">
</form>
<script>
function addoption(e) {
e.preventDefault();
var field = document.createElement('input');
field.type = 'text';
field.name = 'options[]';
var eol = document.createElement('br');
e.target.parentElement.insertBefore(field, e.target);
e.target.parentElement.insertBefore(eol, e.target);
}
</script>
<hr></div>
EOTXT;
} else if ($_POST['newpoll'] == "New Poll") {
$mysql['options'] = $_POST['options'];
unset($_POST['options']);
foreach ($_POST as $key => $value) {
	$mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
foreach ($mysql['options'] as $key => $value) {
	$mysql['options'][$key] = $GLOBALS['mysqli']->real_escape_string($value);
}
$sql = "INSERT INTO poll_options (poll_id, opttext) VALUES";
$sql .= " ('{$mysql['poll_id']}', '{$mysql['options'][0]}')";
unset($mysql['options'][0]);
foreach ($mysql['options'] as $opt) {
	$sql .= ", ('{$mysql['poll_id']}', '{$opt}')";
}
$GLOBALS['mysqli']->query($sql);

$sql = <<<EOSQL
UPDATE requests SET
	visible = '1'
	, voteable = '1'
EOSQL;
if ($_POST['unchanged'] != "on") {
	$sql .= <<<EOSQL
	, title = '{$mysql['title']}'
	, description = '{$mysql['desc']}'
EOSQL;
	if ($_POST['preservedate'] != "on") {
		if (!empty($_POST['newdate']))
			$sql .= ", submitdate = '{$mysql['newdate']}'";
		else
			$sql .= ", submitdate = NOW()";
	}
} /* endif $_POST['unchanged'] */
$sql .= " WHERE request_id = '".$mysql['poll_id']."'";
$GLOBALS['mysqli']->query($sql);
echo 'Success.';
//notify user that his request was made into poll
$sql = <<<EOSQL
INSERT INTO messages VALUES ('', '0', '{$mysql['submitter']}', 'Your Request was accepted and made into a poll.', '1', '0', NOW(), '0')
EOSQL;
$GLOBALS['mysqli']->query($sql) or die($GLOBALS['mysqli']->error);
} else if ($_GET['takedown'] == "Deactivate") {
$id = $GLOBALS['mysqli']->real_escape_string($_GET['poll_id']);
$sql = "UPDATE requests SET visible = 0 WHERE request_id = '{$id}'";
$GLOBALS['mysqli']->query($sql);
$sql = "UPDATE requests SET voteable = 0 WHERE request_id = '{$id}'";
$GLOBALS['mysqli']->query($sql);
echo 'Voting closed.</br>';
echo 'Poll deactivated.';
} else if ($_GET['close'] == "Close Voting") {
$id = $GLOBALS['mysqli']->real_escape_string($_GET['poll_id']);
$sql = "UPDATE requests SET voteable = 0 WHERE request_id = '{$id}'";
$GLOBALS['mysqli']->query($sql);
echo 'Voting closed.';
}

