<?php
include_once("backend/allfunctions.php");
include("header.php");
if ($_POST['purchase']) {
    echo <<<EOFORM
<center><h2>April Fool's!</h2></center>
<center>Hahaha, holy shit, do you pay to win in EVERY game you play?</center>
EOFORM;
} else {
    echo <<<EOFORM
<table class="table table-striped table-bordered">
<tr><td>Basic starter kit with 1 million bits and 1,000 oil, energy, copper, and sugar.</td><td><span class="text-success">$5.00</span></td><td><input type="radio" name="lol"/></td></tr>
<tr><td>For players who want to get ahead quickly. 5 million bits, 5,000 copper, 5,000 energy, and 5 factories.</td><td><span class="text-success">$10.00</span></td><td><input type="radio" name="lol"/></td></tr>
<tr><td>This kit will get you to the top of the rankings in no time at all. 50 million bits, 10,000 of every resource (except DNA) in the game, and 10 malls.<br/>
Includes composites and precision parts- this package has it all.</td><td><span class="text-success">$20.00</span></td><td><input type="radio" name="lol"/></td></tr>
<tr><td>The ultimate. For a mere $50 USD, I will clone the nation (including forces) of any player you choose, bit for bit and resource for resource.<br/>
Make sure you can handle it before you buy!</td><td><span class="text-success">$50.00</span></td><td><input type="radio" name="lol"/></td></tr>
</table>
<center><form action="purchase.php" method="post"><input type="submit" name="purchase" value="Continue to Checkout" class="btn btn-success"/></form>
EOFORM;
}
include("footer.php");
?>