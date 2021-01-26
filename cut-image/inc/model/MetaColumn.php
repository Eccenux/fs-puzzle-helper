<?php
/**
 * Meta-data for single column cut (rows).
 */
class MetaColumn {
	/** Relative path to the image. */
	public $img = '';
	/** Row ends */
	public $rowEnds = array();

	public function __construct($img, $rowEnds) {
		$this->img = $img;
		$this->rowEnds = $rowEnds;
	}
}