<?php
require_once './inc/ImageHelper.php';

/**
 * FS puzzle image cutter.
 */
class Cutter {
	// gap between columns
	public $gap = 10;

	// top boundary; top bar height (usually 15 or 100)
	public $top = 100;

	// column width; usually 300 or 500
	// note $imgw = $colw - $gap;
	public $colw = 300;

	// number of columns
	public $cols = 19;

	// output path
	public $out = './';

	public function __construct($file, $out) {
		$this->file = $file;
		$this->out = $out;

		$r = $g = $b = 50;	// expected background
		$this->ih = new ImageHelper($r, $g, $b);
	}

	/**
	 * Cut raw jpg file.
	 * 
	 * @return false on failure.
	 */
	public function cut() {
		if (!$this->init()) {
			return false;
		}

		// TODO find top boundary; top bar height (usually 15 or 100)
		// TODO find column width; usually 300 or 500

		// TODO find number of columns
		// find width of the whole image
		// (start from right with $y = $top+10; could do $x-=10 step)
		// calculate number of columns from that

		// cut columns loop
		//$this->cols = 1;
		for ($c=1; $c <= $this->cols; $c++) { 
			$this->cutCol($c);
		}

		return true;
	}

	/**
	 * Main probing point (X).
	 *
	 * @param int $column
	 * @return int
	 */
	private function getProbeX($column) {
		return $this->getStartX($column) + 10;
	}
	private function getStartX($column) {
		$x = ($column - 1) * $this->colw;
		return $x;
	}

	/**
	 * Find column height.
	 * 
	 * $r = $g = $b = 50;	// expected background
	 *
	 * @param int $column
	 * @return int height
	 */
	private function colHeight($column)
	{
		$h = $this->h;
		$img = $this->img;

		// main probing point
		$probeX = $this->getProbeX($column);

		$distance = 2;		// acceptable color distance
		$curTime = microtime(true);
		$startY = $h - 1;
		$minY = $this->top;
		for ($step = 200; $step > 1;) {
			$colh = $this->ih->findBoundBottom($img, $probeX, $startY, $minY, $distance, $step);
			if (is_null($colh)) {
				$colh = $h;
				break;
			}
			$startY = $colh;
			$step = ceil($step/2);
		}
		$timeConsumed = round(microtime(true) - $curTime,3)*1000;
		echo "[column=$column] colh = $colh (x=$probeX); dt=$timeConsumed\n";
		return $colh;
	}

	/**
	 * Cut column to images.
	 *
	 * @param int $column
	 * @return void
	 */
	private function cutCol($column)
	{
		$curTime = microtime(true);

		// find end of column (height of column)
		$colh = $this->colHeight($column);

		// skip if column height was not found (probably empty column)
		if ($colh == $this->h) {
			return;
		}

		// find image ends
		$rowEnds = $this->rowEnds($column, $colh);

		// debug
		var_export($rowEnds);
		echo "\n";
		$timeConsumed = round(microtime(true) - $curTime,3)*1000;
		echo "[column=$column] total dt=$timeConsumed\n";

		// crop images to files
		$rowEnds[] = $colh;
		$startY = $this->top;
		for ($r=1; $r <= count($rowEnds); $r++) { 
			$startX = $this->getStartX($column);
			$imgW = $this->colw - $this->gap;
			$endY = $rowEnds[$r-1];
			$imgH = $endY - $startY + 2;
			$output = $this->out . sprintf("/col_%03d_%03d.jpg", $column, $r);
			$imgCell = imagecrop($this->img, array(
				'x'=>$startX, 'y'=>$startY,
				'width'=>$imgW, 'height'=>$imgH,
			));
			if ($imgCell !== FALSE) {
				imagejpeg($imgCell, $output);
				imagedestroy($imgCell);
			}
			// next
			$startY = $endY + $this->gap - 1;
		}
	}

	/**
	 * Find row endings for a column.
	 * 
	 * TODO maybe I should confirm candidate by checking 2-3 points on the right (changing probeX)
	 *
	 * @param int $column
	 * @param int $colh Calculated height.
	 * @return array of Y; final row Y will not be returned (if colh was acurate).
	 */
	private function rowEnds($column, $colh)
	{
		// $h without gap
		$h = $colh - ($this->gap * 2);
		if ($h < $this->gap) {
			return array();
		}

		$distance = 8;		// acceptable distance
		$okAvg = 3;			// acceptable AVG of RGB (checked when minOK is reached)
		// I assume gap is larger then $minOk
		$minOk = 4;			// minimum valid points (more will be checked if okAvg was not reached)

		$img = $this->img;

		// main probing point
		$probeX = $this->getProbeX($column);

		$okCount = 0;
		$candidate = -1;
		$candidateInfo = '';
		$rowEnds = array();
		for ($y = $this->top; $y < $h; $y++) {
			$ok = $this->ih->checkBackDistance($img, $probeX, $y, $distance);

			// debug info & avg check
			if ($ok) {
				$rgb = $this->ih->getRgb($img, $probeX, $y);
				$diff = $this->ih->getBackDistance($img, $probeX, $y);
				$candidateInfo .= "[okCount=$okCount] candidate=$candidate [y=$y] ".$rgb->dump()." ".$diff->dump().";\n";
			}

			// rejection & reset
			if (!$ok) {
				if ($okCount > 0) {
					echo "rejected: $candidate [okCount=$okCount]\n";
				}
				$okCount = 0;
				$candidate = -1;
				$candidateInfo = '';

			// candidate registration & update
			} else {
				$okCount++;
				if ($okCount == 1) {
					$candidate = $y;

				// found
				} else if ($okCount >= $minOk && $diff->avg <= $okAvg) {
					$rowEnds[] = $candidate;
					echo "\n.\n.\n";
					echo $candidateInfo;
					echo "accepted: $candidate\n.\n.\n";
					$okCount = 0;
					$candidate = -1;
					$candidateInfo = '';
					$y += $this->gap;
				}
			}
		}
		return $rowEnds;
	}

	/**
	 * Init base data.
	 *
	 * @return false on failure.
	 */
	private function init()
	{
		// prepare input
		$file = $this->file;
		$img = imagecreatefromjpeg($file);
		if ($img === false) {
			echo "Unable to read image!";
			return false;
		}
		$this->img = $img;

		// prepare output
		if (!file_exists($this->out)) {
			mkdir($this->out, 0777, true);
		}

		// base props
		$this->w = imagesx($img);
		$this->h = imagesy($img);
		return true;
	}
}