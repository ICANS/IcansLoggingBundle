<?php
/**
 * Declares the Image Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\ImageEventListener;
use Icans\Ecf\Component\Event\Image\ResizeImageEvent;

/**
 * Test case for the Image Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class ImageEventListenerTest extends BaseEventListenerTest
{
    /**
     * @var ImageEventListener
     */
    private $eventListener;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventListener = new ImageEventListener($this->loggerMock);
    }

    /**
     * Tests ImageEventListener->onResizeImageEvent()
     */
    public function testOnResizeImageEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $identifier = 'ab213213213';
        $width = 100;
        $height = 500;

        $contextArray = array (
            'storageIdentifier' => $identifier,
            'width' => $width,
            'height' => $height
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(ResizeImageEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onResizeImageEvent($eventMock);
    }



    /**
     * Tests ImageEventListener->onResizeImageEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnResizeImageEventWrongParameter()
    {
        $eventMock = array();
        $this->eventListener->onResizeImageEvent($eventMock);
    }
}

