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
	public $colw = 500;

	// number of columns
	public $cols = 25;

	// output path
	public $out = './';
	// column images
	public $outCol = './';

	/**
	 * Init.
	 *
	 * @param string $file Raw (un-cut) image path.
	 * @param string $out Cell output (HiFi).
	 * @param string $outCol Columns output (for xls, LowFi).
	 */
	public function __construct($file, $out, $outCol) {
		$this->file = $file;
		$this->out = $out;
		$this->outCol = $outCol;

		$r = $g = $b = 50;	// expected background
		$this->ih = new ImageHelper($r, $g, $b);
	}

	/**
	 * Cut raw jpg file.
	 * 
	 * @return false on failure.
	 */
	public function cut($column = null) {
		if (!$this->init()) {
			return false;
		}

		// TODO find top boundary; top bar height (usually 15 or 100)
		// TODO find column width; usually 300 or 500

		// TODO find number of columns
		// max based on width of the whole image
		$maxCols = floor($this->w / $this->colw);
		if ($this->cols > $maxCols) {
			$this->cols = $maxCols;
		}
		// (start from right with $y = $top+10; could do $x-=10 step)
		// calculate number of columns from that

		// cut columns loop
		if (is_null($column)) {
			for ($c=1; $c <= $this->cols; $c++) { 
				$this->cutCol($c);
			}
		// just one
		} else {
			$this->cutCol($column);
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
		/**
		// fail override
		if ($column==10) {
			$rowEnds = array (
				377,
				662,
				1162,
				//1413,
				1662,
				//1855,
			);
		} elseif ($column==6) {
			$rowEnds = array (
				//389,
				590,
				1090,
				1590,
				1968,
				2345,
			);
		}
		/**/

		// debug
		var_export($rowEnds);
		echo "\n";
		$timeConsumed = round(microtime(true) - $curTime,3)*1000;
		echo "[column=$column] total dt=$timeConsumed\n";

		// crop images to cells
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
				imagejpeg($imgCell, $output, 100);
				imagedestroy($imgCell);
			}
			// next
			$startY = $endY + $this->gap - 1;
		}

		// crop images to column
		// TODO: refactor common parts of crop
		$rowEnds[] = $colh;
		$startY = $this->top;
		$startX = $this->getStartX($column);
		$imgW = $this->colw - $this->gap;
		$imgH = $colh - $startY;
		$output = $this->outCol . sprintf("/col_%d.jpg", $column);
		$imgCell = imagecrop($this->img, array(
			'x'=>$startX, 'y'=>$startY,
			'width'=>$imgW, 'height'=>$imgH,
		));
		if ($imgCell !== FALSE) {
			$imgSmall = imagescale($imgCell, 200);
			imagejpeg($imgSmall, $output);
			imagedestroy($imgCell);
			imagedestroy($imgSmall);
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
		$minHeight = 50;

		// $h without gap
		$h = $colh - $minHeight;
		if ($h < $this->gap) {
			return array();
		}

		$distance = 8;		// acceptable distance
		$okAvg = 2.5;		// acceptable AVG of RGB (checked when minOK is reached)
		// I assume gap is larger then $minOk
		$minOk = 4;			// minimum valid points (more will be checked if okAvg was not reached)

		$img = $this->img;

		// main probing point
		$probeX = $this->getProbeX($column);

		$okCount = 0;
		$candidate = -1;
		$candidateInfo = '';
		$rowEnds = array();
		$prevY = $this->top;
		for ($y = $this->top; $y < $h; $y++) {
			$reset = false;
			$ok = $this->ih->checkBackDistance($img, $probeX, $y, $distance);
			
			// reject to small
			if ($ok) {
				$rowH = $y - $prevY;
				if ($rowH < $minHeight) {
					$ok = false;
					$reset = true;
					echo "rejected to small: $y [rowH:$rowH < minHeight:$minHeight]\n";
				}
			}

			// debug info & avg check
			if ($ok) {
				$rgb = $this->ih->getRgb($img, $probeX, $y);
				$diff = $this->ih->getBackDistance($img, $probeX, $y);
				$candidateInfo .= "[okCount=$okCount] candidate=$candidate [y=$y] ".$rgb->dump()." ".$diff->dump().";\n";
			}

			// rejection
			if (!$ok) {
				if ($okCount > 0) {
					$rgb = $this->ih->getRgb($img, $probeX, $y);
					$diff = $this->ih->getBackDistance($img, $probeX, $y);
					echo "[overY] [y=$y] ".$rgb->dump()." ".$diff->dump().";\n";
					echo "rejected: $candidate [okCount=$okCount]\n";
				}
				$reset = true;

			// candidate registration & update
			} else {
				$okCount++;
				if ($okCount == 1) {
					$candidate = $y;

				// found
				} else if ($okCount >= $minOk && $diff->avg <= $okAvg) {
					// probe over X
					$startY = $y-1;
					$height = 3;
					$startX = $probeX;
					$stepX = ceil($this->colw / 50);
					$okX = $this->checkOverX($column, $startY, $height, $startX, $stepX);

					if (!$okX) {
						echo "rejected over X: $candidate [okCount=$okCount]\n";
					} else {
						echo "\n.\n.\n";
						echo $candidateInfo;
						echo "accepted: $candidate\n.\n.\n";

						$rowEnds[] = $candidate;
						$prevY = $candidate;
						$y += $this->gap;
						$reset = true;
					}
				}
			}

			// reset candidate
			if ($reset) {
				$okCount = 0;
				$candidate = -1;
				$candidateInfo = '';
			}
		}
		return $rowEnds;
	}

	/**
	 * Check background over X axis (horizontal).
	 *
	 * @param int $column
	 * 
	 * @param int $startY
	 * @param int $height Should be small, must be smaller then gap.
	 * 
	 * @param int $startX
	 * @param int $stepX Bigger step, faster computation.
	 * @return void
	 */
	private function checkOverX($column, $startY, $height, $startX, $stepX)
	{
		$distanceX = 14;
		$okAvgX = 10;

		$img = $this->img;
		$endY = $startY + $height;
		for ($probeY = $startY; $probeY <= $endY; $probeY++) {
			$colEnd = $this->getStartX($column+1) - $this->gap;
			for ($x = $startX; $x < $colEnd; $x+=$stepX) {
				$ok = $this->ih->checkBackDistance($img, $x, $probeY, $distanceX);
				$diff = $this->ih->getBackDistance($img, $x, $probeY);
				if (!$ok || $diff->avg > $okAvgX) {
					$rgb = $this->ih->getRgb($img, $x, $probeY);
					$dx = $x - $startX;
					echo "[overX] [$dx, $probeY] ".$rgb->dump()." ".$diff->dump().";\n";
					return false;
				}
			}
		}
		return true;
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
		if (!file_exists($this->outCol)) {
			mkdir($this->outCol, 0777, true);
		}

		// clear dir
		$files = glob($this->out . '/*.jpg');
		foreach($files as $file) {
			if(is_file($file))
				unlink($file);
		}
		// column dir
		$files = glob($this->outCol . '/*.jpg');
		foreach($files as $file) {
			if(is_file($file))
				unlink($file);
		}

		// base props
		$this->w = imagesx($img);
		$this->h = imagesy($img);
		return true;
	}
}