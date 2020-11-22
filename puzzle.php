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
	<main>
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
				// params for testing
				// e.g.: puzzle.php?dir=cells_&column=2
				$dir = !empty($_GET['dir']) ? $_GET['dir'] : "cells";
				$startCol = !empty($_GET['column']) ? $_GET['column'] : 1;

				$base = "./img-auto-cut/$dir/";
				$rowCounts = array();
				for ($column=$startCol; $column < 30; $column++) {
					$files = glob($base . sprintf("/col_%03d_*.jpg", $column));
					if (empty($files)) {
						break;
					}
					$rowCounts[$column] = count($files);
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
	<aside>
		<h2>Dump to xls via list</h2>
		<button id="portallist-run">dump to list</button> (reverse order)
		<div id="portallist-out"></div>
		<script>
			(function() {
				let out = document.querySelector('#portallist-out');
				let dump = document.querySelector('#portallist-run');
				dump.onclick = function() {
					let container = document.createElement('ul');
					container.style.cssText = 'margin:0;padding:0';
					if (out.firstChild) {
						out.removeChild(out.firstChild)
					}
					app.portalsViewModel.state.portals
						.map(p=>{
							return {
								col: p.col,
								row: p.row,
								data: p.puzzleData(),
							}
						})
						.reverse()
						.forEach(p => {
							let nel = document.createElement('li');
							nel.className = 'text';
							nel.innerHTML = `
								<label>${p.col}, ${p.row}</label>
								<input value="${p.data}" onclick="this.select();">
							`;
							container.appendChild(nel);
						});
					;
					out.appendChild(container);
				};
			})();
		</script>
	</aside>
	<aside>
		<h2>Dump to xls via textarea</h2>
		<button id="portaldump-run">dump</button>
		<textarea id="portaldump-out" style="width: 100%;"></textarea>
		<script>
			(function() {
				let textarea = document.querySelector('#portaldump-out');
				let dump = document.querySelector('#portaldump-run');
				dump.onclick = function() {
					let portals = app.portalsViewModel.state.portals
						.map(p=>{
							return {
								col: p.col,
								row: p.row,
								data: p.puzzleData(),
							}
						})
						.map((p)=>`${p.col}, ${p.row}\n${p.data}`)
					;
					textarea.value = portals.join('\n');
				};
			})();
		</script>
	</aside>


</body>
</html>