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

// uneven required for 2020-11/2020-12
//$uneven = false;
// Note! This also allows cutting to columns.
$uneven = true;

$day = date("d");
$baseDir = "../img-auto-cut-{$day}/";
$cellsDir = "{$baseDir}cells/";
$columnsDir = "{$baseDir}cols/";

/**/
//
// cut to columns (when uneven is true)
//
	$dir = './input/*.jpg';
	$files = FileHelper::filesByTime($dir);
	if (empty($files)) {
		die('\n[ERROR] No files in input dir.');
	}
	$newestFile = array_pop($files);
	echo "Cutting: $newestFile\n";

	$cutter = new Cutter($newestFile, $cellsDir, $columnsDir);

	//$cutter->cut(null, $uneven);
	$cutter->cutToColumns();

	// copy test view
	$fileName = 'test-view.php';
	if (!copy($fileName, $baseDir.$fileName)) {
		echo "\n[WARNING] Failed to copy $fileName...";
	}
	if (!copy($newestFile, $baseDir.basename($newestFile))) {
		echo "\n[WARNING] Failed to copy $newestFile...";
	}

/**/
//
// cut columns one-by-one
//
$dir = $columnsDir.'col_*.jpg';
$files = glob($dir);
if (empty($files)) {
	die('\n[ERROR] No files in input dir.');
}
$cutTime = time();
$cutter = new Cutter($files[0], $cellsDir, $columnsDir, true);
$cutter->setLogPath($cutTime);
$cutter->clearCells();
foreach ($files as $file) {
	$fileName = basename($file);
	$column = intval(preg_replace('#[^0-9]+#', '', $fileName));
	echo "\n[INFO] file: $fileName";
	$cutter = new Cutter($file, $cellsDir, $columnsDir, true);
	$cutter->setLogPath($cutTime);
	$cutter->cutColumn($column);
}
/**/

echo "\nDone\n";
