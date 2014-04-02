<?php
namespace MOC\Math\Fitting\FigureOfMerit;

use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;
use MOC\Math\DataSeries;

class LeastSquares implements FigureOfMeritFunctionInterface {

	/**
	 * @param DataSeries $data
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 * @return mixed
	 */
	public function calculate(DataSeries $data, MathematicalFunctionInterface $mathematicalFunction) {
		$squaredSum = 0.0;
		/** @var $dataPoint \MOC\Math\Point */
		foreach ($data as $dataPoint) {
			$squaredSum += pow($dataPoint->getY() - $mathematicalFunction->evaluateAtPoint($dataPoint->getX()),2) / pow($dataPoint->getStdDeviation(), 2);
		}
		return $squaredSum;
	}

}