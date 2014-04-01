<?php
namespace MOC\Math;

use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

/**
 * Class MathUtility
 *
 * Utility class for various math operations
 *
 * @package MOC\Math
 */
class MathUtility {

	/**
	 * Given a mathematical function and an array of floats, evaluate the function if each point, and return a DataSeries
	 * object.
	 *
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 * @param array<float> $points An array of floats.
	 * @return DataSeries
	 */
	public static function evaluateFunctionAtPoints(MathematicalFunctionInterface $mathematicalFunction, array $points) {
		$data = array();
		foreach ($points as $x) {
			$y = $mathematicalFunction->evaluateAtPoint($x);
			$data[] = new Point($x,$y);
		}
		return new DataSeries($data);
	}

	/**
	 * Given a mathematical function, a range of min and max value and desired total number if points - return a DataSeries
	 * with the function evaluated in all points within the range.
	 *
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 * @param float $min The minimum value to evalutate the function in
	 * @param float $max The maximum value to evaluate the function in
	 * @param integer $numberOfPoints Number of points to evalue the function in between $min and $max
	 * @return DataSeries
	 */
	public static function evaluateFunctionInInterval(MathematicalFunctionInterface $mathematicalFunction, $min, $max, $numberOfPoints) {
		$xValues = array();
		$delta = ($max-$min) / ($numberOfPoints-1);

		for ($i = 0; $i < $numberOfPoints; $i++) {
			$xValues[] = $min + $i * $delta;
		}
		return self::evaluateFunctionAtPoints($mathematicalFunction, $xValues);
	}

}