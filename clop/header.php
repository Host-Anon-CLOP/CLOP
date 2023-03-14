<?php
include("backend/backend_header.php");
$headernumber = rand(1, 16);
if ($headernumber == 4 || $headernumber == 2) {
    $headerextension = "gif";
} else {
    $headerextension = "png";
}

if ((date("H") % 2 == 0) && (date("i") == 0) && (date("s") == 0)) {
    $countdowntimer = "NOW!";
} else if ((date("i") == 0) && (date ("s") == 0)) {
    $countdowntimer = "1:00:00";
} else {
    if (date("H") % 2 == 0) {
        $countdowntimer = "1:";
    } else {
        $countdowntimer = "0:";
    }
    if (date("s") == 0) {
        $minutes = 60 - date("i");
        $format = "%1$02d:00";
        $countdowntimer .= sprintf($format, $minutes);
    } else {
        $seconds = 60 - date("s");
        $minutes = 59 - date("i");
        $format = "%1$02d:%2$02d";
        $countdowntimer .= sprintf($format, $minutes, $seconds);
    }
}

if (!$_SESSION['css']) {
    # original default css - white
    #$stylesheets =<<<EOFORM
    #<link href="css/bootstrap.min.css" rel="stylesheet" media="Screen">
    #<link rel="stylesheet" href="css/bootstrap-theme.min.css">
    $stylesheets =<<<EOFORM
    <link href="css/slate.min.css" rel="stylesheet" media="Screen">
EOFORM;
    $internal = "";
} else if ($_SESSION['css'] == 1) {
    # black
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
    # grey
    $stylesheets =<<<EOFORM
    <link href="css/bootstrap.min.css" rel="stylesheet" media="Screen">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
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
$timer = 7200 - (date("U") - 3600) % 7200; # tick timer in seconds
} else {
$timer = 7200 - date("U") % 7200; # tick timer in seconds
}
$currenttime = date("Y-m-d H:i:s");
header("Content-Type: text/html; charset=utf-8");
echo <<<EOFORM
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <link rel="icon" type="image/png" href="images/favicon.png" />
    {$stylesheets}
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/masonry.pkgd.min.js"></script>
    <style type="text/css">
        
ul.nav li.dropdown:hover > ul.dropdown-menu {
display: block;
}
span.spoiler {
color: #000000;
background: #000000;
}

span.spoiler:hover {
color: #FFFFFF;
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
    </style>

    <script type="text/javascript">
      function doCountdownTick(countdown_seconds){
        if(countdown_seconds == 0)
          countdown_seconds = 60 * 60 * 2;
       
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
    
    <title>{$extratitle}&gt;CLOP, the game of geoponitics</title>
</head>
<body alink="#00ae0e" link="#00ae0e" vlink="#1f8001">
<meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
<div id="content-outer">
EOFORM;
if (!$_SESSION['hidebanners']) {
echo <<<EOFORM
<center><img src="images/{$headernumber}.{$headerextension}" height="250"/></center>
EOFORM;
}
echo <<<EOFORM
<center><span id="topmessage">{$topmessage}</span></center>
<center>&gt;4CLOP Community: <a href="https://boards.4channel.org/mlp/thread/39723279" target="_new_win">/MLP/ Thread</a> | <a href="https://discord.gg/TbmQ5R4zJn" target="_new_win">Discord</a> | <a href="https://irc.4clop.com" target="_new_win">IRC</a> | <a href="https://github.com/ergochat/ergo/blob/stable/docs/USERGUIDE.md#account-registration" target="_new_win">IRC Guide</a> &nbsp;&nbsp;&nbsp;&nbsp; Announcements: Ticks are now 2 hours | War Ticks are now 12 & 24 server time</center>
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
if ($headernationlist) {
    if (count($headernationlist) == 1) {
        $nationline = "<li><a>{$nationname}</a></li>";
    } else {
        $nationline = <<<EOFORM
<li><a><form action="" method="post"><select name="switchnation_id" onchange='this.form.submit()'>
EOFORM;
        foreach ($headernationlist as $headernation_id => $headernationname) {
			$nationline .= <<<EOFORM
<option value="{$headernation_id}"
EOFORM;
			if ($headernation_id == $_SESSION['nation_id']) {
				$nationline .= " selected ";
			}
        $nationline .= <<<EOFORM
>{$headernationname}</option>
EOFORM;
    }
    $nationline .= <<<EOFORM
</select></form></a></li>
EOFORM;
}
}
if ($_SESSION['user_id']) {
echo <<<EOFORM
<nav class="navbar navbar-default" role="navigation">
<div class="navbar-header">
<a class="navbar-brand" href="index.php">&gt;CLOP</a>
</div>
<ul class="nav navbar-nav">
  <li><a class="dropdown-toggle" href="messages.php">Messages <span class="badge">{$messagenumber}</span></a></li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">User <b class="caret"></b></a>
    <ul class="dropdown-menu">
      <li><a href="userinfo.php">User Info</a></li>
      <li><a href="transfer.php">Empire Transfers</a></li>
      <li><a href="blocklist.php">Blocklist</a></li>
      <li><a href="friends.php">Friends/Enemies</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </li>
  <li class="dropdown">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Alliance <span class="badge">{$alliancemessagenumber}</span><b class="caret"></b></a>
  <ul class="dropdown-menu">
    <li><a href="myalliance.php">My Alliance<span class="badge">{$alliancemessagenumber}</span></a></li>
    <li><a href="alliances.php">Alliances</a></li>
    <li><a href="allianceattacks.php">Alliance Attacks</a></li>
  </ul>
</li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Nation <b class="caret"></b></a>
    <ul class="dropdown-menu">
      <li><a href="overview.php">Overview</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li><a href="actions.php">Actions</a></li>
      <li><a href="favoriteactions.php">Favorite Actions</a></li>
      <li><a href="majoractions.php">Major Actions</a></li>
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Capitalism <span class="badge">{$dealnumber}</span><b class="caret"></b></a>
    <ul class="dropdown-menu">
      <li><a href="marketplace.php">Resources Marketplace</a></li>
      <li><a href="buyermarketplace.php">Buyer's Resources Marketplace</a></li>
      <li><a href="deals.php">Deals <span class="badge">{$dealnumber}</span></a></li>
      <li><a href="marketplace.php?mode=weapons">Weapons Marketplace</a></li>
      <li><a href="buyermarketplace.php?mode=weapons">Buyer's Weapons Marketplace</a></li>
      <li><a href="marketplace.php?mode=armor">Armor Marketplace</a></li>
      <li><a href="buyermarketplace.php?mode=armor">Buyer's Armor Marketplace</a></li>
      <li><a href="myoffers.php">My Offers</a></li>
      <li><a href="embargoes.php">Embargoes</a></li>
    </ul>
  </li>
  
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">War <span class="badge">{$incomingnumber}</span><b class="caret"></b></a>
    <ul class="dropdown-menu">
      <li><a href="forcesyourway.php">Incoming <span class="badge">{$incomingnumber}</span></a></li>
      <li><a href="makeequipment.php?mode=weapons">Make Weapons</a></li>
      <li><a href="makeequipment.php?mode=armor">Make Armor</a></li>
      <li><a href="createforces.php">Create Forces</a></li>
      <li><a href="equipforces.php">Equip Forces</a></li>
      <li><a href="groupforces.php">Group Forces</a></li>
      <li><a href="sendforces.php">Send Forces</a></li>
    </ul>
  </li>
  
</ul>
<ul class="nav navbar-nav navbar-right pull-right">
  {$nationline}
  <li><a>Server time: {$currenttime}</a></li>
  <li><a>Next tick: <span class="text-danger" id="countdown">{$countdowntimer}</span></a></li>
  <li><a href="search.php">Search</a></li>
  <li><a href="statistics.php">Stats</a></li>
  <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Rankings <b class="caret"></b></a>
  <ul class="dropdown-menu">
      <li><a href="rankings.php?mode=gdp">GDP Per Turn</a></li>
      <li><a href="rankings.php?mode=statues">Statues</a></li>
      <li><a href="rankings.php?mode=longevity">Longevity</a></li>
      <li><a href="empirerankings.php">Empire Size</a></li>
      <li><a href="ascensions.php">Ascensions</a></li>
      <li><a href="graveyard.php">Graveyard</a></li>
      </ul>
      </li>
      <li><a href="news.php">News</a></li>
      <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><font color="orange">Guides </font><b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="rules.php">Rules</a></li>
        <li><a href="https://docs.google.com/document/u/0/d/1jSinNyYJCHkoDvQgaJkD_z2g9SV5G6v9gsKI-nwmrYM/mobilebasic" target="_new_win">Recommended Guide</a></li>
        <li><a href="https://4clop.com/images/the_chart.png" target="_new_win">Enviro Loss Chart</a></li>
        <li><a href="guide.php">Another Guide</a></li>
        <li><a href="warguide.php">War Guide</a></li>
      </ul>
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
    <div class="navbar-header">
    <a class="navbar-brand" href="index.php">&gt;CLOP</a>
    </div>
    <ul class="nav navbar-nav navbar-right pull-right">
      <li><a>Server time: {$currenttime}</a></li>
      <li><a>Next turn: <span class="text-danger" id="countdown"></span></a></li>
      <li><a href="search.php">Search</a></li>
      <li><a href="statistics.php">Stats</a></li>
      <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Rankings <b class="caret"></b></a>
      <ul class="dropdown-menu">
          <li><a href="rankings.php?mode=gdp">GDP Per Turn</a></li>
          <li><a href="rankings.php?mode=statues">Statues</a></li>
          <li><a href="rankings.php?mode=longevity">Longevity</a></li>
          <li><a href="empirerankings.php">Empire Size</a></li>
          <li><a href="ascensions.php">Ascensions</a></li>
          <li><a href="graveyard.php">Graveyard</a></li>
      </ul>
      </li>
      <li><a href="news.php">News</a></li>
      <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><font color="orange">Guides </font><b class="caret"></b></a>
      <ul class="dropdown-menu">
        <li><a href="rules.php">Rules</a></li>
        <li><a href="https://docs.google.com/document/u/0/d/1jSinNyYJCHkoDvQgaJkD_z2g9SV5G6v9gsKI-nwmrYM/mobilebasic" target="_new_win">Recommended Guide</a></li>
        <li><a href="https://4clop.com/images/the_chart.png" target="_new_win">Enviro Loss Chart</a></li>
        <li><a href="guide.php">Another Guide</a></li>
        <li><a href="warguide.php">War Guide</a></li>
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
          <input type="password" class="form-control" id="password" placeholder="Password" name="password" maxlength="250">
        </div>
        <span><input type="submit" value="Log in" class="btn btn-success"/> <a href="newuser.php" class="btn btn-info">New User</a></span>
      </form>
    </p>
    {$errormessages}
    {$infomessages}
EOFORM;
}
?>