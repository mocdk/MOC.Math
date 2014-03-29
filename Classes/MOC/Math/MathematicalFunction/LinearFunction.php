<?php
namespace MOC\Math\MathematicalFunction;

use MOC\Math\Exception\InvalidDegreesOfFreedomException;
use MOC\Math\Exception\MathematicalFunctionNotInitializedException;

/**
 * Class LinearFunction
 *
 * Representation of the linear function y = ax + b
 *
 * @package MOC\Math
 */
class LinearFunction implements LinearCombinationOfFunctions  {

	/**
	 * @var float
	 */
	protected $coefficient;

	/**
	 * @var float
	 */
	protected $offset;

	/**
	 * Return this function's degrees of freedom. This is the number of paramteres used for describing the function.
	 *
	 * @return integer
	 */
	public function getDegreesOfFreedom() {
		return 2;
	}

	/**
	 * Set internal parameters. Ie. the cofficient of the linie function
	 *
	 * @param array<float> $parameters
	 * @throws \InvalidDegreesOfFreedomException
	 */
	public function setParameters(array $parameters) {
		if (count($parameters) !== $this->getDegreesOfFreedom()) {
			throw new InvalidDegreesOfFreedomException('Number of parameters does not match degrees of freedom', 1395402670);
		}
		$this->coefficient = floatVal($parameters[0]);
		$this->offset = floatval($parameters[1]);
	}

	/**
	 * Evaluate the function in the given point
	 *
	 * Given an x-variable, calculate a y-variable. The class will first be fitted with the required context in order to receive the information it needs
	 *
	 * @param float $x The x-coordinate to calculate the y-coordinate for
	 * @return float
	 * @throws \MOC\Math\Exception\MathematicalFunctionNotInitializedException
	 */
	public function evaluateAtPoint($x) {
		if ($this->coefficient === NULL || $this->offset === NULL) {
			throw new MathematicalFunctionNotInitializedException();
		}
		return $x * $this->coefficient + $this->offset;
	}

	/**
	 * Return the string representation of this function
	 * @return string
	 */
	public static function getName() {
		return 'Linear function';
	}


	/**
	 * Return the parameters which describe this curve. Will return an array of floats
	 *
	 * @return array<float>
	 */
	public function getParameters() {
		return array($this->coefficient, $this->offset);
	}

	/**
	 * @param integer $x
	 * @param integer $number
	 * @return float
	 * @throws \Exception
	 */
	public function evaluateNthBasisFunctionAtPoint($x, $number) {
		if ($number > $this->getDegreesOfFreedom()) {
			throw new \Exception('Unknown degree of freedom');
		}
		if ($number == 0) {
			return 1;
		}
		if ($number == 1) {
			return $x;
		}
	}

}