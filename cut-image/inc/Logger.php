<?php

/**
 * Simple logging.
 */
class Logger {
	private $logPath;

	public function __construct($path, $file) {
		$this->logPath = $path .'/'. $file;
		if (!is_dir($path)) {
			mkdir($path, 0777, true);
		}
	}

	public function log($text)
	{
		file_put_contents($this->logPath, $text, FILE_APPEND);
	}
}