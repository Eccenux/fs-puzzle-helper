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

		// TODO cut columns loop
		$this->cols = 1;
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
		$probeX = 10 + ($column - 1) * $this->colw;
		return $probeX;
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

		$distance = 2;		// aceptable color distance
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
		$distance = 10;		// aceptable distance
		$img = $this->img;

		// find end of column (height of column)
		$colh = $this->colHeight($column);

		// main probing point
		$probeX = $this->getProbeX($column);

		/**/
		// find image end (should confirm by checking 2-3 points on the right)
		$minOk = 5;
		$okCount = 0;
		$candidate = -1;
		$columnEnds = array();
		for ($y = $this->top; $y < $colh; $y++) {
			$rgb = $this->ih->getRgb($img, $probeX, $y);
			$diff = $this->ih->getBackDistance($img, $probeX, $y);
			$ok = $this->ih->checkBackDistance($img, $probeX, $y, $distance);
			if ($ok) {
				echo "[y=$y]: ".$rgb->dump()." ".$diff->dump()."; candidate=$candidate [okCount=$okCount]\n";
			}

			if (!$ok) {
				if ($okCount > 0) {
					echo "rejected\n\n";
				}
				$okCount = 0;
				$candidate = -1;
			} else {
				$okCount++;
				if ($okCount == 1) {
					$candidate = $y;
				} else if ($okCount >= $minOk) {
					$columnEnds[] = $candidate;
					$okCount = 0;
					$y += $this->gap;
					echo "accepted: $candidate\n\n";
				}
			}
		}
		var_export($columnEnds);
		// crop image to file
		// next
		/**/
	}

	/**
	 * Init base data.
	 *
	 * @return false on failure.
	 */
	private function init()
	{
		$file = $this->file;
		$img = imagecreatefromjpeg($file);
		if ($img === false) {
			echo "Unable to read image!";
			return false;
		}
		$this->img = $img;
		
		$this->w = imagesx($img);
		$this->h = imagesy($img);
		return true;
	}
}