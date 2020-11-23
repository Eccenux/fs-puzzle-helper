<?php
	date_default_timezone_set('Europe/Warsaw');

	// Google sheet URL
	$gsUrl = !empty($_GET['gsurl']) ? $_GET['gsurl'] : "";
	$gsId = "";
	if (preg_match("#^https://docs.google.com/spreadsheets/(\w+/[^/?]+)/#", $gsUrl, $matches)) {
		$gsId = $matches[1];
	}

	// params for testing
	// e.g.: puzzle.php?dir=cells_&column=2
	$dir = !empty($_GET['dir']) ? $_GET['dir'] : "cells";
	$startCol = !empty($_GET['column']) ? $_GET['column'] : 1;

	// prepare file lists and counts
	$base = "./img-auto-cut/$dir/";
	$colCount = 0;
	$rowFiles = array();
	$rowCounts = array();
	for ($column=$startCol; $column < 30; $column++) {
		$files = glob($base . sprintf("/col_%03d_*.jpg", $column));
		if (empty($files)) {
			break;
		}
		$colCount++;
		$rowCounts[$column] = count($files);
		$rowFiles[$column] = $files;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Portal cells</title>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="copyright" content="Maciej Jaros">

	<link rel="stylesheet" type="text/css" href="puzzle.css">
	<link rel="icon" href="img/icon.svg" sizes="any" type="image/svg+xml">
	<link rel="shortcut icon" href="img/icon.svg" sizes="any" type="image/svg+xml">

	<!--
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<script src="js/main.js" type="module"></script>
</head>
<body>
	<aside id="passcode-container">
		<?php if (!empty($gsId)) { ?>
			<section class="frame">
				<iframe width='1000' height='200' frameborder='0' src='https://docs.google.com/spreadsheets/<?=$gsId?>/edit?rm=minimal#gid=1662443983'></iframe>
			</section>
			<section id="sheet-size-controls">
				<button class="resize" data-class="gs-hide">Hide GS</button>
				<button class="resize" data-class="gs-default">Default GS</button>
				<button class="resize" data-class="gs-large">Large GS</button>
			</section>
		<?php } else { ?>
			<section id="sheet-form">
				<form action="">
					<label title="Sheet URL">Google Sheet URL:</label>
					<input name="gsurl" type="url" value="https://docs.google.com/spreadsheets/d/111gE09r7AqnhXfsuNouOssruunuRt3rTXw7Nt42zpVU/edit#gid=1662443983">
					<input type="submit" value="submit">
				</form>
			</section>
		<?php } ?>
		<!-- passcode to columns (chars and graphs) -->
		<section id="passcode-columns">
			<table>
				<tr>
					<th>Column</th>
					<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
						<th class="passcode-col-no"><?=$column?></td>
					<?php } ?>
				</tr>
				<?php if (empty($gsId)) { ?>
				<tr>
					<th>Char</th>
					<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
						<td class="passcode-col-char">
							<input data-col="<?=$column?>" name="char" type="text" value="">
						</td>
					<?php } ?>
				</tr>
				<?php } ?>
				<tr>
					<th>Map</th>
					<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
						<td class="passcode-col-map">
							<button data-col="<?=$column?>" title="Show map for column">👁‍</button>
						</td>
					<?php } ?>
				</tr>
			</table>
			<div class="map">
				<div id="main-char-map" class="char-map"></div>
			</div>
		</section>
	</aside>
	<main>
		<?php /*
		<!-- passcode base info -->
		<section id="passcode-info">
			<div class="form">
				<p class="text">
					<label for="passcode_field_url" title="Puzzle image">Image:</label>
					<input  id="passcode_field_url" name="url" type="url" value="">
				</p>
				<p class="text">
					<label for="passcode_field_code">Code:</label>
					<input  id="passcode_field_code" name="code" type="text" value="">
				</p>
			</div>
			<div class="map">
				<div id="main-char-map" class="char-map"></div>
			</div>
		</section>
		<!-- passcode to columns (chars and graphs) -->
		<section id="passcode-columns">
			<table>
				<tr>
					<th>Column</th>
					<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
						<th class="passcode-col-no"><?=$column?></td>
					<?php } ?>
				</tr>
				<tr>
					<th>Format</th>
					<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
						<td class="passcode-col-format">
							<input data-col="<?=$column?>" name="char" type="text" value="">
						</td>
					<?php } ?>
				</tr>
				<tr>
					<th>Char</th>
					<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
						<td class="passcode-col-char">
							<input data-col="<?=$column?>" name="char" type="text" value="">
						</td>
					<?php } ?>
				</tr>
				<tr>
					<th>Map</th>
					<?php for ($column=$startCol; $column <= $colCount; $column++) { ?>
						<td class="passcode-col-map">
							<button data-col="<?=$column?>" title="Show map for column">👁‍</button>
						</td>
					<?php } ?>
				</tr>
			</table>
		</section>
		*/?>

		<section id="main-controls">
			<button id="reset-all" title="toggle all columns state">reset all</button>
			&bull;
			<button id="toggle-zoomer">toggle bottom zoomer</button>
			<button id="toggle-right-zoomer">toggle right zoomer</button>
			&bull;
			<button id="toggle-view-all" title="toggle temporary reset of done state">view all</button>
			&bull;
			<a href="img-auto-cut/all.jpg" target="_blank">all.jpg</a>
		</section>
		<section id="zoomer" class="medium3">
			<section class="main">
				<button class="zoomer-hide" title="close zoomer">✕</button>
				<figure>
					<img src="img-auto-cut/cells/col_001_001.jpg" />
					<figcaption></figcaption>
				</figure>
				<div id="cell-form" class="form" style="display: none;">
					<p class="checkbox"><label>Done <input type="checkbox" name="done"></label></p>
					<p class="text">
						<label for="zoomer_field_puzzle" title="FS puzzle format">FS puzzle: 📋</label>
						<input  id="zoomer_field_puzzle" name="puzzle" type="text" value="">
					</p>
					<p class="text">
						<label for="zoomer_field_notes">Notes:</label>
						<input  id="zoomer_field_notes" name="notes" type="text" value="">
					</p>
				</div>
			</section>
			<section id="zoomer-list-controls" style="display: none;">
				<button class="resize" data-class="small">small (6)</button>
				<button class="resize" data-class="medium5">5</button>
				<button class="resize" data-class="medium4">4</button>
				<button class="resize" data-class="medium3">3</button>
				<button class="resize" data-class="medium2">2</button>
				<button class="resize" data-class="big">big (1)</button>
				&bull;
				<button class="clear">clear</button>
			</section>
			<section id="zoomer-list">
			</section>
		</section>
		<section id="columns">
			<section>
			<?php
				for ($column=$startCol; $column <= $colCount; $column++) {
					$files = $rowFiles[$column];
					//echo '<section class="column" id="col_'.$column.'"><h2>'.$column.' <em>('.count($files).')</em></h2>';
					echo '<section class="column" id="col_'.$column.'"><h2>'.$column.'</h2>';
					foreach ($files as $file) {
						$id = preg_replace('#\.\w+$#', '', basename($file));
						if (preg_match('#.+?0*([0-9]+).+?0*([0-9]+).*#', $id, $matches)) {
							$col = $matches[1];
							$row = $matches[2];
							$title = "col $col, row $row";
							echo "<img src='$file' id='cell_{$id}' title='{$title}' data-col='$col' data-row='$row' />";
						}
					}
					echo '</section>';
				}
			?>
			</section>
		</section>
		<section id="controls">
			<p class="text">
				<label>No. of portals (per column):</label>
				<input type="text" value="<?=implode("\t", $rowCounts)?>"/>
			</p>
		</section>
	</main>

	<script>
		// css: var(--scrollbar-width)
		//document.documentElement.style.setProperty('--scrollbar-width', (window.innerWidth - document.documentElement.clientWidth) + "px");		
	</script>

	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		$("input[type=checkbox]").checkboxradio();
		$('#zoomer-list').sortable();
		$('#zoomer-list').disableSelection();
	</script>

	<!-- experimental/prototypes -->

	<!-- leaflet as es5 -->
	<link rel="stylesheet" href="leaflet/leaflet.css" />
	<script src="leaflet/leaflet.js"></script>
	<script src="js/ColumnsMap.js"></script>

	<!-- dumps -->
	<aside style="padding:5px">
		<h2>Portals dump</h2>
		<button id="portallist-run">dump to list</button> (reverse order)
		<div id="portallist-out"></div>
		<script>
			(function() {
				let out = document.querySelector('#portallist-out');
				let dump = document.querySelector('#portallist-run');
				dump.onclick = function() {
					let container = document.createElement('table');
					container.className = 'dump-table';
					container.innerHTML = `<thead>
						<tr>
							<th style="width:3em">Cell</th>
							<th style="width:25em">FS puzzle</th>
							<th>Notes</th>
						</tr>
					</thead><tbody></tbody>`;
					let containerBody = container.querySelector('tbody');
					if (out.firstChild) {
						out.removeChild(out.firstChild)
					}
					app.portalsViewModel.state.portals
						.map(p=>{
							return {
								col: p.col,
								row: p.row,
								notes: p.notes,
								data: p.puzzleData(),
							}
						})
						.reverse()
						.forEach(p => {
							let nel = document.createElement('tr');
							//nel.className = 'text';
							nel.innerHTML = `
								<tr>
									<td>${p.col}, ${p.row}</td>
									<td><input value="${p.data}" onclick="this.select();" style="width:100%"></td>
									<td>${p.notes}</td>
								</tr>
							`;
							containerBody.appendChild(nel);
						});
					;
					out.appendChild(container);
				};
			})();
		</script>
	</aside>

	<aside style="padding:5px">
		<h2>Locations dump per column</h2>
		<button id="locationslist-run">dump columns</button> (row order)
		<div id="locationslist-out"></div>
		<script>
			(function() {
				let out = document.querySelector('#locationslist-out');
				let dump = document.querySelector('#locationslist-run');
				dump.onclick = function() {
					// prepare container / header
					let container = document.createElement('table');
					container.className = 'dump-table';
					container.innerHTML = `<thead>
						<tr>
							<th style="width:3em">Column</th>
							<th>Locations</th>
						</tr>
					</thead><tbody></tbody>`;
					let containerBody = container.querySelector('tbody');
					if (out.firstChild) {
						out.removeChild(out.firstChild)
					}

					// create `cols` as locations list by column
					let cols = [];
					app.portalsViewModel.state.portals
						.map(p=>{
							return {
								col: p.col,
								row: p.row,
								ll: p.getLatLon(),
							}
						})
						// sort by col, row (col 1 first)
						.sort(
							(a,b)=> a.col == b.col ? a.row - b.row : a.col - b.col
						)
						.forEach(p=>{
							if (!(p.col in cols)) {
								cols[p.col] = [];
							}
							cols[p.col].push(p.ll);
						})
					;
					console.log(cols);

					// create rows
					cols
						.forEach((ll, col) => {
							let nel = document.createElement('tr');
							//nel.className = 'text';
							let data = ll.join('\n');
							nel.innerHTML = `
								<tr>
									<td>${col}</td>
									<td><textarea rows="5" onclick="this.select();" style="width:100%">${data}</textarea></td>
								</tr>
							`;
							containerBody.appendChild(nel);
						});
					;
					
					// display
					out.appendChild(container);
				};
			})();
		</script>
	</aside>

	<aside style="padding:5px">
		<h2>Locations dump flat</h2>
		<button id="locationslistflat-run">dump all</button> (row order)
		<textarea id="locationslistflat-out" style="width: 100%; height:30vh;"></textarea>
		<script>
			(function() {
				let out = document.querySelector('#locationslistflat-out');
				let dump = document.querySelector('#locationslistflat-run');
				dump.onclick = function() {
					// create `cols` as locations list by column
					let cols = [];
					app.portalsViewModel.state.portals
						.map(p=>{
							return {
								col: p.col,
								row: p.row,
								ll: p.getLatLon(),
							}
						})
						// sort by col, row (col 1 first)
						.sort(
							(a,b)=> a.col == b.col ? a.row - b.row : a.col - b.col
						)
						.forEach(p=>{
							if (!(p.col in cols)) {
								cols[p.col] = [];
							}
							cols[p.col].push(p.ll);
						})
					;
					console.log(cols);

					// dump
					let locations = '';
					cols
						.forEach((ll, col) => {
							//nel.className = 'text';
							let data = ll.join('\n');
							locations += data + '\n';
						});
					;
					
					// display
					out.value = locations;
				};
			})();
		</script>
	</aside>

</body>
</html>