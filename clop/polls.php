<?php
require_once("backend/backend_polls.php");
$extratitle = "Polls - ";
include("header.php");
echo <<<EOTXT
<style>
.growable {
	position: relative;
}
.growable:hover {
	width: calc((100 / var(--my-width)) * 100%);
	left: var(--left-offset) !important;
	color: #000000 !important;
	z-index: 2;
	position: relative;
	left: 0px;
}
#option-tooltip {
	position: absolute;
	z-index: 2;
	color: white;
}
.invisible {
	display: none;
}
.visible {
	display: block;
}
div[name="poll-results"] {
	border: solid 1px;
	border-radius: 3px;
	border-color: white;
}
</style>
<script>
function extrainfo(e) {
var dat = e.target.parentElement.parentElement.dataset;
var elem = $('div[name="extended-desc"]', e.target.parentElement.parentElement.parentElement.parentElement)[0];
suffix = (dat.numvotes == 1) ? ' Vote</b></span>' : ' Votes</b></span>';
elem.innerHTML = dat.desc + ' <b>' + dat.vote + '</b><span class="pull-right"><b>' + dat.numvotes + suffix;
}

function hideinfo(e) {
var elem = $('div[name="extended-desc"]', e.target.parentElement.parentElement.parentElement)[0];
elem.innerText = '';
}

window.addEventListener('load', function () {
var allusers = document.getElementById('allusers').dataset.allusers;
var polls = $$('div.row.well');
var i, votes, el, t;
for (i = 0; i < polls.length; ++i) {
	votes = $('div[name="poll-results"]', polls[i])[0].dataset.totalvotes;
	el = $('span[name="totalvotes-here"]', polls[i])[0];
	el.innerText = votes;
	(votes == 1) ? el.nextSibling.textContent = " Vote" : 0;
	t = $('span[name="turnout-here"]', polls[i])[0];
	t.innerText = parseInt(((votes / allusers) * 100), 10);
}
});

function $$ (selector, el) {
    if (!el) {el = document;}
    return el.querySelectorAll(selector);
}
</script>
<center>
<h3>People's Republic of >ReClop</h2>
<h5>Global rules 1 & 2 doesn't apply here!</h5>
</center>
<b>You are allowed to vote once per each poll, and you cannot change your vote. Your vote is cast automatically when you select appropriate option, so be careful!</b>
</br>Also note that the poll result isn't binding. The polls may last for indefinite time period.
</br>
EOTXT;
if (count($polls) > 0) {
echo $totalusers;
foreach($polls as $poll) {
	echo $poll.'</br>';
}
} else {
echo '</br>There are currently no active polls.';
}
