<?php
/**
 * Implements a filter for a monolog record to identify if "Route Matched"
 *
 * @author    Mike Lohmann <mike.lohmann@icans-gmbh.com>
 * @copyright 2011 ICANS GmbH (http://www.icans-gmbh.com)
 */
namespace ICANS\Bundle\IcansLoggingBundle\Filter;

use Icans\Ecf\Component\Logging\FilterInterface;

/**
 * Filters the record for Route Matched
 */
class RouteMatchedFilter implements FilterInterface
{
    /**
     * Const for the string to match against
     */
    const ROUTE_MATCHED = "Route Matched";

    /**
     * {@inheritDoc}
     */
    public function isRecordToBeFiltered(array $record)
    {
        $length = strlen(self::ROUTE_MATCHED);
        return isset($record['body']['event_body']) && (substr($record['body']['event_body'], 0,
            $length) === self::ROUTE_MATCHED);
    }
}
