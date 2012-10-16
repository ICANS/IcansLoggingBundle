<?php
/**
 * Declares the Page Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\PageEventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Test case for the Page Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class PageEventListenerTest extends BaseEventListenerTest
{
    /**
     * @var PageEventListener
     */
    private $eventListener;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventListener = new PageEventListener($this->loggerMock, $this->multiEcMock);
    }

    /**
     * PageEventListener -> onPageRequestEvent()
     */
    public function testOnPageRequestEventMasterRequest()
    {
        $eventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $sessionMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));

        $eventMock->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($requestMock));

        $requestMock->expects($this->once())
            ->method('hasSession')
            ->will($this->returnValue(true));

        $requestMock->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($sessionMock));

        $sessionMock->expects($this->exactly(1))
            ->method('set');

        $this->eventListener->onPageRequestEvent($eventMock);

    }

    /**
     * PageEventListener -> onPageRequestEvent()
     */
    public function testOnPageRequestEventSubRequest()
    {
        $eventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $requestMock = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $sessionMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::SUB_REQUEST));

        $eventMock->expects($this->never())
            ->method('getRequest');

        $requestMock->expects($this->never())
            ->method('getSession');

        $sessionMock->expects($this->never())
            ->method('set');

        $this->eventListener->onPageRequestEvent($eventMock);

    }

    /**
     *  PageEventListener -> onPageResponseEvent
     */
    public function testOnPageResponseEventMasterRequest()
    {
        $eventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock =  $this->getMock('Symfony\Component\HttpFoundation\Response');

        $eventMock->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));

        $eventMock->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($responseMock));

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with('icans.pagehit');

        $this->eventListener->onPageResponseEvent($eventMock);
    }

    /**
     *  PageEventListener -> onPageResponseEvent
     */
    public function testOnPageResponseEventSubRequest()
    {
        $eventMock = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock =  $this->getMock('Symfony\Component\HttpFoundation\Response');

        $eventMock->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::SUB_REQUEST));

        $eventMock->expects($this->never())
            ->method('getResponse');

        $responseMock->expects($this->never())
            ->method('getStatusCode');

        $this->loggerMock->expects($this->never())
            ->method('info');

        $this->eventListener->onPageResponseEvent($eventMock);
    }

    /**
     * Tests PageEventListener->onPageRequestEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnPageRequestEventWrongParameter()
    {
        $eventMock = array();

        $this->eventListener->onPageRequestEvent($eventMock);
    }

    /**
     * Tests PageEventListener->onPageResponseEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnPageResponseEventWrongParameter()
    {
        $eventMock = array();

        $this->eventListener->onPageResponseEvent($eventMock);
    }


}

