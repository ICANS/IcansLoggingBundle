<?php
/**
 * Declares the RabbitMqHandler class.
 *
 * @author    Oliver Peymann
 * @copyright 2012 ICANS GmbH (http://www.icans-gmbh.com)
 */
namespace ICANS\Bundle\IcansLoggingBundle\Handler;

use Icans\Ecf\Component\Logging\FilterInterface;

use Monolog\Handler\AbstractProcessingHandler;

use Monolog\Logger;

use OldSound\RabbitMqBundle\RabbitMq\Producer;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RabbitMqHandler class for sending event message to a rabbit mq instance
 */
class RabbitMqHandler extends AbstractProcessingHandler
{
    /**
     * @var \OldSound\RabbitMqBundle\RabbitMq\Producer
     */
    private $eventMessageProducer;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var string
     */
    private $routingKey;

    /**
     * @var string
     */
    private $riakVNode;

    /**
     * Default constructor
     *
     * @param ContainerInterface $container
     * @param string $routingKey
     * @param string $riakVNode
     * @param int $level The minimum logging level at which this handler will be triggered
     * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(
        ContainerInterface $container,
        $routingKey,
        $riakVNode,
        $level = Logger::DEBUG,
        $bubble = true // => has to be set to "false" after successfull message handling
    )
    {
        $this->eventMessageProducer = null;
        $this->container = $container;
        $this->routingKey = $routingKey;
        $this->riakVNode = $riakVNode;
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        // Theses are additional properties that can be set for the amqp message
        $additionalProperties= array(
            'application_headers' => array(
                "x-riak-target-vnode" => array(
                    "S", $this->riakVNode
                )
            )
        );
        $producer = $this->getEventMessageProducer();
        if (null !== $producer) {
            try {
                // Since we do not want to log the serialuized message body that gets added by monolog, we remove it.
                if (!empty($record['formatted'])) {
                    unset($record['formatted']);
                }
                $producer->publish(json_encode($record), $this->routingKey, $additionalProperties);
                $this->bubble = false; // = the record was successfully consumed
            } catch (\Exception $e) {
                $this->bubble = true; // = the record was NOT successfully consumed
            }
        } else {
            $this->bubble = true; // = the record was NOT successfully consumed
        }
    }

    /**
     * Add Icans\Ecf\Component\Logging\FilterInterface to this Handler
     *
     * @param Icans\Ecf\Component\Logging\FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @inheritDoc
     */
    public function isHandling(array $record)
    {
        try {
            if (null === $this->getEventMessageProducer()) {
                return false;
            }
        } catch (\Exception $e) {

            return false;
        }
        if (isset($record['message_loglevel_value'])) {
            return $record['message_loglevel_value'] >= $this->level;
        } else {
            return parent::isHandling($record);
        }

    }

    /**
     * Adds an array of Icans\Ecf\Component\Logging\FilterInterface to this Handler
     *
     * @param array $filters
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * Helper function wrapping the EventMessageProducer to cater for graceful handling of AMQP failures.
     *
     * @return Producer|null
     */
    private function getEventMessageProducer()
    {
        try {
            if (null === $this->eventMessageProducer) {
                $this->eventMessageProducer = $this->container->get('old_sound_rabbit_mq.message_event_producer');
            }
        } catch (\Exception $e) {
            $this->eventMessageProducer = null;
        }

        return $this->eventMessageProducer;
    }
}
