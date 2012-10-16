<?php
/**
 * AnalyticsProcessor Test case
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author    Sebastian Latza
 * @author    Oliver Peymann
 * @copyright (C) 2011 ICANS GmbH
 */
namespace Icans\Ecf\Bundle\LoggingBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;

/**
 * test case for the AnalyticsProcessor
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author    Sebastian Latza
 * @author    Oliver Peymann
 * @copyright (C) 2011 ICANS GmbH
 */
class AnalyticsProcessorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    private $sessionMock;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject
     */
    private $containerMock;

    /**
     * @var AnalyticsProcessor
     */
    private $analyticsProcessor;

    /**
     * @var string
     */
    private $pulseId = '1234';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $securedArea = null;

    /**
     * @var string
     */
    private $requestTime = '1340008395587';

    /**
     * @var string
     */
    private $sessionId = '123456';

    /**
     * @var string
     */
    private $userId = '23905742304';

    /**
     * set up the test case
     */
    protected function setUp()
    {
        $this->containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');


        // We cannot use a mock of the desired class as custom de/serialization kills the expects
        // $userMock = $this->getMockBuilder('Icans\Ecf\Bundle\SecurityBundle\User\CustomerVaultUser')
        $userMock = $this->getMockBuilder('\stdClass')
            ->setMethods(array('getId'))
            ->disableOriginalConstructor()
            ->getMock();

        // needs to be any as the serialized MockObject does not work with once or exactly
        $userMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($this->userId));

        // create a Mock of standardClass to serialize it - unfortunately this is not possible with
        // a mock of Symfony\Component\Security\Core\Authentication\Token\TokenInterface
        $stdMock = $this->getMock('\stdClass', array('getUser'));
        $stdMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($userMock));

        $serializedMock = serialize($stdMock);

        $this->securedArea = $serializedMock;

        $this->requestMock = $this->getMock('Symfony\Component\HttpFoundation\Request',
            array(),
            array(),
            '',
            false
        );

        $serverMock = $this->getMock('\Symfony\Component\HttpFoundation\ParameterBag',
            array(),
            array(),
            '',
            false
        );
        $serverMock->expects($this->once())
            ->method('get')
            ->with('HTTP_USER_AGENT')
            ->will($this->returnValue('dummyserver'));
        $this->requestMock->server =  $serverMock;

        $this->containerMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($this->requestMock));

        $headerBagMock = $this->getMock('\Symfony\Component\HttpFoundation\HeaderBag',
            array(),
            array(),
            '',
            false
        );

        $headerBagMock->expects($this->once())
            ->method('has')
            ->with('pulse')
            ->will($this->returnValue(true));

        $headerBagMock->expects($this->once())
            ->method('get')
            ->with('pulse')
            ->will($this->returnValue($this->pulseId));

        $this->requestMock->headers =  $headerBagMock;

        $this->requestMock->expects($this->once())
            ->method('hasSession')
            ->will($this->returnValue(true));

        $sessionMock = $this->getMock('Symfony\Component\HttpFoundation\Session',
            array(),
            array(),
            '',
            false
        );

        $sessionMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($this->sessionId));

        $sessionMock->expects($this->at(1))
            ->method('get')
            ->with('_request_time')
            ->will($this->returnValue($this->requestTime));

        $sessionMock->expects($this->at(2))
            ->method('get')
            ->with('_security_secured_area')
            ->will($this->returnValue($this->securedArea));

        $this->requestMock->expects($this->exactly(3))
            ->method('getSession')
            ->will($this->returnValue($sessionMock));

        $this->analyticsProcessor = new AnalyticsProcessor($this->containerMock);
    }

    /**
     * ensure the analytics record contains the correct fields
     */
    public function testProcessRecordWithGivenServiceType()
    {
        $record = array();

        $logLevelName = 'DEBUG';
        $logLevelValue = '200';

        $record['level'] = $logLevelValue;
        $record['message'] = 'message';
        $record['level_name'] = $logLevelName;
        $record['channel'] = 'channel';
        $record['datetime'] = new \DateTime('@' . $this->requestTime);
        $record['extra'] = array();

        $contextServiceType = 'RemoteService';
        $contextServiceComponent = 'CustomerVault';
        $contextServiceInstance = 'ecf-sandbox';
        $contextVersion = 1;

        $eventArray = array(
            'serviceType' => $contextServiceType,
            'serviceComponent' => $contextServiceComponent,
            'serviceInstance' => $contextServiceInstance,
            'version' => $contextVersion,
            'handle' => 'handle',
        );
        $record['context'] = $eventArray;

        $this->prepareMocks();

        $_SERVER['HTTP_HOST'] = 'testHost';

        $analyticsRecord = $this->analyticsProcessor->processRecord($record);

        $this->assertEquals($logLevelName, $analyticsRecord['message_loglevel']);
        $this->assertEquals($logLevelValue, $analyticsRecord['message_loglevel_value']);
        $this->assertEquals(AnalyticsProcessor::PULSE_PREFIX . $this->pulseId, $analyticsRecord['pulse']);
        $this->assertEquals(1, $analyticsRecord['envelope_version']);
        $this->assertEquals('app', $analyticsRecord['origin_type']);

        $this->assertEquals($contextServiceType, $analyticsRecord['origin_service_type']);
        $this->assertEquals($contextServiceComponent, $analyticsRecord['origin_service_component']);
        $this->assertEquals($contextServiceInstance, $analyticsRecord['origin_service_instance']);
        $this->assertEquals($contextVersion, $analyticsRecord['event_version']);

        // we only check the existence of the "renderStartTime" field, as the source and format of this representation
        // isn't known during this unittest ..
        // @TODO: factor out timestamp creation and formatting
        $this->assertNotEmpty($analyticsRecord['event_body']['extra']['request']['renderStartTime']);
        $this->assertRegExp(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{8}[+\-]\d{4}$/',
            $analyticsRecord['event_body']['extra']['request']['renderStartTime']);
        // .. but at least, we're able to ensure the stored timestamp is our test-start time (or something greater as,
        // time goes by ;))
        $this->assertGreaterThanOrEqual(
            $this->requestTime,
            $analyticsRecord['event_body']['extra']['request']['renderStartTimestamp']);
        $this->assertInternalType('integer', $analyticsRecord['event_body']['extra']['request']['renderStartTimestamp']);

        $this->assertEquals($this->sessionId, $analyticsRecord['event_body']['extra']['request']['Session']);

        $this->assertEquals($this->sessionId, $analyticsRecord['event_body']['extra']['request']['Session']);
        $this->assertEquals($this->userId, $analyticsRecord['event_body']['extra']['request']['customerId']);
    }

    /**
     * ensure the analytics record contains the correct fields
     */
    public function testProcessRecordWithOutGivenServiceType()
    {
        $record = array();

        $logLevelName = 'DEBUG';
        $logLevelValue = '600';

        $record['level'] = $logLevelValue;
        $record['message'] = 'message';
        $record['level_name'] = $logLevelName;
        $record['channel'] = 'channel';
        $record['datetime'] = new \DateTime('@' . $this->requestTime);
        $record['extra'] = array();
        $record['context'] = array();

        $this->prepareMocks();

        $_SERVER['HTTP_HOST'] = 'testHost';

        $analyticsRecord = $this->analyticsProcessor->processRecord($record);

        $this->assertEquals($logLevelName, $analyticsRecord['message_loglevel']);
        $this->assertEquals($logLevelValue, $analyticsRecord['message_loglevel_value']);
        $this->assertEquals(AnalyticsProcessor::PULSE_PREFIX . $this->pulseId, $analyticsRecord['pulse']);
        $this->assertEquals(1, $analyticsRecord['envelope_version']);
        $this->assertEquals('app', $analyticsRecord['origin_type']);

        $this->assertEquals('genericService', $analyticsRecord['origin_service_type']);
        $this->assertEquals('ecf', $analyticsRecord['origin_service_component']);
        $this->assertEquals('symfony', $analyticsRecord['origin_service_instance']);

        $this->assertEquals('symfony.exception.' .$record['channel'], $analyticsRecord['event_handle']);

        // we only check the existence of the "renderStartTime" field, as the source and format of this representation
        // isn't known during this unittest ..
        // @TODO: factor out timestamp creation and formatting
        $this->assertNotEmpty($analyticsRecord['event_body']['extra']['request']['renderStartTime']);
        // .. but at least, we're able to ensure the stored timestamp is our test-start time (or something greater,
        // as time goes by ;))
        $this->assertGreaterThanOrEqual(
            $this->requestTime,
            $analyticsRecord['event_body']['extra']['request']['renderStartTimestamp']);
        $this->assertEquals($this->sessionId, $analyticsRecord['event_body']['extra']['request']['Session']);
        $this->assertEquals($this->userId, $analyticsRecord['event_body']['extra']['request']['customerId']);
    }

    /**
     * ensure the magic method __invoke is working fine
     */
    public function testInvokeMethod()
    {
        $record = array();

        $logLevelName = 'DEBUG';
        $logLevelValue = '600';

        $record['level'] = $logLevelValue;
        $record['message'] = 'message';
        $record['level_name'] = $logLevelName;
        $record['channel'] = 'channel';
        $record['datetime'] = new \DateTime('@' . $this->requestTime);
        $record['extra'] = array();
        $record['context'] = array();

        $_SERVER['HTTP_HOST'] = 'testHost';

        $analyticsRecord = $this->analyticsProcessor->__invoke($record);

        $this->assertEquals($logLevelName, $analyticsRecord['message_loglevel']);
        $this->assertEquals($logLevelValue, $analyticsRecord['message_loglevel_value']);
        $this->assertEquals(AnalyticsProcessor::PULSE_PREFIX . $this->pulseId, $analyticsRecord['pulse']);
        $this->assertEquals(1, $analyticsRecord['envelope_version']);
        $this->assertEquals('app', $analyticsRecord['origin_type']);

        $this->assertEquals('genericService', $analyticsRecord['origin_service_type']);
        $this->assertEquals('ecf', $analyticsRecord['origin_service_component']);
        $this->assertEquals('symfony', $analyticsRecord['origin_service_instance']);

        $this->assertEquals('symfony.exception.' .$record['channel'], $analyticsRecord['event_handle']);

        // we only check the existence of the "renderStartTime" field, as the source and format of this representation
        // isn't known during this unittest ..
        // @TODO: factor out timestamp creation and formatting
        $this->assertNotEmpty($analyticsRecord['event_body']['extra']['request']['renderStartTime']);
        // .. but at least, we're able to ensure the stored timestamp is our test-start time (or something greater,
        // as time goes by ;))
        $this->assertGreaterThanOrEqual(
            $this->requestTime,
            $analyticsRecord['event_body']['extra']['request']['renderStartTimestamp']);
        $this->assertEquals($this->sessionId, $analyticsRecord['event_body']['extra']['request']['Session']);
        $this->assertEquals($this->userId, $analyticsRecord['event_body']['extra']['request']['customerId']);
    }

    /**
     * set up some mocks and their behaviour
     */
    private function prepareMocks()
    {
        $this->requestMock->expects($this->once())
            ->method('getRequestUri')
            ->will($this->returnValue('requestUri'));

        $this->requestMock->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('methodName'));

        $this->requestMock->expects($this->once())
            ->method('getBaseUrl')
            ->will($this->returnValue('baseUrl'));

        $this->requestMock->expects($this->once())
            ->method('getClientIp')
            ->will($this->returnValue('ipAdress'));

        $this->requestMock->expects($this->once())
            ->method('getCharsets')
            ->will($this->returnValue('charssets'));

        $this->requestMock->expects($this->once())
            ->method('getLanguages')
            ->will($this->returnValue('enDe'));

        $this->requestMock->expects($this->once())
            ->method('getScriptName')
            ->will($this->returnValue('scriptName'));

        $this->requestMock->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('uri'));

        $this->requestMock->expects($this->once())
            ->method('hasPreviousSession')
            ->will($this->returnValue('prevSession'));

        $this->requestMock->expects($this->once())
            ->method('isSecure')
            ->will($this->returnValue('notSecure'));

        $this->requestMock->expects($this->once())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue('no'));
    }
}

