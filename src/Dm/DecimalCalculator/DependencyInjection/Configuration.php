<?php

namespace Dm\DecimalCalculator\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('dm_decimal_calculator');

        $rootNode
            ->children()
                ->scalarNode('implementation')->defaultValue('dm.decimal_calculator.implementation.zdenekdrahos_bn_php')->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('scale')->defaultValue(20)->end()
                        ->integerNode('round')->defaultValue(null)->end()
                    ->end()
                ->end()
                ->arrayNode('constants')
                    ->defaultValue(array(
                        'PI' => '3.14159265358979323846264338327950288419716939937510582097494459230781640628620899862803482534211706798214808651328230664709384460955058223172535940812848111745028410270193852110555964462294895493038196',
                        'π' => '3.14159265358979323846264338327950288419716939937510582097494459230781640628620899862803482534211706798214808651328230664709384460955058223172535940812848111745028410270193852110555964462294895493038196',
                        'e' => '2.71828182845904523536028747135266249775724709369995957496696762772407663035354759457138217852516642742746639193200305992181741359662904357290033429526059563073813232862794349076323382988075319525101901'
                    ))
                    ->prototype('scalar')->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
