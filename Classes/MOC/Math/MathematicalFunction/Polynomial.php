<?php
namespace MOC\Math\MathematicalFunction;

use MOC\Math\Exception\InvalidDegreesOfFreedomException;
use MOC\Math\Exception\MathematicalFunctionNotInitializedException;

/**
 * Class Polynomial
 *
 * Representation of the linear function y = ax + b
 *
 * @package MOC\Math
 */
class Polynomial implements LinearCombinationOfFunctions  {

	/**
	 * @var array<float>
	 */
	protected $parameters;

	/**
	 * @var integer
	 */
	protected $order;

	/**
	 * @param integer $order
	 */
	public function __construct($order) {
		if ($order < 0) {
			throw new \Exception('Unable to make a polynomial of order less than 1');
		}
		$this->order = $order;
	}

	/**
	 * Evaluate the function in the given point
	 *
	 * Given an x-variable, calculate a y-variable. The class will first be fitted with the required context in order to receive the information it needs
	 *
	 * @param float $x The x-coordinate to calculate the y-coordinate for
	 * @return float
	 */
	public function evaluateAtPoint($x) {
		$sum = 0.0;
		for ($j=0; $j <= $this->order; $j++) {
			$sum += $this->parameters[$j] * pow($x, $j);
		}
		return $sum;
	}

	/**
	 * Evaluate the n'th bases function of this linear combination.
	 *
	 * @param float $x
	 * @param integer $number
	 * @return float mixed
	 */
	public function evaluateNthBasisFunctionAtPoint($x, $number) {
		if ($number > $this->order) {
			throw new \Exception('Trying to evalualte the $number basis of a polynomial of order ' . $this->order);
		}
		return pow($x, $number);
	}

	/**
	 * Return this function's degrees of freedom. This is the number of paramteres used for describing the function.
	 *
	 * @return integer
	 */
	public function getDegreesOfFreedom() {
		return $this->order + 1;
	}

	/**
	 * The internal parameters of function.
	 *
	 * @param array <float> $parameters
	 * @return void
	 */
	public function setParameters(array $parameters) {
		if (count($parameters) != $this->getDegreesOfFreedom()) {
			throw new \Exception('Number of parameters must match the degrees of freedom');
		}
		$this->parameters = $parameters;
	}

	/**
	 * Return the name of this function
	 * @return string
	 */
	public static function getName() {
		return 'Polynomial of the ' . $this->order . ' order';
	}

	/**
	 * Return the parameters which describe this curve. Will return an array of floats
	 *
	 * @return array<float>
	 */
	public function getParameters() {
		return $this->parameters;
	}


}