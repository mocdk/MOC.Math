MOC.Math
========

Math library for PHP. Include Matrix manipulation, equation solving and linear and un-linear fitting

Some of the matrix calculations could also be done using the PHP lapack extensions.
The MOC/Math package is a native PHP implementation which is bound to be slower, but does not require any external
libraries.

The library is not feature complete, and contains just enough to do linear and non-linear fitting. The algorithms are
inspired by the very excellent book "Numerial recepies".

Installing
----------

Easiest way is to include it with composer in your existing composer project.


```sh
composer require moc/math
```

And then start using is directly in your project.

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use \MOC\Math\DataSeries;
$data = DataSeries::fromArray(array(
	array(0, 1.0, 0.1),
	array(1, 1.5, 0.2),
	array(2, 3.0, 0.09),
	array(3, 4.8, 0.11),
	array(4, 6.1, 0.15)
));
foreach($data as $point) {
	printf("X: %4.2f\tY: %4.2f\n", $point->getX(), $point->getY());
}
```



Matrix and vectors
------------------

The matrix class is constructed with a two dimensional rows-first array. Example

```php
$matrix = new Matrix(array(
	array(1, 2, 5),
	array(5, 2, 7),
	array(3, 9, 2)
));
```

The Matrix also has tro factory classes for creating empty and identity classes

```php
 $identity = Matrix::identity(2)
```

Will create the 2x2 matrix with diagonal elements of 1 and rest set to 0

```php
$empty = Matrix::emptyMatrix(2,2)
```

Will create a 2x2 matrix with all elements set to 0.

The matrix class includes methods like

* multiplyWithVectorFromRight
* equals
* getInverse

The vector class is constructed by

```php
$vector = new Vector(array(2,3,4);
```
And includes methods for finding norm, length etc.

DataSeries and Point
--------------------

The Point class represents a single point in the XY plane, with a possible error attached to it.

To create a point x=0, y=1 with an (optional) error of 0.5, use this

```php
$point = new \MOC\Math\Point(array(0.00, 1.00, 0.5);
```

A DataSeries object is used for containing a series of datapoints. Its constructor takes an array of Point objects, but
it also contains convenient factory methods for creating DataSeries from arrays.

```php
$data = DataSeries::fromArray(array(
array(0, 1.0),
array(1, 1.5),
array(2, 3.0),
array(3, 4.8),
array(4, 6.1)
));
```

With errors attached to each point

```php
$data = DataSeries::fromArray(array(
	array(0, 1.0, 0.1),
	array(1, 1.5, 0.2),
	array(2, 3.0, 0.09),
	array(3, 4.8, 0.11),
	array(4, 6.1, 0.15)
));
```

A dataseries implements countable, ArrayAccess, and Iterator, so it can be used like this

```php

foreach ($data as $point) {
	// Do stuff with $point which is now a \MOC\Math\Point object
}

print count($data);

print "Point 2: " . $data[2]->getY();

```

GaussJordan elimination
-----------------------

Included is also an implementation of the GaussJordan elimination algorithm used to diagonalize matrixes. This is useful
when finding inverse matrixes, or when solving N equations in M unknown problems, or just fitting data to model data.

```php

$solver = new \MOC\Math\GaussJordan();
$solver->solve($matrixA, $matrixB);
```

Note that the method solve actually alters the matrixes $matrixA and $matrixB. The $matrix B must have as many rows as the
$matrixA has columns, otherwise an exception is thrown.

After calling solve, the $matrixA will be the identity matrix, and all operations needed to make $matrixA this, are applied
to $matrixB as well. So if $matrixA is a square matrix and $matrixB is the identiy matrix, a call to solve will make $matrixB
the inverse of $matrixA.

The algorithm uses partial pivoting.

Linear regression or linear least squares
-----------------------------------------

The main purpose of the Math package is to provide a fitting engine for fitting a dataset to a given model. Part of that
is linear regression where the data is fitted to a model that can be described as a linear combination of basisfunctions.
The individual basisfunction can vary unlinearly in x, but can not be dependent on the parameters.

##### Example:

Fit a dataset to the function y(x) = a + bx + cx^2

The individual basis function are 1, x and x^2 and we wish to find the parameters a, b, c that makes the model fit our
data best using a least-squares linear regression.

This can be done by calling using the LinearFitingEngine

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use \MOC\Math\DataSeries;
use \MOC\Math\Fitting\LinearFittingEngine;
use \MOC\Math\MathematicalFunction\Polynomial;

$data = DataSeries::fromArray(array(
	array(0, 1.0),
	array(1, 1.5),
	array(2, 3.0),
	array(3, 4.8),
	array(4, 6.1)
));
$functionToFitTo = new Polynomial(2);
$fitter = new LinearFittingEngine($data, $functionToFitTo);
$fitter->fit();
print "Result: " . $functionToFitTo . PHP_EOL; // Will render the function with is parameters
print 'Parameters: ' . PHP_EOL;
print_r($functionToFitTo->getParameters()) . PHP_EOL;
print 'Chi^: ' . $fitter->getChiSquared() . PHP_EOL;
```

The actual solving is done with GaussJordan elimination with full pivoting. This has the drawback that the the matrix
diagonalization might encounter zero pivot elements, resulting in a singular matrix. An exception will be thrown in this case.

We could implement a singular value decomposition algorithm which is better at handling this. This is on the todo list.

Note that the individual points could have errors set on them as well, and that will affect the fitting algorithm which takes
this into account.

```php
$data = \MOC\Math\DataSeries::fromArray(array(
	array(0, 1.0, 0.1),
	array(1, 1.5, 0.2),
	array(2, 3.0, 0.09),
	array(3, 4.8, 0.11),
	array(4, 6.1, 0.15)
));
```

Non-linear regression or nonlinear least squares
------------------------------------------------

Fitting data to a model that is not a linear combination requires a different approach. Instead an iterative solution is
required. Start out with one value of the parameters, and calculate Chi-squared. then using the Levenberg-Marquardt Method
to alter the parameters in an intelligent way, recalculate Chi-squared. If the fit is better, then keep these parametes,
otherwise try another set of parameters.
Keep on untill chi-squared is reaching a minimum. The minimum might not be the global minimum, so its important to start
out with initial values of the parameters close to the expected values.

This library also provides a solver for this kind of problems.

The solver is more error prone, and the function that needs fitting needs to be initialized with a "best guess" of the
variables, otherwise you risk reaching a local minimum which is not the best fit for the data.

### Example

Fitting a dataseries to a Gauss described by y(x) = a + b*exp(- ((x-c)/d)^2) is exactly this kind of problem. It could
also be a (finite) sum of Gaussians, but this example its just a single Gauss. The Gauss is implemented using the
\MOC\Math\MathematicalFunction\GaussianFunction which implements the NonLinearCombinationOfFunctions interface required
by the NonLinearFittingEngine:

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use \MOC\Math\DataSeries;
use \MOC\Math\Fitting\NonLinearFittingEngine;
use \MOC\Math\MathematicalFunction\GaussianFunction;

$data = DataSeries::fromArray(array(
	array(0.5, 0.25, 0.05),
	array(1.13, 0.7, 0.10),
	array(1.6, 1.8, 0.12),
	array(2.0, 2.8, 0.09),
	array(2.5, 1.8, 0.04),
	array(3.0, 0.7, 0.001),
	array(3.5, 0.2, 0.01)
));
$functionToFitTo = new GaussianFunction();

//Do a best guess on the parameters on basis of the dataset.
$bestGuess = array (0, 1.8, 1.8, 1);
$functionToFitTo->setParameters($bestGuess);

//In this example, we only solve for the b,c and d parameters, we keep the a fixed.
$fitter = new NonLinearFittingEngine($data, $functionToFitTo, array(FALSE, TRUE, TRUE, TRUE));
$fitter->fit();

print 'Result: ' . $functionToFitTo . PHP_EOL; // Will render the function with is parameters
print 'Parameters: ' . PHP_EOL;
print_r($functionToFitTo->getParameters()) . PHP_EOL;
print 'Chi^2: ' . $fitter->getChiSquared() . PHP_EOL;
print 'Iterations: ' . $fitter->getNumberOfIterations() . PHP_EOL;
```

Evaluating functions in ranges
------------------------------

The library contains some utility functions for evaluating function in an interval, useful when visualizing data.

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use \MOC\Math\DataSeries;
use \MOC\Math\MathematicalFunction\Polynomial;

$function = new \MOC\Math\Polynomial(2);
$function->setParameters(array(0.00, 1, 1));
$data = \MOC\Math\MathUtility::evaluateFunctionInInterval($function, -2.0, 2.0, 100); //Evalute from -2 to 2 in 100 steps

foreach($data as $point) {
	printf("X: %4.2f\tY: %4.2f\n", $point->getX(), $point->getY());
}
```
