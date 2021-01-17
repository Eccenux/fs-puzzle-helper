<?php
// require_once './inc/ColumnMeta.php';

/**
 * Meta-data for columns cut.
 */
class MetaColumns {
	public $top = -1;
	public $gap = -1;
	/** Position of column ends (boundaries) */
	private $columnEnds = array();
	private $columnCount = 0;

	/**
	 * Set column ends/widths.
	 *
	 * @param int[] $columnEnds
	 */
	public function setEnds($columnEnds) {
		$this->columnEnds = $columnEnds;
		$this->columnCount = count($columnEnds);
	}
	public function getEnds() {
		return $this->columnEnds;
	}

	public function summary() {
		$text = "\n";
		$text .= "top = {$this->top}\n";
		$text .= "gap = {$this->gap}\n";
		$text .= "columnCount = {$this->columnCount}\n";
		$text .= "columnEnds = ".implode(',', $this->columnEnds)."\n";
		return $text;
	}

	public function toJson() {
		$text = json_encode(array(
			'top' => $this->top,
			'gap' => $this->gap,
			'columnCount' => $this->columnCount,
			'columnEnds' => $this->columnEnds,
		), JSON_PRETTY_PRINT);
		return $text;
	}
}