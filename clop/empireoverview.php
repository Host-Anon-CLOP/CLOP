<?php
include("backend/backend_empireoverview.php");
$extratitle = "Empire Overview - ";
include("header.php");

#$displaytype = "stockpiles"
#$displaytype = "ticksworth"
$displaytype = "net";

if ($displaytype == "net") {
    $display_resource_type = "Resources - Net";
} else if ($displaytype == "ticksworth") {
    $display_resource_type = "Resources - Ticks Worth";
} else {
    $display_resource_type = "Resources - Stockpiles";
}

echo <<<EOFORM
<style>
button[name="switchnation_id"] {
  white-space: nowrap; /* Prevent wrapping i.e. force to a single line */
  text-overflow: ellipsis; /* ellipsis where text gets cut off */
  overflow-x: hidden; /* Required for prior two to work */
  width: 80px; /* Or however wide you want the columns to be */
}
</style>

<div class="row">
  <div class="col-md-6">
   <div class="panel panel-default">
     <div class="panel-heading">$display_resource_type</div>
     <table class="table">
      <thead>
        <tr>
        
EOFORM;
        if (!$nationinfo['hideicons']) {
            echo <<<EOFORM
            <td style="width: 16px;"></td>
EOFORM;
        }
        echo <<<EOFORM
        <td style="text-align: left;">Resource</td>
EOFORM;

foreach ($empirenations as $nation_id => $nation_name) {
    echo <<<EOFORM
    <td style="text-align: left;"><div title="{$nation_name}"><form action="overview.php" method="post"><button name="switchnation_id" type="submit" value="{$nation_id}">{$nation_name}</button></form></div></td>
EOFORM;
}

echo <<<EOFORM
<td>TOTAL</td></tr></thead><tbody>
EOFORM;

# get satisfaction per nation
echo <<<EOFORM
<tr><td></td><td style="text-align: left;">Satisfaction</td>
EOFORM;
foreach ($empirenations as $nation_id => $nation_name) {
    if ($resources[$nation_id]['satisfaction'] > 0) {
        $displaycolor = "text-success";
    } else if ($resources[$nation_id]['satisfaction'] == 0) {
        $displaycolor = "text-warning";
    } else {
        $displaycolor = "text-danger";
    }
    $displayamount = commas($resources[$nation_id]['satisfaction']);
    echo <<<EOFORM
    <td style="text-align: left;"><span class="{$displaycolor}">{$displayamount}</span></td>
EOFORM;
}
echo <<<EOFORM
<td></td></tr>
EOFORM;

# get nlr rep per nation
echo <<<EOFORM
    <tr><td></td><td style="text-align: left;">NLR Rep</td>
EOFORM;
foreach ($empirenations as $nation_id => $nation_name) {
    if ($resources[$nation_id]['nlr'] > 0) {
        $displaycolor = "text-success";
    } else if ($resources[$nation_id]['nlr'] == 0) {
        $displaycolor = "text-warning";
    } else {
        $displaycolor = "text-danger";
    }
    $displayamount = commas($resources[$nation_id]['nlr']);
    echo <<<EOFORM
    <td style="text-align: left;"><span class="{$displaycolor}">{$displayamount}</span></td>
EOFORM;
}
echo <<<EOFORM
    <td></td></tr>
EOFORM;

# get se rep per nation
echo <<<EOFORM
    <tr><td></td><td style="text-align: left;">SE Rep</td>
EOFORM;
foreach ($empirenations as $nation_id => $nation_name) {
    if ($resources[$nation_id]['se'] > 0) {
        $displaycolor = "text-success";
    } else if ($resources[$nation_id]['se'] == 0) {
        $displaycolor = "text-warning";
    } else {
        $displaycolor = "text-danger";
    }
    $displayamount = commas($resources[$nation_id]['se']);
    echo <<<EOFORM
    <td style="text-align: left;"><span class="{$displaycolor}">{$displayamount}</span></td>
EOFORM;
}
echo <<<EOFORM
    <td></td></tr>
EOFORM;

# get funds per nation
echo <<<EOFORM
    <tr><td></td><td style="text-align: left;">Funds</td>
EOFORM;
$total = 0;


if ($displaytype == "net") {
    # net for funds - get gdp minus taxes
    foreach ($empirenations as $nation_id => $nation_name) {
        $total = $total + $resources[$nation_id]['funds'];
        $displaycolor = "text-success";
        $displayamount = commas($resources[$nation_id]['funds']);
        echo <<<EOFORM
        <td style="text-align: left;"><span class="{$displaycolor}">{$displayamount}</span></td>
    EOFORM;
    }
} else if ($displaytype == "ticksworth") {
    # do nothing as no relevant ticksworth for funds
} else {
    # stockpiles - funds per nation
    foreach ($empirenations as $nation_id => $nation_name) {
        $total = $total + $resources[$nation_id]['funds'];
        if ($resources[$nation_id]['funds'] > 500000000) {
            $displaycolor = "text-warning";
        } else if ($resources[$nation_id]['funds'] > 0) {
            $displaycolor = "text-success";
        } else {
            $displaycolor = "text-danger";
        }
        $displayamount = commas($resources[$nation_id]['funds']);
        echo <<<EOFORM
        <td style="text-align: left;"><span class="{$displaycolor}">{$displayamount}</span></td>
    EOFORM;
    }
}

if ($total > 0) {
    $displaycolor = "text-success";
} else if ($total == 0) {
    $displaycolor = "text-warning";
} else {
    $displaycolor = "text-danger";
}
$displaytotal = commas($total);
echo <<<EOFORM
<td style="text-align: left;"><span class="{$displaycolor}">{$displaytotal}</span></td></tr>
EOFORM;

# iterate all nations, and all resources per nation
foreach ($all_resources_list as $resource_id => $resource_name) {
$total = 0;
echo <<<EOFORM
    <tr>
EOFORM;

if (!$nationinfo['hideicons']) {
    echo <<<EOFORM
    <td style="width: 16px;"><img src="images/icons/{$resource_name}.png"/></td>
EOFORM;
    }

echo <<<EOFORM
    <td style="text-align: left;">{$resource_name}</td>
EOFORM;

foreach ($empirenations as $nation_id => $nation_name) {
    $total = $total + $resources[$nation_id][$resource_id];
    if ($resources[$nation_id][$resource_id] > 50000) {
        $displaycolor = "text-warning";
    } else if ($resources[$nation_id][$resource_id] > 0) {
        $displaycolor = "text-success";
    } else {
        $displaycolor = "text-danger";
    }
    $displayamount = commas($resources[$nation_id][$resource_id]);
    echo <<<EOFORM
    <td style="text-align: left;"><span class="{$displaycolor}">{$displayamount}</span></td>
EOFORM;
}
 
if ($total > 0) {
    $displaycolor = "text-success";
} else if ($total == 0) {
    $displaycolor = "text-warning";
} else {
    $displaycolor = "text-danger";
}
$displaytotal = commas($total);
echo <<<EOFORM
<td style="text-align: left;"><span class="{$displaycolor}">{$displaytotal}</span></td></tr>
EOFORM;
}

echo <<<EOFORM
    </tbody></table>
EOFORM;


include("footer.php");
?>