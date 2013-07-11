<?php
/**
 * Declares IcansLoggingExtension
 *
 * @author    Sebastian Latza
 * @copyright Copyright (c) 2007-2011 ICANS GmbH (http://www.icans-gmbh.com)
 * @version   $Id: $
 */
namespace ICANS\Bundle\IcansLoggingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;

/**
 * IcansLogging extension to enable automatic registration of services.
 *
 * @copyright Copyright (c) 2007-2011 ICANS GmbH (http://www.icans-gmbh.com)
 */
class IcansLoggingExtension extends Extension
{
    /**
     * Loads the configuration.
     *
     * @param array $configs An array of configuration options
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('flume.xml');
        $loader->load('rabbit_mq.xml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        //flume
        if ($config['flume_client'] != null) {
            $container->setParameter('icans_logging.flume_client.host', $config['flume_client']['host']);
            $container->setParameter('icans_logging.flume_client.port', $config['flume_client']['port']);
            $container->setParameter('icans_logging.flume_client.recvTimeout', $config['flume_client']['recvTimeout']);
        }

        //rabbit
        $container->setParameter('icans_logging.rabbitmq.routing_key', $config['rabbit_mq_client']['routing_key']);

        $definition = $container->getDefinition('icans.logging.service.rabbit_mq');
        $definition->addMethodCall(
            'addAdditionalProperties',
            array($config['rabbit_mq_client']['additional_properties'])
        );

        //all
        $container->setParameter('icans_logging.formatter', $config['logger']['formatter']);

        $container->setParameter('icans_logging.logger.log_level', $config['logger']['log_level']);
        $container->setParameter('icans_logging.logger.bubbles', $config['logger']['bubbles']);
    }
}