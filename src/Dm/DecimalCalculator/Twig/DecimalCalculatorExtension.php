<?php

namespace Dm\DecimalCalculator\Twig;

use Dm\DecimalCalculator\Contract\DecimalCalculatorInterface;
use Dm\DecimalCalculator\Twig\Tag\DecimalCalculatorTokenParser;

class DecimalCalculatorExtension extends \Twig_Extension {

    protected $calculator;

    protected $options;

    public function __construct(DecimalCalculatorInterface $calculator, array $options = array())
    {
        $this->calculator = $calculator;
        $this->options = array_merge(array(
            'scale' => null,
            'round' => null
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(
            new DecimalCalculatorTokenParser(array(
                'scale' => $this->options['scale'],
                'round' => $this->options['round']
            ))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('calculate', array($this, 'calculateFilter')),
        );
    }

    /**
     * Calculates expression as a twig filter
     *
     * @param string $expression        Expression to evaluate
     * @param array $vars               Vars for expression
     * @param null $round               Rounding precision
     * @param null $scale               Operational precision
     * @return string
     */
    public function calculateFilter($expression, array $vars, $round = null, $scale = null)
    {
        return $this->getCalculator()->evaluate($expression, $vars, $round, $scale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dm.decimal_calculator';
    }

    public function getCalculator()
    {
        return $this->calculator;
    }

} 