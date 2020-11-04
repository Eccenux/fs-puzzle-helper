<?php

class Rgb {
	public function __construct($rgb, $r, $g, $b) {
		$this->rgb = $rgb;
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}
	public function dump() {
		return "(r:{$this->r};g:{$this->g};b:{$this->b})";
	}
}
class RgbDistance {
	public function __construct($max, $avg) {
		$this->max = $max;
		$this->avg = $avg;
	}
	public function dump() {
		return "(max:{$this->max};avg:{$this->avg})";
	}
}

class ImageHelper {
	/**
	 * Init with expected background RGB.
	 *
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 */
	public function __construct($r, $g, $b) {
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}

	/**
	 * Get RBG from image.
	 *
	 * @param resource $img Image.
	 * @param int $x
	 * @param int $y
	 * @return Rgb
	 */
	public function getRgb($img, $x, $y)
	{
		$rgb = imagecolorat($img, $x, $y);
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		return new Rgb($rgb, $r, $g, $b);
	}

	/**
	 * Get RBG distance at given point.
	 *
	 * @param resource $img Image.
	 * @return RgbDistance
	 */
	public function getBackDistance($img, $x, $y)
	{
		$rgb = $this->getRgb($img, $x, $y);

		$diffs = array(abs($rgb->r-$this->r), abs($rgb->g-$this->g), abs($rgb->b-$this->b));
		$max = max($diffs);
		$avg = array_sum($diffs)/count($diffs);

		return new RgbDistance($max, $avg);
	}

	/**
	 * Check RBG distance at given point.
	 *
	 * @param resource $img Image.
	 * @return true if max(abs(RGB-back)) <= distance
	 */
	public function checkBackDistance($img, $x, $y, $distance)
	{
		$rgb = $this->getRgb($img, $x, $y);
		$dr = abs($rgb->r-$this->r);
		if ($dr > $distance) return false;
		$dg = abs($rgb->g-$this->g);
		if ($dg > $distance) return false;
		$db = abs($rgb->b-$this->b);
		if ($db > $distance) return false;

		return true;
	}

	/**
	 * Find bottom boundary Check RBG distance at given point.
	 * 
	 * Note! Higher step => lower accuracy, but greater speed.
	 *
	 * @param resource $img Image.
	 * @param int $probeX Probing point.
	 * @param int $startY Starting point.
	 * @return RgbDistance
	 */
	public function findBoundBottom($img, $probeX, $startY, $minY, $distance, $step)
	{
		for ($y = $startY; $y >= $minY; $y-=$step) {
			$ok = $this->checkBackDistance($img, $probeX, $y, $distance);
			if (!$ok) {
				return $y + $step + 1;
			}
		}
		return null;
	}
}