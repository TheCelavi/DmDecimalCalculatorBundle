<?php

namespace Dm\DecimalCalculator\Twig\Tag;

/**
 * Class TokenParser
 *
 * {% calculate scale, round, vars = 4, 2, {'varName': value} %} expression {% endcalculate %}
 */
class DecimalCalculatorTokenParser extends \Twig_TokenParser {

    protected $defaults;

    public function __construct($defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $names = $this->parser->getExpressionParser()->parseAssignmentExpression();

        if ($stream->nextIf(\Twig_Token::OPERATOR_TYPE, '=')) {

            $values = $this->parser->getExpressionParser()->parseMultitargetExpression();

            $stream->expect(\Twig_Token::BLOCK_END_TYPE);

            if (count($names) !== count($values)) {
                throw new \Twig_Error_Syntax("When using decimal number calculator, you must have the same number of variables and assignments.", $stream->getCurrent()->getLine(), $stream->getFilename());
            }

            $body = $this->parser->subparse(array($this, 'decideCalculateEnd'), true);

            $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        } else {

            throw new \Twig_Error_Syntax('Decimal calculator expression requires variable assignment.', $stream->getCurrent()->getLine(), $stream->getFilename());
        }

        $parsed = array_merge(array(), $this->defaults);

        foreach ($names as $key => $value) {
            switch ($value->getAttribute('name')) {
                case 'scale':
                    $parsed['scale'] = $values->getNode($key);
                    break;
                case 'round':
                    $parsed['round'] = $values->getNode($key);
                    break;
                case 'vars':
                    $parsed['vars'] = $values->getNode($key);
                    break;
                default:
                    throw new \Twig_Error_Syntax(sprintf('Unknown assignment of "%s" attribute from possible attributes (round, scale) for decimal number calculator.', $value), $stream->getCurrent()->getLine(), $stream->getFilename());
                    break;
            }
        }

        $nodes = array('body' => $body);
        $attributes = array();

        foreach ($parsed as $key => $value) {
            if ($value instanceof \Twig_Node) {
                $nodes[$key] = $value;
            } else {
                $attributes[$key] = $value;
            }
        }

        if (!isset($nodes['vars'])) {
            throw new \Twig_Error_Syntax('Decimal calculator expression requires variable assignment.', $stream->getCurrent()->getLine(), $stream->getFilename());
        }

        return new DecimalCalculatorNode($nodes, $attributes, $lineno, $this->getTag());

    }

    /**
     * {@inheritdoc}
     */
    public function getTag()
    {
        return 'calculate';
    }

    /**
     * Check if it is end of node
     *
     * @param \Twig_Token $token
     * @return bool
     */
    public function decideCalculateEnd(\Twig_Token $token)
    {
        return $token->test('endcalculate');
    }
}