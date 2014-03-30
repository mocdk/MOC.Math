<?php
namespace MOC\Math\MathematicalFunction;

/**
 * Interface LinearCombinationsOfFunctions
 *
 * Representation of a linear combination of functions. This could be something like a polynomial
 *
 *  y = sum_[i:1..n] (a[i] * x^(i-1))
 *
 * , a sum of sines and cosines or any other function that can be represented as linear combinations of
 * basis functions.
 *
 * Note that the individual basisfunctions can be wildly unlinear with respect to x. The linearity here, is in
 * respect to its dependence of the parameters a[1..n]
 *
 * @package MOC\Math
 */
interface LinearCombinationOfFunctions extends MathematicalFunctionInterface {

	/**
	 * Evaluate the n'th bases function of this linear combination.
	 *
	 * @param float $x
	 * @param integer $number
	 * @return float mixed
	 */
	public function evaluateNthBasisFunctionAtPoint($x, $number);
}