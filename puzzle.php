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
<body class="hide-passcode">
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
			<?php include './inc/tpls/passcode.columns.php'; ?>
		</section>
	</aside>
	<main>
		<section id="main-controls">
			<button id="reset-all" title="reset state of columns and portals">reset all</button>
			&bull;
			<button id="toggle-zoomer" title="toggle bottom/left zoomer">zoomer ↕️</button>
			<button id="toggle-right-zoomer" title="toggle right/left zoomer">zoomer ↔️</button>
			&bull;
			<button id="toggle-view-all" title="toggle temporary reset of done state">view all</button>
			<button id="toggle-hide-done" title="hide/show done columns" class="show-hide-button shown">done</button>
			<button id="toggle-hide-passcode" title="hide/show passcode" class="show-hide-button hidden">passcode</button>
		</section>
		<section id="zoomer" class="medium3">
			<?php include './inc/tpls/zoomer.php'; ?>
		</section>
		<section id="columns">
			<?php include './inc/tpls/columns.php'; ?>
		</section>
		<section id="controls">
			<p class="text">
				<label>No. of portals (per column):</label>
				<input type="text" value="<?=implode("\t", $rowCounts)?>"/>
			</p>
			<p>
				&nbsp; <a href="img-auto-cut/all.jpg" target="_blank">all.jpg</a>
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
	<?php include './inc/tpls/puzzle.dump.php'; ?>

	<!-- browser detection for CSS (yes, I do need it 😢) -->
	<script>
		(function(){
			var isFox = navigator.userAgent.indexOf("Firefox") != -1;
			var isChrome = !!window.chrome;
			if (isChrome && !isFox) {
				console.log('is chrome', {isChrome, isFox});
				document.body.classList.add('is-chrome');
			}
		})();
	</script>
</body>
</html>