<?php
include("backend/backend_header.php");
/*$headernumber = rand(1, 10);
if ($headernumber == 4 || $headernumber == 2) {
    $headerextension = "gif";
} else {
    $headerextension = "png";
}*/
if (!$_SESSION['css']) {
    $stylesheets =<<<EOFORM
    <link href="css/bootstrap.min.css" rel="stylesheet" media="Screen">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
EOFORM;
    $internal = "";
} else if ($_SESSION['css'] == 1) {
    $stylesheets =<<<EOFORM
    <link href="css/cyborg.min.css" rel="stylesheet" media="Screen">
EOFORM;
    $internal =<<<EOFORM
    .form-control{
    background-color: #555;
    color: #eee;
    }
EOFORM;
} else {
    $stylesheets =<<<EOFORM
    <link href="css/slate.min.css" rel="stylesheet" media="Screen">
EOFORM;
    $internal = "";
}
if ($_SESSION['hidereports']) {
    $reportcss=<<<EOFORM
    .report-showbutton{display: block;}
    .report-details{display: none;}
EOFORM;
} else {
    $reportcss=<<<EOFORM
    .report-showbutton{display: none;}
    .report-details{display: block;}
EOFORM;
}
if (date("I")) {
$timer = 10800 - (date("U") + 3600) % 10800;
} else {
$timer = 10800 - date("U") % 10800;
}
$currenttime = date("Y-m-d H:i:s");
header("Content-Type: text/html; charset=utf-8");
echo <<<EOFORM
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <link rel="icon" type="image/png" href="images/favicon.png" />
    {$stylesheets}
    <style type="text/css">
        
ul.nav li.dropdown:hover > ul.dropdown-menu {
display: block;
}
        {$internal}
        {$reportcss}
#content{
	min-width: 500px;
	max-width: 95%;
	margin-left: auto;
	margin-right: auto;
	padding: 40px
}
#content-outer{
	
}
.text-width-micro{
	width: 64px;
}
span.spoiler {
color: #000000;
background: #000000;
}

span.spoiler:hover {
color: #FFFFFF;
}
    </style>
    <script type="text/javascript">
      function doCountdownTick(countdown_seconds){
        if(countdown_seconds == 0)
          countdown_seconds = 60 * 60 * 3;
        var hours = Math.floor(countdown_seconds / 60 / 60);
        var mins = Math.floor(countdown_seconds / 60) - (hours * 60);
        var secs = countdown_seconds % 60;
       
        var countdownstring =
          hours + ":" + 
          ("0" + mins).slice(-2) + ":" + 
          ("0" + secs).slice(-2);
        document.getElementById("countdown").innerHTML = countdownstring;
       
        countdown_seconds--;
        setTimeout("doCountdownTick(" + countdown_seconds + ")", 1000);
      }
      window.onload = function(){
        doCountdownTick({$timer});
      }
    </script>
    <title>{$extratitle}The Compounds of Harmony</title>
</head>
<body alink="#00ae0e" link="#00ae0e" vlink="#1f8001">
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
<div id="content-outer">
EOFORM;
/*if (!$_SESSION['hidebanners']) {
echo <<<EOFORM
<center><img src="images/{$headernumber}.{$headerextension}"/></center>
EOFORM;
}*/
echo <<<EOFORM
<center>Whatever you want to know, it's probably on the <a href="http://8ch.net/compounds/" target="_new_win">8chan board</a>.</center>
<center><span id="topmessage">&quot;{$topmessage}&quot;</span></center>
EOFORM;
if ($errors) {
    $errormessages.=<<<EOFORM
    <div class="alert alert-danger">
EOFORM;
    foreach ($errors as $value) {
        $errormessages.=<<<EOFORM
        <div class="error">{$value}</div>
EOFORM;
    }
    $errormessages.=<<<EOFORM
    </div>
EOFORM;
}
if ($infos) {
    $infomessages.=<<<EOFORM
    <div class="alert alert-info">
EOFORM;
    foreach ($infos as $value) {
        $infomessages.=<<<EOFORM
        <div class="info">{$value}</div>
EOFORM;
    }
    $infomessages.=<<<EOFORM
    </div>
EOFORM;
}
if ($userinfo['alliance_id']) {
echo <<<EOFORM
<nav class="navbar navbar-default" role="navigation">
<div class="navbar-header">
</div>
<ul class="nav navbar-nav">
  <li class="dropdown"><a class="dropdown-toggle" href="messages.php">Messages <span class="badge">{$messagenumber}</span></a></li>
  <li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">User <span class="badge">{$invitationnumber}</span><b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="overview.php">Overview</a></li>
		<li><a href="useractions.php">Actions</a></li>
        <li><a href="clopactions.php">&gt;CLOP Actions</a></li>
		<li><a href="reports.php">Reports</a></li>
		<li><a href="userinfo.php">User Info</a></li>
		<li><a href="allianceinvites.php">Alliance Invitations</a><span class="badge">{$invitationnumber}</span></li>
		<li><a href="blocklist.php">Blocklist</a></li>
		<li><a href="logout.php">Logout</a></li>
	</ul>
  </li>
  <li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Resources <b class="caret"></b></a>
	<ul class="dropdown-menu">
	  <li><a href="autocompounds.php">Automatic Compounding</a></li>
	  <li><a href="manualcompounds.php">Manual Compounding</a></li>
      <li><a href="bankresources.php">Bank Resources</a></li>
      <li><a href="philippy.php">Get Free Resources</a></li>
      <li><a href="placeonphilippy.php">Offer Free Resources</a></li>
	</ul>
  </li>
  <li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Alliance <span class="badge">{$alliancemessagenumber}</span><b class="caret"></b></a>
	<ul class="dropdown-menu">
	  <li><a href="alliancemessages.php">Messages <span class="badge">{$alliancemessagenumber}</span></a></li>
	  <li><a href="allianceoverview.php">Overview</a></li>
	  <li><a href="alliancereports.php">Reports</a></li>
	  <li><a href="allianceactions.php">Actions</a></li>
      <li><a href="alliancedeals.php">Deals</a></li>
      <li><a href="alliancebankresources.php">Bank Resources</a></li>
      <li><a href="makealliance.php">Create New Alliance</a></li>
	</ul>
  </li>
  <li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Trading <span class="badge">{$dealnumber}</span><b class="caret"></b></a>
	<ul class="dropdown-menu">
	  <li><a href="marketplace.php">Search Market</a></li>
	  <li><a href="placeonmarket.php">Place on Market</a></li>
	  <li><a href="deals.php">Deals <span class="badge">{$dealnumber}</span></a></li>
	  <li><a href="myoffers.php">My Offers</a></li>
	</ul>
  </li>
  <li class="dropdown">
	<a class="dropdown-toggle" href="#">War <span class="badge">{$totalincomingnumber}</span><b class="caret"></b></a>
	  <ul class="dropdown-menu">
        <li><a href="makewar.php">Make War</a></li>
		<li><a href="incoming.php">Incoming <span class="badge">{$incomingnumber}</span></a></li>
		<li><a href="outgoing.php">Outgoing</a></li>
		<li><a href="alliancemakewar.php">Make War (Alliance)</a></li>
		<li><a href="allianceincoming.php">Incoming (Alliance) <span class="badge">{$allianceincomingnumber}</span></a></li>
		<li><a href="allianceoutgoing.php">Outgoing (Alliance)</a></li>
        <li><a href="voidactions.php">Void</a></li>
	  </ul>
  </li>
  <li class="dropdown">
	<a class="dropdown-toggle" href="#">Harmony <b class="caret"></b></a>
	<ul class="dropdown-menu">
	<li><a class="dropdown-toggle" href="harmonyactions.php">Change Costs</a></li>
	<li><a class="dropdown-toggle" href="changepositions.php">Change Element Positions</a></li>
	</ul>
  </li>
</ul>
<ul class="nav navbar-nav navbar-right pull-right">
  <li><a>Server time: {$currenttime}</a></li>
  <li><a>Next tick: <span class="text-danger" id="countdown">{$countdowntimer}</span></a></li>
  <li><a href="search.php">Search</a></li>
  <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Rankings <b class="caret"></b></a>
  <ul class="dropdown-menu">
	  <li><a href="rankings.php?mode=production">Production</a></li>
      <li><a href="alliancerankings.php?mode=production">Alliance Production</a></li>
      <li><a href="rankings.php?mode=unallied">Unallied Players</a></li>
  </ul>
  </li>
  <li><a href="news.php">News</a></li>
  <li><a href="rules.php">Rules</a></li>
  <li><a href="guide.php"><font color="orange">Guide</font></a></li>
</ul>
<div class="navbar-header"></div>
</nav>
  <div id="content">  
    {$errormessages}
    {$infomessages}
EOFORM;
} else if ($userinfo['user_id']) {
	echo <<<EOFORM
<nav class="navbar navbar-default" role="navigation">
<div class="navbar-header">
</div>
<ul class="nav navbar-nav">
  <li class="dropdown"><a class="dropdown-toggle" href="messages.php">Messages <span class="badge">{$messagenumber}</span></a></li>
  <li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">User <span class="badge">{$invitationnumber}</span><b class="caret"></b></a>
	<ul class="dropdown-menu">
	<li><a href="userinfo.php">User Info</a></li>
	<li><a href="allianceinvites.php">Alliance Invitations<span class="badge">{$invitationnumber}</span></a></li>
	<li><a href="makealliance.php">Create New Alliance</a></li>
	<li><a href="blocklist.php">Blocklist</a></li>
	<li><a href="logout.php">Logout</a></li>
	</ul>
  </li>
  <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Get an alliance to play! <b class="caret"></b></a>
  <ul class="dropdown-menu">
  <li><a href="alliancerankings.php?mode=production">Message the alliance leaders</a></li>
  <li><a href="http://8ch.net/compounds/">Post on the board</a></li>
  </ul>
  </li>
  <li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Demo Pages <b class="caret"></b></a>
  <ul class="dropdown-menu">
	<li><a href="demooverview.php">Overview</a></li>
	<li><a href="demoactions.php">Actions</a></li>
</ul>
</ul>
<ul class="nav navbar-nav navbar-right pull-right">
  <li><a>Server time: {$currenttime}</a></li>
  <li><a>Next tick: <span class="text-danger" id="countdown">Loading...</span></a></li>
  <li><a href="search.php">Search</a></li>
  <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Rankings <b class="caret"></b></a>
  <ul class="dropdown-menu">
  <li><a href="rankings.php?mode=production">Production</a></li>
  <li><a href="alliancerankings.php?mode=production">Alliance Production</a></li>
  </ul>
  </li>
  <li><a href="news.php">News</a></li>
  <li><a href="rules.php">Rules</a></li>
  <li><a href="guide.php"><font color="orange">Guide</font></a></li>
</ul>
<div class="navbar-header"></div>
</nav>
  <div id="content">  
    {$errormessages}
    {$infomessages}
EOFORM;
} else {
  echo <<<EOFORM
  <nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header"></div>
    <ul class="nav navbar-nav navbar-right pull-right">
      <li><a>Server time: {$currenttime}</a></li>
      <li><a>Next tick: <span class="text-danger" id="countdown">{$countdowntimer}</span></a></li>
      <li><a href="search.php">Search</a></li>
      <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Rankings <b class="caret"></b></a>
      <ul class="dropdown-menu">
      <li><a href="rankings.php?mode=production">Production</a></li>
      <li><a href="alliancerankings.php?mode=production">Alliance Production</a></li>
      </ul>
      </li>
      <li><a href="news.php">News</a></li>
      <li><a href="rules.php">Rules</a></li>
      <li><a href="guide.php"><font color="orange">Guide</font></a></li>
    </ul>
  </nav>
  <div id="content"> 
    <p>
      <form name="login_top" method="post" action="login.php" role="form">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" id="username" placeholder="Username" name="username" maxlength="25">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" placeholder="Password" name="password" maxlength="25">
        </div>
        <span><input type="submit" name="login" value="Log in" class="btn btn-success"/>
        <input type="submit" name="cloplogin" value="Log in (&gt;CLOP account)" class="btn btn-success"/>
        <a href="newuser.php" class="btn btn-info">All-New User</a></span>
      </form>
    </p>
    {$errormessages}
    {$infomessages}
EOFORM;
}