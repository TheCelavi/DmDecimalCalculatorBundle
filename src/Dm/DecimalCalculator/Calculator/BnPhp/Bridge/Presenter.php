<?php

namespace Dm\DecimalCalculator\Calculator\BnPhp\Bridge;

use BN\Compiler\EvaluatorResponder;
use BN\Compiler\Postfix\Operands\OperandsSummary;

class Presenter implements EvaluatorResponder {

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var int
     */
    private $statementsCount;

    /**
     * @var string|int|bool
     */
    private $result;

    public function __construct()
    {
        $this->statementsCount = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function nextStatement($statement)
    {
        $this->statementsCount++;
    }

    /**
     * {@inheritdoc}
     */
    public function unknownToken($token)
    {
        $this->addError(sprintf('Unknown lexeme "%s".', $token));
    }

    /**
     * {@inheritdoc}
     */
    public function unknownOperator($operator)
    {
        $this->addError(sprintf('Undefined operator "%s".', $operator));
    }

    /**
     * {@inheritdoc}
     */
    public function mismatchedBrackets($mismatchedBracket)
    {
        $this->addError(sprintf('Mismatched bracket "%s".', $mismatchedBracket));
    }

    /**
     * {@inheritdoc}
     */
    public function invalidOperands($operatorSymbol, OperandsSummary $operands)
    {
        $this->addError(
            sprintf('Operator "%s" must have "%s", but had "%s" operands.', $operatorSymbol, $operands->expectedCount, $operands->countOperands())

        );
    }

    /**
     * {@inheritdoc}
     */
    public function missingOperator($expectedOperandsCount)
    {
        $this->addError(sprintf('Missing operator with "%s" operands.', $expectedOperandsCount));
    }

    /**
     * {@inheritdoc}
     */
    public function undefinedVariable($variableName)
    {
        $this->addError(sprintf('Undefined variable %s.', $variableName));
    }

    /**
     * {@inheritdoc}
     */
    public function exception(\Exception $e)
    {
        $class = get_class($e);
        $this->addError(
            sprintf('"%s" in file "%s" on line "%s" with message: "%s".', $class, $e->getFile(), $e->getLine(), $e->getMessage())
        );
    }

    /**
     * {@inheritdoc}
     */
    private function addError($errorMessage)
    {
        $this->errors[] = "Statement {$this->statementsCount}:  {$errorMessage}\n";
    }

    /**
     * {@inheritdoc}
     */
    public function hasErrors()
    {
        return (bool) count($this->errors);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function result($result)
    {
        $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        if ($this->hasErrors()) {
            throw new \RuntimeException('Evaluation of expression has errors, result is not available.');
        }
        return $this->result;
    }

} 