<?php
/**
 * Declares the Reflexmail Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\ReflexmailEventListener;
use Icans\Ecf\Component\Event\Messaging\MessageEvent;

/**
 * Test case for the Reflexmail Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class ReflexmailEventListenerTest extends BaseEventListenerTest
{
    /**
     * @var ReflexmailEventListener
     */
    private $eventListener;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventListener = new ReflexmailEventListener($this->loggerMock);
    }

    /**
     * Tests ReflexmailEventListener->onMessageSentEvent()
     */
    public function testOnMessageSentEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $contextArray = array (
            'test' => 'test',
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(MessageEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onMessageSentEvent($eventMock);
    }



    /**
     * Tests ReflexmailEventListener->onMessageSentEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnMessageSentEventWrongParameter()
    {
        $eventMock = array();
        $this->eventListener->onMessageSentEvent($eventMock);
    }


}

