<?php
namespace Niras\Meia\Tests\Unit\Statistics\Math;

use MOC\Math\Matrix;
use MOC\Math\Vector;

class MatrixTest extends \PHPUnit_Framework_TestCase {

	public function dataProviderForNumberOfRows() {
		return array(
			'oneXone' => array(
				'data' => array(array(1)),
				'rows' => 1
			),
			'twoXone' => array(
				'data' => array(array(1), array(2)),
				'rows' => 2
			),
			'oneXtwo' => array(
				'data' => array(array(1,2)),
				'rows' => 1
			)
		);
	}

	public function dataProviderForNumberOfColumns() {
		return array(
			'oneXone' => array(
				'data' => array(array(1)),
				'columns' => 1
			),
			'twoXone' => array(
				'data' => array(array(1), array(2)),
				'columns' => 1
			),
			'oneXtwo' => array(
				'data' => array(array(1,2)),
				'columns' => 2
			)
		);
	}

	/**
	 * @dataProvider dataProviderForNumberOfRows
	 * @test
	 */
	public function getNumberOfRows($data, $rows) {
		$matrix = new Matrix($data);
		$this->assertEquals($matrix->getNumberOfRows(), $rows);
		//$this->assertEquals($matrix->getNumberOfCOlumns(), 3);
	}

	/**
	 * @dataProvider dataProviderForNumberOfColumns
	 * @test
	 */
	public function getNumberOfColumns($data, $columns) {
		$matrix = new Matrix($data);
		$this->assertEquals($matrix->getNumberOfCOlumns(), $columns);
	}

	/**
	 * @test
	 */
	public function getValueAtPosition() {
		$matrix = new Matrix(array(
			array(1.0, 1.0),
			array(2.0, 2.0),
			array(3.0, 3.0)
		));
		$this->assertEquals(1.0, $matrix->getValueAtPosition(0,0));
		$this->assertEquals(3.0, $matrix->getValueAtPosition(2,0));
	}

	/**
	 * @test
	 */
	public function multiplyWithVectorFromRight() {
		$matrix = new Matrix(array(
			array(1.0, 1.0),
			array(2.0, 2.0),
			array(3.0, 3.0)
		));
		$vector = new Vector(array(1.0, 2.0));
		$newMatrix = $matrix->multiplyWithVectorFromRight($vector);
		$expected = new Matrix(array(
			array(1.0, 2.0),
			array(2.0, 4.0),
			array(3.0, 6.0)
		));
		$this->assertTrue($newMatrix->equals($expected));
	}

	public function dataProviderForEquals() {
		return array(
			'oneXoneEqual' => array(
				'matrix1' => new Matrix(array(array(1))),
				'matrix2' => new Matrix(array(array(1))),
				'expected' => TRUE
			),
			'twoXtwoEqual' => array(
				'matrix1' => new Matrix(array(array(1,2), array(3,4))),
				'matrix2' => new Matrix(array(array(1,2), array(3,4))),
				'expected' => TRUE
			),
			'twoXtwoUnEquals' => array(
				'matrix1' => new Matrix(array(array(1,2), array(3,4))),
				'matrix2' => new Matrix(array(array(1,2), array(3,5))),
				'expected' => FALSE
			),
			'unEqualSize' => array(
				'matrix1' => new Matrix(array(array(1,2), array(3,4))),
				'matrix2' => new Matrix(array(array(1,2,5), array(3,4,5))),
				'expected' => FALSE
			),
		);
	}

	/**
	 * @param $matrix1
	 * @param $matrix2
	 * @test
	 * @dataProvider dataProviderForEquals
	 */
	public function testEquals(Matrix $matrix1,Matrix $matrix2, $expected) {
		$this->assertEquals($expected, $matrix1->equals($matrix2));
	}

	/**
	 * @test
	 */
	public function interchangeRows() {
		$matrix = new Matrix(array(
			array(1.0, 1.0),
			array(2.0, 2.0),
			array(3.0, 3.0)
		));
		$expected = new Matrix(array(
			array(2.0, 2.0),
			array(1.0, 1.0),
			array(3.0, 3.0)
		));
		$this->assertTrue($matrix->interchangeRows(0,1)->equals($expected));
		$this->assertTrue($matrix->interchangeRows(1,0)->equals($matrix));
	}

	/**
	 * @test
	 */
	public function multiplyRowByNumber() {
		$matrix = new Matrix(array(
			array(1.0, 4.0),
			array(2.0, 5.0),
			array(3.0, 6.0)
		));
		$expected = new Matrix(array(
			array(1.0, 4.0),
			array(3.0, 7.5),
			array(3.0, 6.0)
		));
		$this->assertTrue($matrix->multiplyRowByNumber(1, 1.5)->equals($expected));
	}

	/**
	 * @test
	 */
	public function addMultipleOfOtherRowToRow() {
		$matrix = new Matrix(array(
			array(1.0, 4.0),
			array(2.0, 5.0),
			array(3.0, 6.0)
		));
		$expected = new Matrix(array(
			array(1.0, 4.0),
			array(3.5, 11.0),
			array(3.0, 6.0)
		));
		$this->assertTrue($matrix->addMultipleOfOtherRowToRow(1.5, 0, 1)->equals($expected));
	}

	public function findBiggestValueProvider() {
		return array(
			array (
				'matrix' => new Matrix(array(
					array(1.0, 4.0),
					array(2.0, 5.0),
					array(3.0, 6.0)
				)),
				'excludeRow' => NULL,
				'excludeColumn' => NULL,
				'expectedValue' => 6.0,
				'expectedRow' => 2,
				'expectedColumn' => 1
			),
			array (
				'matrix' => new Matrix(array(
					array(1.0, 4.0),
					array(2.0, 5.0),
					array(3.0, 6.0)
				)),
				'excludeRow' => 2,
				'excludeColumn' => NULL,
				'expectedValue' => 5.0,
				'expectedRow' => 1,
				'expectedColumn' => 1
			),
			array (
				'matrix' => new Matrix(array(
					array(1.0, 4.0),
					array(2.0, 5.0),
					array(3.0, 6.0)
				)),
				'excludeRow' => NULL,
				'excludeColumn' => 1,
				'expectedValue' => 3.0,
				'expectedRow' => 2,
				'expectedColumn' => 0
			),
			array (
				'matrix' => new Matrix(array(
					array(1.0, 4.0),
					array(2.0, 5.0),
					array(3.0, 6.0)
				)),
				'excludeRow' => 2,
				'excludeColumn' => 0,
				'expectedValue' => 5.0,
				'expectedRow' => 1,
				'expectedColumn' => 1
			)

		);
	}

	/**
	 * @dataProvider findBiggestValueProvider
	 * @test
	 */
	public function findBiggestValue($matrix, $excludeRow, $excludeColumn, $expectedValue, $expectedRow, $expectedColumn) {
		$result = $matrix->findBiggestValue($excludeRow, $excludeColumn);
		$this->assertEquals($expectedValue, $result['value']);
		$this->assertEquals($expectedRow, $result['row']);
		$this->assertEquals($expectedColumn, $result['column']);
	}

	/**
	 * @test
	 */
	public function expandToSize() {

		$matrix = new Matrix(array(
			array(1.0, 4.0),
			array(2.0, 5.0),
			array(3.0, 6.0)
		));
		$expected = new Matrix(array(
			array(1.0, 4.0, 0.00),
			array(2.0, 5.0, 0.00),
			array(3.0, 6.0, 0.00),
			array(0.0, 0.0, 0.0)
		));
		$this->assertTrue($matrix->expandToSize(3,4)->equals($expected));

		$matrix = new Matrix(array(
			array(1.0, 4.0),
			array(2.0, 5.0),
			array(3.0, 6.0)
		));
		$expected = new Matrix(array(
			array(0.0, 0.0, 0.0),
			array(0.0, 1.0, 4.0),
			array(0.0, 2.0, 5.0),
			array(0.0, 3.0, 6.0),
		));
		$this->assertTrue($matrix->expandToSize(3,4, 0.00, TRUE)->equals($expected));
	}

}
