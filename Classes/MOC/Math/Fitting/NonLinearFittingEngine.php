<?php
namespace MOC\Math\Fitting;

use MOC\Math\DataSeries;
use MOC\Math\Exception\GeneralMathException;
use MOC\Math\GaussJordan;
use MOC\Math\MathematicalFunction\NonLinearCombinationOfFunctions;
use MOC\Math\Matrix;
use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

/**
 * Class FittingEngine
 *
 * Mathematical fitting engine. This particular class uses the Levenberg-Marquardt method to solve the non-lineary
 * fitting of data to functions that depend on the parameters in a non-linear way.
 *
 * An example of a non-linear combination of functions is a Gauss:
 *
 * y(x) = a + b * exp(-((x-c)/2)^2)
 *
 * @package MOC\Math
 */
class NonLinearFittingEngine extends AbstractFittingEngine {

	/**
	 * @var float
	 */
	protected $alambda = 0.001;

	/**
	 * @var
	 */
	protected $chisqr;

	/**
	 * @var
	 */
	protected $previousChisqr;

	/**
	 * @var Matrix
	 */
	protected $alpha;

	/**
	 * @var array
	 */
	protected $trialParameters;

	/**
	 * @var int
	 */
	protected $numberOfIterations = 0;

	/**
	 * Fit data to a nonliner combination of functions.
	 *
	 * If you only wish to fit some of the parameters in the non-linear combination, then provide the
	 * $parameters to fit for with an array of the same length as the number of parameters, each value being
	 * either true or false depending on wheter to fit this parameters. Default is to fit all parameters.
	 *
	 * Note that the function must already be initialized with best-guess parameters.
	 *
	 * @param DataSeries $data The data to fit to
	 * @param MathematicalFunctionInterface $function The function to fit the data to
	 * @param array<boolean> $parametersToFitFor
	 */
	public function __construct(DataSeries $data, MathematicalFunctionInterface $function, array $parametersToFitFor = NULL) {
		if ($parametersToFitFor === NULL) {
			$parametersToFitFor = array_fill(0, $function->getDegreesOfFreedom(), TRUE);
		}
		$this->parametersToFitFor = $parametersToFitFor;

		$this->numberOfParameters = $function->getDegreesOfFreedom();

		foreach ($parametersToFitFor as $fitParameter) {
			if ($fitParameter) {
				$this->numberOfParameteresToFit++;
			}
		}
		if ($this->numberOfParameteresToFit == 0) {
			throw new \Exception('At least one parameters should be fitted for');
		}
		$this->function = $function;
		$this->data = $data;

		$this->covar = Matrix::emptyMatrix($this->numberOfParameteresToFit, $this->numberOfParameteresToFit);
		$this->solver = new GaussJordan();

		$this->initialize();
	}

	/**
	 * Do the actual fitting
	 *
	 * @param integer $maxSteps The maximum number of steps when iterating. Throws an exception is this is reached
	 * @return void
	 * @throws GeneralMathException
	 */
	public function fit($maxSteps = 1000) {

		for ($l = 0; $l< $maxSteps; $l++) {
			$prevChi = $this->chisqr;
			$this->iterate();
			$diffChisqrt = $prevChi - $this->chisqr ;
			if ($diffChisqrt != 0.00) {
				if ($diffChisqrt > 0 && $diffChisqrt < 0.000000001) {
					$this->numberOfIterations = $l;
					return;
				}
			}
		}
		$this->numberOfIterations = $maxSteps;
		throw new GeneralMathException('Unable to do nonLiniear fit. Convergence not reached after $maxStemps steps');
	}


	/**
	 * @param DataSeries $data
	 * @param NonLinearCombinationOfFunctions $function
	 * @param null $parametersToFitFor
	 */
	protected function initialize() {
		$this->mrqcof();
		$this->previousChisqr = $this->chisqr;
		$this->trialParameters = $this->function->getParameters();
	}

	/**
	 * Run a single iteration of the fitting algorithm
	 *
	 * @return void
	 */
	protected function iterate() {
		$da = Matrix::emptyMatrix($this->numberOfParameteresToFit,1);
		for ($j=0; $j < $this->numberOfParameteresToFit; $j++) {
			for ($k=0; $k < $this->numberOfParameteresToFit; $k++) {
				$this->covar->setValueAtPosition($j, $k, $this->alpha->getValueAtPosition($j, $k));
			}
			$this->covar->setValueAtPosition($j, $j, $this->alpha->getValueAtPosition($j, $j) * (1.0 + $this->alambda));
			$da->setValueAtPosition($j, 0, $this->beta->getValueAtPosition($j, 0));
		}

		$this->solver->solve($this->covar, $da);

		// Make a new try
		$j = 0;
		for ($l = 0; $l < $this->function->getDegreesOfFreedom(); $l++) {
			if ($this->parametersToFitFor[$l]) {
				$this->trialParameters[$l] = $this->trialParameters[$l] + $da->getValueAtPosition($j,0);
				$j++;
			}
		}

		$oldParams = $this->function->getParameters();
		$this->function->setParameters($this->trialParameters);
		$this->mrqcof();

		if ($this->chisqr < $this->previousChisqr) {
				//New solution is better. The function is already initalized to the new (and better) parameters.
			$this->alambda = 0.1 * $this->alambda;
			$this->previousChisqr = $this->chisqr;
			$this->beta = clone($da);
			$this->alpha = clone($this->covar);
		} else {
				// Old solution was better, reset parameters
			$this->chisqr = $this->previousChisqr;
			$this->function->setParameters($oldParams);
			$this->alambda = 10.0 * $this->alambda;
		}
	}

	/**
	 * @param DataSeries $data
	 * @param NonLinearCombinationOfFunctions $function
	 * @param null $parametersToFitFor
	 * @return array
	 * @throws \Exception
	 */
	protected function mrqcof() {
			//Initalize Alpha and beta
		$this->alpha = Matrix::emptyMatrix($this->numberOfParameteresToFit, $this->numberOfParameteresToFit);
		$this->beta = Matrix::emptyMatrix($this->numberOfParameteresToFit, 1);
		$this->chisqr = 0.00;

		//Summation loop over all data
		/** @var $point \MOC\Math\Point */
		foreach ($this->data as $i => $point) {

			$sig2i = 1.0/(pow($point->getStdDeviation(), 2));
			$dy = $point->getY() - $this->function->evaluateAtPoint($point->getX());
			$j = 0;
			for ($l = 0; $l < $this->numberOfParameters; $l++) {
				if ($this->parametersToFitFor[$l] == TRUE) {
					$wt = $this->function->evalueateNthBasesFunctionDerivedWithRespectToParamterAtPoint($point->getX(), $l) * $sig2i;
					$k = 0;
					for ($m = 0; $m <= $l; $m++) {
						if ($this->parametersToFitFor[$m] == TRUE) {
							$this->alpha->setValueAtPosition($j, $k, $this->alpha->getValueAtPosition($j, $k) +  $wt * $this->function->evalueateNthBasesFunctionDerivedWithRespectToParamterAtPoint($point->getX(), $m));
							$k++;
						}
					}
					$this->beta->setValueAtPosition($j, 0, $this->beta->getValueAtPosition($j,0) + $dy * $wt);
					$j++;
				}
			}
			$this->chisqr += $dy * $dy * $sig2i;
		}

			// Fill in so alpha is symmetric
		for ($j = 1; $j < $this->numberOfParameteresToFit; $j++) {
			for ($k = 0; $k < $j; $k++) {
				$this->alpha->setValueAtPosition($k, $j, $this->alpha->getValueAtPosition($j, $k));
			}
		}
	}

	/**
	 * After fitting return the covariance matrix. The diagonal elements represents the std deviation in the individual
	 * parameters.
	 *
	 * If some parameters where not fitted, the stdDeviation is set to one.
	 *
	 * @return Matrix
	 */
	public function getCovarianceMatrix() {
		//@Todo Call covsrt to expand into the full size!
		return $this->covar;
	}

	/**
	 * After fitting, return the value of chi squared.
	 *
	 * @return float;
	 */
	public function getChiSquared() {
		return $this->chisqr;
	}

	/**
	 * Return the number of iteraitons needed to reach convergence
	 * @return int
	 */
	public function getNumberOfIterations() {
		return $this->numberOfIterations;
	}


}
