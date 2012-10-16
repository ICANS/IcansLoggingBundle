<?php
/**
 * Declares the Search Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\SearchEventListener;
use Icans\Ecf\Component\Event\Search\SearchQueryEvent;

/**
 * Test case for the Search Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class SearchEventListenerTest extends BaseEventListenerTest
{
    /**
     * @var SearchEventListener
     */
    private $eventListener;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventListener = new SearchEventListener($this->loggerMock);
    }

    /**
     * Tests SearchEventListener->onSearchQueryEvent()
     */
    public function testOnSearchQueryEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $searchQuery = 'TROLOLOL';

        $contextArray = array (
                'searchQuery' => $searchQuery,
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(\Icans\Ecf\Component\Event\Search\SearchQueryEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onSearchQueryEvent($eventMock);
    }



    /**
     * Tests SearchEventListener->onSearchQueryEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnSearchQueryEventWrongParameter()
    {
        $eventMock = array();
        $this->eventListener->onSearchQueryEvent($eventMock);
    }


}

