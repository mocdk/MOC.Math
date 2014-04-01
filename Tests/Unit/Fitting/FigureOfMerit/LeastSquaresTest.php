<?php
namespace MOC\Math\Tests\Unit\Statistics\Math;

use MOC\Math\GaussJordan;
use MOC\Math\Matrix;
use MOC\Math\DataSeries;

class LeastSquare extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function calculate() {
		$data = DataSeries::fromArray(array(
			array(1.0, 1.0),
			array(1.0, 2.0),
		));


	}
}