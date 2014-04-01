<?php
namespace MOC\Math\Fitting\FigureOfMerit;

use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

/**
 * Interface FigureOfMeritFunctionInterface
 *
 * The figure-of-merit-function determines how well a model fits a certain matehematical function. It
 * yealds a low value for a good fit, and a high value for a poor fit.
 * This way fitting data to function turns into a multidimensional minimization problem.
 *
 * The best known figure-of-merit-function is the least-squares fit, but Chi-squared and others exists as well.
 *
 * @package MOC\Math\Fitting
 */
interface FigureOfMeritFunctionInterface {

	/**
	 * @param DataSeries $data
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 * @return mixed
	 */
	public function calculate(DataSeries $data, MathematicalFunctionInterface $mathematicalFunction);

}