<?php
include("backend/backend_makedeal.php");
$extratitle = "Make Deal - ";
include("header.php");
echo <<<EOFORM
  <center>Making a deal with</center>
  <center><h4>{$dealinfo['username']}</h4></center>
  <center>Cost to Give: {$charitycost} Charity&nbsp;&nbsp;Cost to Trade: {$dealcost} Fairness
  <div class="row">
    <div class="col-md-6">
      <div class="panel panel-warning">
        <div class="panel-heading">Offer</div>
EOFORM;
  if (!empty($offeritems)) {
    echo <<<EOFORM
          <table class="table">
            <tbody>
EOFORM;
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
echo elementsdropdown(true, true);
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
        </div>
      </div>
    <div class="col-md-6">
        <div class="panel panel-success">
          <div class="panel-heading">Request</div>
EOFORM;
  if (!empty($askitems)) {
  echo <<<EOFORM
          <table class="table">
            <tbody>
EOFORM;
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
echo elementsdropdown(true, true);
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
      </div>
      </div>
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