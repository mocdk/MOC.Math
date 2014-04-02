<?php
namespace MOC\Math\Fitting;

use MOC\Math\DataSeries;

use MOC\Math\GaussJordan;
use MOC\Math\MathematicalFunction\LinearCombinationOfFunctions;
use MOC\Math\Matrix;
use MOC\Math\Fitting\FigureOfMerit\LeastSquares;
use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

/**
 * Class FittingEngine
 *
 * Mathematical fitting engine for linear combination of functions.
 * It uses Gauss-Jordan for solving.
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
 * @package MOC\Math
 */
class LinearFittingEngine extends AbstractFittingEngine {

	/**
	 * Do a fit of linear function
	 *
	 * @param DataSeries $data
	 * @param MathematicalFunctionInterface $mathemeticalFuntion
	 * @param array An array indicating which paramateres to fit for. Length is the same as the functions degreesOfFreedom, and each entry is a boolen indicating whenter this variable should be fitted
	 */
	public function fit() {
		$afunc = array();

		/** @var $point \MOC\Math\Point  */
		foreach($this->data as $i => $point) {

				// Evaluate each of the basis functions in point X_i
			for ($j = 0; $j < $this->numberOfParameters; $j++) {
				$afunc[$j] = $this->function->evaluateNthBasisFunctionAtPoint($point->getX(), $j);
			}
			$ym = $point->getY();
			if ($this->numberOfParameteresToFit < $this->numberOfParameters) {
				for ($j=0; $j < $this->numberOfParameters; $j++) {
					if ($this->parametersToFitFor[$j] === FALSE) {
						$ym = $ym - $data[$j]->getY() * $afunc[$j];
					}
				}
			}

				// Calculate lower triangle of the Covariance matrix
			$sigma = $point->getStdDeviation();
			$sig2i = 1.0 / pow($sigma,2);
			$j = 0;
			for ($l = 0; $l < $this->numberOfParameters; $l++) {
				if ($this->parametersToFitFor[$l] === TRUE) {
					$wt = $afunc[$l] * $sig2i;
					$k = 0;
					for ($m=0; $m <= $l; $m++) {
						if ($this->parametersToFitFor[$m] === TRUE) {
							$this->covar->setValueAtPosition($j, $k, $this->covar->getValueAtPosition($j, $k) + $wt * $afunc[$m]);
							$k++;
						}
					}
					$this->beta->setValueAtPosition($j, 0, $this->beta->getValueAtPosition($j, 0) + $wt * $ym);
					$j++;
				}
			}

		}
			// Mirror the covar matrix in the diagonal
		for ($j = 1; $j < $this->numberOfParameters; $j++) {
			for ($k = 0; $k < $j; $k++) {
				$this->covar->setValueAtPosition($k, $j, $this->covar->getValueAtPosition($j, $k));
			}
		}

		$this->solver->solve($this->covar, $this->beta);
		$a = array();
		$j=0;
		for ($l=0; $l < $this->numberOfParameters; $l++) {
			if ($this->parametersToFitFor[$l] === TRUE) {
				$a[$l] = $this->beta->getValueAtPosition($j,0);
				$j++;
			}
		}
		$this->function->setParameters($a);
	}

	/**
	 * After fitting, return the value of chi squared.
	 *
	 * @return float;
	 */
	public function getChiSquared() {
		$merit = new LeastSquares();
		return $merit->calculate($this->data, $this->function);
	}

}
