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
			<button id="toggle-zoomer">toggle zoomer</button>
			&bull;
			<button id="toggle-view-all" title="toggle temporary reset of done state">view all</button>
			&bull;
			<a href="img-auto-cut/all.jpg" target="_blank">all.jpg</a>
		</section>
		<section id="zoomer" class="medium3">
			<section class="main">
				<figure>
					<img src="img-auto-cut/cells/col_001_001.jpg" />
					<figcaption></figcaption>
				</figure>
				<div id="cell-form" style="display: none;">
					<label>Done <input type="checkbox" name="done"></label>
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
			No. of portals (per column): <input type="text" value="<?=implode("\t", $rowCounts)?>"/>
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
</body>
</html>