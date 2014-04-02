<?php
namespace MOC\Math\Fitting;

use MOC\Math\DataSeries;

use MOC\Math\GaussJordan;
use MOC\Math\MathematicalFunction\LinearCombinationOfFunctions;
use MOC\Math\Matrix;
use MOC\Math\Fitting\FigureOfMerit\LeastSquares;
use MOC\Math\MathematicalFunction\MathematicalFunctionInterface;

abstract class AbstractFittingEngine {

	/**
	 * @var DataSeries
	 */
	protected $data;

	/**
	 * @var LinearCombinationOfFunctions
	 */
	protected $mathematicalFunction;

	/**
	 * @var array
	 */
	protected $parametersToFitFor;

	/**
	 * @var integer
	 */
	protected $numberOfParameteresToFit;

	/**
	 * @var integer
	 */
	protected $numberOfParameters;

	/**
	 * @var Matrix
	 */
	protected $covar;

	/**
	 * @var GaussJordan
	 */
	protected $solver;

	/**
	 * @var \MOC\Math\Matrix
	 */
	protected $beta;

	/**
	 * @param DataSeries $data
	 * @param LinearCombinationOfFunctions $mathematicalFunction
	 * @param array $parametersToFitFor
	 */
	public function __construct(DataSeries $data, LinearCombinationOfFunctions $function, array $parametersToFitFor = NULL) {
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
		$this->beta = Matrix::emptyMatrix($this->numberOfParameteresToFit,1);
		$this->solver = new GaussJordan();

	}

	/**
	 * @return float
	 */
	abstract public function getChiSquared();

	/**
	 * @return mixed
	 */
	abstract public function fit();
}