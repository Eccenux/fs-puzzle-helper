<?php
/**
 * Cut images to cell-images (portals).
 * 
 * Requires PHP 5.5 or higher (due to using e.g. `imagecrop`).
 */
date_default_timezone_set("Europe/Warsaw");
ini_set("memory_limit", "2048M");

require_once "./inc/Cutter.php";
require_once "./inc/FileHelper.php";

// @todo move column cutting to separate function? Could first cut columns and when columns are fine cut cells. And also don't always need cells.
// uneven for 2020-11/2020-12
$uneven = false;
$uneven = true;
$day = date("d");
$baseDir = "../img-auto-cut-$day/";

/**/
$testing = false;
// $testing = true;
if (!$testing) {
	$dir = './input/*.jpg';
	$files = FileHelper::filesByTime($dir);
	if (empty($files)) {
		die('[ERROR] No files in input dir.');
	}
	$newest_file = array_pop($files);
	echo "Cutting: $newest_file\n";

	$cutter = new Cutter($newest_file, "{$baseDir}cells/", "{$baseDir}");

	$cutter->cut(null, $uneven);

} else {
	// testing
	echo "\nWARNING! Running in test mode!\n";

	// $cutter = new Cutter("raw.jpg", "{$baseDir}cells_/", "{$baseDir}");
	// // $cutter->cut(2);
	// $cutter->cut();

	$files = glob("*.jpg");
	foreach ($files as $file) {
		echo "\n.\n.\n[TEST] file: $file\n";
		$cutter = new Cutter($file, "{$baseDir}cells_/", "{$baseDir}");
		$cutter->cut(-1);
	}
	
	echo "\nWARNING! Running in test mode!\n";
}

/**

// pseudo cut uneven columns
$baseDir = '{$baseDir}';
$dir = $baseDir.'col_*.jpg';
$files = glob($dir);
$colCounts = (require($baseDir."cut-data.php"));
if (empty($files)) {
	die('[ERROR] No files in input dir.');
}
$cutter = new Cutter($files[0], $baseDir."cells/", $baseDir."");
$cutter->clearCells();
foreach ($files as $file) {
	$fileName = basename($file);
	$column = intval(preg_replace('#[^0-9]+#', '', $fileName));
	$colCount = $colCounts[$fileName];
	echo "\n[INFO] file: $fileName; $colCount";
	$cutter = new Cutter($file, $baseDir."cells/", $baseDir."");
	$cutter->cutUneven($column, $colCount);
}

/**/
// cut columns one-by-one
//$baseDir = '{$baseDir}';
$dir = $baseDir.'col_*.jpg';
$files = glob($dir);
if (empty($files)) {
	die('[ERROR] No files in input dir.');
}
$cutTime = time();
$cutter = new Cutter($files[0], $baseDir."cells/", $baseDir."", true);
$cutter->setLogPath($cutTime);
$cutter->clearCells();
foreach ($files as $file) {
	$fileName = basename($file);
	$column = intval(preg_replace('#[^0-9]+#', '', $fileName));
	echo "\n[INFO] file: $fileName";
	$cutter = new Cutter($file, $baseDir."cells/", $baseDir."", true);
	$cutter->setLogPath($cutTime);
	$cutter->cutColumn($column);
}
/**/

echo "\nDone\n";
