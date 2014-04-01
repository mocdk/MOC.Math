<?php
namespace MOC\Math\MathematicalFunction;

/**
 * Interface FunctionInterface
 *
 * A representation of a One dimensional mathematical function.
 *
 * @package MOC\Math
 */
interface MathematicalFunctionInterface {

	/**
	 * Return this function's degrees of freedom. This is the number of parameters used for describing the function.
	 *
	 * @return integer
	 */
	public function getDegreesOfFreedom();

	/**
	 * The internal parameters of function.
	 *
	 * @param array<float> $parameters
	 * @return void
	 */
	public function setParameters(array $parameters);

	/**
	 * Evaluate the function in the given point
	 *
	 * Given an x-variable, calculate a y-variable. The class will first be fitted with the required context in order to receive the information it needs
	 *
	 * @param float $x The x-coordinate to calculate the y-coordinate for
	 * @return float
	 */
	public function evaluateAtPoint($x);

	/**
	 * Return the name of this function
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Return the parameters which describe this curve. Will return an array of floats
	 *
	 * @return array<float>
	 */
	public function getParameters();

	/**
	 * Render this function as a string
	 *
	 * @return string
	 */
	public function __toString();
}
