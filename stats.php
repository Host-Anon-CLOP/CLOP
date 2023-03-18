<?php
require_once('./backend/backend_stats.php');
$extratitle = "Server Statistics - ";
include('header.php');
?>
<style>/*
td {
	border-style: solid;
	border-width: 1px;
	border-collapse: collapse;
	padding: 2px;
	padding-right: 5px;
	padding-left: 5px;
}*/
</style>
<div class="container-fluid">
<div class="row">
<div class="col-md-6" id="div-left">
	<ul>
	<li>Nations:<ul>
		<li>Grand total of active nations: <abbr title="may or may not be one more than sum of nations by regions, because it includes admin nation when it's not stasised"><?= $totals['nations'] ?><abbr></li>
		<li>No. of nations ever created: <?= $totals['evernations'] ?></li>
		<li>By Region:<ul>
			<?php
			foreach ($regiontypes as $region => $name) {
				echo '<li>'.$name.': '.$nationstats[$region]['nations'].'</br>'
				.'No. of users having this nation type: '.$nationstats[$region]['users'].'</li>';
			}
			?>
			</ul></li>
		<li>By Subregion:<ul>
			<?php
			foreach ($subregiontypes as $subregion => $name) {
				echo '<li>'.$name.': '.$subregionstats[$subregion]['nations'].'</br>'
				.'No. of users having this region type: '.$subregionstats[$subregion]['users'].'</li>';
			}
			?>
			</ul></li>
		</ul></li>
	<li>Resources: <ul>
		<li><table class="table-striped table-bordered table-condensed table-hover">
			<thead><th>Resource</th><th>Total Stock</th><th>Total Production</th></thead>
			<tbody>
			<?php
			echo '<tr><td>Bits</td><td>'.$display['totalbits'].'</td><td>'.$display['totalgdp'].'</td></tr>';
			foreach ($display['resources'] as $rid => $total) {
				$prodtotal = commas($totals['resources'][$rid]['produced']);
				echo '<tr><td>'.$totals['resources'][$rid]['name'].'</td>'
					.'<td>'.$total.'</td>'.'<td>'.$prodtotal.'</td></tr>';
			}
			?></tbody>
			</table></li>
		</ul></li>
	<li>Buildings: <ul>
		<li><table class="table-striped table-bordered table-condensed table-hover">
			<thead><th>Building</th><th>Total Built</th><th>Total Disabled</th></thead>
			<tbody>
			<?php
			foreach ($display['buildings'] as $rid => $fig) {
				echo '<tr><td>'.$totals['buildings'][$rid]['name'].'</td>'
					.'<td>'.$fig['total'].'</td><td>'.$fig['disabled'].'</td></tr>';
			}
			?></tbody>
			</table></li>
		</ul></li>
	</ul></div>
<div class="col-md-6" id="div-right">
	<ul>
	<li>Players:<ul>
		<li>No. of users ever registered: <?= $totals['everusers'] ?></li>
		<li>No. of stasised players: <?= $totals['stasiscount'] ?></li>
		<li>No. of active players: <?= $totals['users'] ?></li>
		</ul></li>
	<li>Alliances:<ul>
		<li>Active: <?= $totals['alliances'] ?></li>
		<li>Ever created: <?= $totals['everalliances'] ?></li>
		<li>Allianceless players: <?= $totals['nonallied'] ?></li>
		</ul></li>
	<li>Averages:<ul>
		<li>Nations per Player: <?= $averages['playernations'] ?></li>
		<li>Resources:<ul>
			<li>table with avg. stock/production</li>
			</ul></li>
		<li>Marketplace:<ul>
			<li>table with avg. market prices</li>
			</ul></li>
		</ul></li>
	</ul></div>
</div>
</div></div>
<?php
include('footer.php');
