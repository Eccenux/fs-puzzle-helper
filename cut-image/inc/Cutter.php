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
	public $top = 0;

	// column width; usually 300 or 500
	// note $imgw = $colw - $gap;
	// (will be re-calculated down)
	public $colw = 1000;

	// number of columns
	// (will be re-calculated)
	public $cols = 50;

	// set to true for single column processing
	public $singleColumn = false;

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
	 * Warning/error messages.
	 *
	 * @var array
	 */
	private $messages = array();

	/**
	 * Init.
	 *
	 * @param string $file Raw (un-cut) image path.
	 * @param string $out Cell output (HiFi).
	 * @param string $outCol Columns output (for xls, LowFi).
	 */
	public function __construct($file, $out, $outCol, $singleColumn = false) {
		$this->file = $file;
		$this->out = $out;
		$this->outCol = $outCol;
		$this->singleColumn = $singleColumn;

		$this->setLogPath(time(), $file);

		$r = $g = $b = 50;	// expected background
		$this->ih = new ImageHelper($r, $g, $b);
	}

	/**
	 * Set path for internal logs.
	 * 
	 * @param int $time Unix timestamp (e.g. from `time()`).
	 * @param string $file Original file name or a file info.
	 * @return void
	 */
	public function setLogPath($time, $file='') {
		if (empty($file)) {
			$file = 'cut';
		}
		$fileInfo = basename($file);
		$this->outLogCurrent = date("Y-m-d\TH.i.s", $time) . '--' . $fileInfo;
	}
	private function getLogPath() {
		return $this->outLogBase . $this->outLogCurrent . '/';
	}

	/**
	 * Cut uneven column
	 *
	 * @return void
	 */
	public function cutUneven($column, $colCount) {
		if (!$this->init(true)) {
			return false;
		}
		$startX = 0;
		$startY = 0;
		$imgW = $this->w;
		$imgH = ceil($this->h / $colCount * 3);
		$stepY = ceil($this->h / $colCount / 2);
		for ($r=1; $r <= $colCount; $r++) { 
			$output = $this->out . sprintf("/col_%03d_%03d.jpg", $column, $r);
			$this->crop($output, 100, array(
				'x'=>$startX, 'y'=>$startY,
				'width'=>$imgW, 'height'=>$imgH,
			));
			// next
			$startY += $stepY;
			$imgH += $stepY*2;
		}
	}

	/**
	 * Cut raw jpg file.
	 * 
	 * @deprecated Use cutToColumns with cutColumn instead.
	 * 
	 * @param int $column Column number or...
	 * 	null (default) => cut all
	 * 	-1 => just calculate top and width and exit
	 * 	>0 => cut only one column (1st => 1)
	 * @return false on failure.
	 */
	public function cut($column = null, $uneven = false) {
		if (!$this->init()) {
			return false;
		}

		$cutMeta = new CutMeta();
		$cutMeta->gap = $this->gap;

		// find top boundary; top bar height (usually 15 or 100)
		$cutMeta->top = $this->top = $this->findTop();

		// find column width; usually 300 or 500
		$colEnds = $this->findColW(90, $uneven);
		//$colEnds = 300;
		if (!$uneven) {
			$cutMeta->colWidth = $this->colw = $colEnds;
			$this->colEnds = array($this->colw);
		} else {
			$cutMeta->colWidth = $this->colw = $colEnds[0];
			$this->colEnds = $colEnds;
		}
		if ($uneven) {
			echo "\ncolEnds: ".implode(', ', $colEnds);
			//die();
		}
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

		// check widths
		$colEndsCount = count($this->colEnds);
		if ($uneven && ($colEndsCount < $this->cols)) {
			echo "\n[WARNING] colEndsCount ($colEndsCount) < cols ($this->cols)";
			//die();
		}

		// only crop images to column
		if ($uneven) {
			$startY = 100;
			$startX = 0;
			foreach ($this->colEnds as $cutNum => $colEnd) {
				$imgW = $colEnd - $startX;

				// get height
				$probeX = $startX + ceil($imgW / 2);
				$colh = $this->findBottom($probeX);
				//$imgH = $this->h - $startY;
				$imgH = $colh - $startY;

				$output = $this->outCol . sprintf("/col_%03d.jpg", $cutNum+1);
				$this->crop($output, 100, array(
					'x'=>$startX, 'y'=>$startY,
					'width'=>$imgW, 'height'=>$imgH,
				));
				$startX = $colEnd;
			}
			// cell cut not supported...yet
			return true;
		}

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
	 * Cut raw jpg file to columns.
	 * 
	 * @param int $column Column number or...
	 * 	null (default) => cut all
	 * 	-1 => just calculate top and width and exit
	 * 	>0 => cut only one column (1st => 1)
	 * @return false on failure.
	 */
	public function cutToColumns() {
		if (!$this->init()) {
			return false;
		}

		$cutMeta = new CutMeta();
		$cutMeta->gap = $this->gap;

		// find top boundary; top bar height (usually 15 or 100)
		$cutMeta->top = $this->top = $this->findTop();

		// find column widths (ends)
		$colEnds = $this->findColW(90, true);
		$cutMeta->colWidth = $this->colw = $colEnds[0];
		$this->colEnds = $colEnds;
		echo "\ncolEnds: ".implode(', ', $colEnds);

		// find number of columns
		$cutMeta->colCount = $this->cols = count($this->colEnds);

		// only crop images to column
		$startY = 100;
		$startX = 0;
		foreach ($this->colEnds as $cutNum => $colEnd) {
			$imgW = $colEnd - $startX;

			// get height
			$colh = $this->findColumnHeight($startX, $imgW);
			//$imgH = $this->h - $startY;
			$imgH = $colh - $startY;

			$output = $this->outCol . sprintf("/col_%03d.jpg", $cutNum+1);
			$this->crop($output, 100, array(
				'x'=>$startX, 'y'=>$startY,
				'width'=>$imgW, 'height'=>$imgH,
			));
			$startX = $colEnd;
		}

		// cut info
		$fullSummary = $cutMeta->summary(true);
		$logger = new Logger($this->getLogPath(), '_summary');
		$logger->log($fullSummary);
		echo "\nSummary:\n". $cutMeta->summary();

		return true;
	}

	/**
	 * Main probing point (X).
	 *
	 * @deprecated don't really work for uneven cols.
	 * 
	 * @param int $column
	 * @return int
	 */
	private function getProbeX($column) {
		if ($this->singleColumn) {
			return floor($this->w / 2);
		}
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
	private function findTop($probeX = 90)
	{
		$logger = new Logger($this->getLogPath(), 'top');
		ob_start();

		$h = $this->h;
		$img = $this->img;

		// normally top shouldn't exceed this value
		$maxTop = 200;

		// main probing point
		// that doesn't work when images are centered within column
		//$probeX = $this->gap + 1;

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

		// validation
		if ($top > $maxTop) {
			$topInfo = "[WARNING] Top seem too large: $top > $maxTop (at x:$probeX). Try to add white dot at x:$probeX.";
			echo "\n$topInfo\n";
		}
		return $top;
	}

	/**
	 * Find column height for given range.
	 * 
	 * @param integer $startX Column start.
	 * @param integer $width Column width.
	 * @return integer Column height.
	 */
	private function findColumnHeight($startX, $width)
	{
		// need to check few points mainly because of thin images
		// and also because those thin images can be aligned right or left
		$probes = array(
			$startX + ceil($width * 0.15),
			$startX + ceil($width * 0.3),
			$startX + ceil($width * 0.5),
			$startX + ceil($width * 0.6),
			$startX + ceil($width * 0.85),
		);

		$curTime = microtime(true);

		$totalHeight = $this->h;
		$heights = array();
		foreach ($probes as $probeX) {
			$bottom = $this->findBottom($probeX);
			// for all-vertical images the probe might miss all images, skip that probe
			if ($bottom < $totalHeight) {
				$heights[] = $bottom;
			}
		}

		$height = empty($heights) ? $totalHeight : max($heights);

		// this might happen e.g. when there is an extra master-passcode image on the bottom
		// it could be fine if there was no empty space left
		if ($height >= $totalHeight - 100) {
			echo "\n[WARNING] column height ($height) ~= image height ($totalHeight). At X: $startX.\nMaybe cut out master code image from bottom (but leave some empty space).";
		}

		// $timeConsumed = round(microtime(true) - $curTime,3)*1000;
		// echo "\ncolumn height = $height (start=$startX; width=$width); dt=$timeConsumed[ms]";

		return $height;
	}

	/**
	 * Find columns width.
	 * 
	 * usually 300 or 500
	 * note $imgw = $colw - $gap;
	 * 
	 * @param integer $startX StartX, should start with < min cell-img.w
	 * @return integer|array 1st column width or array of column ends for uneven.
	 */
	private function findColW($startX = 90, $uneven = false)
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
					if (!$uneven && count($candidates) > 1) {
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
		if (!$uneven) {
			if (count($candidates) > 1) {
				if ($candidates[0] * 2 != $candidates[1]) {
					die ("\n[ERROR] Column width candidates do not match! Try manualy setting `colw` (instead of calling `findColW`)\n");
				}
				return $candidates[0];
			}
			die ("\n[ERROR] Unable to calculate column width! Try manualy setting `colw` (instead of calling `findColW`)\n");
		} else {
			return $candidates;
		}
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
	 * @deprecated don't work for uneven cols.
	 * 
	 * @param int $column
	 * @return int height
	 */
	private function findColHeight($column)
	{
		// main probing point
		$probeX = $this->getProbeX($column);

		$curTime = microtime(true);
		$colh = $this->findBottom($probeX);
		$timeConsumed = round(microtime(true) - $curTime,3)*1000;
		echo "[column=$column] colh = $colh (x=$probeX); dt=$timeConsumed\n";

		return $colh;
	}
	/**
	 * Find bottom / column height.
	 * 
	 * @param int $probeX Probing point on X (constant).
	 * @param int $startY Probing point on Y (starter, going up). Defaults to image height.
	 * @return int height (last Y that is still a background)
	 */
	private function findBottom($probeX, $startY = -1)
	{
		$h = $this->h;
		$img = $this->img;

		if ($probeX >= $this->w) {
			return $h;
		}

		$distance = 2;		// acceptable color distance
		if ($startY <= 0) {
			$startY = $h - 1;
		}
		$minY = $this->top;
		for ($step = 200; $step > 1;) {
			$colh = $this->ih->findBoundBottom($img, $probeX, $startY, $minY, $distance, $step);
			// all pixels were background pixels...
			if (is_null($colh)) {
				$colh = $h;
				// ...that might still be a fluke for big steps, so we brake for small steps only
				if ($step < 10) {
					break;
				}
			}
			// this means we made no progress...
			if ($startY < $colh) {
				// ...but we might still make some progress at smaller steps, so we brake for small steps only
				if ($step < 10) {
					break;
				}
			} else {
				$startY = $colh;
			}
			$step = ceil($step/2);
		}
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
		$dtLogger = new Logger($this->getLogPath(), "dt_col_cut");

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
		// dump messages
		if (!empty($this->messages)) {
			echo "\n".implode("\n", $this->messages)."\n";
			$this->messages = array();
		}
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
		$dtLogger->log("[column=$column] total dt=$timeConsumed\n");
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
	 * Cut column file (single column).
	 * 
	 * @param int $column Column number (for stats and file names).
	 * @return false on failure.
	 */
	public function cutColumn($column) {
		if (!$this->init(true)) {
			return false;
		}
		$logger = new Logger($this->getLogPath(), sprintf("col_cut_%03d", $column));

		// we assume column is already cut to size
		$colh = $this->h;

		// find rows
		ob_start();
		echo "\n[rowEnds]";
		$rowEnds = $this->rowEnds($column, $colh);

		// log cut info
		$logger->log(ob_get_clean());
		
		// dump messages
		if (!empty($this->messages)) {
			echo "\n".implode("\n", $this->messages)."\n";
			$this->messages = array();
		}

		// crop images to cells
		$rowEnds[] = $colh;
		$startY = 0;
		$startX = 0;
		$imgW = $this->w;
		for ($r=1; $r <= count($rowEnds); $r++) { 
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

		return true;
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
		if ($rect['height'] > $this->h - $rect['y']) {
			$rect['height'] = $this->h - $rect['y'];
		}
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
			'maxHeight' => $this->colw + ($this->gap * 2),		// max row height (higher then this will generate a warning)
			//'dieOnOverflow' => true,		// die when high seems wrong
			'minHeight' => 100,		// min row height (lower then smallest row)
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
		$maxHeight = $options['maxHeight'];

		$img = $this->img;

		// main probing point
		$probeX = $this->getProbeX($column);

		$okCount = 0;
		$candidate = -1;
		$candidateInfo = '';
		$rowEnds = array();
		$rowNum = 1;
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
				
				// too large for a gap
				} else if ($okCount >= $this->gap + 2) {
					echo "\n";
					$this->debugPoint($probeX, $y);
					echo "rejected too large gap: $candidate [okCount=$okCount]\n";
					$reset = true;

				// found
				} else if ($okCount >= $minOk && $diff->avg <= $okAvg) {
					// probe over X
					$startY = $y-1;
					$height = 3;
					$startX = $probeX;
					$stepX = ceil($this->colw / 50);
					// change Y if close to edge
					if ($okCount >= $this->gap - 1) {
						$startY = $candidate + floor($this->gap / 2);
					}
					$okX = $this->checkOverX($column, $startY, $height, $startX, $stepX);

					if (!$okX) {
						echo "rejected over X: $candidate [okCount=$okCount]\n";
					
					// accepted
					} else {
						// final height check
						$heightInfo = "";
						if ($rowH > $maxHeight) {
							$heightInfo = "[WARNING] Row height is too large: $rowH > $maxHeight (at y:$y; col: $column, row: $rowNum).";
							$this->messages[] = $heightInfo;
							$heightInfo .= "\n";
						}

						echo "\n.\n.\n";
						echo $candidateInfo;
						echo $heightInfo;
						echo "accepted: $candidate\n.\n.\n";

						$rowEnds[] = $candidate;
						$rowNum++;
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
		$distanceX = 16;
		$okAvgX = 10;

		$img = $this->img;
		$endY = $startY + $height;
		for ($probeY = $startY; $probeY <= $endY; $probeY++) {
			$colEnd = ($this->singleColumn) ? $this->w : $this->getStartX($column+1) - $this->gap;
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
	private function init($columnImage = false)
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

		// clear dirs
		if (!$columnImage) {
			$this->clearCells();
			$this->clearColumns();
		}

		// base props
		$this->w = imagesx($img);
		$this->h = imagesy($img);
		return true;
	}

	/**
	 * Clear cells dir.
	 */
	public function clearCells()
	{
		$files = glob($this->out . '/*.jpg');
		foreach($files as $file) {
			if(is_file($file))
				unlink($file);
		}
	}
	/**
	 * Clear columns dir.
	 */
	public function clearColumns()
	{
		$files = glob($this->outCol . '/*.jpg');
		foreach($files as $file) {
			if(is_file($file))
				unlink($file);
		}
	}
}