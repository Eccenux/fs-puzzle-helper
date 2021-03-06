<?php
	date_default_timezone_set('Europe/Warsaw');

	// Google sheet URL
	$gsUrl = !empty($_GET['gsurl']) ? $_GET['gsurl'] : "";
	$gsId = "";
	$gsSheetId = "1662443983";	// default gid from template
	if (preg_match("#^https://docs.google.com/spreadsheets/(\w+/[^/?]+)/#", $gsUrl, $matches)) {
		$gsId = $matches[1];
	}
	if (!empty($gsId) && preg_match("@/spreadsheets/.+[#?]gid=([0-9a-f]+)@", $gsUrl, $matches)) {
		$gsSheetId = $matches[1];
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

	<link rel="stylesheet" type="text/css" href="puzzle.css?1434">
	<link rel="icon" href="img/icon.svg" sizes="any" type="image/svg+xml">
	<link rel="shortcut icon" href="img/icon.svg" sizes="any" type="image/svg+xml">

	<!--
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<!-- leaflet as es5 -->
	<link rel="stylesheet" href="leaflet/leaflet.css" />
	<script src="leaflet/leaflet.js"></script>

	<script src="js/main.js" type="module"></script>
</head>
<body>
	<aside id="passcode-container">
		<?php include './inc/tpls/passcode.sheet.php'; ?>
		<!-- passcode to columns (chars and graphs) -->
		<section id="passcode-columns">
			<?php include './inc/tpls/passcode.columns.php'; ?>
		</section>
	</aside>
	<main>
		<section id="main-controls">
			<?php include './inc/tpls/controls.main.php'; ?>
		</section>
		<section id="zoomer" class="medium3">
			<?php include './inc/tpls/zoomer.php'; ?>
		</section>
		<section id="columns">
			<?php include './inc/tpls/columns.php'; ?>
		</section>
		<section id="controls">
			<?php include './inc/tpls/controls.extras.php'; ?>
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