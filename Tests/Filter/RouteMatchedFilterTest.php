<?php
/**
 * Declares the RouteMatchedFilterTest class.
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author    Malte Stenzel
 * @copyright 2012 ICANS GmbH (http://www.icans-gmbh.com)
 */
namespace ICANS\Bundle\IcansLoggingBundle\Filter;

/**
 * Tests the RouteMatchedFilter implementation used to check if the record contains a value "Route Matched" for the key
 * ['body']['eventBody']
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @author    Malte Stenzel
 * @copyright 2012 ICANS GmbH (http://www.icans-gmbh.com)
 */
class RouteMatchedFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests if filter finds "route matched" value in record
     * @dataProvider testFilterRouteMatchedTrueDataProvider
     */
    public function testFilterRouteMatchedTrue(array $record) {
        $routeMatchedFilter = new RouteMatchedFilter();
        $this->assertTrue($routeMatchedFilter->isRecordToBeFiltered($record));
    }

    /**
     * Data Provider for testFilterRouteMatchedTrue
     * @return array
     */
    public function testFilterRouteMatchedTrueDataProvider() {
        return array(
            // Testlauf
            array(
                array('bar' => 'baz', 'body' => array('test' => 1, 'event_body' => 'Route Matched')),
            ),
        );
    }

    /**
     * Tests if filter returns false if no "route matched" value has been found
     * @dataProvider testFilterRouteMatchedFalseDataProvider
     */
    public function testFilterRouteMatchedFalse(array $record) {
        $routeMatchedFilter = new RouteMatchedFilter();
        $this->assertFalse($routeMatchedFilter->isRecordToBeFiltered($record));
    }

    /**
     * Data Provider for testFilterRouteMatchedFalse
     * @return array
     */
    public function testFilterRouteMatchedFalseDataProvider() {
        return array(
            // Testlauf
            array(
                array('bar' => 'baz', 'body' => array('test' => 1, 'event_body' => 'Route not Matched')),
            ),
            array( // Key not found => false
                array(),
            ),
            array( // Key not found => false
                array('bar' => 'baz', 'testDT' => new \DateTime()),
            ),
        );
    }

}