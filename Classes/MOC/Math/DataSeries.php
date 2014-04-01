<?php
namespace MOC\Math;
use MOC\Math\Exception\GeneralMathException;


/**
 * Class DataSeries
 *
 * Encapsulator for general series of datapoints.
 *
 * Each point consist of an x-value, a y-value and optionally an error, and is represented by the Point object
 *
 * @todo Make countable, iterable and arrayAccess
 *
 * @package MOC\Math
 */

class DataSeries extends \ArrayIterator implements \ArrayAccess {

	/**
	 * Create a new series of data from an array of values.
	 *
	 * @param array $data
	 * @throws Exception\GeneralMathException
	 * @return DataSeries
	 */
	public static function fromArray(array $data) {
		$points = array();
		foreach($data as $index => $dataPointArray) {
			$length = count($dataPointArray);
			if ($length < 2 || $length > 3) {
				throw new GeneralMathException('Index ' . $index .' of the input to DataSeries did not contain 2 or 3 values');
			}
			if (count($dataPointArray) == 2) {
				$dataPointArray[2] = 0.00;
			}
			$points[] = new Point($dataPointArray[0], $dataPointArray[1], $dataPointArray[2]);
		}
		return new DataSeries($points);
	}

	/**
	 * @return array
	 */
	public function getDataValues() {
		$out = array();
		foreach($this as $point) {
			$out[] = $point->getY();
		}
		return $out;
	}
}
