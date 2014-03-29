<?php
namespace MOC\Math;

/**
 * Class Vector
 *
 * Represents an abitrary length vector
 *
 * @package MOC\Math
 */
class Vector {

	protected $values;

	/**
	 * @param array<float> $values
	 */
	public function __construct($values) {
		$this->values = $values;
	}

	/**
	 * Return a new vector of a given length, initialized to zeroes
	 *
	 * @param int $length
	 * @param value float
	 * @return Vector
	 */
	public static function getEmpty($length, $value = 0.00) {
		return new Vector(array_fill,0, $length, $value);
	}

	/**
	 * The length of the vector
	 *
	 * @return int
	 */
	public function getLength() {
		return count($this->values);
	}

	/**
	 * @return float
	 */
	public function getNorm() {
		$normSquared = 0.00;
		foreach ($this->values as $value) {
			$normSquared += pow($value,2);
		}
		return sqrt($normSquared);
	}

	/**
	 * @param integer $index
	 * @return float
	 */
	public function getIndex($index) {
		return $this->values[$index];
	}

	/**
	 * @param Vector $vector
	 * @throws \Exception
	 */
	public function multiplyDot(Vector $vector) {
		if ($vector->getLength() !== $this->getLength()) {
			throw new \Exception('Unable to multiply two vectors of different size');
		}
		$result = 0.00;
		foreach ($this->values as $i => $value) {
			$result += $value * $vector->getIndex($i);
		}
		return $result;
	}

}