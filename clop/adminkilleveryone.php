<?php
include("backend/allfunctions.php");
if ($_SESSION['user_id'] == 1) {
echo <<<EOFORM
<form action="adminkilleveryone.php" method="post">
<input name="user_id"/>
<input type="submit" value="Kill"/>
</form>
EOFORM;
if ($_POST['user_id']) {
    $sql = "SELECT username from users WHERE user_id = '{$_POST['user_id']}'";
    $rs = onelinequery($sql);
    $sql=<<<EOSQL
    INSERT INTO news (message, posted)
    VALUES ('The admin has disposed of {$rs['username']}!', NOW())
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = "SELECT nation_id from nations WHERE user_id = '{$_POST['user_id']}'";
    $sth = $GLOBALS['mysqli']->query($sql);
    while ($rs = mysqli_fetch_array($sth)) {
    $sql = "DELETE FROM resources WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM marketplace WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM nations WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM weapons WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM armor WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM forcegroups WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM forces WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM recipefavorites WHERE nation_id = '{$rs['nation_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = <<<EOSQL
    UPDATE forcegroups SET location_id = nation_id, departuredate = NULL, attack_mission = 0 WHERE destination_id = {$rs['nation_id']} OR location_id = {$rs['nation_id']}
EOSQL;
    $GLOBALS['mysqli']->query($sql);
    $sql = "SELECT deal_id FROM deals WHERE fromnation = '{$rs['nation_id']}' OR tonation = '{$rs['nation_id']}'";
    $sth2 = $GLOBALS['mysqli']->query($sql);
    while ($rs2 = mysqli_fetch_array($sth2)) {
        $sql = "DELETE FROM dealitems_offered WHERE deal_id = '{$rs2['deal_id']}'";
        $GLOBALS['mysqli']->query($sql);
        $sql = "DELETE FROM dealitems_requested WHERE deal_id = '{$rs2['deal_id']}'";
        $GLOBALS['mysqli']->query($sql);
        $sql = "DELETE FROM dealarmor_offered WHERE deal_id = '{$rs2['deal_id']}'";
        $GLOBALS['mysqli']->query($sql);
        $sql = "DELETE FROM dealarmor_requested WHERE deal_id = '{$rs2['deal_id']}'";
        $GLOBALS['mysqli']->query($sql);
        $sql = "DELETE FROM dealweapons_offered WHERE deal_id = '{$rs2['deal_id']}'";
        $GLOBALS['mysqli']->query($sql);
        $sql = "DELETE FROM dealweapons_requested WHERE deal_id = '{$rs2['deal_id']}'";
        $GLOBALS['mysqli']->query($sql);
    }
    $sql = "DELETE FROM deals WHERE fromnation = '{$rs['nation_id']}'";
    }
    $sql = "DELETE FROM embargoes WHERE embargoer = '{$_POST['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM embargoes WHERE embargoee = '{$_POST['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM nations WHERE user_id = '{$_POST['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM messages WHERE fromuser = '{$_POST['user_id']}' OR touser = '{$_POST['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
    $sql = "DELETE FROM users WHERE user_id = '{$_POST['user_id']}'";
    $GLOBALS['mysqli']->query($sql);
    echo "User {$_POST['user_id']} has been killed.";
}
}

?>