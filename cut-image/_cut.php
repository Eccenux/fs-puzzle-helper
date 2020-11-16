<?php
/**
 * Cut images to cell-images (portals).
 * 
 * Requires PHP 5.5 or higher (due to using e.g. `imagecrop`).
 */
date_default_timezone_set("Europe/Warsaw");
ini_set("memory_limit", "2048M");

require_once "./inc/Cutter.php";

$testing = false;
// $testing = true;
if (!$testing) {
	$dir = './input';
	$files = scandir($dir, SCANDIR_SORT_DESCENDING);
	if (empty($files)) {
		die('[ERROR] No files in input dir.');
	}
	$newest_file = $dir .'/'. $files[0];
	echo "Cutting: $newest_file\n";

	$cutter = new Cutter($newest_file, "../img-auto-cut/cells/", "../img-auto-cut/");
	$cutter->cut();

} else {
	// testing
	echo "\nWARNING! Running in test mode!\n";

	// $cutter = new Cutter("raw.jpg", "../img-auto-cut/cells_/", "../img-auto-cut/");
	// // $cutter->cut(2);
	// $cutter->cut();

	$files = glob("*.jpg");
	foreach ($files as $file) {
		echo "\n.\n.\n[TEST] file: $file\n";
		$cutter = new Cutter($file, "../img-auto-cut/cells_/", "../img-auto-cut/");
		$cutter->cut(-1);
	}
	
	echo "\nWARNING! Running in test mode!\n";
}

echo "\nDone\n";
