<?php
/**
 * Declares the Customer Event Listener test case
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;

use ICANS\Bundle\IcansLoggingBundle\Listener\CustomerEventListener;
use Icans\Ecf\Component\Event\Content AS ContentEvent;
use Icans\Ecf\Component\Event\Customer\RegisterCustomerEvent;
use Icans\Ecf\Component\Event\Customer\UserMigrationEvent;
use Icans\Ecf\Component\Event\Customer\RoleChangeEvent;

/**
 * Test case for the Customer Event Listener
 *
 * @author      Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
class CustomerEventListenerTest extends BaseEventListenerTest
{
    /**
     * @var CustomerEventListener
     */
    private $eventListener;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();

        $this->eventListener = new CustomerEventListener($this->loggerMock);
    }

    /**
     * Tests CustomerEventListener->onRegisterEvent()
     */
    public function testOnRegisterEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $email = 'username';
        $locale = 'en';

        $contextArray = array (
            'secure_email' => $email,
            'prefferedLocale' => $locale,
        );
        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(RegisterCustomerEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onRegisterEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onMigrationEvent()
     */
    public function testOnMigrationEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');
        $contextArray = array (
            'secure_email' => 'psuser@test.com',
            'migrationSourceEcId' => 'pokerstrategy',
        );
        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(UserMigrationEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onMigrationEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onCreateContentEvent()
     */
    public function testOnCreateContentEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $email = 'example@domain.com';
        $locale = 'en';

        $contextArray = array (
            'email' => $email,
            'prefferedLocale' => $locale,
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(ContentEvent\CreateContentEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onCreateContentEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onFallbackContentDeliveredEvent()
     */
    public function testOnFallbackContentDeliveredEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $email = 'example@domain.com';
        $locale = 'en';

        $contextArray = array (
            'email' => $email,
            'prefferedLocale' => $locale,
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(ContentEvent\FallbackContentDeliveredEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onFallbackContentDeliveredEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onViewContentEvent()
     */
    public function testOnViewContentEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $email = 'example@domain.com';
        $locale = 'en';

        $contextArray = array (
            'email' => $email,
            'prefferedLocale' => $locale,
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(ContentEvent\ViewContentEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onViewContentEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onChangeContentEvent()
     */
    public function testOnChangeContentEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $email = 'example@domain.com';
        $locale = 'en';

        $contextArray = array (
            'email' => $email,
            'prefferedLocale' => $locale,
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(ContentEvent\ChangeContentEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onChangeContentEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onRoleChangeEvent()
     */
    public function testOnRoleChangeEvent()
    {
        $eventMock = $this->getMock('Icans\Ecf\Component\Event\EventInterface');

        $email = 'example@domain.com';
        $locale = 'en';

        $contextArray = array (
            'email' => $email,
            'prefferedLocale' => $locale,
        );

        $eventMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($contextArray));

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(RoleChangeEvent::EVENT_HANDLE, $contextArray);

        $this->eventListener->onRoleChangeEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onRegisterEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnRegisterEventWrongParameter()
    {
        $eventMock = array();

        $this->eventListener->onRegisterEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onCreateContentEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnCreateContentEventWrongParameter()
    {
        $eventMock = array();

        $this->eventListener->onCreateContentEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onFallbackContentDeliveredEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnFallbackContentDeliveredEventWrongParameter()
    {
        $eventMock = array();

        $this->eventListener->onFallbackContentDeliveredEvent($eventMock);
    }

    /**
     * Tests CustomerEventListener->onViewContentEvent()
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testOnViewContentEventWrongParameter()
    {
        $eventMock = array();

        $this->eventListener->onViewContentEvent($eventMock);
    }
}

