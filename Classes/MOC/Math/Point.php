<?php
namespace MOC\Math;

/**
 * Class Point
 *
 * @package MOC\Math
 */
class Point {

	/**
	 * @var float
	 */
	protected $x = 0.0;

	/**
	 * @var float
	 */
	protected $y = 0.0;

	/**
	 * @param float $x
	 * @param float $y
	 */
	public function __construct($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}

	/**
	 * @return float
	 */
	public function getX() {
		return $this->x;
	}

	/**
	 * @return float
	 */
	public function getY() {
		return $this->y;
	}

}