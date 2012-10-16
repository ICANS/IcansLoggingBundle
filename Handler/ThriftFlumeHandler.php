<?php
/**
 * Declares the FlumeThriftHandler class.
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author    Sebastian Latza
 * @copyright 2011 ICANS GmbH (http://www.icans-gmbh.com)
 */
namespace ICANS\Bundle\IcansLoggingBundle\Handler;

use Icans\Ecf\Component\Logging\Flume AS Flume;
use Icans\Ecf\Component\Logging\Handler\ThriftFlumeProcessingHandler;

use Monolog\Logger;

use Thrift AS Thrift;

/**
 * FlumeHandler bridges log messages between monolog and flume
 */
class ThriftFlumeHandler extends ThriftFlumeProcessingHandler
{
    /**
     * {@inheritDoc}
     */
    public function write(array $record)
    {
        $timestamp = intval(microtime(true) * 1000);
        $hostName = isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost';
        $mappedLogLevel = $this->mapLoggerLogLevelToFlumePriority($record['message_loglevel_value']);
        $event = new Flume\ThriftFlumeEvent(array(
                                                 'priority' => $mappedLogLevel,
                                                 'timestamp' => $timestamp,
                                                 'host' => $hostName,
                                                 'body' => $record['formatted']
                                            ));

        $this->sendThriftFlumeEvent($event);
    }

}
