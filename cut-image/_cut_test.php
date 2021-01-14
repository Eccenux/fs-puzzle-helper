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

$day = date("d");
$baseDir = "../img-auto-cut-$day-test/";

// testing
echo "\nWARNING! Running in test mode!\n";

// $cutter = new Cutter("raw.jpg", "{$baseDir}cells_/", "{$baseDir}");
// // $cutter->cut(2);
// $cutter->cut();

$files = glob("*.jpg");
foreach ($files as $file) {
	echo "\n.\n.\n[TEST] file: $file\n";
	$cutter = new Cutter($file, "{$baseDir}cells/", "{$baseDir}");
	$cutter->cut(-1);
}

echo "\nWARNING! Running in test mode!\n";
