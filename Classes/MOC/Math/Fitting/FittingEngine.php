<?php
namespace MOC\Math\Fitting;

use MOC\Math\GaussJordan;
use MOC\Math\MathematicalFunction\LinearCombinationOfFunctions;
use MOC\Math\Matrix;
use Niras\Meia\Statistics\Data\DataSeries;
use MOC\Math\Fitting\FigureOfMerit\LeastSquares;
use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

/**
 * Class FittingEngine
 *
 * Mathematical fitting engine. This particular class uses the Levenberg-Marquardt method to solve the non-lineary
 * fitting of data to functions in a non-linear way, and Gauss-Jordan elimination for the case of linear functions.
 *
 * The term linear relates to dependency of the coefficients of the basisfunctions. The individual functions can depend
 * wildly un-lineary on x. So the fit is to a linear combinations of basisfunctions.
 *
 * Ex: A (probably well known) lineary function could be y(x) = a + bx og y(x), or y(y) = a + bx + cx^2 or perhaps a
 * lineary combinations of harmonics.
 *
 * y(x) = a * cos(x) + b*cos(x)
 *
 * Notice that many function can be transformed into a linear combinations of basisfunctions.
 *
 * For non-linery combination off functions, antoher approach is needed. An example of a non-linear combination of
 * functions is a Gauss
 *
 * y(x) = a + b * exp(-(x-c)^2 / d
 *
 * These two types of functions are represented by the LinearCombinationOfFunctions and UnLinearCombinationOfFunctions
 * interfaces provided with this package.
 *
 * @package MOC\Math
 */
class FittingEngine {

	/**
	 * Do the actual fitting
	 *
	 * @param DataSeries $data
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 * @return float
	 */
	public function fitDataToMathematicalFunction(DataSeries $data, MathematicalFunctionInterface &$mathematicalFunction) {
		$mathematicalFunction->setParameters(array(1.0, 0.2));
		return 0.8;
	}

	/**
	 * Do a fit of linear function
	 *
	 * @param DataSeries $data
	 * @param MathematicalFunctionInterface $mathemeticalFuntion
	 * @param array An array indicating which paramateres to fit for. Length is the same as the functions degreesOfFreedom, and each entry is a boolen indicating whenter this variable should be fitted
	 */
	public function linearFit(DataSeries $data, LinearCombinationOfFunctions $mathematicalFunction, array $parametersToFitFor = NULL) {

		$ndat = count($data);

		if ($parametersToFitFor === NULL) {
			$parametersToFitFor = array_fill(0, $mathematicalFunction->getDegreesOfFreedom(), TRUE);
		}

		$numberOfParameters = count($parametersToFitFor);
		if ($numberOfParameters != $mathematicalFunction->getDegreesOfFreedom()) {
			throw new \Exception('Number of parameters must match degrees of freedom in the function');
		}

		$afunc = array();

		$numberOfParameteresToFit = 0;
		foreach ($parametersToFitFor as $fitParameter) {
			if ($fitParameter) {
				$numberOfParameteresToFit++;
			}
		}
		if ($numberOfParameteresToFit == 0) {
			throw new \Exception('At least one parameters shoud be fitted for');
		}

			// Initialize covar and beta variable used for the solutions
		$covar = Matrix::emptyMatrix($numberOfParameteresToFit, $numberOfParameteresToFit);
		$beta = Matrix::emptyMatrix($numberOfParameteresToFit,1);

		foreach ($data->getData() as $i => $point) {

				// Evaluate each of the basis functions in point X_i
			for ($j = 0; $j < $numberOfParameters; $j++) {
				$afunc[$j] = $mathematicalFunction->evaluateNthBasisFunctionAtPoint($point[0], $j);
			}
			$ym = $point[1];
			if ($numberOfParameteresToFit < $numberOfParameters) {
				for ($j=0; $j < $numberOfParameters; $j++) {
					if ($parametersToFitFor[$j] === FALSE) {
						$ym = $ym - $data->getValueAtIndex($j) * $afunc[$j];
					}
				}
			}

				// Calculate lower triangle of the Covariance matrix
			$sig2i = 1.0 / pow($data->getErrorAtIndex($i),2);
			$j = 0;
			for ($l = 0; $l < $numberOfParameters; $l++) {
				if ($parametersToFitFor[$l] === TRUE) {
					$wt = $afunc[$l] * $sig2i;
					$k = 0;
					for ($m=0; $m <= $l; $m++) {
						if ($parametersToFitFor[$m] === TRUE) {
							$covar->setValueAtPosition($j, $k, $covar->getValueAtPosition($j, $k) + $wt * $afunc[$m]);
							$k++;
						}
					}
					$beta->setValueAtPosition($j, 0, $beta->getValueAtPosition($j, 0) + $wt * $ym);
					$j++;
				}
			}

		}
			// Mirror the covar matrix in the diagonal
		for ($j = 1; $j < $numberOfParameters; $j++) {
			for ($k = 0; $k < $j; $k++) {
				$covar->setValueAtPosition($k, $j, $covar->getValueAtPosition($j, $k));
			}
		}
		$solver = new GaussJordan();
		$solver->solve($covar, $beta);
		$a = array();
		$j=0;
		for ($l=0; $l < $numberOfParameters; $l++) {
			if ($parametersToFitFor[$l] === TRUE) {
				$a[$l] = $beta->getValueAtPosition($j,0);
				$j++;
			}
		}
		$mathematicalFunction->setParameters($a);

	}

	/**
	 * @param DataSeries $data
	 * @param MathematicalFunctionInterface $mathematicalFunction
	 */
	public function nonLinearFit(DataSeries $data, MathematicalFunctionInterface &$mathematicalFunction, array $startingPoint) {
		$residual = 0.0;
		/*
         *@todo
		 * - Calculate least squares with starting point as parameters
		 * - Modify parameters according to algorith (By calcualting Hessian)
		 * - Calcualte least squares and see if it is less than before
		 * - Repeat untill least squares does not change (a local minima in parameter)
		 */
		$mathematicalFunction->setParameters($startingPoint);
		$merit = new LeastSquares();
		print "TEST: " . $merit->calculate($data, $mathematicalFunction);

		exit();
	}
}
