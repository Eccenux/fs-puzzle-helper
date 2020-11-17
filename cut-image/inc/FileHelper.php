<?php

class FileHelper {
	/**
	 * Get files sorted by time.
	 *
	 * @param string $dir Dir or dir pattern (glob compat.).
	 * @return array Files sorted from oldest to youngest.
	 */
	public static function filesByTime($dir) {
		$files = glob($dir);
		$filesSorted = array();
		foreach ($files as $file) {
			$dt = filemtime($file);
			$filesSorted[$dt] = $file;
		}
		ksort ($filesSorted);
		return $filesSorted;
	}
}