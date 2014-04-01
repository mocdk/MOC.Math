<?php
namespace MOC\Math\Tests\Unit\Statistics\Math\MAthematicalFunction;

use MOC\Math\MathematicalFunction\LinearFunction;

class LinearFunctionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function evaluateAtPoint() {
		$function = new LinearFunction();
		$function->setParameters(array(1.0,1.0)); // y = 1 + x
		$this->assertEquals(1.0, $function->evaluateAtPoint(0.0));
		$this->assertEquals(2.0, $function->evaluateAtPoint(1.0));
		$this->assertEquals(6.0, $function->evaluateAtPoint(5.0));
		$this->assertEquals(-4.0, $function->evaluateAtPoint(-5.0));

		$function->setParameters(array(2.0,3.0)); // y = 2 + 3x
		$this->assertEquals(-10.0, $function->evaluateAtPoint(-4.0));
		$this->assertEquals(8.0, $function->evaluateAtPoint(2.0));
	}

	/**
	 * @test
	 */
	public function evaluateNthBasisFunctionAtPoint() {
		$function = new LinearFunction();
		$function->setParameters(array(1.0, 2.0)); // y = 1 + 2x
		$this->assertEquals(1.0, $function->evaluateNthBasisFunctionAtPoint(2.0, 0));
		$this->assertEquals(2.0, $function->evaluateNthBasisFunctionAtPoint(2.0, 1));
	}

}
