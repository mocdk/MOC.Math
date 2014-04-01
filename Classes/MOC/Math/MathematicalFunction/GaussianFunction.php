<?php
namespace MOC\Math\MathematicalFunction;

use MOC\Math\Exception\InvalidDegreesOfFreedomException;
use MOC\Math\Exception\MathematicalFunctionNotInitializedException;
use TYPO3\Flow\Cache\Backend\NullBackend;

/**
 * Class GaussianFunction
 *
 * @package MOC\Math
 */
class GaussianFunction implements MathematicalFunctionInterface  {

	/**
	 * The height of the bell peak, or "a"
	 *
	 * @var float
	 */
	protected $peakHeight;

	/**
	 * The center of the bell peak, or "b"
	 *
	 * @var float
	 */
	protected $peakPosition;

	/**
	 * The width of the bell, or "c"
	 *
	 * @var float
	 */
	protected $width;

	/**
	 * The offset of the bell, or "d"
	 *
	 * @var float
	 */
	protected $offset;

	/**
	 * @return int
	 */
	public function getDegreesOfFreedom() {
		return 4;
	}

	/**
	 * Given an x-variable, calculate a y-variable
	 *
	 * @param float $x The x-coordinate to calculate the y-coordinate for
	 * @return float
	 */
	public function evaluateAtPoint($x) {
		if ($this->peakHeight === NULL || $this->peakPosition === NULL || $this->width === NULL || $this->offset === NULL ) {
			throw new MathematicalFunctionNotInitializedException();
		}
		return ($this->peakHeight * exp( - pow($x - $this->peakPosition, 2) / (2 * pow($this->width, 2)))) + $this->offset;
	}

	/**
	 * @param array $parameters
	 * @return void
	 */
	public function setParameters(array $parameters) {
		if (count($parameters) !== $this->getDegreesOfFreedom()) {
			throw new InvalidDegreesOfFreedomException('Number of parameters does not match degrees of freedom', 1395402670);
		}

		$this->peakHeight = $parameters[0];
		$this->peakPosition = $parameters[1];
		$this->width = $parameters[2];
		$this->offset = $parameters[3];
	}

	/**
	 * Return the string representation of this function
	 *
	 * @return string
	 */
	public function __toString() {
		$string = 'y(x) = ' . $this->peakHeight . 'exp(-(x - ' .  $this->peakPosition . ')^2 / ' . (2 * pow($this->width, 2)) . ')';
		if ($this->offset > 0) {
			$string .= ' + ' . $this->offset;
		}
		return $string;
	}

	/**
	 * Return the parameters which describe this curve. Will return an array of floats
	 *
	 * @return array<float>
	 */
	public function getParameters() {
		return array($this->peakHeight, $this->peakPosition, $this->width, $this->offset);
	}


	/**
	 * @return string
	 */
	public function getName() {
		return 'Gausian function';
	}

}
