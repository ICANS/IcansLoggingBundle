<?php
/**
 * This file contains the RabbitMqMessageProducer implementation.
 *
 * PHP Version 5.3
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @copyright 2012 ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Handler\RabbitMq;

use ICANS\Component\IcansLoggingComponent\AMQPMessageProducerInterface;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * This class defines RabbitMqMessageProducer used to send messages to RabbitMQ using
 * the oldsound/rabbitmq bundle. It is just a wrapper to be able to have no dependency
 * from the component to the bundle of oldsound.
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @copyright 2012 ICANS GmbH
 */
class RabbitMqMessageProducer implements  AMQPMessageProducerInterface
{
    /**
     * @var Producer
     */
    private $rabbitMqProducer;

    public function __construct(Producer $rabbitMqProducer)
    {
        $this->rabbitMqProducer = $rabbitMqProducer;
    }

    /**
     * @inheritDoc
     */
    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {
        $this->rabbitMqProducer->publish($msgBody, $routingKey, $additionalProperties);
    }

}