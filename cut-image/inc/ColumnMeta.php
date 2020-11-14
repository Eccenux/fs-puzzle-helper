<?php

/**
 * Column cut meta-data.
 */
class ColumnMeta {
	private $colNumber = -1;
	public $h = 0;
	public $rowEnds = array();

	public function __construct($colNumber) {
		$this->colNumber = $colNumber;
	}
	
	public function summary() {
		$text = "";
		$text .= "[column={$this->colNumber}]\n";
		$text .= "h = {$this->h}\n";
		$text .= "rowCount = ".count($this->rowEnds)."\n";
		$text .= "rowEnds = ".implode(',', $this->rowEnds)."\n";
		return $text;
	}
}