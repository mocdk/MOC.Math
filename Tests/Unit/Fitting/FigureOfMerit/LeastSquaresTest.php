<?php
namespace MOC\Math\Tests\Unit\Statistics\Math;

use MOC\Math\GaussJordan;
use MOC\Math\Matrix;
use Niras\Meia\Statistics\Data\DataSeries;

class LeastSquare extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function calculate() {
		$data = new DataSeries(array(
			array(1.0, 1.0),
			array(1.0, 2.0),
		));

	}
}