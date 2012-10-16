<?php
/**
 * Declares IcansLoggingExtension
 *
 * @author    Sebastian Latza
 * @copyright Copyright (c) 2007-2011 ICANS GmbH (http://www.icans-gmbh.com)
 * @version   $Id: $
 */
namespace ICANS\Bundle\IcansLoggingBundle\DependencyInjection;

use Monolog\Logger;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('icans_logging');

        $rootNode
            ->children()
                ->arrayNode('flume_client')->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('host')->defaultValue('localhost')->end()
                            ->scalarNode('port')->defaultValue(9120)->end()
                            ->scalarNode('recvTimeout')->defaultValue(5000)->end()
                        ->end()
                 ->end()
            ->end();

        $rootNode
            ->children()
                ->arrayNode('rabbit_mq_client')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('additionalProperties')->addDefaultsIfNotSet()
                                ->defaultValue(array())->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        $rootNode
            ->children()
                ->arrayNode('logger')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('formatter')->defaultValue('monolog.formatter.json')->end()
                        ->scalarNode('log_level')->defaultValue(Logger::INFO)->end()
                        ->booleanNode('bubbles')->defaultValue(true)->end()
                    ->end()
                ->end()
            ->end();


        return $treeBuilder;
    }
}
