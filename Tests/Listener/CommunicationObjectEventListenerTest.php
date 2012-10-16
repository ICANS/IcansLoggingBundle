<?php
/**
 * Declares the CommunicationObject Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\CommunicationObjectEventListener;
use Icans\Ecf\Component\Event\CommObject\CreateCommObjectEvent;

/**
 * Test case for the CommunicationObject Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class CommunicationObjectEventListenerTest extends BaseEventListenerTest
{
    /**
     * @var CommunicationObjectEventListener
     */
    private $eventListener;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventListener = new CommunicationObjectEventListener($this->loggerMock);
    }

    /**
     * Tests onCreateCommObjectEvent->onRegisterEvent()
     */
    public function testOnCreateCommObjectEvent()
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
            ->with(CreateCommObjectEvent::EVENT_HANDLE,
                   $contextArray);

        $this->eventListener->onCreateCommObjectEvent($eventMock);
    }

    /**
     * Tests onCreateCommObjectEvent->onRegisterEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnCreateCommObjectEventWrongParameter()
    {
        $eventMock = array();

        $this->eventListener->onCreateCommObjectEvent($eventMock);
    }
}

