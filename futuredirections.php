<?php
require_once("backend/allfunctions.php");
needsuser();
include("header.php");
echo <<<EOTXT
I don't have all that much ideas so far
<ul>
<li>A proper password hashing mechanism</li>
<li><s>Shorten newbie peroid to 7-14 days</s> Done ✓</li>
<li>Maybe do some tweaks to war system, i dunno</li>
<li><s>Finish request feature form and provide voting system :)</s> Done ✓</li>
<li><s>Notification for alliance messages, much like with user messages</s> Done ✓</li>
<li>Three alliance types: closed, semiclosed and open. They will differ only by a recrutation method</li>
<li>More async UI, maybe</li>
<li>Extend troop names charset to ASCII 32-126</li>
<li>Bring back nation transfers (but not nation burning)</li>
<li>Improve site caching, if host allows it</li>
<li>Global site stats</li>
<li><span class="spoiler">Super sikret idea donut steel™: Introduce truly fiat currency</span></li>
<li><span class="spoiler">[DONUT STEEL™ INTENSIFIES]</span></li>
<li><span class="spoiler">Decentralise server and DB, if possible</span></li>
<li><span class="spoiler">Introduce decentralised trust schema, ideally</span></li>
<li>Actually start working on some things on this list</li>
</ul></br></br>
EOTXT;
include('footer.php');
