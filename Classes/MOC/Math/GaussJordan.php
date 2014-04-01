<?php
namespace MOC\Math;

/**
 * Class GaussJordan
 *
 * Perform GaussJordan elimination of a matrix. Usefull for doing matrix inversions, or solving N equations of M unknown
 * problem. And used for fitting data.
 *
 * The implementation follows the recepies of the well known book Numerical Recepies
 *
 * @package MOC\Math
 */
class GaussJordan {

	/**
	 * Implementation of the gaussj algorithn from Numerical Recepies.
	 *
	 * Warning the matrixes a and b will we changed during the solving!
	 *
	 * Note that this might not be the most efficient way, and you should consider using LRU decomposition. Buts its
	 * quite stable.
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

			// find the pivot element by searching the entire matrix for its largest value, but excluding columns already reduced.
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

				//Now interchange row to move the pivot element to a diagonal
			$ipiv[$icol] = $ipiv[$icol] + 1;
			if( $irow != $icol) {
				$a->interchangeRows($irow, $icol);
				$b->interchangeRows($irow, $icol);
			}

			// Remember permutations to later
			$indxr[$i] = $irow;
			$indxc[$i] = $icol;
			if ($a->getValueAtPosition($icol, $icol) == 0) {
				throw new \Exception('Singullar matrix');
			}

			// Now divinde the found row to make the pivot element 1
			$pivinv = 1.0 / $a->getValueAtPosition($icol, $icol);
			$a->multiplyRowByNumber($icol, $pivinv);
			$b->multiplyRowByNumber($icol, $pivinv);

			// And reduce all other rows but the pivoted row with the value of the pivot row
			for ($ll=0; $ll < $n; $ll++) {
				if ($ll != $icol) {
					$dum = $a->getValueAtPosition($ll, $icol);
					$a->addMultipleOfOtherRowToRow((-1.0 * $dum), $icol, $ll);
					$b->addMultipleOfOtherRowToRow((-1.0 * $dum), $icol, $ll);
				}
			}
		} // End main loop over columns, and we're done!

	}
}