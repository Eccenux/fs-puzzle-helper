<?php
require_once './inc/ImageHelper.php';
require_once './inc/Logger.php';
require_once './inc/ColumnMeta.php';
require_once './inc/CutMeta.php';

/**
 * FS puzzle image cutter.
 */
class Cutter {
	// gap between columns (and rows)
	public $gap = 10;

	// top boundary; top bar height (usually 15 or 100)
	// (will be re-calculated)
	public $top = 100;

	// column width; usually 300 or 500
	// note $imgw = $colw - $gap;
	// (will be re-calculated down)
	public $colw = 1000;

	// number of columns
	// (will be re-calculated)
	public $cols = 50;

	// output path
	public $out = './';
	// column images
	public $outCol = './';
	// base logs path
	public $outLogBase = './logs/';
	// sub dir
	private $outLogCurrent = '';

	/**
	 * Helper for background/color operations.
	 *
	 * @var ImageHelper
	 */
	private $ih;

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

		$this->outLogCurrent = date("Y-m-d\TH.i.s") . '--' . basename($file);

		$r = $g = $b = 50;	// expected background
		$this->ih = new ImageHelper($r, $g, $b);
	}

	private function getLogPath() {
		return $this->outLogBase . $this->outLogCurrent . '/';
	}

	/**
	 * Cut raw jpg file.
	 * 
	 * @param int $column Column number or...
	 * 	null (default) => cut all
	 * 	-1 => just calculate top and width and exit
	 * 	>0 => cut only one column (1st => 1)
	 * @return false on failure.
	 */
	public function cut($column = null) {
		if (!$this->init()) {
			return false;
		}

		$cutMeta = new CutMeta();
		$cutMeta->gap = $this->gap;

		// find top boundary; top bar height (usually 15 or 100)
		$cutMeta->top = $this->top = $this->findTop();

		// find column width; usually 300 or 500
		$cutMeta->colWidth = $this->colw = $this->findColW();
		// exit (tests)
		if ($column === -1) {
			return false;
		}

		// just one cut
		if (!is_null($column)) {
			$this->cutCol($column);
			return true;
		}

		// find number of columns
		$cutMeta->colCount = $this->cols = $this->findColNo();

		// cut columns loop
		$maxh = 0;
		for ($c=1; $c <= $this->cols; $c++) { 
			$col = $this->cutCol($c);
			$cutMeta->add($col);
			if ($maxh < $col->h) {
				$maxh = $col->h;
			}
		}

		// all.jpg
		$this->crop("{$this->outCol}/all.jpg", 100, array(
			'x'=>0, 'y'=>$this->top,
			'width'=>$this->colw*$this->cols, 'height'=>$maxh,
		));

		// cut info
		$fullSummary = $cutMeta->summary(true);
		$logger = new Logger($this->getLogPath(), '_summary');
		$logger->log($fullSummary);
		echo $cutMeta->summary();

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
	 * Find top bar height.
	 * 
	 * @return int position of image from top.
	 */
	private function findTop()
	{
		$logger = new Logger($this->getLogPath(), 'top');
		ob_start();

		$h = $this->h;
		$img = $this->img;

		// main probing point
		$probeX = $this->gap + 1;

		$distance = 2;		// acceptable color distance
		$curTime = microtime(true);
		$startY = 0;
		$maxY = $h-1;
		$steps = array(10, 3, 1);
		foreach ($steps as $step) {
			$top = $this->ih->findBoundTop($img, $probeX, $startY, $maxY, $distance, $step);
			if (is_null($top)) {
				$top = $h;
				break;
			}
			$startY = $top;
		}
		$timeConsumed = round(microtime(true) - $curTime,3)*1000;
		echo "top = $top (x=$probeX); dt=$timeConsumed\n.\n";
		$logger->log(ob_get_clean());
		return $top;
	}

	/**
	 * Find columns width.
	 * 
	 * usually 300 or 500
	 * note $imgw = $colw - $gap;
	 * 
	 * @param integer $startX StartX, should start with < min cell-img.w
	 * @return integer 1st column width
	 * For now only return first column width.
	 * The algorithm can however be used to calculate all widths.
	 */
	private function findColW($startX = 90)
	{
		$img = $this->img;

		$logger = new Logger($this->getLogPath(), 'colw');
		ob_start();

		//$startX = 90;	// 90 < min cell-img.w
		$probeY = $this->top + 50;	// 50 < min cell-img.h
		$probeY2 = floor($this->h * 0.2);
		$probeY3 = floor($this->h * 0.4);
		//$maxX = $this->colw;
		$maxX = $this->w - 1;

		// initial state = in cell img
		$stepInCell = floor($this->gap / 2) + 1;
		$stepInGap = 1;
		// Note! due to JPG distortions this need to be a loose check
		// But it is also highly unlikely that going down you wouldn't get some pixel higly off the scale
		$distance = 25;
		$candidates = array();
		for ($x = $startX; $x <= $maxX; $x += $stepInCell) {
			$ok = $this->ih->checkBackDistance($img, $x, $probeY, $distance);

			// quick re-check
			if ($ok) {
				$ok = $this->ih->checkBackDistance($img, $x, $probeY2, $distance);
				if ($ok) {
					$ok = $this->ih->checkBackDistance($img, $x, $probeY3, $distance);
				}
			}

			// going out of cell?
			if ($ok) {
				echo "(x=$x); gap?\n";
				$gapLength = 0;
				$noMoreColumns = false;
				$gapStart = $x;

				// verify && find exact border
				while ($this->isColumnGap($x, $distance)) {
					$gapLength++;
					$x += $stepInGap;
					if ($x > $maxX) {	// total img end
						break;
					}
					if (!empty($candidates) && $gapLength > $candidates[0]) {	// gap is too wide => there are no more columns
						$noMoreColumns = true;
						break;
					}
					$ok = $this->ih->checkBackDistance($img, $x, $probeY, $distance);
					if (!$ok) {			// column ended
						break;
					}
					echo "(x=$x); gap\n";
				}

				if ($gapLength > 3) {
					if (!$noMoreColumns) {
						echo "(x=$x); column end (gap=$gapLength)\n";
						$candidates[] = $x;
					} else {
						echo "(x=$x); no more columns (gap=$gapLength; start=$gapStart)\n";
						$candidates[] = $gapStart;
						break;
					}

					// end early (only check 2 cols)
					if (count($candidates) > 1) {
						break;
					}
				}
			}
		}

		if (!empty($candidates)) {
			echo "candidates: ".implode(', ', $candidates);
		}

		// end logging
		$logger->log(ob_get_clean());

		// verify candidates
		if (count($candidates) > 1) {
			if ($candidates[0] * 2 != $candidates[1]) {
				die ("\n[ERROR] Column width candidates do not match! Try manualy setting `colw` (instead of calling `findColW`)\n");
			}
			return $candidates[0];
		}
		die ("\n[ERROR] Unable to calculate column width! Try manualy setting `colw` (instead of calling `findColW`)\n");
	}

	/**
	 * Verify that X is a column gap.
	 *
	 * @param int $x
	 * @param int $distance
	 * @return boolean
	 */
	private function isColumnGap($x, $distance)
	{
		$img = $this->img;

		$probeY = $this->top + 50;	// 50 < min cell-img.h

		$probeX = $x;
		$startY = $probeY;
		$maxY = floor($this->h * 0.66);
		$stepY = $this->gap;
		$boundY = $this->ih->findBoundTop($img, $probeX, $startY, $maxY, $distance, $stepY);
		if (is_null($boundY)) {
			return true;
		}
		return false;
	}

	/**
	 * Find number of columns.
	 * 
	 * @return int number of columns.
	 */
	private function findColNo()
	{
		$logger = new Logger($this->getLogPath(), 'col_count');
		ob_start();

		$img = $this->img;

		// main probing point
		$probeY = $this->top + 50;

		$distance = 2;		// acceptable color distance
		$startX = $this->w - 1;
		$minX = $this->colw;
		$step = 200;
		$bound = $this->ih->findBoundRight($img, $probeY, $startX, $minX, $distance, $step);
		if (is_null($bound)) {
			$bound = $this->w;
		}
		$info = $this->debugPoint($startX, $probeY, true);
		echo "boundary with step=$step: $info";

		for ($cols = ceil($bound / $this->colw) + 1; $cols >= 1; $cols--) {
			echo "cols test: $cols\n";
			$colh = $this->findColHeight($cols);
			if ($colh < $this->h) {
				break;
			}
		}

		echo "cols=$cols\n.\n";

		$logger->log(ob_get_clean());
		return $cols;
	}

	/**
	 * Debug image point.
	 * 
	 * Note that this a bit computation heavy.
	 *
	 * @param int $x
	 * @param int $y
	 * @param boolean $return Default is to echo info.
	 * @return string|void Info string.
	 */
	private function debugPoint($x, $y, $return = false)
	{
		$rgb = $this->ih->getRgb($this->img, $x, $y);
		$diff = $this->ih->getBackDistance($this->img, $x, $y);
		$info = "[x=$x, y=$y] ".$rgb->dump()." ".$diff->dump()."\n";
		if ($return) {
			return $info;
		}
		echo $info;
	}

	/**
	 * Find column height.
	 * 
	 * @param int $column
	 * @return int height
	 */
	private function findColHeight($column)
	{
		$h = $this->h;
		$img = $this->img;

		// main probing point
		$probeX = $this->getProbeX($column);
		if ($probeX >= $this->w) {
			return $h;
		}

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
	 * @return ColumnMeta Column meta (height 0 if empty).
	 */
	private function cutCol($column)
	{
		$meta = new ColumnMeta($column);

		$logger = new Logger($this->getLogPath(), sprintf("col_cut_%03d", $column));

		$curTime = microtime(true);

		// find end of column (height of column)
		ob_start();
		echo "\n[findColHeight]";
		$colh = $this->findColHeight($column);
		$logger->log(ob_get_clean());

		// skip if column height was not found (probably empty column)
		if ($colh == $this->h) {
			return $meta;
		}
		$meta->h = $colh;

		// find image ends
		ob_start();
		echo "\n[rowEnds]";
		$rowEnds = $this->rowEnds($column, $colh);
		$meta->rowEnds = $rowEnds;
		$logger->log(ob_get_clean());
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
		ob_start();
		echo "\n[summary]\n";
		var_export($rowEnds);
		echo "\n";
		$timeConsumed = round(microtime(true) - $curTime,3)*1000;
		echo "[column=$column] total dt=$timeConsumed\n";
		$logger->log(ob_get_clean());

		// crop images to cells
		$rowEnds[] = $colh;
		$startY = $this->top;
		for ($r=1; $r <= count($rowEnds); $r++) { 
			$startX = $this->getStartX($column);
			$imgW = $this->colw - $this->gap;
			$endY = $rowEnds[$r-1];
			$imgH = $endY - $startY + 2;
			$output = $this->out . sprintf("/col_%03d_%03d.jpg", $column, $r);
			$this->crop($output, 100, array(
				'x'=>$startX, 'y'=>$startY,
				'width'=>$imgW, 'height'=>$imgH,
			));
			// next
			$startY = $endY + $this->gap - 1;
		}

		// crop images to column
		$rowEnds[] = $colh;
		$startY = $this->top;
		$startX = $this->getStartX($column);
		$imgW = $this->colw - $this->gap;
		$imgH = $colh - $startY;
		$output = $this->outCol . sprintf("/col_%d.jpg", $column);
		$this->crop($output, 75, array(
			'x'=>$startX, 'y'=>$startY,
			'width'=>$imgW, 'height'=>$imgH,
		), 200);

		return $meta;
	}

	/**
	 * Crop and save image.
	 *
	 * @param string $outFile Output file path.
	 * @param int $quality JPG quality.
	 * @param array $rect (x,y,width,height).
	 * @param int $scaledWidth (optional) New width.
	 * @return boolean false upon failure.
	 */
	private function crop($outFile, $quality, $rect, $scaledWidth=false) {
		$cropped = imagecrop($this->img, $rect);
		if ($cropped !== false) {
			if ($scaledWidth === false) {
				imagejpeg($cropped, $outFile, $quality);
			} else {
				$scaled = imagescale($cropped, $scaledWidth);
				imagejpeg($scaled, $outFile, $quality);
				imagedestroy($scaled);
			}
			imagedestroy($cropped);
			return true;
		}
		return false;
	}

	/**
	 * Find row endings for a column.
	 * 
	 * @param int $column
	 * @param int $colh Calculated height.
	 * @param array $options Algorithm options override.
	 * @return array of Y; final row Y will not be returned (if colh was acurate).
	 */
	private function rowEnds($column, $colh, $options = array())
	{
		$defaultOptions = array(
			'minHeight' => 50,		// min row height (lower then smallest row)
			'distance' => 10,		// acceptable distance for candidate search
			'okAvg' => 2.5,			// acceptable AVG of RGB (checked when minOK is reached)
			'minOk' => 4,			// minimum valid points (more will be checked if okAvg was not reached)
									// Note! This should be true: minOk < gap
		);
		$options = array_merge($defaultOptions, $options);

		$minHeight = $options['minHeight'];

		// $h without gap
		$h = $colh - $minHeight;
		if ($h < $this->gap) {
			return array();
		}

		$distance = $options['distance'];
		$okAvg = $options['okAvg'];
		$minOk = $options['minOk'];

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