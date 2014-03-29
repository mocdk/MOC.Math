<?php
namespace MOC\Math;

/**
 * Class Matrix
 *
 * Reprentation of a matrix of floats. Data is internaly stores as a two dimensional rows-first array.
 *
 * @package MOC\Math
 */
class Matrix {

	/**
	 * @var array The internal data,
	 */
	private $data;

	/**
	 * Creates a new MxN matrix og floats
	 *
	 * The input must be an array of length M, each entry being an array og length N.
	 *
	 * ex: The matrix
	 *
	 * 1 0 1 2
	 * 0 1 2 3
	 *
	 * Should be constructed as
	 *
	 * array(
	 *   array(1, 0, 1, 2),
	 *   array(0, 1, 2, 3),
	 * );
	 *
	 * @param array $data
	 */
	public function __construct(array $data) {
		$this->data = $data;
	}

	/**
	 * @param integer $dimensions
	 * @return Matrix
	 */
	public static function identityMatrix($dimensions) {
		$data = array();
		for ($i = 0; $i < $dimensions; $i++) {
			$row = array_fill(0,$dimensions, 0.00);
			$row[$i] = 1.00;
			$data[] = $row;
		}
		return new Matrix($data);
	}

	/**
	 * Create a new MxN matrix initliazed with a certain value (default is 0.00)
	 *
	 * @param integer $rows
	 * @param integer $columns
	 * @param float $value
	 * @return Matrix
	 */
	public static function emptyMatrix($rows, $columns, $value = 0.00) {
		$data = array();
		for ($i = 0; $i < $rows; $i++) {
			$row = array_fill(0, $columns, $value);
			$data[] = $row;
		}
		return new Matrix($data);
	}

	/**
	 * @return int
	 */
	public function getNumberOfRows() {
		return count($this->data);
	}

	/**
	 * @return int
	 */
	public function getNumberOfColumns() {
		if (!isset($this->data[0])) {
			return 0;
		}
		return count($this->data[0]);
	}

	/**
	 * @param integer $row
	 * @param integer $column
	 * @return float
	 */
	function getValueAtPosition($row, $column) {
		if ($column > $this->getNumberOfCOlumns()) {
			throw new \Exception('Trying to access column ' . $column . ' of a matrix with only ' . $this->getNumberOfCOlumns() . ' columns');
		}
		if ($row > $this->getNumberOfRows()) {
			throw new \Exception('Trying to access row ' . $row . ' of a matrix with only ' . $this->getNumberOfRows() . ' rows');
		}
		return $this->data[$row][$column];
	}

	/**
	 * @param integer $row
	 * @param integer $column
	 * @param float $value
	 * @throws \Exception
	 */
	function setValueAtPoint($row, $column, $value) {
		if ($column > $this->getNumberOfCOlumns()) {
			throw new \Exception('Trying to set column ' . $column . ' of a matrix with only ' . $this->getNumberOfCOlumns() . ' columns');
		}
		if ($row > $this->getNumberOfRows()) {
			throw new \Exception('Trying to set row ' . $row . ' of a matrix with only ' . $this->getNumberOfRows() . ' rows');
		}
		$this->data[$row][$column] = $value;
	}

	/**
	 *
	 */
	function __toString() {
		$content = '';
		foreach ($this->data as $i => $row) {
			foreach ($row as $j => $value) {
				$content .= sprintf("%6.4f\t", $value);
			}
			$content .= PHP_EOL;
		}
		return $content;
	}

	/**
	 * Multiply from right with a vector. A new matrix is returned.
	 *
	 * @param Vector $vector
	 * @return Matrix
	 */
	public function multiplyWithVectorFromRight(Vector $vector) {
		if ($vector->getLength() !== $this->getNumberOfColumns()) {
			throw new \Exception('Unable to multiply a matrix with ' . $this->getNumberOfColumns() . ' with a vector of length ' . $vector->getLength());
		}
		$newData = array();
		foreach ($this->data as $row) {
			$newRow = array();
			foreach($row as $index => $value) {
				$newRow[] = $value * $vector->getIndex($index);
			}
			$newData[] = $newRow;
		}
		return new Matrix($newData);
	}

	/**
	 * @return array
	 */
	public function getAsArray() {
		return $this->data;
	}

	/**
	 * Compare two matrixes
	 *
	 * @param Matrix $matrix
	 */
	public function equals(Matrix $matrix) {
		$isEqual = TRUE;
		if ($this->getNumberOfRows() !== $matrix->getNumberOfRows()) {
			return FALSE;
		}
		if ($this->getNumberOfCOlumns() !== $matrix->getNumberOfCOlumns()) {
			return FALSE;
		}
		foreach($this->data as $rowNumber => $row) {
			foreach ($row as $columnNumber => $value) {
				if ($value != $matrix->getValueAtPosition($rowNumber, $columnNumber)) {
					return FALSE;
				}
			}
		}
		return $isEqual;
	}

	/**
	 * Compute the inverse.
	 *
	 * Caclucates it by Gauss Jordan elimination with partial pivoting.
	 *
	 * @return Matrix
	 * @throws \Exception
	 */
	public function getInverse() {
		if ($this->getNumberOfRows() != $this->getNumberOfCOlumns()) {
			throw new \Exception('Unable to calculate inverse of non-square matrix');
		}
		$matrix = clone($this);
		$matrix2 = Matrix::identityMatrix($this->getNumberOfRows());
		$solver = new GaussJordan();
		$solver->solve($matrix, $matrix2);
		return $matrix2;
	}

	// ********************* Rows used for various algorithms like GausJoran *********************

	/**
	 * Expand a matrix into a new and bigger size. New elements will be initialize to the value of $value
	 *
	 * Notice that the matrix is actually changed!
	 *
	 * @param integer $newNumberOfColumns
	 * @param integer $newNumberOfRows
	 * @param float $value Value to add to the matrix position Default is 0.0
	 * @param boolean $addRowsAndColumnsBefore If set to TRUE, the elements will be added before (both row- and columnwise. Default is to add them after.
	 * @return Matrix reference to it self
	 */
	public function expandToSize($newNumberOfColumns, $newNumberOfRows, $value = 0.00, $addRowsAndColumnsBefore = FALSE) {
		if ($newNumberOfColumns < $this->getNumberOfColumns() || $newNumberOfRows < $this->getNumberOfRows()) {
			throw new \Exception('Can not shrink matrix');
		}

		for ($i = 0; $i < $newNumberOfRows; $i++) {
			$newData[$i] = array_fill(0,$newNumberOfColumns, $value);
		}
		$rowOffset = $addRowsAndColumnsBefore ? ($newNumberOfRows - $this->getNumberOfRows()) : 0;
		$columnOffset = $addRowsAndColumnsBefore ? ($newNumberOfColumns - $this->getNumberOfColumns()) : 0;
		foreach ($this->data as $rowNumber => $row) {
			foreach($row as $columnNumber => $value) {
				$newData[$rowNumber + $rowOffset][$columnNumber + $columnOffset] = $value;
			}
		}
		$this->data = $newData;
		return $this;
	}

	/**
	 * Find the biggest absolute value of the matix, possibly excluding a row and/or column
	 *
	 * This us used by various algoriths, like Gauss-Jordan, to choose pivot element
	 *
	 * @todo: Refactor to allow specifying more that one exlude row/col
	 *
	 * @param integer|null $exclueRow
	 * @param integer|null $exclueColumn
	 * @return float
	 */
	public function findBiggestValue($excludeRow = NULL, $excludeColumn = NULL) {
		$biggestValue = 0.0;
		$biggestRowNumber = 0;
		$biggestColumnNumber = 0;
		foreach ($this->data as $rowNumber => $row) {
			foreach ($row as $columnNumber => $value) {
				if ($excludeRow === NULL || $excludeRow !== $rowNumber) {
					if ($excludeColumn === NULL || $excludeColumn !== $columnNumber) {
						$absoluteValue = abs($value);
						if ($absoluteValue > $biggestValue) {
							$biggestValue = $absoluteValue;
							$biggestColumnNumber = $columnNumber;
							$biggestRowNumber = $rowNumber;
						}
					}
				}
			}
		}
		return array(
			'value' => $biggestValue,
			'row' => $biggestRowNumber,
			'column' => $biggestColumnNumber,
		);
	}

	/**
	 * Interchange two rows of the matrix. Changes the matrix!
	 *
	 * This operation changes the actual object, and return itself in order to be able to chain it.
	 * @param $row1
	 * @param $row2
	 * @return Matrix
	 */
	public function interchangeRows($row1, $row2) {
		$temp = $this->data[$row2];
		$this->data[$row2] = $this->data[$row1];
		$this->data[$row1] = $temp;
		return $this;
	}

	/**
	 * Multiply a column of a matrix by a non-zero number
	 *
	 * Used for matrix diagonlization
	 *
	 * @param integer $row
	 * @param float $number
	 * @return Matrix
	 * @throws \Exception
	 */
	public function multiplyRowByNumber($row, $number) {
		if ($number === 0.00) {
			throw new \Exception('Unable to multiply row af matrix by 0');
		}
		if ($row < 0 || $row > $this->getNumberOfRows()) {
				throw new \Exception('Trying to modify the ' . $row . 'th row of a matrix with ' . $this->getNumberOfRows() . ' rows');
		}
		for ($l= 0; $l < $this->getNumberOfCOlumns(); $l++) {
			$this->data[$row][$l] = $this->data[$row][$l] * $number;
		}
		return $this;
	}

	/**
	 * Add non-zero multiple one row to another row
	 *
	 * @param float $multiple The multiple of $rowTwo multiplied onto $rowOne
	 * @param integer $rowToMultiplyWith The row to change.
	 * @param integer $rowToAddTo The row to multiply with
	 * @return Matrix
	 * @throws \Exception
	 */
	public function addMultipleOfOtherRowToRow($multiple, $rowToMultiplyWith, $rowToAddTo) {
		if ($rowToMultiplyWith < 0 || $rowToMultiplyWith > $this->getNumberOfRows()) {
			throw new \Exception('Trying to modify the ' . $rowToMultiplyWith . 'th row of a matrix with ' . $this->getNumberOfRows() . ' rows');
		}
		if ($rowToAddTo < 0 || $rowToAddTo > $this->getNumberOfRows()) {
			throw new \Exception('Trying to modify the ' . $rowToAddTo . 'th row of a matrix with ' . $this->getNumberOfRows() . ' rows');
		}
		for ($l= 0; $l < $this->getNumberOfColumns(); $l++) {
			$this->data[$rowToAddTo][$l] = $this->data[$rowToAddTo][$l] + $this->data[$rowToMultiplyWith][$l] * $multiple;
		}
		return $this;
	}

}