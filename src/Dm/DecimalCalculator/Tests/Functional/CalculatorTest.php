<?php

require_once dirname(__DIR__) . '/../../../../../../../../app/AppKernel.php';

class CalculatorTest extends \PHPUnit_Framework_TestCase {

    protected $kernel;

    /**
     * @var \Dm\DecimalCalculator\Contract\DecimalCalculatorInterface calculator
     */
    protected $calculator;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function setUp()
    {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
        $container = $this->kernel->getContainer();

        $this->calculator = $container->get('dm.decimal_calculator');
        $this->twig = $container->get('twig');

        $this->twig->setLoader(new Twig_Loader_String());

        parent::setUp();
    }

    public function testServiceSuccess()
    {
        $cases = array(
            array(
                'expression' => '1+2+3*4',
                'vars' => array(),
                'scale' => '10',
                'round' => '2',
                'output' => '15.00'
            ),
            array(
                'expression' => '1+2+3*variable',
                'vars' => array('variable' => 4),
                'scale' => '10',
                'round' => '2',
                'output' => '15.00'
            ),
            array(
                'expression' => '1+2+3*variable/10',
                'vars' => array('variable' => 4),
                'scale' => '20',
                'round' => '6',
                'output' => '4.200000'
            )
        );

        foreach ($cases as $case) {
            $this->assertSame($case['output'], $this->calculator->evaluate($case['expression'], $case['vars'], $case['round'], $case['scale']));
        }
    }

    /**
     * @expectedException \Dm\DecimalCalculator\Exception\InvalidExpressionException
     */
    public function testInvalidExpression()
    {
        $this->calculator->evaluate('1+variable', array());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgument()
    {
        $this->calculator->evaluate('1+variable', array('variable'));
    }

    public function testTwigTag()
    {
        $cases = array(
            array(
                'twig' => '{% calculate scale, round, vars=10, 2, {} %} 1 + 3 * 4 {% endcalculate %}',
                'vars' => array(),
                'output' => '13.00'
            ),
            array(
                'twig' => '{% calculate scale, round, vars=10, 2, {variable: 100} %} (1 + 3 * 4) / variable {% endcalculate %}',
                'vars' => array(),
                'output' => '0.13'
            )
        );

        foreach ($cases as $case) {
            $this->assertSame($case['output'], $this->twig->render($case['twig'], $case['vars']));
        }
    }

    public function testTwigFilter()
    {
        $cases = array(
            array(
                'twig' => '{{ "1+2/variable"|calculate({variable: 2}, 2, 10) }}',
                'vars' => array(),
                'output' => '2.00'
            )
        );

        foreach ($cases as $case) {
            $this->assertSame($case['output'], $this->twig->render($case['twig'], $case['vars']));
        }
    }

    public function tearDown()
    {
        $this->kernel->shutdown();

        parent::tearDown();
    }


} 