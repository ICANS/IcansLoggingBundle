<?php
/**
 * Declares the Video Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\VideoEventListener;
use Icans\Ecf\Component\Event\Video AS Video;

/**
 * Test case for the Video Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class VideoEventListenerTest extends BaseEventListenerTest
{
    /**
     * @var VideoEventListener
     */
    private $eventListener;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventListener = new VideoEventListener($this->loggerMock);
    }

    /**
     * Tests VideoEventListener->testOnVideoPublishEvent()
     */
    public function testOnVideoPublishEvent()
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
            ->with(Video\PublishEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onVideoPublishEvent($eventMock);
    }

    /**
     * Tests VideoEventListener->testOnVideoErrorEvent()
     */
    public function testOnVideoErrorEvent()
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
            ->with(Video\ErrorEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onVideoErrorEvent($eventMock);
    }

    /**
     * Tests VideoEventListener->onVideoCleanupEvent()
     */
    public function testOnVideoCleanupEvent()
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
            ->with(Video\CleanupEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onVideoCleanupEvent($eventMock);
    }

    /**
     * Tests VideoEventListener->onVideoPublishEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnVideoPublishEventWrongParameter()
    {
        $eventMock = array();
        $this->eventListener->onVideoPublishEvent($eventMock);
    }

    /**
     * Tests VideoEventListener->onVideoErrorEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnVideoErrorEventWrongParameter()
    {
        $eventMock = array();
        $this->eventListener->onVideoErrorEvent($eventMock);
    }

    /**
     * Tests VideoEventListener->onVideoCleanupEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnVideoCleanupEventWrongParameter()
    {
        $eventMock = array();
        $this->eventListener->onVideoCleanupEvent($eventMock);
    }


}

