<?php

namespace Dm\DecimalCalculator\Calculator\BnPhp\Bridge;

use BN\Compiler\Grammar\GrammarBuilder;

class GrammarDecorator extends GrammarBuilder
{
    public $keywordsLanguage = array();
    public $keywordsConstants = array();

    /**
     * {@inheritdoc}
     */
    public function statementsSeparator($statementsSeparator)
    {
        $this->keywordsLanguage[] = $statementsSeparator;
        return parent::statementsSeparator($statementsSeparator);
    }

    /**
     * {@inheritdoc}
     */
    public function assign($assignOperator)
    {
        $this->keywordsLanguage[] = $assignOperator;
        return parent::assign($assignOperator);
    }

    /**
     * {@inheritdoc}
     */
    public function keyword($symbol, $tokenType)
    {
        $this->keywordsLanguage[] = $symbol;
        return parent::keyword($symbol, $tokenType);
    }

    /**
     * {@inheritdoc}
     */
    public function numberConstant($symbol, $value)
    {
        $this->keywordsConstants[] = $symbol;
        return parent::numberConstant($symbol, $value);
    }
}