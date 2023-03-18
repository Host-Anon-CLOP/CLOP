<?php
include("backend/backend_makedeal.php");
$extratitle = "Make Deal - ";
include("header.php");
$paidamount = commas($dealinfo['paid']);
echo <<<EOFORM
  <center>Making a deal with</center>
  <center><h4>{$dealinfo['name']}</h4></center>
  <center><h5>Deal Cost: <span class="text-danger">{$paidamount}</span></h5></center>
EOFORM;

echo <<<EOFORM
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-warning">
        <div class="panel-heading">Offer</div>
EOFORM;

  if (!empty($offeritems) || !empty($offerweapons) || !empty($offerarmor) || ($dealinfo['amount'] && !$dealinfo['askingformoney'])) {
    echo <<<EOFORM
          <table class="table">
            <tbody>

EOFORM;

    if ($dealinfo['amount'] && !$dealinfo['askingformoney']) {
      $money = commas($dealinfo['amount']);
      echo <<<EOFORM
              <tr>
                <td style="text-align: right;">Bits</td>
                <td>{$money}</td>
                <td>
              <form action="makedeal.php" method="post">
                <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                
                <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                <input type="submit" name="removemoney" value="Remove Money" class="btn btn-warning btn-block"/>
              </form>
              </td></tr>
EOFORM;
    } 
    if ($offeritems) {
    foreach ($offeritems as $item) {
    $itemamount = commas($item['amount']);
    echo <<<EOFORM
              <tr>
                <td style="text-align: right;">{$item['name']}</td>
                <td>{$itemamount}</td>
                <td>
                  <form action="makedeal.php" method="post">
                 <input type="hidden" name="type" value="item"/>
                    <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                    <input type="hidden" name="type" value="item"/>
                    <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                    <input type="hidden" name="resource_id" value="{$item['resource_id']}"/>
                    <input type="submit" name="removeoffer" value="Remove Item" class="btn btn-warning btn-block"/>
                  </form>
                </td>
              </tr>

EOFORM;
    }
    }
    if ($offerweapons) {
    foreach ($offerweapons as $item) {
    $weaponamount = commas($item['amount']);
    echo <<<EOFORM
              <tr>
                <td style="text-align: right;">{$item['name']}</td>
                <td>{$weaponamount}</td>
                <td>
                  <form action="makedeal.php" method="post">
                    <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                    <input type="hidden" name="type" value="weapon"/>
                    <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                    <input type="hidden" name="resource_id" value="{$item['weapon_id']}"/>
                    <input type="submit" name="removeoffer" value="Remove Weapon" class="btn btn-warning btn-block"/>
                  </form>
                </td>
              </tr>

EOFORM;
    }
    }
    if ($offerarmor) {
    foreach ($offerarmor as $item) {
    $armoramount = commas($item['amount']);
    echo <<<EOFORM
              <tr>
                <td style="text-align: right;">{$item['name']}</td>
                <td>{$armoramount}</td>
                <td>
                  <form action="makedeal.php" method="post">
                    <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                    <input type="hidden" name="type" value="armor"/>
                    <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                    <input type="hidden" name="resource_id" value="{$item['armor_id']}"/>
                    <input type="submit" name="removeoffer" value="Remove Armor" class="btn btn-warning btn-block"/>
                  </form>
                </td>
              </tr>

EOFORM;
    }
    }
    echo "</tbody></table>";
  }
  echo <<<EOFORM
        <div class="panel-footer">
          <form action="makedeal.php" method="post" class="form-inline" role="form">
            <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
            <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
            <input type="hidden" name="type" value="item"/>
            Offer this item: 
            <select name="resource_id" class="form-control" style="width:210px;"/>
      
EOFORM;
foreach($resourceoptions as $option) {
  echo <<<EOFORM
              <option value="{$option['resource_id']}">
                {$option['optionslistname']}
              </option>

EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="amount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="offeritem" value="Offer Item" class="btn btn-primary"/>
               </span>
            </div>
          </form>
        </div>
        <div class="panel-footer">
          <form action="makedeal.php" method="post" class="form-inline" role="form">
            <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
            <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
            <input type="hidden" name="type" value="weapon"/>
            Offer this weapon: 
            <select name="resource_id" class="form-control" style="width:210px;"/>
      
EOFORM;
foreach($weaponoptions as $option) {
  echo <<<EOFORM
              <option value="{$option['resource_id']}">
                {$option['optionslistname']}
              </option>

EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="amount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="offeritem" value="Offer Weapon" class="btn btn-primary"/>
               </span>
            </div>
          </form>
        </div>
        <div class="panel-footer">
          <form action="makedeal.php" method="post" class="form-inline" role="form">
            <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
            <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
            <input type="hidden" name="type" value="armor"/>
            Offer this armor: 
            <select name="resource_id" class="form-control" style="width:210px;"/>
      
EOFORM;
foreach($armoroptions as $option) {
  echo <<<EOFORM
              <option value="{$option['resource_id']}">
                {$option['optionslistname']}
              </option>

EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="amount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="offeritem" value="Offer Armor" class="btn btn-primary"/>
               </span>
            </div>
          </form>
          </div>
EOFORM;
if(!$dealinfo['amount']) {
  echo <<<EOFORM
          <form action="makedeal.php" method="post">
            <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
            <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
            <div class="input-group">
              <input type="text" class="form-control" name="amount"/>
              <span class="input-group-btn">
                  <input type="submit" name="offermoney" value="Offer This Much Money" class="btn btn-primary"/>
               </span>
            </div>
          </form>
EOFORM;
}

echo <<<EOFORM
        </div>
      </div>
    <div class="col-md-6">
        <div class="panel panel-success">
          <div class="panel-heading">Request</div>

EOFORM;
  if (!empty($askitems) || !empty($askweapons) || !empty($askarmor) || ($dealinfo['amount'] && $dealinfo['askingformoney'])) {
  echo <<<EOFORM
          <table class="table">
            <tbody>

EOFORM;

    if ($dealinfo['amount'] && $dealinfo['askingformoney']) {
      $money = commas($dealinfo['amount']);
      echo <<<EOFORM
              <tr>
                <td style="text-align: right;">Bits</td>
                <td>{$money}</td>
                <td>
              <form action="makedeal.php" method="post">
                <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                <input type="submit" name="removemoney" value="Remove Money" class="btn btn-warning btn-block"/>
              </form></td></tr>

EOFORM;
    }
    if ($askitems) {
    foreach ($askitems as $item) {
    $itemamount = commas($item['amount']);
       echo <<<EOFORM
            <tr>
              <td style="text-align: right;">{$item['name']}</td>
              <td>{$itemamount}</td>
              <td>
                <form action="makedeal.php" method="post">
                  <input type="hidden" name="type" value="item"/>
                  <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                  <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                  <input type="hidden" name="resource_id" value="{$item['resource_id']}"/>
                  <input type="submit" name="removeask" value="Remove Item" class="btn btn-warning btn-block"/>
                </form>
              </td>
            </tr>

EOFORM;
    }
    }
    if ($askweapons) {
    foreach ($askweapons as $item) {
    $weaponamount = commas($item['amount']);
       echo <<<EOFORM
            <tr>
              <td style="text-align: right;">{$item['name']}</td>
              <td>{$weaponamount}</td>
              <td>
                <form action="makedeal.php" method="post">
                  <input type="hidden" name="type" value="weapon"/>
                  <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                  <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                  <input type="hidden" name="resource_id" value="{$item['weapon_id']}"/>
                  <input type="submit" name="removeask" value="Remove Weapon" class="btn btn-warning btn-block"/>
                </form>
              </td>
            </tr>

EOFORM;
    }
    }
    if ($askarmor) {
    foreach ($askarmor as $item) {
    $armoramount = commas($item['amount']);
       echo <<<EOFORM
            <tr>
              <td style="text-align: right;">{$item['name']}</td>
              <td>{$armoramount}</td>
              <td>
                <form action="makedeal.php" method="post">
                  <input type="hidden" name="type" value="armor"/>
                  <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                  <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                  <input type="hidden" name="resource_id" value="{$item['armor_id']}"/>
                  <input type="submit" name="removeask" value="Remove Armor" class="btn btn-warning btn-block"/>
                </form>
              </td>
            </tr>

EOFORM;
    }
    }
  echo "</tbody></table>";
  }
  echo <<<EOFORM
        <div class="panel-footer">
          <form action="makedeal.php" method="post" class="form-inline" role="form">
            <input type="hidden" name="type" value="item"/>
            <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
            <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
            Request this item: 
            <select name="resource_id" class="form-control" style="width:210px;">
EOFORM;
foreach($resourceoptions as $option) {
  echo <<<EOFORM
              <option value="{$option['resource_id']}">
                {$option['optionslistname']}
              </option>
EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="amount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="askitem" value="Request Item" class="btn btn-primary"/>
               </span>
            </div>
          </form>
          </div>
        <div class="panel-footer">
          <form action="makedeal.php" method="post" class="form-inline" role="form">
            <input type="hidden" name="type" value="weapon"/>
            <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
            <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
            Request this item: 
            <select name="resource_id" class="form-control" style="width:210px;">
EOFORM;
foreach($weaponoptions as $option) {
  echo <<<EOFORM
              <option value="{$option['resource_id']}">
                {$option['optionslistname']}
              </option>
EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="amount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="askitem" value="Request Weapon" class="btn btn-primary"/>
               </span>
            </div>
          </form>
          </div>
        <div class="panel-footer">
          <form action="makedeal.php" method="post" class="form-inline" role="form">
            <input type="hidden" name="type" value="armor"/>
            <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
            <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
            Request this item: 
            <select name="resource_id" class="form-control" style="width:210px;">
EOFORM;
foreach($armoroptions as $option) {
  echo <<<EOFORM
              <option value="{$option['resource_id']}">
                {$option['optionslistname']}
              </option>
EOFORM;
}
echo <<<EOFORM
            </select>
            in this quantity: 
            <div class="input-group">
              <input type="text" class="form-control" name="amount" placeholder="Qty"/>
              <span class="input-group-btn">
                  <input type="submit" name="askitem" value="Request Armor" class="btn btn-primary"/>
               </span>
            </div>
          </form>
          </div>
EOFORM;
if(!$dealinfo['amount']) {
  echo <<<EOFORM
              <form action="makedeal.php" method="post">
                <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
                <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
                <div class="input-group">
                  <input type="text" class="form-control" name="amount"/>
                  <span class="input-group-btn">
                      <input type="submit" name="requestmoney" value="Request This Much Money" class="btn btn-primary"/>
                   </span>
                </div>
              </form>

EOFORM;
  }
echo <<<EOFORM
        </div>
      </div>
      </div>
EOFORM;
  echo <<<EOFORM
  <div class="row">
    <form action="makedeal.php" method="post">
      <input type="hidden" name="token_makedeal" value="{$_SESSION['token_makedeal']}"/>
      <input type="hidden" name="deal_id" value="{$dealinfo['deal_id']}"/>
      <div class="col-md-6">
        <input type="submit" name="canceldeal" value="Cancel Deal" class="btn btn-danger btn-block"/>
      </div>
      <div class="col-md-6">
        <input type="submit" name="finalizedeal" value="Finalize Deal" class="btn btn-success btn-block"/>
      </div>
    </form>
  </div>
EOFORM;
include("footer.php");
?>