<?php
namespace MOC\Math;

/**
 * Class Point
 *
 * Representation of a 2 dimensional point in a set of data.
 * Each point consists of an X and a Y value, and possibly an error in Y.
 * X,Y and the optional error must be numbers (integer, floats etc.).
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
	 * @var float
	 */
	protected $error = 0.00;

	/**
	 * @param float $x
	 * @param float $y
	 * @param $error Optional error in y. Default is 0.00
	 */
	public function __construct($x, $y, $error = 0.00) {
		$this->x = $x;
		$this->y = $y;
		$this->error = $error;
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

	/**
	 * @return float
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Return the error, but returned with 1.00 if the error is set to 0.00. Otherwise most algorithms will get a division by zero
	 * @return float
	 */
	public function getStdDeviation() {
		if ($this->error == 0.0) {
			return 1.00;
		}
		return $this->error;
	}

}