DmDecimalCalculatorBundle
=========================

*Decimal calculator bundle provides you with the possibility to execute
mathematical calculations with arbitrary precision in PHP.*

PHP uses floating point precision for expressing the numbers and
executing mathematical operations against numbers. Floating point
numbers are aproximation of the number, which can not be used every
time, especialy when it comes to operations within the context of
financial calculations. To find out more about this issue, see:
http://php.net/manual/en/language.types.float.php

Decimal calculator provides you with the tool to work with numbers with
arbitrary precision and avoid floating point approximation issues.

The bundle provides you with:

-  Service, for calculations on controller/service level
-  Twig extension, for calculations within the templates.

Configuration
-------------

Full default configuration can be presented as:

::

    dm_decimal_calculator:
        implementation: dm.decimal_calculator.implementation.zdenekdrahos_bn_php
        defaults:
            scale: 10
            round: ~
        constants:
            PI: '3.14159265358979323846264338327950288419716939937510582097494459230781640628620899862803482534211706798214808651328230664709384460955058223172535940812848111745028410270193852110555964462294895493038196'
            π: '3.14159265358979323846264338327950288419716939937510582097494459230781640628620899862803482534211706798214808651328230664709384460955058223172535940812848111745028410270193852110555964462294895493038196'
            e: '2.71828182845904523536028747135266249775724709369995957496696762772407663035354759457138217852516642742746639193200305992181741359662904357290033429526059563073813232862794349076323382988075319525101901'

``implementation`` defines a concrete implementation of decimal
calculator. By default, ``zdenekdrahos/bn-php`` library bridge is
provided and implemented for mathematical operations (see more in
*Extending a bundle* section).

``scale`` defines the operational precision scale which ought to be used
(that is, how many decimal points to use in mathematical operations with
the numbers). Higer scale will decrease of risk of accumulating rounding
errors in successive mathematical operations, while on the other hand it
will increase processor and memory consumption. ``round`` defines the
number of decimal points for final result, by default the same number of
decimal points is used as defined in ``scale``.

Section contstants defines most common mathematical constants that can
be used (PI, e). Please note that numbers are, and must be, provided as
a string.

Service
-------

Decimal calculator can be accesed via service as:

::

    $container->get('dm.decimal_calculator');

Service defines only one method:

::

    $container->get('dm.decimal_calculator')->evaluate($expression, array $vars, $roundingScale = null, $operationalScale = null);

-  $expression - mathematical string to evaluate
-  $vars - array of key/value pairs where keys are labels of variables
   in expression which ought to be replaced with given values
-  $roundingScale - the number of decimal for evaluated result, or
   default will be used
-  $operationalScale - the number of decimals for numbers which ought to
   be used in mathematical operations, or defaults will be used if
   parameter value is null

Please note that type of evaluated result is number as string.

Example
~~~~~~~

::

    $calculator = $container->get('dm.decimal_calculator');
    echo $calculator->evaluate('1 + varX * varY', array('varX'=>10, 'varY'=>'0.128'), 1, 10); // Result is: '2.3'  

Twig
----

If it is required to execute mathematical operations in templates, the
service is not best solution. Therefore, Twig tag and Twig filter is
provided.

Calculator can be executed by using Twig tag:

::

    {% calculate vars, round, scale = {varX: 100}, 2, 10 %}
        (1 + 3 * 4) / varX
    {% endcalculate %}

or by using Twig filters:

::

    '(1 + 3 * 4) / varX'|calculate({varX: 100}, 2, 10)

Note that in Twig tag as well as in Twig filter ``round`` and ``scale``
are optional parameters and can be omitted.

Extending a bundle
------------------

This bundle defines a service which is facade to actual calculator that
does the heavy duty. The calculator is bridge to concrete
implementation. Default implementation is a bridge to
``zdenekdrahos/bn-php`` library which uses BC Math PHP library for
arbitrary precision calculations.

If you are not hapy with used implementation, you can replace the
calculator implementation with your own. In order to achieve that, you
have to create your own calculator which implements
``Dm\DecimalCalculator\Contract\DecimalCalculatorInterface``, define it
as a service (it can be a private service) and in configuration set
``implementation`` key to your own service.

License
-------

This bundle is licensed under MIT licence, courtesy of Nikola Svitlica,
a.k.a TheCelavi
