<?php
include_once("backend/allfunctions.php");
$extratitle = "Guide - ";
include("header.php");
echo <<<EOFORM
<center><a href="https://docs.google.com/document/d/1mrJnz1faZSvpOq-fkogEodSo6UXF0e4iUTAn957Pw-c/edit?usp=sharing">Click here for an in-depth, player-created guide.</a></center>
You'll get most of your advice from other players, so just a few tips:<br/>
<b>You need an alliance to actually play the game.</b> Don't have one? Ask on the board or start messaging alliance leaders.<br/>
Make sure you have enough elements to pay your complements. If you don't, you start losing satisfaction and, ultimately, production.<br/>
Elements are generated, autocompounding is done, and then complements are assessed. In that order.<br/>
Be careful if you're focusing your production, as, while it ultimately generates more elements, it also comes with a high risk of imbalance.<br/>
At the individual level, complements are required when you have a stockpile greater than (your production * 6) + 50.<br/>
At the alliance level, complements are required if there's more than 100 times the number of people in the alliance.<br/>
<b>If your alliance can't pay its complements, the whole alliance suffers.</b><br/>
The current cost of complements for individuals is 40%, which can be lessened to 2.5% through satisfaction and alliance satisfaction.<br/>
Alliance complement requirements start at 10% and can go down to 5%.
A satisfaction above 1000 does not do anything extra but serves as a buffer. In general, it's good to have a high satisfaction to protect yourself.
EOFORM;
include("footer.php");
?>