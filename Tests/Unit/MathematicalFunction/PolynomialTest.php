<?php
namespace MOC\Math\Tests\Unit\Statistics\Math\MAthematicalFunction;

use MOC\Math\MathematicalFunction\LinearFunction;
use MOC\Math\MathematicalFunction\Polynomial;

class PolynomialTest extends \PHPUnit_Framework_TestCase {

	public function evaluateAtPointDataProvider() {
		return array(
			'zeroDegree' => array(
				'order' => 0,
				'parameters' => array(1.0), // y= 1
				'assertions' => array(
					array('point' => 0.0,'expected' => 1.0),
					array('point' => 1.0,'expected' => 1.0),
					array('point' => 2.0,'expected' => 1.0),
					array('point' => -2.0,'expected' => 1.0),
				)
			),
			'firstDegree' => array(
				'order' => 1,
				'parameters' => array(1.0,2.0), // y= 1 + 2x
				'assertions' => array(
					array('point' => 0.0,'expected' => 1.0),
					array('point' => 1.0,'expected' => 3.0),
					array('point' => 2.0,'expected' => 5.0),
					array('point' => -2.0,'expected' => -3.0),
				)
			),
			'secondDegree' => array(
				'order' => 2,
				'parameters' => array(1.0, 2.0, -1), // y= 1 + 2x -x^2
				'assertions' => array(
					array('point' => 0.0,'expected' => 1.0),
					array('point' => 1.0,'expected' => 2.0),
					array('point' => 2.0,'expected' => 1.0),
					array('point' => -2.0,'expected' => -7.0),
				)
			)
		);
	}

	/**
	 * @dataProvider evaluateAtPointDataProvider
	 * @test
	 */
	public function evaluateAtPoint($order, $parameters, $assertions) {
		$function = new Polynomial($order);
		$function->setParameters($parameters);
		foreach($assertions as $assertion) {
			$this->assertEquals($assertion['expected'], $function->evaluateAtPoint($assertion['point']));
		}
	}

	/**
	 * @test
	 */
	public function evaluateNthBasisFunctionAtPoint() {
		$function = new Polynomial(2);
		$function->setParameters(array(1.0, 2.0, -1));
		$this->assertEquals(1.0, $function->evaluateNthBasisFunctionAtPoint(2.0, 0));
		$this->assertEquals(2.0, $function->evaluateNthBasisFunctionAtPoint(2.0, 1));
		$this->assertEquals(4.0, $function->evaluateNthBasisFunctionAtPoint(2.0, 2));
	}

}
