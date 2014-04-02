MOC.Math
========

Math library for PHP. Include Matrix manipulation, equation solving and lineary and un-linear fitting

Comprehensive native PHP library for various Math stuff.

Matrix and vector calculations
==============================

The package contains two classes used for vector and matrix manipulation. The \MOC\Math\Vector and the \MOC\Math\Matrix
classes.

Matrix
------

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

Vector
------

The vector class is constructed by

```php
$vector = new Vector(array(2,3,4);
```
And includes methods for finding norm, length etc.

GaussJordan elimination
-----------------------

Included is also an implementation of the GaussJordan elimination algorith used to diagonalize matrixes. This is useful
 when finding inverse matrixes, or when solving N equations in M unknown problems, or just fitting data to model data.


Linear regression
-----------------

The main purpose of the Math package is to provide a fitting engine for fitting a dataset to a given model. Part of that
is lineary regression where the data is fitted to a model that can be described as a linear combination of basisfunctions.
The individual basisfunction can vary unlinearly with x, but can not be dependent on the parameters.

##### Example:

Fit a dataset to the function y(x) = a + bx + cx^2

The individual basis function is 1, x and x^2 and we wish to find the parameters a, b, c that makes the model fit our
data best.

This can be done by calling using the LinearFitingEngine

```php
$data = new DataSet(array(
array(0, 1.0),
array(1, 1.5),
array(2, 3.0),
array(3, 4.8),
array(4, 6.1)
));
$functionToFitTo = new \MOC\Math\Polynomial(2);
$fitter = new \MOC\Math\Fitting\LinearFittingEngine($data, $functionToFitTo);
$fitter->fit();
print "Result: " . $functionToFitTo . PHP_EOL; // Will render the function with is parameters
print 'Parameters: ' . PHP_EOL;
print_r($functionToFitTo->getParameters()) . PHP_EOL;
print 'Chi^: ' . $fitter->getChiSquared() . PHP_EOL;
```

The actual solving is done with GaussJordan elimination with full pivoting. This has the drawback that the the matrix
diagonalization might encounter zero pivot elements, resulting in singular matrixes. An exception will be thrown in that case.

We could implement a singular value decomposition algorithm which is better at handling this.

Note that the individual points could have errors set on them as well, and that would have affected the fitting algorithm,

```php
$data = new DataSet(array(
array(0, 1.0, 0.1),
array(1, 1.5, 0.2),
array(2, 3.0, 0.09),
array(3, 4.8, 0.11),
array(4, 6.1, 0.15)
));
```

Non-linear regression
---------------------

Fitting data to a model that is not a linear combination requires a different approach. Instead an iterative solution is
 required. This library also provides a solver for this kind of problems.

The solver is more error prone, and the function that needs fitting needs to be initialized with a "best guess" of the
variables.

### Example

Fitting a dataset to a Gauss described by y(x) = a + b*exp(- ((x-c)/d)^2) is exactly this kind of problem.

```php
$data = new DataSet(array(
	array(0.5, 0.25, 0.05),
	array(1.13, 0.7, 0.10),
	array(1.6, 1.8, 0.12),
	array(2.0, 2.8, 0.09),
	array(2.5, 1.8, 0.04),
	array(3.0, 0.7, 0.001),
	array(3.5, 0.2, 0.01)
));
$functionToFitTo = new \MOC\Math\Gauss();

//Do a best guess on the parameters on basis of the dataset.
$bestGuess = array (0, 1.8, 1.8, 1);
$functionToFitTo->setParameters($bestGuess);

//In this example, we only solve for the b,c and d parameters, we keep the a fixed.
$fitter = new NonLinearFittingEngine($data, $fittedFunction, array(FALSE, TRUE, TRUE, TRUE));
$fitter->fit();

print "Result: " . $functionToFitTo . PHP_EOL; // Will render the function with is parameters
print 'Parameters: ' . PHP_EOL;
print_r($functionToFitTo->getParameters()) . PHP_EOL;
print 'Chi^: ' . $fitter->getChiSquared() . PHP_EOL;
```

Evaluating functions in ranges
------------------------------

The library contains some utility functions for evaluating function in an interval, usefull when visualizing data.

```php
$function = new \MOC\Math\Polynomial(2);
$function->setParameters(array(0.00, 1, 1);
$data = \MOC\Math\MathUtility::evaluateFunctionInInterval($function, -2.0, 2.0, 100); //Evalute from -2 to 2 in 100 steps

foreach($data as $point) {
	sprintf('X: %4.2f\tY: %4.2f", $point->getX(). $point->getY());
}
```



