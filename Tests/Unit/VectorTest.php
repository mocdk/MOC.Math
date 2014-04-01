<?php
namespace MOC\Math\Tests\Unit\Statistics\Math;

use MOC\Math\Vector;

class VectorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function norm() {
		$vector = new Vector(array(1.0, 1.0, 1.0));
		$this->assertEquals(sqrt(3), $vector->getNorm());

		$vector = new Vector(array(1.0, 2.0, 3.0));
		$this->assertEquals(sqrt(14), $vector->getNorm());

		$vector = new Vector(array(0.0, 0.0, 0.0));
		$this->assertEquals(0.0, $vector->getNorm());
	}

	/**
	 * @dataProvider dataProviderForGetLength
	 * @test
	 * @param $data
	 * @param $expectedLength
	 */
	public function length($data, $expectedLength) {
		$vector = new Vector($data);
		$this->assertEquals($expectedLength, $vector->getLength());
	}

	/**
	 * @test
	 */
	public function multiplyDot() {
		$vector1 = new Vector(array(1.0, 2.0, 3.0));
		$vector2 = new Vector(array(1.0, 2.0, 3.0));
		$this->assertEquals(14, $vector1->multiplyDot($vector2));
		$this->assertEquals(14, $vector2->multiplyDot($vector1));
	}

	/**
	 * @test
	 */
	public function getIndex() {
		$vector1 = new Vector(array(1.0, 2.0, 3.0));
		$this->assertEquals(2.0, $vector1->getIndex(1));
	}

	/**
	 * @return array
	 */
	public function dataProviderForGetLength() {
		return array(
			'twoDimensional' => array(
				'data' => array(1,1),
				'expectedLength' => 2
			),
			'zeroDimensional' => array(
				'data' => array(),
				'expectedLength' => 0
			),
			'oneDimensional' => array(
				'data' => array(1,2,3),
				'expectedLength' => 3
			)
		);
	}
}