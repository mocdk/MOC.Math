<?php
namespace MOC\Math\Tests\Unit\Statistics\Math;

use MOC\Math\DataSeries;

class DataSeriesTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function fromArray() {
		$data = DataSeries::fromArray(array(
			array(0.00, 0.5, 1.00),
			array(1.00, 1.4, 1.00),
			array(2.00, 2.3, 0.8)
		));

		$this->assertEquals(3, count($data));
	}


	/**
	 * @test
	 */
	public function arrayAccess() {
		$data = DataSeries::fromArray(array(
			array(0.00, 0.5, 1.00),
			array(1.00, 1.4, 1.00),
			array(2.00, 2.3, 0.8)
		));

		$point = $data[1];
		$this->assertInstanceOf('MOC\Math\Point', $point);
		$this->assertEquals(1.00, $point->getX());
		$this->assertEquals(1.4, $point->getY());
		$this->assertEquals(1.00, $point->getError());
	}

	/**
	 * @test
	 */
	public function getDataValues() {
		$data = DataSeries::fromArray(array(
			array(0.00, 0.5, 1.00),
			array(1.00, 1.4, 1.00),
			array(2.00, 2.3, 0.8)
		));
		$this->assertEquals(array(0.5, 1.4, 2.3), $data->getDataValues());
	}
}
