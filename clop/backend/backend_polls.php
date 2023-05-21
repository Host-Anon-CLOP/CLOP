<?php
require_once("backend/allfunctions.php");
needsuser();
foreach ($_POST as $key => $value) {
    $mysql[$key] = $GLOBALS['mysqli']->real_escape_string($value);
}

class VoteColors {
	private $colors = array('#FF0000', '#00FF00', '#0000FF');
	private $size;
	private $iter = 0;

	function __construct() {
		$this->size = count($this->colors);;
	}

	public function getcolor() {
		$cur = $this->colors[$this->iter];
		++$this->iter;
		if ($this->iter == $this->size) $this->iter = 0;
		return $cur;
	}

	public function newpoll() {
		$this->iter = 0;
	}
}
$colorgen = new VoteColors();

/*
 * As much as I don't mind goto, and this would be a case when it's legitimately useful
 * this is the the anti-goto-boogeyman way to implement this without unnecessary if()s
 * Fuck these people who are afraid of a goto. But I did things your way people. Are you happy?
 */
function getpollbody($pollID, $isvoteable)
{
$poll = '';
if ($isvoteable == 1) {

	$sql = "SELECT 1 FROM votes WHERE user_id = '{$_SESSION['user_id']}' AND poll_id = '{$pollID}'";
	$vote = $GLOBALS['mysqli']->query($sql);
	if ($vote->num_rows == 0) {
	//not voted
		$pollToken = "tokenfor_".$pollID;
		$_SESSION[$pollToken] = sha1(rand());

		$sql = "SELECT optid, opttext FROM poll_options WHERE poll_id = '".$pollID."'";
		$sth2 = $GLOBALS['mysqli']->query($sql);
		$poll .= <<<EOTXT
<div><form action="polls.php" method="post" name="vote_poll_{$pollID}">
<input type="hidden" name="{$pollToken}" value="{$_SESSION[$pollToken]}">
<input type="hidden" name="vote" value="{$pollID}">
EOTXT;
		while ($rs2 = $sth2->fetch_array()) {
			 $poll .= <<<EOTXT
<input type="radio" name="option" value="{$rs2['optid']}" onclick="this.form.submit()">
<span>{$rs2['opttext']}</span></br>
EOTXT;
		}
		$poll .= '</form></div>';
		//disclose total votes cast as well
		$sql = "SELECT COUNT(*) AS numvotes FROM votes WHERE poll_id = '{$pollID}'";
		$sth = onelinequery($sql);
		$poll .= <<<EOTXT
<div style="display: none;" name="poll-results" data-totalvotes="{$sth['numvotes']}"></div>
EOTXT;
		//finish early, without displaying the votes
		return $poll;
	} /* endif $vote->num_rows */ 
} /* endif $isvoteable */ else {
$poll .= '<b>Poll closed.</b></br>';
}
$sql = <<<EOSQL
SELECT * FROM (SELECT v.option, count(v.option) AS num, o.opttext AS text, IF(v.user_id = '{$_SESSION['user_id']}', '(Voted)', '') AS myvote FROM votes AS v INNER JOIN poll_options AS o ON v.option = o.optid WHERE v.poll_id = '{$pollID}' GROUP BY `option` WITH ROLLUP) AS tab ORDER BY num DESC, `option` ASC
EOSQL;
$res = $GLOBALS['mysqli']->query($sql);
//row with total votes is the first one
$rs = $res->fetch_array();
$total = (double)$rs['num'];
$sum = 0;
$poll .= '<div name="poll-results" data-totalvotes="'.$total.'">';
while ($rs = $res->fetch_array()) {
	$color = $GLOBALS['colorgen']->getcolor();
	$width = ($rs['num'] / $total) * 100.0;
	$dispWidth = (int)$width;
	$tmp = ($sum / $width) * -100;
	$sum += $width;
	$rs['text'] = str_replace('"', '&quot;', $rs['text']);
	$poll .= <<<EOTXT
<div style="width: {$width}%; height: 20px; display: inline-block;" data-desc="{$rs['text']}" data-vote="{$rs['myvote']}" data-numvotes="{$rs['num']}" data-percentage="{$width}" onmouseover="extrainfo(event)" onmouseleave="hideinfo(event)">
<div class="growable" style="background: {$color}; color: {$color}; --my-width: {$width}; --left-offset: {$tmp}%;">
<p style="text-align: center;">{$dispWidth}%</p></div></div>
EOTXT;
}
$GLOBALS['colorgen']->newpoll();
$poll .= '</div></br><div name="extended-desc"></div>';
return $poll;
}

if (isset($_POST['vote'])) {
	$pollToken = "tokenfor_".$mysql['vote'];
	if ($_POST[$pollToken] != $_SESSION[$pollToken]) {
		$errors[] = "Try Again.";
	}
	//TODO: check if $mysql['vote'] IN TABLE requests (AND isvoteable = 1)
	//and check if $mysql['option'] IN poll_options WHERE poll_id = $mysql['vote']
	if (!$errors) {
		$sql = <<<EOSQL
INSERT INTO votes (`poll_id`, `user_id`, `option`, `date`) VALUES ('{$mysql['vote']}', '{$_SESSION['user_id']}', '{$mysql['option']}', NOW())
EOSQL;
		$GLOBALS['mysqli']->query($sql);
		$infos[] = "Thank you for voting.";
	}
}

$sql = "SELECT COUNT(*) AS numusers FROM users WHERE stasismode = '0'";
$sth = onelinequery($sql);
$allusers = $sth['numusers'];

$sql = <<<EOSQL
SELECT rq.request_id, rq.submitter, rq.title, rq.description, rq.submitdate, rq.voteable, u.username FROM requests as rq INNER JOIN users as u ON rq.submitter = u.user_id WHERE rq.visible = '1' AND rq.isbug = '0' ORDER BY rq.submitdate DESC
EOSQL;
$sth = $GLOBALS['mysqli']->query($sql);
$totalusers = '<div style="display: none;" id="allusers" data-allusers="'.$allusers.'"></div>';
while ($rs = $sth->fetch_array()) {
	$poll = <<<EOTXT
<div class="row well">
<span>Suggested by: <a href="viewuser.php?user_id={$rs['submitter']}">{$rs['username']}</a></span><span class="pull-right">Submit date: {$rs['submitdate']}</span></br>
<span><b>{$rs['title']}</b></span><span class="pull-right"><span name="totalvotes-here"></span> Votes</span></br><span class="pull-right">Voter turnout: <b><span name="turnout-here"></span>%</b></span></br>
<button class="btn btn-success" onclick="this.nextElementSibling.className = 'well visible'; this.style.display = 'none';">Show More</button>
<div class="well invisible" name="poll-content">
<div>{$rs['description']}</div></br>
EOTXT;
$poll .= getpollbody($rs['request_id'], $rs['voteable']);
$poll .= "</div></div>";

$polls[] = $poll;
}
