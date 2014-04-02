<?php
namespace MOC\Math\MathematicalFunction;

use MOC\Math\Exception\GeneralMathException;
use MOC\Math\Exception\InvalidDegreesOfFreedomException;
use MOC\Math\Exception\MathematicalFunctionNotInitializedException;
use TYPO3\Flow\Cache\Backend\NullBackend;

/**
 * Class GaussianFunction
 *
 * @package MOC\Math
 */
class GaussianFunction implements NonLinearCombinationOfFunctions  {

	/**
	 * The height of the bell peak, or "a"
	 *
	 * @var float
	 */
	protected $peakHeight;

	/**
	 * The center of the bell peak, or "b"
	 *
	 * @var float
	 */
	protected $peakPosition;

	/**
	 * The width of the bell, or "c"
	 *
	 * @var float
	 */
	protected $width;

	/**
	 * The offset of the bell, or "d"
	 *
	 * @var float
	 */
	protected $offset;

	/**
	 * @return int
	 */
	public function getDegreesOfFreedom() {
		return 4;
	}

	/**
	 * Given an x-variable, calculate a y-variable
	 *
	 * @param float $x The x-coordinate to calculate the y-coordinate for
	 * @return float
	 */
	public function evaluateAtPoint($x) {
		if ($this->peakHeight === NULL || $this->peakPosition === NULL || $this->width === NULL || $this->offset === NULL ) {
			throw new MathematicalFunctionNotInitializedException();
		}
		return ($this->offset + $this->peakHeight * exp( - pow( ($x - $this->peakPosition) / $this->width, 2)));
	}

	/**
	 * @param array $parameters
	 * @return void
	 */
	public function setParameters(array $parameters) {
		if (count($parameters) !== $this->getDegreesOfFreedom()) {
			throw new InvalidDegreesOfFreedomException('Number of parameters does not match degrees of freedom', 1395402670);
		}
		$this->offset = $parameters[0];
		$this->peakHeight = $parameters[1];
		$this->peakPosition = $parameters[2];
		$this->width = $parameters[3];
	}

	/**
	 * Return the string representation of this function
	 *
	 * @return string
	 */
	public function __toString() {
		return sprintf('y(x) = %6.4f + %6.4f * exp( - ((x - %6.4f) / %6.4f)^2)', $this->offset, $this->peakHeight, $this->peakPosition, $this->width);
	}

	/**
	 * Return the parameters which describe this curve. Will return an array of floats
	 *
	 * @return array<float>
	 */
	public function getParameters() {
		return array($this->offset, $this->peakHeight, $this->peakPosition, $this->width);
	}


	/**
	 * @return string
	 */
	public function getName() {
		return 'Gausian function';
	}

	/**
	 * Evalue the n'th basis function of this non-linear combination when derived with respect to one of the parameters
	 *
	 * @param float $x
	 * @param integer $parameterNumber
	 * @return float
	 */
	public function evalueateNthBasesFunctionDerivedWithRespectToParamterAtPoint($x, $parameterNumber) {
		$arg = ($x - $this->peakPosition) / $this->width;
		$ex = exp( - pow($arg, 2));
		$fac = $this->peakHeight * $ex * 2.0 * $arg;
		switch($parameterNumber) {
			case 0:
				return 0;
				break;
			case 1:
				return $ex;
				break;
			case 2:
				return $fac / $this->width;
				break;
			case 3:
				return $fac * $arg / $this->width;
				break;
			default:
				throw new GeneralMathException('Unknown parameter (' . $parameterNumber . ') to derive from. Gaussian only has 4 parameters.');
		}
	}


}
