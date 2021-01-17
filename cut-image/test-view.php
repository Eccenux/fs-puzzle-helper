<?php
	/**
	 * Test viewer for cells and columns.
	 * Copied to destination dir.
	 */

	date_default_timezone_set('Europe/Warsaw');
	
	// params for testing
	$basePath = "./";
	$cellDir = "cells";
	$colsDir = "cols";
	
	// prepare columns list
	$colsPath = "$basePath$colsDir/";
	$colFiles = glob($colsPath . "/col_*.jpg");

	// prepare file lists and counts
	$cellsPath = "$basePath$cellDir/";
	$startCol = 1;
	$colCount = 0;
	$rowFiles = array();
	$rowCounts = array();
	for ($column=$startCol; $column < 40; $column++) {
		$files = glob($cellsPath . sprintf("col_%03d_*.jpg", $column));
		if (empty($files)) {
			break;
		}
		$colCount++;
		$rowCounts[$column] = count($files);
		$rowFiles[$column] = $files;
	}
	
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <title>Test cols</title>
    <meta charset="UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		:root {
			--col-count: <?=$colCount?>;
		}
		body {
			padding: 0;
			margin: 0;
		}
		.cols {
			display: grid;
			grid-template-columns: repeat(var(--col-count), 1fr);
		}
		.cols img {
			width: 100%;
		}
		.cols,
		.all img {
			width: 50%;
		}
		.copy img {
			width: 55%;
		}
	</style>
</head>
<body>

<div class="cols">
	<?php foreach($colFiles as $img) { ?>
		<?php $fileName = basename($img); ?>
		<img src="<?=$img?>" title="<?=$fileName?>">
	<?php } ?>
</div>

<div class="all">
	<img src="all.jpg">
</div>

<div class="copy">
	<img src="copy.jpg">
</div>

</body>
</html>

