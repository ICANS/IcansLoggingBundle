<?php
/**
 * Declares the Form Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\FormEventListener;
use Icans\Ecf\Component\Event\Form\ErrorEvent;

/**
 * Test case for the Form Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class FormEventListenerTest extends BaseEventListenerTest
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

        $this->eventListener = new FormEventListener($this->loggerMock);
    }

        /**
         * Tests FormEventListener->onFormErrorEvent()
         */
        public function testOnFormErrorEvent()
        {
            $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

            $field = 'ab213213213';
            $message = 'you can haz cheeseburgers';

            $contextArray = array (
                'field' => $field,
                'message' => $message,
            );

            $eventMock->expects($this->once())
                ->method('getData')
                ->will($this->returnValue($contextArray));

            $this->loggerMock->expects($this->once())
                ->method('info')
                ->with(ErrorEvent::EVENT_HANDLE, $contextArray);

            $this->eventListener->onFormErrorEvent($eventMock);
        }


    /**
     * Tests FormEventListener->onFormErrorEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnFormErrorEventWrongParameter()
    {
        $eventMock = array();
        $this->eventListener->onFormErrorEvent($eventMock);
    }
}

