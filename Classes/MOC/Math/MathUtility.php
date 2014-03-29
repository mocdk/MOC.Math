<?php
namespace MOC\Math;


use Niras\Meia\Statistics\Data\DataSeries;
use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

class MathUtility {

	/**
	 * @param MathematicalFunctionInterface $
	 * @param array $points
	 * @return DataSeries
	 */
	public static function evaluateFunctionAtPoints(MathematicalFunctionInterface $mathematicalFunction, array $points) {
		$data = array();
		foreach ($points as $x) {
			$y = $mathematicalFunction->evaluateAtPoint($x);
			$data[] = array($x,$y);
		}
		return new DataSeries($data);
	}

	/**
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 * @param $min
	 * @param $max
	 * @param $numberOfPoints
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

	/**
	 * @todo: Move to expand function
	 * @param $covar Existing covariance matrix
	 * @param integer $ma Dimension of the new covariant matrix
	 * @param $ia
	 * @param integer $mfit Number of paramters to fit
	 * @return Matrix
	 */
	public static function covsrt(Matrix $covar, $ma, $ia, $mfit) {
		$newCovar = Matrix::emptyMatrix($ma, $ma);
		//for($i = )
	}
}