<?php
date_default_timezone_set("Europe/Warsaw");

require_once "./inc/Cutter.php";

$cutter = new Cutter("raw.jpg", "./tmp/");
$cutter->cut();
