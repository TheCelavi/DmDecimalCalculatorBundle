<?php

namespace Dm\DecimalCalculator\Calculator;

use Dm\DecimalCalculator\Contract\DecimalCalculatorInterface;
use Dm\DecimalCalculator\Exception\InvalidExpressionException;

/**
 * Class Calculator
 * @package Dm\DecimalCalculator\Calculator
 *
 * A service implementation
 */
class Calculator implements DecimalCalculatorInterface {

    /**
     * @var int
     */
    private $scale;

    /**
     * @var int
     */
    private $round;

    /**
     * @var DecimalCalculatorInterface
     */
    private $calculator;

    /**
     * Evaluates expression and returns a result of mathematical operations on decimal numbers with given precision
     *
     * @param string $expression                Expression to evaluate
     * @param array $vars                       The array of vars for expressions, keys are variable names in expression
     * @param null|integer $roundingScale       The rounding scale of returned result, if null, default rounding up scale will be used
     * @param null|integer $operationalScale    The operational scale, number of decimals to work with, if null, default will be used
     * @return string                           The result of evaluation
     * @throws \InvalidArgumentException        Thrown when invalid arguments are provided
     * @throws InvalidExpressionException       Thrown when provided expression is not valid
     */
    public function evaluate($expression, array $vars, $roundingScale = null, $operationalScale = null)
    {
        if (trim($expression) == '') {
            return ''; // Edge case
        }

        if (is_null($operationalScale)) {
            $operationalScale = $this->getScale();
        }

        if (!is_null($roundingScale) && $roundingScale > $operationalScale) {
            throw new \InvalidArgumentException(sprintf('Rounding scale %s can not be greater than operational scale %s.', $roundingScale, $operationalScale));
        }

        return $this->calculator->evaluate($expression, $vars, $operationalScale, $roundingScale);
    }

    /**
     * Get current default operational scale
     *
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Set default operational scale
     *
     * @param int $scale            The scale
     * @return Calculator $this
     */
    public function setScale($scale)
    {
        $this->scale = $scale;
        return $this;
    }

    /**
     * Get default round up scale
     *
     * @return int
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set default round up scale
     *
     * @param int $round            The scale
     * @return Calculator $this
     */
    public function setRound($round)
    {
        $this->round = $round;
        return $this;
    }

    /**
     * Set concrete calculator implementation
     *
     * @param DecimalCalculatorInterface $calculator    A calculator concrete implementation
     * @return Calculator $this
     */
    public function setCalculator(DecimalCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
        return $this;
    }

    /**
     * Set mathematical constant
     *
     * @param string $label         Constant label
     * @param string $value         Constant value
     * @return Calculator $this
     */
    public function setConstant($label, $value)
    {
        $this->calculator->setConstant($label, $value);
        return $this;
    }

    /**
     * Set several mathematical constants at once
     *
     * @param array $constants      Constants to set, keys are labels
     * @return Calculator $this
     */
    public function setConstants(array $constants)
    {
        foreach ($constants as $label => $value) {
            $this->setConstant($label, $value);
        }

        return $this;
    }
} 