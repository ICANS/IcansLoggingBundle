<?php
/**
 * Defines EventFactoryTest class
 *
 * @author Stefan Miertschink
 * @copyright ICANS GmbH 2012
 * @version $Id: $
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests;

use ICANS\Bundle\IcansLoggingBundle\EventFactory;
use Icans\Ecf\Component\Event\Application\ContentCreatedAndPlacedEvent;
use Icans\Ecf\Component\Event\Customer\CustomerStatuslevelChangedEvent;
use Icans\Ecf\Component\Event\Customer\RegisterCustomerEvent;
use Icans\Ecf\Component\Event\Customer\UserMigrationEvent;
use Icans\Ecf\Component\Event\Customer\EmailVerificationEvent;
use Icans\Ecf\Component\Event\Customer\RequestEmailVerificationEvent;
use Icans\Ecf\Component\Event\Customer\PasswordRecoveryEvent;
use Icans\Ecf\Component\Event\Customer\RoleChangeEvent;
use Icans\Ecf\Component\Event\Content\CreateContentEvent;
use Icans\Ecf\Component\Event\Content\ChangeContentEvent;
use Icans\Ecf\Component\Event\Content\FallbackContentDeliveredEvent;
use Icans\Ecf\Component\Event\Content\ViewContentEvent;
use Icans\Ecf\Component\Event\CommObject\CommunicationObjectsActiveStateChangedEvent;
use Icans\Ecf\Component\Event\CommObject\CreateCommObjectEvent;
use Icans\Ecf\Component\Event\Image\ResizeImageEvent;
use Icans\Ecf\Component\Event\Search\SearchQueryEvent;
use Icans\Ecf\Component\Event\Form\ErrorEvent as FormErrorEvent;
use Icans\Ecf\Component\Event\Content\Repository\IllegalQueryEvent;
use Icans\Ecf\Component\Event\Video\ErrorEvent as VideoErrorEvent;
use Icans\Ecf\Component\Event\Video\PublishEvent;
use Icans\Ecf\Component\Event\Video\CleanupEvent;
use Icans\Ecf\Component\Event\Content\AttachContentToNodeEvent;
use Icans\Ecf\Component\Event\Content\Structure\RelocateNodeEvent;
use Icans\Ecf\Component\Event\Cache\ContentCacheMissEvent;
use Icans\Ecf\Component\Event\Messaging\MessageEvent;
use Icans\Ecf\Component\EcfConstants\LogEvent;

/**
 * tests the eventFactoryClass
 *
 * @author Stefan Miertschink
 * @copyright ICANS GmbH 2012
 *
 * @group events
 */
class EventFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventFactory
     */
    protected $eventFactory;

    /**
     * @var string
     */
    protected $callingClass;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcherMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * create the eventFactory
     */
    public function setUp()
    {
        $serviceType        = 'serviceType';
        $serviceInstance    = 'serviceInstance';
        $this->callingClass = 'callingClass';

        $this->dispatcherMock = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');

        $this->containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->loggerMock = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');

        $this->eventFactory = new EventFactory(
            $this->containerMock,
            $this->dispatcherMock,
            $serviceType,
            $serviceInstance,
            $this->loggerMock
        );
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CustomerRegistered Event
     */
    public function testDispatchRegisterCustomerEvent()
    {
        $verificationLink = 'http://example.com/token';
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(RegisterCustomerEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchRegisterCustomerEvent(
            $this->callingClass,
            'email',
            'xx',
            'emai',
            $verificationLink,
            'ecId',
            'customerId'
        );
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CustomerRegistered Event
     */
    public function testDispatchUserMigrationEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(UserMigrationEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchUserMigrationEvent(
            $this->callingClass,
            'screenName',
            'xx',
            'email',
            'ecId',
            'customerId',
            'user_login_idx',
            'pokerstrategy'
        );
    }

    /**
     * Tests that request verification mail event is dispatched properly
     */
    public function testDispatchRequestEmailVerificationEvent()
    {
        $verificationLink = 'http://example.com/token';
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(RequestEmailVerificationEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchRequestEmailVerificationEvent(
            $this->callingClass,
            'screenName',
            'xx',
            'email',
            $verificationLink,
            'ecId',
            'customerId'
        );
    }

    /**
     * Tests that verification event is dispatched properly
     */
    public function testDispatchEmailVerificationEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(EmailVerificationEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchEmailVerificationEvent(
            $this->callingClass,
            'customerId',
            'screenName',
            'ecId'
        );
    }

    /**
     * Tests that password recovery event is dispatched properly
     */
    public function testDispatchPasswordRecoveryEvent()
    {
        $verificationLink = 'http://example.com/token';
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(PasswordRecoveryEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchPasswordRecoveryEvent(
            $this->callingClass,
            'screenName',
            'xx',
            'emai',
            $verificationLink,
            'ecId'
        );
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CustomerRegistered Event
     */
    public function testDispatchRoleChangeEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(RoleChangeEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchRoleChangeEvent($this->callingClass, 'userId', 'ecId', 'userName', array('role'));
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CreateContent Event
     */
    public function testDispatchCreateContentEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with('icans.content.create');
        $this->eventFactory->dispatchCreateContentEvent($this->callingClass, 'type', 'cgi', 'xx', 'liveId', 'author',
            array('rawdata'));
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CreateContent Event
     */
    public function testDispatchUpdateContentEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(ChangeContentEvent::EVENT_HANDLE);

        $authorProfileMock = $this->getMock(
            'Icans\Ecf\Component\ContentService\Api\V1\UserProfile\UserProfileInterface'
        );
        $authorProfileMock->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue('customerId'));

        $contentPropertiesMock = $this->getMock('Icans\Ecf\Component\ContentService\Api\V1\ContentInterface');
        $contentPropertiesMock->expects($this->once())
            ->method('getAuthorProfile')
            ->will($this->returnValue($authorProfileMock));


        $localizationPropertiesMock = $this->getMock(
            'Icans\Ecf\Component\ContentService\Api\V1\LocalizationInterface'
        );
        $versionPropertiesMock      = $this->getMock('Icans\Ecf\Component\ContentService\Api\V1\VersionInterface');

        $this->eventFactory->dispatchUpdateContentEvent(
            $this->callingClass,
            $contentPropertiesMock,
            $localizationPropertiesMock,
            $versionPropertiesMock,
            array('changedProperties'),
            array('rawData')
        );
    }

    /**
     * ensure the correct handle is dispatched when dispatching a FallbackContentDelivered Event
     */
    public function testDispatchFallbackContentDeliveredEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(FallbackContentDeliveredEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchFallbackContentDeliveredEvent($this->callingClass, 'cgi', 'xx');
    }

    /**
     * ensure the correct handle is dispatched when dispatching a ViewContent Event
     */
    public function testDispatchViewContentEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(ViewContentEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchViewContentEvent($this->callingClass, 'cgi', 'xx', 'en');
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CreateCommObject Event
     */
    public function testDispatchCommunicationObjectsActiveStateChange()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(CommunicationObjectsActiveStateChangedEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchCommunicationObjectsActiveStateChange(
            $this->callingClass,
            array('foo'),
            'nodeFoo',
            'parentBar',
            'localeFoo',
            false
        );
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CreateCommObject Event
     */
    public function testDispatchCreateCommObjectEvent()
    {
        $commObjectMock = $this->getMock(
            '\Icans\Ecf\Component\ContentService\Api\V1\CommunicationObject\CommunicationObjectInterface'
        );
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            CreateCommObjectEvent::EVENT_HANDLE
        );
        $nodeMock = $this->getMock('\Icans\Ecf\Component\ContentStructure\Api\V1\StructuralNodeInterface');

        $this->eventFactory->dispatchCreateCommObjectEvent(
            $this->callingClass,
            $nodeMock,
            'xx',
            $commObjectMock
        );
    }

    /**
     * ensure the correct handle is dispatched when dispatching a ResizeImageEvent
     */
    public function testDispatchResizeImageEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(ResizeImageEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchResizeImageEvent($this->callingClass, 'identifier', 100, 200);
    }

    /**
     * ensure the correct handle is dispatched when dispatching a ResizeImageEvent
     */
    public function testDispatchSearchQueryEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(SearchQueryEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchSearchQueryEvent($this->callingClass, 'tradimo');
    }

    /**
     * ensure the correct handle is dispatched when dispatching a Form ErrorEvent
     */
    public function testDispatchFormErrorEvent()
    {
        $this->dispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(FormErrorEvent::EVENT_HANDLE);
        $this->eventFactory->dispatchFormErrorEvent($this->callingClass, 'field', 'message');
    }

    /**
     * ensure the correct handle is dispatched when dispatching a ContentCreatedAndPlacedEvent
     */
    public function testDispatchContentCreatedAndPlacedEvent()
    {
        $nodeMock = $this->getMock('Icans\Ecf\Component\ContentStructure\Api\V1\StructuralNodeInterface');

        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            ContentCreatedAndPlacedEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Application\ContentCreatedAndPlacedEvent')
        );

        $this->eventFactory->dispatchContentCreatedAndPlacedEvent(
            $this->callingClass,
            $nodeMock,
            'cgi',
            'locale'
        );
    }

    /**
     * ensure the correct handle is dispatched when dispatching a CustomerStatusChangeEvent
     */
    public function testDispatchCustomerStatusChangeEvent()
    {
        $oldStatuslevelMock = $this->getMock('Icans\Ecf\Component\Statuslevel\StatuslevelInterface');
        $newStatuslevelMock = $this->getMock('Icans\Ecf\Component\Statuslevel\StatuslevelInterface');

        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            CustomerStatuslevelChangedEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Customer\CustomerStatuslevelChangedEvent')
        );

        $this->eventFactory->dispatchCustomerStatusChangedEvent(
            $this->callingClass,
            'foo',
            $newStatuslevelMock,
            $oldStatuslevelMock,
            'bar'
        );
    }

    /**
     * Ensures the correct handling for the illegal query event dispatching.
     */
    public function testDispatchIllegalQueryEvent()
    {
        $query = "'some':'test'";

        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            IllegalQueryEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Content\Repository\IllegalQueryEvent')
        );

        $this->eventFactory->dispatchIllegalQueryEvent($this->callingClass, $query);
    }

    /**
     * Ensures correct handling of video error event
     */
    public function testDispatchVideoErrorEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            VideoErrorEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Video\ErrorEvent')
        );
        $this->eventFactory->dispatchVideoErrorEvent($this->callingClass, 'storageIdentifier', 'errorText');
    }

    /**
     * Ensures correct handling of
     */
    public function testDispatchVideoPublishEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            PublishEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Video\PublishEvent')
        );
        $this->eventFactory->dispatchVideoPublishEvent($this->callingClass, 'source', 'destination', 'rights');
    }

    /**
     * Ensures correct handling of
     */
    public function testDispatchVideoCleanupEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            CleanupEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Video\CleanupEvent')
        );
        $this->eventFactory->dispatchVideoCleanupEvent($this->callingClass, 'destination', 'reason');
    }

    /**
     * Ensures correct handling of
     */
    public function testDispatchAttachContentToNodeEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            AttachContentToNodeEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Content\AttachContentToNodeEvent')
        );
        $this->eventFactory->dispatchAttachContentToNodeEvent($this->callingClass, 'node', 'contentGroupIdentifier',
            'id', 'type', array());
    }

    /**
     * Ensures correct handling of
     */
    public function testDispatchRelocateNodeEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            RelocateNodeEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Content\Structure\RelocateNodeEvent')
        );

        $nodeMock = $this->getMock('Icans\Ecf\Component\ContentStructure\Api\V1\StructuralNodeInterface');
        $newParentMock = $this->getMock('Icans\Ecf\Component\ContentStructure\Api\V1\StructuralNodeInterface');

        $this->eventFactory->dispatchRelocateNodeEvent($this->callingClass, $nodeMock, $newParentMock);
    }

    /**
     * Ensures correct handling of
     */
    public function testDispatchContentCacheMissEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            ContentCacheMissEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Cache\ContentCacheMissEvent')
        );

        $this->eventFactory->dispatchContentCacheMissEvent($this->callingClass, 'contentFetchMethod',
            'contentSelector', 'contentRawData');
    }

    /**
     * Ensures correct handling of
     */
    public function testDispatchNotificationEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            MessageEvent::EVENT_HANDLE,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Messaging\MessageEvent')
        );

        $this->eventFactory->dispatchNotificationEvent($this->callingClass,
            'type',
            'mesageId',
            'locale',
            'recipient',
            'subject',
            'body');
    }

    /**
     * Ensures correct handling of
     */
    public function testDispatchReflexMailEvent()
    {
        $this->dispatcherMock->expects($this->once())->method('dispatch')->with(
            LogEvent::REFLEX_MAIL_ID,
            $this->isInstanceOf('Icans\Ecf\Component\Event\Messaging\MessageEvent')
        );

        $this->eventFactory->dispatchReflexMailEvent($this->callingClass,
            'type',
            'mesageId',
            'locale',
            'recipient',
            'sender',
            'subject',
            'body'
        );
    }
}
