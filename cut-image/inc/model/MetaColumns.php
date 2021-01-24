<?php
// require_once './inc/ColumnMeta.php';

/**
 * Meta-data for columns cut.
 */
class MetaColumns {
	/** Top bar height (usually 100 or 15) */
	public $top = -1;
	/** Gap between columns (and rows) */
	public $gap = -1;
	/** Position of column ends (X boundaries) */
	private $columnEnds = array();
	private $columnCount = 0;
	/**
	 * Column bottoms (Y boundaries).
	 * 
	 * Note that height of a column would be $bottom - $top;
	 */
	public $columnBottoms = array();

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
	public function isEmpty() {
		return $this->columnCount < 1;
	}

	public function summary() {
		$text = "\n";
		$text .= "top = {$this->top}\n";
		$text .= "gap = {$this->gap}\n";
		$text .= "columnCount = {$this->columnCount}\n";
		$text .= "columnEnds = ".implode(',', $this->columnEnds)."\n";
		return $text;
	}

	/**
	 * Dump to JSON.
	 *
	 * @return string JSON encoded data.
	 */
	public function toJson() {
		$text = json_encode(array(
			'top' => $this->top,
			'gap' => $this->gap,
			'columnCount' => $this->columnCount,
			'columnEnds' => $this->columnEnds,
			'columnBottoms' => $this->columnBottoms,
		), JSON_PRETTY_PRINT);
		return $text;
	}
}