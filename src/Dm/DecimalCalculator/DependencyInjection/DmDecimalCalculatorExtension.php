<?php

namespace Dm\DecimalCalculator\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * {@inheritdoc}
 */
class DmDecimalCalculatorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        /**
         * Configure service
         */
        if ($container->hasDefinition('dm.decimal_calculator')) {

            $definition = $container->getDefinition('dm.decimal_calculator');

            $definition->addMethodCall('setCalculator', array(new Reference($config['implementation'])));
            $definition->addMethodCall('setScale', array($config['defaults']['scale']));
            $definition->addMethodCall('setRound', array($config['defaults']['round']));
            $definition->addMethodCall('setConstants', array($config['constants']));
        }

        /**
         * Configure twig extension
         */
        if ($container->hasDefinition('dm.decimal_calculator.twig.extension')) {

            $definition = $container->getDefinition('dm.decimal_calculator.twig.extension');

            $definition->replaceArgument(1, array(
                'round' => $config['defaults']['round'],
                'scale' => $config['defaults']['scale']
            ));
        }
    }
}
