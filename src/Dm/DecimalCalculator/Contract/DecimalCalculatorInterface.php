<?php

namespace Dm\DecimalCalculator\Contract;

use Dm\DecimalCalculator\Exception\InvalidExpressionException;

/**
 * Interface DecimalCalculatorInterface
 *
 * Defines the interface which each concrete implementation of math executor with arbitrary precision must implement
 */
interface DecimalCalculatorInterface {

    /**
     * Evaluates mathematical expression string with arbitrary precision
     *
     * @param string $expression            Expression to evaluate
     * @param array $vars                   The variable values in expression
     * @param null|integer $roundingScale   The number of decimals to round up, if any
     * @param integer $operationalScale     The decimal precision when executing operations
     * @return string                       The result of evaluation
     * @throws \InvalidArgumentException    Thrown when invalid arguments are provided
     * @throws InvalidExpressionException   Thrown when provided expression is not valid
     */
    public function evaluate($expression, array $vars, $roundingScale = null, $operationalScale = null);

    /**
     * Set a constant
     *
     * @param string $label                 The constant label
     * @param string $value                 The constant value as big number string
     */
    public function setConstant($label, $value);

} 