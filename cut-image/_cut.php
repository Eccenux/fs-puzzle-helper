<?php
/**
 * Cut images to cell-images (portals).
 * 
 * Requires PHP 5.5 or higher (due to using e.g. `imagecrop`).
 */
date_default_timezone_set("Europe/Warsaw");
ini_set("memory_limit", "2048M");

require_once "./inc/Cutter.php";

$cutter = new Cutter("raw.jpg", "../img-auto-cut/cells/");
$cutter->cut();
// testing
//$cutter = new Cutter("raw.jpg", "../img-auto-cut/cells_/");
//$cutter->cut(2);
