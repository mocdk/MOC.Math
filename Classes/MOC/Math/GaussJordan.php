<?php
namespace MOC\Math;

/**
 * Class GaussJordan
 *
 * Perform GaussJordan elimination of a matrix
 *
 * @package MOC\Math
 */
class GaussJordan {

	/**
	 * Implementation of the gaussj algorithn from Numerical Recepies.
	 *
	 * Warning the matrixes a and b will we changed during the solving!
	 *
	 * @param Matrix $a
	 * @param Matrix $b
	 * @return void
	 */
	public function solve(Matrix $a, Matrix $b) {
		$n = $a->getNumberOfRows();
		if ($n !== $a->getNumberOfCOlumns()) {
			throw new \Exception('Input matrix a must be square');
		}
		if ($b->getNumberOfRows() !== $n) {
			throw new \Exception('Input matrix b must have the same number of columns as input a');
		}
		$m = $b->getNumberOfColumns();

		$ipiv = array_fill(0, $n, 0);
		$indxr = array_fill(0, $n, 0);
		$indxc = array_fill(0, $n, 0);
		for ($i=0; $i < $n; $i++) {
			$big = 0;
			for ($j = 0; $j < $n; $j++) {
				if ($ipiv[$j] != 1) {
					for ($k=0; $k < $n; $k++) {
						if ($ipiv[$k] == 0) {
							$absVal = abs($a->getValueAtPosition($j,$k));
							if ($absVal > $big) {
								$big = $absVal;
								$irow = $j;
								$icol = $k;
							}
						} elseif ($ipiv[$k] > 1) {
							throw new \Exception('Singular matrix in GaussJordan->solve');
						}
					}
				}
			}

			$ipiv[$icol] = $ipiv[$icol] + 1;
			if( $irow != $icol) {
				$a->interchangeRows($irow, $icol);
				$b->interchangeRows($irow, $icol);
			}
			$indxr[$i] = $irow;
			$indxc[$i] = $icol;
			if ($a->getValueAtPosition($icol, $icol) == 0) {
				throw new \Exception('Singullar matrix');
			}
			$pivinv = 1.0 / $a->getValueAtPosition($icol, $icol);

			$a->multiplyRowByNumber($icol, $pivinv);
			$b->multiplyRowByNumber($icol, $pivinv);

			// Do reduction (needs test and refactoring)
			for ($ll=0; $ll < $n; $ll++) {
				if ($ll != $icol) {
					$dum = $a->getValueAtPosition($ll, $icol);
					$a->addMultipleOfOtherRowToRow((-1.0 * $dum), $icol, $ll);
					$b->addMultipleOfOtherRowToRow((-1.0 * $dum), $icol, $ll);
				}
			}
		} // End main loop over columns
	}
}

				/*
				for ($l=0; $l < $n; $l++) {
					$dum = $a->getValueAtPosition($irow, $l);
					$a->setValueAtPoint($irow, $l, $a->getValueAtPosition($icol,$l));
					$a->setValueAtPoint($icol, $l, $dum);
				}
				for ($l=0; $l < $m; $l++) {
					$dum = $a->getValueAtPosition($irow, $l);
					$b->setValueAtPoint($irow, $l, $b->getValueAtPosition($icol,$l));
					$b->setValueAtPoint($icol, $l, $dum);
				}
				*/
