<?php
namespace MOC\Math\MathematicalFunction;

/**
 * Interface NonLinearCombinationsOfFunctions
 *
 * @package MOC\Math
 */
interface NonLinearCombinationOfFunctions extends MathematicalFunctionInterface {

	/**
	 * Evaluate the n'th basis function of this linear combination.
	 *
	 * @param float $x
	 * @param integer $number
	 * @return float
	 */
	//public function evaluateNthBasisFunctionAtPoint($x, $number);

	/**
	 * Evalue the n'th basis function of this non-linear combination when derived with respect to one of the parameters
	 *
	 * @param float $x
	 * @param integer $number
	 * @return float
	 */
	public function evalueateNthBasesFunctionDerivedWithRespectToParamterAtPoint($x, $number);
}