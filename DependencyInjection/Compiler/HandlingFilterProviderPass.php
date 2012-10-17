<?php
/**
 * Declares HandlingFilterProviderPass
 *
 * @author    Mike Lohmann
 * @copyright Copyright (c) 2007-2012 ICANS GmbH (http://www.icans-gmbh.com)
 */
namespace ICANS\Bundle\IcansLoggingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * HandlingFilterProviderPass collects all configured (tagged) filters for handling method and adds them to
 * the handler.
 */
class HandlingFilterProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        // for flume
        if ($container->hasDefinition('icans.logging.service.flume')) {
            $definition = $container->getDefinition('icans.logging.service.flume');
            foreach ($container->findTaggedServiceIds('icans.logging.handling_filter.flume') as $id => $attributes) {
                $definition->addMethodCall('addHandlingFilter', array(new Reference($id)));
            }
        }

        // for rabbit-mq
        if ($container->hasDefinition('icans.logging.service.rabbit_mq')) {
            $definition = $container->getDefinition('icans.logging.service.rabbit_mq');
            foreach ($container->findTaggedServiceIds('icans.logging.handling_filter.rabbit_mq') as $id =>
                     $attributes) {
                $definition->addMethodCall('addHandlingFilter', array(new Reference($id)));
            }
        }

        //all
        $definition = $container->getDefinition($container->getParameter('icans.logging.formatter'));
        $definition->addMethodCall(
            'setFormatter',
            array(new Reference($container->getParameter('icans.logging.formatter')))
        );
    }
}