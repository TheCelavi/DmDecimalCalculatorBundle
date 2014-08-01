<?php

namespace Dm\DecimalCalculator\Calculator\BnPhp;

use BN\Compiler\EvaluatorInteractor;
use BN\Compiler\Scanner\Converter\LexemeToBNNumber;
use Dm\DecimalCalculator\Calculator\BnPhp\Bridge\Number;
use Dm\DecimalCalculator\Calculator\BnPhp\Bridge\Presenter;
use Dm\DecimalCalculator\Calculator\BnPhp\Bridge\NumberFactory;
use Dm\DecimalCalculator\Calculator\BnPhp\Bridge\GrammarDecorator;
use Dm\DecimalCalculator\Calculator\BnPhp\Bridge\OperatorsFactory;
use Dm\DecimalCalculator\Contract\DecimalCalculatorInterface;
use Dm\DecimalCalculator\Exception\InvalidExpressionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class BnPhpCalculator
 * @package Dm\DecimalCalculator\Calculator\BnPhp
 *
 * Default implementation of decimal number calculator
 * by using BN-PHP library and BC Math PHP package
 */
class BnPhpCalculator implements DecimalCalculatorInterface {

    /**
     * @var array
     */
    protected $constants;

    /**
     * @var Bridge\NumberFactory
     */
    protected $numberFactory;

    /**
     * @var Bridge\GrammarDecorator
     */
    protected $grammar;

    /**
     * @var EvaluatorInteractor
     */
    protected $evaluator;

    public function __construct()
    {
        $this->constants = array();
        $this->numberFactory = new NumberFactory();
        $this->grammar = new GrammarDecorator(new LexemeToBNNumber());

        $this->grammar
            ->statementsSeparator(';')
            ->whiteSpace("\n", "\r", "\n\r", "\t", ',')
            ->assign('=')
            ->brackets('(', ')')
            ->unarySigns('-', '+');

        $operatorsFactory = new OperatorsFactory();

        $this->grammar->operators($operatorsFactory->getAll());
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($expression, array $vars, $roundingScale = null, $operationalScale = null)
    {
        bcscale($operationalScale);

        if (!$this->evaluator) {
            foreach ($this->constants as $label => $value) {
                $this->grammar->numberConstant($label, $this->numberFactory->createNumber($value));
            }
            $this->evaluator = new EvaluatorInteractor($this->grammar);
        }

        $std = new \stdClass();

        $sourceKeys = array_keys($vars);
        $transformed = array();

        foreach ($sourceKeys as $key) {
            $transformed[] = '$'.ContainerBuilder::camelize($key);
        }

        $expression = str_replace($sourceKeys, $transformed, $expression);

        foreach ($vars as $key => $val) {
            $std->{ContainerBuilder::camelize($key)} = $this->numberFactory->createNumber($val);
        }

        $presenter = new Presenter();

        $this->evaluator->__invoke($expression, $std, $presenter);

        if ($presenter->hasErrors()) {
            throw new InvalidExpressionException(implode('; ', $presenter->getErrors()));
        } else {

            $result = $presenter->getResult();

            if ($roundingScale && $result instanceof Number) {
                return $result->round($roundingScale)->__toString();
            } else {
                return $result->__toString();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setConstant($label, $value)
    {
        // Voids the evaluator -> new constants added in runtime...
        $this->evaluator = null;

        $this->constants[$label] = $value;
        return $this;
    }

}