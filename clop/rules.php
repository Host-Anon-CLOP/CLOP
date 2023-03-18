<?php
include_once("backend/allfunctions.php");
$extratitle = "Rules - ";
include("header.php");
echo <<<EOFORM
<b>1. </b>The admin is always right.<br/>
<b>2. </b>If he's not, consult  §1</br>
<b>3. </b>Read the footer.</br>
<b>4. No, seriously, read the footer</b></br>
<b>5. </b>You can put everything in your nation/user/alliance description except:</br>
<ul>
<li>The sites you are linking to may not return HTTP "Header Access-Control-Allow-Origin" with a value of "*" or "reclop.tk"</li>
<li>No remote code execution. Other than JS, obviously. If you're going to embed flash, that fine as well, provided you warn the user appropriately</li>
<li>Your script may not modify DOM outside of &lt;div id="content"&gt;</li>
<li>In case point above is not obvious, that prohibits scripts that rewrite links to escape the nation/user/alliance description page</li>
</ul>
<b>6. </b>One IP <-> One Account</br>
<b>7. </b>Leave real life out of it</br>
<b>8. </b>This rules may change at any time without prior notice</br>
</br><b>9. </b>Nationbuilding <b>is</b> allowed <abbr title="vox populi, vox dei">(decided by a public poll)</abbr>, freestyle mode. No regulations apply.</br>
</br>
<b>Other than these, there are no rules.</b> Take over the economy, manipulate the market, lie extravagantly, gang up on whoever you want.
EOFORM;
include("footer.php");
?>