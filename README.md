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

::
	$matrix = new Matrix(array(
		array(1, 2, 5),
		array(5, 2, 7),
		array(3, 9, 2)
	));

The Matrix also has tro factory classes for creating empty and identity classes

 $identity = Matrix::identity(2)

Will create the 2x2 matrix with diagonal elements of 1 and rest set to 0

 $empty = Matrix::emptyMatrix(2,2)

Will create a 2x2 matrix with all elements set to 0.

The matrix class includes methods like

* multiplyWithVectorFromRight
* equals
* getInverse

Vector
------

The vector class is constructed by

::
	$vector = new Vector(array(2,3,4);

And includes methods for finding norm, length etc.

GaussJordan elimination
-----------------------

Included is also an implementation of the GaussJordan elimination algorith used to diagonalize matrixes. This is usefull
 when finding inverse matrixes, or when solving N euations in M unknown problems, or just fitting data to model data.



Linear regression
-----------------

The main purpose of the Math package is to provide a fitting engine for fitting a dataset to a given model. Part of that
is lineary regression where the data is fitted to a model that can be described as a lineary combination of functions.
The individual basisfunction can vary unlinearyly by x.

example:

Fit a dataset to the function y(x) = a + bx + cx^2

The individual basis function is 1, x and x^2 and we wish to find the parameters a, b, c that makes the model fit our
data best.

This can be done by calling the method linearFit in the FittingEngine

::

 $data = new DataSet(array(
 	array(0, 1.0),
 	array(1, 1.5),
 	array(2, 3.0),
 	array(3, 4.8),
 	array(4, 6.1)
 ));
 $fittedFunction = new Polynomial(2);
 $fitter = new \MOC\Math\Fitting\FittingEngine();
 $fitter->linearFit($data, $fittedFunction);
 print $fittedFunction;
 print 'Parameters: ';
 print_r($fittedFunction->getParameters());

The actual solving is done with GaussJordan elimination with full pivoting.