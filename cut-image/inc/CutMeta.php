<?php
require_once './inc/ColumnMeta.php';

/**
 * Cut meta-data.
 */
class CutMeta {
	public $top = -1;
	public $gap = -1;
	public $colWidth = -1;
	public $colCount = -1;
	/** column cut info (array of ColumnMeta) */
	private $columns = array();

	public function add(ColumnMeta $columnMeta) {
		$this->columns[] = $columnMeta;
	}

	public function summary($full = false) {
		$text = "";
		$text .= "top = {$this->top}\n";
		$text .= "gap = {$this->gap}\n";
		$text .= "colWidth = {$this->colWidth}\n";
		$text .= "colCount = {$this->colCount}\n";
		if ($full) {
			foreach($this->columns as $column) {
				$text .= "\n";
				$text .= $column->summary();
			}
		} else {
			$rows = array();
			foreach($this->columns as $column) {
				$rows[] = count($column->rowEnds);
			}
			$text .= "rowCounts = ".implode(',', $rows)."\n";
		}
		return $text;
	}
}