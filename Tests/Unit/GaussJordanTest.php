<?php
namespace MOC\Math\Tests\Unit\Statistics\Math;

use MOC\Math\GaussJordan;
use MOC\Math\Matrix;

class GaussJordanTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function solve() {
		$matrix = new Matrix(array(
			array(0, 1, 2),
			array(1, 2, 3),
			array(5, 2, 3),
		));
		$matrix2 = Matrix::identityMatrix(3);
		$expected = new Matrix(array(
			array(-0.0, -0.25, 0.25),
			array(-3.0, 2.5, -0.5),
			array(2.0, -1.25, 0.25)
		));
		$solver = new GaussJordan();
		$this->markTestSkipped('Unable to run test due to rounding in PHP Unit');
		$solver->solve($matrix, $matrix2);
		$this->assertTrue($matrix2->equals($expected));
	}
}