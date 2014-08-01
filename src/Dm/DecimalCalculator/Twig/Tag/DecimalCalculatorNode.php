<?php

namespace Dm\DecimalCalculator\Twig\Tag;

class DecimalCalculatorNode extends \Twig_Node {

    /**
     * {@inheritdoc}
     */
    public function __construct(array $nodes, array $attributes, $lineno, $tag = null)
    {
        parent::__construct($nodes, $attributes, $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(\Twig_Compiler $compiler)
    {

        $compiler->raw('// @DmDecimalCalculatorBundle')->write("\n");

        $compiler->write('ob_start();')->write("\n");

        $compiler->subcompile($this->getNode('body'))->write("\n");

        $compiler->write('$context[\'dm_decimal_calculator_node_expression_content\'] = ob_get_clean();')->write("\n");

        $compiler->write('echo $this->env->getExtension(\'dm.decimal_calculator\')->getCalculator()->evaluate(');
            $compiler->raw('$context[\'dm_decimal_calculator_node_expression_content\'], ');
            $this->compileVars($compiler);
            $compiler->raw(',');
            $this->compileRound($compiler);
            $compiler->raw(',');
            $this->compileScale($compiler);
        $compiler->write(');');

        $compiler->write("\n");
    }

    /**
     * Compiles the expression between tags
     *
     * @param \Twig_Compiler $compiler
     */
    protected function compileExpression(\Twig_Compiler $compiler)
    {

        $compiler->subcompile($this->getNode('body'));
    }

    /**
     * Compiles the vars for expression
     *
     * @param \Twig_Compiler $compiler
     */
    protected function compileVars(\Twig_Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('vars'));
    }

    /**
     * Compiles the rounding precision
     *
     * @param \Twig_Compiler $compiler
     */
    protected function compileRound(\Twig_Compiler $compiler)
    {
        if ($this->hasNode('round')) {
            $compiler->subcompile($this->getNode('round'));
        } else {
            $round = $this->getAttribute('round');
            if (is_null($round)) {
                $compiler->raw('null');
            } else {
                $compiler->raw(sprintf('%s', $round));
            }
        }
    }

    /**
     * Compiles the operational scale
     *
     * @param \Twig_Compiler $compiler
     */
    protected function compileScale(\Twig_Compiler $compiler)
    {
        if ($this->hasNode('scale')) {
            $compiler->subcompile($this->getNode('scale'));
        } else {
            $scale = $this->getAttribute('scale');
            if (is_null($scale)) {
                $compiler->raw('null');
            } else {
                $compiler->raw(sprintf('%s', $scale));
            }
        }
    }

}