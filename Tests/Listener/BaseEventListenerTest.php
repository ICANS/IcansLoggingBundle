<?php
/**
 * Declares the customerEventListener test case
 *
 * @author      Oliver Buschjost
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @version     $Id: $
 */
namespace ICANS\Bundle\IcansLoggingBundle\Tests\Listener;


/**
 * Test case for the Customer Event Listener
 *
 * @author      Oliver Peymann
 * @copyright   ICANS GmbH
 * @group events
 */
abstract class BaseEventListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected  $loggerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $multiEcMock;

    /**
     * sets up the test case for the unit test
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loggerMock = $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();
        $this->multiEcMock = $this->getMockBuilder('Icans\Ecf\Component\MultiEC\EducationCommunitiesInterface')->getMock();
    }
}

