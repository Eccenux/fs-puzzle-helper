<?php
/**
 * Pseudo cut columns to cell-images (portals).
 * 
 * Can be done after cutting columns to generate a bit smaller images for manual cut.
 */
date_default_timezone_set("Europe/Warsaw");
ini_set("memory_limit", "2048M");

require_once "./inc/Cutter.php";
require_once "./inc/FileHelper.php";

$day = date("d");
$baseDir = "../img-auto-cut-$day/";

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

echo "\nDone\n";
