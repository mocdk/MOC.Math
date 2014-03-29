<?php
namespace MOC\Math;

use TYPO3\Flow\Annotations as Flow;
use Niras\Meia\Statistics\Data\DataSeries;


/**
 * Representation of various useful statistical calculations on dataseries.
 *
 * @package MOC\Math
 * @author Andres <andres@moc.net>
 */
class DataSeriesStatistics {

	/**
	 * The points we work with
	 *
	 * @var array
	 */
	protected $points;

	/**
	 * The x points of our points array
	 *
	 * @var array<float>
	 */
	protected $xPoints;

	/**
	 * The y points of our points array
	 *
	 * @var array<float>
	 */
	protected $yPoints;

	/**
	 * @var float
	 */
	protected $mean;

	/**
	 * Number of data points
	 *
	 * @var float
	 */
	protected $length;

	/**
	 * @var string
	 */
	protected $curveType;

	/**
	 * @var float
	 */
	protected $standardDeviation;

	/**
	 * @var float
	 */
	protected $variance;

	/**
	 * @var float
	 */
	protected $skewness;

	/**
	 * @var float
	 */
	protected $kurtosis;

	/**
	 * Construct a new DataSeriesStatistics from a $dataSeries. Use the static function generate() for public access.
	 *
	 * @param DataSeries $dataSeries
	 */
	protected function __construct(DataSeries $dataSeries) {
		$this->points = $dataSeries->getDataValuesAsCoordinates();
		$this->xPoints = $this->yPoints = array();

		foreach ($this->points as $point) {
			if (array_search($point['x'], $this->xPoints) !== FALSE) {
				throw new \Exception('Invalid dataset to retrieve statistics for - point data not valid, duplicate value in x-axis: "' . $point['x'] . '"', 1395405055);
			}

			$this->xPoints[] = $point['x'];
			$this->yPoints[] = $point['y'];
		}

		$this->length = count($this->yPoints);
		$this->mean = array_sum($this->yPoints) / $this->length;

		$yTotal = array_sum($this->yPoints);
		$xTotal = array_sum($this->xPoints);

		$xyCount = $xxCount = 0;

		foreach ($this->points as $point) {
			$xyCount += ($point['x'] * $point['y']);
			$xxCount += ($point['x'] * $point['x']);
		}

		$this->slope = (($this->length * $xyCount) - ($xTotal * $yTotal)) / (($this->length * $xxCount) - ($xTotal * $xTotal));
		$this->intercept = ($yTotal - ($this->slope * $xTotal)) / $this->length;

		$this->variance = $this->getMomentAboutTheMean(2);
		$this->standardDeviation = sqrt($this->variance);

		/**
		 * This works, from: http://www.phpbuilder.com/snippet/detail.php?type=snippet&id=297
		 * @author dhiranuntk@hotmail.com
		 */
		$array = $this->yPoints;
		$amount = count($array);
		if ($amount > 2) {
			for ($i = 0, $m2 = 0, $m3 = 0, $m4 = 0; $i < $amount; $i++) {
				$value = $this->yPoints[$i] - $this->mean;
				$m2 += pow($value, 2);
				$m3 += pow($value, 3);
				$m4 += pow($value, 4);
			}

			$m2 /= $amount;
			$m3 /= $amount;
			$m4 /= $amount;

			if ($m2 > 0) {
				$this->skewness = ($m3 / pow($m2, 1.5)) * sqrt($amount * ($amount - 1)) / ($amount - 2);

				if ($amount > 3) {
					$this->kurtosis = (($amount + 1) * (($m4/ pow($m2, 2)) - 3)) + 6 * (($amount - 1) / (($amount - 2) * ($amount - 3)));
				}
			}
		}
	}

	/**
	 * Generate a DataSeriesStatistics from a DataSeries
	 *
	 * @param DataSeries $dataSeries
	 * @return DataSeriesStatistics
	 */
	public static function generate(DataSeries $dataSeries) {
		return new DataSeriesStatistics($dataSeries);
	}

	/**
	 * @var array
	 */
	public function getPoints() {
		return $this->points;
	}

	/**
	 * @var float
	 */
	public function getLength() {
		return $this->length;
	}

	/**
	 * @var float
	 */
	public function getMean() {
		return $this->mean;
	}

	/**
	 * @return float
	 */
	public function getStandardDeviation() {
		return $this->standardDeviation;
	}

	/**
	 * @return float
	 */
	public function getVariance() {
		return $this->variance;
	}

	/**
	 * @var float
	 */
	public function getSlope() {
		return $this->slope;
	}

	/**
	 * @var float
	 */
	public function getIntercept() {
		return $this->intercept;
	}

	/**
	 * @return float
	 */
	public function getSkewness() {
		return $this->skewness;
	}

	/**
	 * @return float
	 */
	public function getKurtosis() {
		return $this->kurtosis;
	}

	/**
	 * Returns the kth central moment about the mean of this function's dataset
	 *
	 * @param integer $k The kth central moment to calculate
	 * @return float The kth central moment
	 */
	public function getMomentAboutTheMean($k) {

		$n = count($this->yPoints) - 1;

		$observations = array();
		foreach ($this->yPoints as $y) {
			$observations[] = pow($y - $this->mean, $k);
		}

		return array_sum($observations) / $n;

	}

	/**
	 * Returns the kth standardized moment
	 *
	 * @param integer $k The kth standardized moment to calculate
	 * @return float The kth central moment
	 */
	public function getStandardizedMoment($k) {

		return $this->getMomentAboutTheMean($k) / pow($this->standardDeviation, $k);

	}

}
