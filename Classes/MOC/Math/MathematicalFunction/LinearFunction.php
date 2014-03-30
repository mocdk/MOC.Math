<?php
namespace MOC\Math\MathematicalFunction;

/**
 * Class LinearFunction
 *
 * Representation of the linear function y = ax + b
 *
 * This is a specical case of a the Polynomial with order 1.
 *
 * @package MOC\Math
 */
class LinearFunction extends Polynomial implements LinearCombinationOfFunctions  {

	/**
	 *
	 */
	public function __construct() {
		parent::__construct(1);
	}

	/**
	 * Return the string representation of this function
	 *
	 * @return string
	 */
	public function getName() {
		return 'Linear function';
	}

}