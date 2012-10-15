<?php
/**
 * Declares the RabbitMqHandlerTest class.
 *
 * @author    Oliver Peymann
 * @copyright 2012 ICANS GmbH (http://www.icans-gmbh.com)
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Handler;

use ICANS\Bundle\IcansLoggingBundle\Handler\RabbitMqHandler;

use Monolog\Formatter\FormatterInterface;

use Monolog\Logger;

/**
 * Test class for the rabbit mq handler
 */
class RabbitMqHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $containerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $producerMock;

    /**
     * @var RabbitMqHandler
     */
    private $rabbitMqHandler;

    /**
     * @var string
     */
    private $routingKey = 'routingKeyMock';

    /**
     * @var string
     */
    private $vNode = 'vNodeMock';

    /**
     * @var FormatterInterface
     */
    private $formatterMock;

    /**
     * set up method for the test
     */
    public function setUp()
    {
        $this->producerMock = $this->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\Producer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formatterMock = $this->getMockBuilder('Monolog\Formatter\FormatterInterface')
            ->getMock();

        $this->rabbitMqHandler = new RabbitMqHandler($this->containerMock, $this->routingKey, $this->vNode);
    }

    /**
     * tests the handle and write() function
     */
    public function testHandleAndWrite()
    {
        $testRecord = array(
            'level' => Logger::INFO,
            'testmessage' => 'testmessage',
            'datetime' => new \DateTime(),
            'extra' => array(),
            'formatted' => array()
        );

        $testProperties= array(
            'application_headers' => array(
                "x-riak-target-vnode" => array(
                    "S", $this->vNode
                )
            )
        );

        $this->formatterMock->expects($this->once())
        ->method('format')
        ->with($testRecord)
        ->will($this->returnValue(array()));

        $this->producerMock->expects($this->once())
            ->method('publish')
            ->with(json_encode($testRecord), $this->routingKey, $testProperties);

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('old_sound_rabbit_mq.message_event_producer')
            ->will($this->returnValue($this->producerMock));

        $this->rabbitMqHandler->setFormatter($this->formatterMock);
        $this->assertTrue($this->rabbitMqHandler->handle($testRecord));
    }

    public function testHandleAndWriteWithUnvailableProducer()
    {
        $testRecord = array(
            'level' => Logger::INFO,
            'testmessage' => 'testmessage',
            'datetime' => new \DateTime(),
            'extra' => array(),
            'formatted' => array()
        );

        $testProperties= array(
            'application_headers' => array(
                "x-riak-target-vnode" => array(
                    "S", $this->vNode
                )
            )
        );

        $this->formatterMock->expects($this->once())
            ->method('format')
            ->with($testRecord)
            ->will($this->returnValue(array()));

        $this->producerMock->expects($this->never())
            ->method('publish');

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('old_sound_rabbit_mq.message_event_producer')
            ->will($this->throwException(new \ErrorException('AMQPConnection failed')));

        $this->rabbitMqHandler->setFormatter($this->formatterMock);
        $this->assertFalse($this->rabbitMqHandler->handle($testRecord));
    }


    public function testHandleAndWriteWithFailingPublish()
    {
        $testRecord = array(
            'level' => Logger::INFO,
            'testmessage' => 'testmessage',
            'datetime' => new \DateTime(),
            'extra' => array(),
            'formatted' => array()
        );

        $testProperties= array(
            'application_headers' => array(
                "x-riak-target-vnode" => array(
                    "S", $this->vNode
                )
            )
        );

        $this->formatterMock->expects($this->once())
            ->method('format')
            ->with($testRecord)
            ->will($this->returnValue(array()));

        $this->producerMock->expects($this->once())
            ->method('publish')
            ->with(json_encode($testRecord), $this->routingKey, $testProperties)
            ->will($this->throwException(new \Exception('Error sending data')));

        $this->containerMock->expects($this->once())
            ->method('get')
            ->with('old_sound_rabbit_mq.message_event_producer')
            ->will($this->returnValue($this->producerMock));

        $this->rabbitMqHandler->setFormatter($this->formatterMock);
        $this->assertFalse($this->rabbitMqHandler->handle($testRecord));
    }
}
