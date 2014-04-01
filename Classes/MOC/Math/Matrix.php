<?php
namespace MOC\Math;
use MOC\Math\Exception\GeneralMathException;
use MOC\Math\Exception\MatrixIndexOutOfBoundsException;

/**
 * Class Matrix
 *
 * Representation of a matrix of floats.
 * Data is internally stored as a two dimensional rows-first array.
 *
 * Includes various function on the matrix, like multiplication, but also methods that change the array like multiply
 * row by number, exchange rows etc. Generally methods that change the internal matrix, returns a reference to itself
 * and throws Exceptions if something goes wrong.
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
	 * Factory function for creating a MxM matrix with diagonal elements of 1 and all other set to 0
	 *
	 * @param integer $dimensions
	 * @return Matrix
	 */
	public static function identityMatrix($dimensions) {
		$data = array();
		for ($i = 0; $i < $dimensions; $i++) {
			$row = array_fill(0, $dimensions, 0.00);
			$row[$i] = 1.00;
			$data[] = $row;
		}
		return new Matrix($data);
	}

	/**
	 * Create a new MxN matrix initialized with a certain value (default is 0.00)
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
	 * Return the number of rows of this matrix
	 *
	 * @return int
	 */
	public function getNumberOfRows() {
		return count($this->data);
	}

	/**
	 * Return the numver of columns for this matrix
	 *
	 * @return int
	 */
	public function getNumberOfColumns() {
		if (!isset($this->data[0])) {
			return 0;
		}
		return count($this->data[0]);
	}

	/**
	 * Return the value at a certain position in the matrix.
	 *
	 * @param integer $row
	 * @param integer $column
	 * @return float
	 * @throws MatrixIndexOutOfBoundsException
	 */
	public function getValueAtPosition($row, $column) {
		if ($column > $this->getNumberOfCOlumns()) {
			throw new MatrixIndexOutOfBoundsException('Trying to access column ' . $column . ' of a matrix with only ' . $this->getNumberOfCOlumns() . ' columns');
		}
		if ($row > $this->getNumberOfRows()) {
			throw new MatrixIndexOutOfBoundsException('Trying to access row ' . $row . ' of a matrix with only ' . $this->getNumberOfRows() . ' rows');
		}
		return $this->data[$row][$column];
	}

	/**
	 * Set the value of the matrix at a certain position
	 *
	 * @param integer $row
	 * @param integer $column
	 * @param float $value
	 * @return void
	 * @throws MatrixIndexOutOfBoundsException
	 */
	public function setValueAtPosition($row, $column, $value) {
		if ($column > $this->getNumberOfCOlumns()) {
			throw new MatrixIndexOutOfBoundsException('Trying to set column ' . $column . ' of a matrix with only ' . $this->getNumberOfCOlumns() . ' columns');
		}
		if ($row > $this->getNumberOfRows()) {
			throw new MatrixIndexOutOfBoundsException('Trying to set row ' . $row . ' of a matrix with only ' . $this->getNumberOfRows() . ' rows');
		}
		$this->data[$row][$column] = $value;
	}

	/**
	 * @return string
	 */
	public function __toString() {
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
	 * @throws GeneralMathException
	 */
	public function multiplyWithVectorFromRight(Vector $vector) {
		if ($vector->getLength() !== $this->getNumberOfColumns()) {
			throw new GeneralMathException('Unable to multiply a matrix with ' . $this->getNumberOfColumns() . ' with a vector of length ' . $vector->getLength());
		}
		$newData = array();
		foreach ($this->data as $row) {
			$newRow = array();
			foreach ($row as $index => $value) {
				$newRow[] = $value * $vector->getIndex($index);
			}
			$newData[] = $newRow;
		}
		return new Matrix($newData);
	}

	/**
	 * Return the raw data array
	 *
	 * @return array
	 */
	public function getAsArray() {
		return $this->data;
	}

	/**
	 * Compare two matrixes
	 *
	 * Each value of the matrix is compared (non-stric using ==) agains each other. Returns TRUE if the matrices are equal.
	 *
	 * @param Matrix $matrix
	 * @return boolean
	 */
	public function equals(Matrix $matrix) {
		$isEqual = TRUE;
		if ($this->getNumberOfRows() !== $matrix->getNumberOfRows()) {
			return FALSE;
		}
		if ($this->getNumberOfCOlumns() !== $matrix->getNumberOfCOlumns()) {
			return FALSE;
		}
		foreach ($this->data as $rowNumber => $row) {
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
	 * Calculates it by Gauss Jordan elimination with partial pivoting.
	 *
	 * @return Matrix
	 * @throws GeneralMathException
	 */
	public function getInverse() {
		if ($this->getNumberOfRows() != $this->getNumberOfCOlumns()) {
			throw new GeneralMathException('Unable to calculate inverse of non-square matrix');
		}
		$matrix = clone($this);
		$matrix2 = self::identityMatrix($this->getNumberOfRows());
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
	 * @throws GeneralMathException
	 */
	public function expandToSize($newNumberOfColumns, $newNumberOfRows, $value = 0.00, $addRowsAndColumnsBefore = FALSE) {
		if ($newNumberOfColumns < $this->getNumberOfColumns() || $newNumberOfRows < $this->getNumberOfRows()) {
			throw new GeneralMathException('Can not shrink matrix');
		}

		for ($i = 0; $i < $newNumberOfRows; $i++) {
			$newData[$i] = array_fill(0, $newNumberOfColumns, $value);
		}
		$rowOffset = $addRowsAndColumnsBefore ? ($newNumberOfRows - $this->getNumberOfRows()) : 0;
		$columnOffset = $addRowsAndColumnsBefore ? ($newNumberOfColumns - $this->getNumberOfColumns()) : 0;
		foreach ($this->data as $rowNumber => $row) {
			foreach ($row as $columnNumber => $value) {
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
	 * @param integer|null $excludeRow
	 * @param integer|null $excludeColumn
	 * @return float
	 * @todo: Refactor to allow specifying more that one exlude row/col
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
	 *
	 * @param integer $row1
	 * @param integer $row2
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
	 * Used for matrix diagonalization
	 *
	 * @param integer $row
	 * @param float $number
	 * @return Matrix
	 * @throws GeneralMathException
	 * @throws MatrixIndexOutOfBoundsException
	 */
	public function multiplyRowByNumber($row, $number) {
		if ($number === 0.00) {
			throw new GeneralMathException('Unable to multiply row af matrix by 0');
		}
		if ($row < 0 || $row > $this->getNumberOfRows()) {
				throw new MatrixIndexOutOfBoundsException('Trying to modify the ' . $row . 'th row of a matrix with ' . $this->getNumberOfRows() . ' rows');
		}
		$numberOfColumns = $this->getNumberOfColumns();
		for ($l = 0; $l < $numberOfColumns; $l++) {
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
	 * @throws MatrixIndexOutOfBoundsException
	 */
	public function addMultipleOfOtherRowToRow($multiple, $rowToMultiplyWith, $rowToAddTo) {
		if ($rowToMultiplyWith < 0 || $rowToMultiplyWith > $this->getNumberOfRows()) {
			throw new MatrixIndexOutOfBoundsException('Trying to modify the ' . $rowToMultiplyWith . 'th row of a matrix with ' . $this->getNumberOfRows() . ' rows');
		}
		if ($rowToAddTo < 0 || $rowToAddTo > $this->getNumberOfRows()) {
			throw new MatrixIndexOutOfBoundsException('Trying to modify the ' . $rowToAddTo . 'th row of a matrix with ' . $this->getNumberOfRows() . ' rows');
		}
		$numberOfColumns = $this->getNumberOfColumns();
		for ($l = 0; $l < $numberOfColumns; $l++) {
			$this->data[$rowToAddTo][$l] = $this->data[$rowToAddTo][$l] + $this->data[$rowToMultiplyWith][$l] * $multiple;
		}
		return $this;
	}

}