<?php
include("backend/backend_allianceinvites.php");
$extratitle = "Alliance Invitations - ";
include("header.php");
$token = $_SESSION["token_allianceinvites"];
if ($invitations) {
echo <<<EOFORM
<table>
EOFORM;
foreach ($invitations as $alliance_id => $alliancename) {
echo <<<EOFORM
<tr><td><a href="viewalliance.php?alliance_id={$alliance_id}">{$alliancename}</a></td><td>
<form name="acceptreject{$alliance_id}" action="allianceinvites.php" method="post">
<input type="hidden" name="token_allianceinvites" value="{$token}"/>
<input type="hidden" name="alliance_id" value="{$alliance_id}"/>
<input type="submit" onclick="return confirm('Really accept an invitation to {$alliancename}?')" name="acceptinvitation" value="Accept Invitation" class="btn btn-success"/>
<input type="submit" onclick="return confirm('Really refuse an invitation to {$alliancename}?')" name="refuseinvitation" value="Refuse Invitation" class="btn btn-danger"/>
</form>
</td></tr>
EOFORM;
}
echo <<<EOFORM
</table>
EOFORM;
} else {
echo <<<EOFORM
<center>No one has sent you an invitation to join an alliance.</center>
EOFORM;
}
include("footer.php");
?>