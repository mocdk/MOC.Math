<?php
namespace MOC\Math\Fitting\FigureOfMerit;

use Niras\Meia\Statistics\Data\DataSeries;
use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

class LeastSquares implements FigureOfMeritFunctionInterface {
	/**
	 * @param DataSeries $data
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 * @return mixed
	 */
	public function calculate(DataSeries $data, MathematicalFunctionInterface $mathematicalFunction) {
		$squaredSum = 0.0;
		foreach ($data->getData() as $dataPoint) {
			$squaredSum += pow($dataPoint[1] - $mathematicalFunction->evaluateAtPoint($dataPoint[0]), 2);
		}
		return $squaredSum;
	}

}