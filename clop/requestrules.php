<?php
require_once("backend/allfunctions.php");
needsuser();
include("header.php");
echo <<<EOTXT
<style>
.subtxt {
padding-left: 2em;
}
</style>
<b>1. </b>Don't overdo it</br>
<p class="subtxt">Don't submit too many requests at once. The hardcoded cap is at 5 requests per week, but you should limit yourself to 3 requests per week.</br>
Submitting bugs is extempt from that, but don't go about submitting one-time bugs like "Can't connect to MySQL server" or don't mark features as "bug" because you want them this hard.</br>
Also don't submit request about functionality that already exists (eg. rename my nation when you can do that from major actions).</br>
Resubmitting rejected feature is OK, provided that it's done within reasonable time-frame (that is, wait some time until you resubmit).
</p>
</br>
<b>2. </b>Don't be the "idea guy"</br>
<p class="subtxt">Make >ReClop not suck. Make >ReClop great again. Make >ReClop just like the last epic game I've played.</br>
<b>No.</b></br>
In the spirit of the rule #1, "don't overdo it". Your ideas should be concise, clear and easily understandable.</br>
The idea should be narrow in scope and focus on only one aspect of game at a time.</br>
Also note that this is an <a href="https://gitlab.com/Toldierone/ReClop" target="_blank">open source</a> project. Your request has greater chances of being accepted if you have some sort of basis or ground for your ideal feature. Code is highly appreciated.</br>
</p>
</br>
<a href="requestfeature.php" target="_self">Back</a>
EOTXT;
