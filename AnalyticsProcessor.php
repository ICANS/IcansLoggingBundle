<?php
/**
 * Declares AnalyticsProcessor
 *
 * @author    Sebastian Latza
 * @copyright (C) 2011 ICANS GmbH
 */
namespace Icans\Ecf\Bundle\LoggingBundle;

use Icans\Ecf\Component\ContentService\Api\V1\UserProfile\UserProfileInterface;
use Icans\Ecf\Component\CustomerVault\Api\V0\CustomerVaultUserInterface;
use Icans\Ecf\Component\EcfConstants\LogEvent;
use Icans\Ecf\Component\Logging\PostProcessorInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InactiveScopeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * AnalyticsProcessor enriches the log body with some global request data
 *
 * @author    Sebastian Latza
 * @copyright (C) 2011 ICANS GmbH
 */
class AnalyticsProcessor implements PostProcessorInterface
{
    /**
     * Constant for pulsePrefix
     * @var string
     */
    const PULSE_PREFIX = 'pulse_';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var pulseId
     */
    private $pulse;

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function processRecord(array $record)
    {
        $analyticsRecord = array();
        $analyticsRecord['origin_type'] = 'app';
        if (isset($_SERVER['HTTP_HOST'])) {
            $analyticsRecord['origin_host'] = $_SERVER['HTTP_HOST'];
        }
        $microtime = round(microtime(true) * 1000);
        $analyticsRecord['created_timestamp'] = $microtime;
        $analyticsRecord['created_date'] = date(sprintf('Y-m-d\TH:i:s.%sO', substr($microtime, 1, 8)));
        $analyticsRecord['envelope_version'] = 1;
        $analyticsRecord['message_loglevel_value'] = $record['level'];
        $analyticsRecord['message_loglevel'] = $record['level_name'];


        $event = $record['context'];
        // rawData are not needed here -> are used for update search index
        unset($event['rawData']);

        // Decide oif this is a symfony logmessage or a self-created event
        // Self-created events always have a serviceType
        if (isset($event['serviceType'])) {
            // Defines the service class, e.g. RemoteService
            $analyticsRecord['origin_service_type'] = $event['serviceType'];
            // Defines the service component, e.g. CustomerVault, EC
            $analyticsRecord['origin_service_component'] = $event['serviceComponent'];
            // Defines the service instance, e.g. ecf-sandbox
            $analyticsRecord['origin_service_instance'] = $event['serviceInstance'];
            // Defines the event handle, e.g. icans.customer.register
            $analyticsRecord['event_handle'] = $record['message'];
            // The event version, e.g. '1'
            $analyticsRecord['event_version'] = $event['version'];
            unset($event['serviceType']);
            unset($event['serviceInstance']);
            unset($event['serviceComponent']);
            unset($event['handle']);
            unset($event['version']);
            $analyticsRecord['event_body'] = $event;
        // All other events are handled in a more generic way
        } else {
            // origin fields implemented empty until we decide how to fill them for generic logmessages
            $analyticsRecord['origin_service_type'] = 'genericService';
            $analyticsRecord['origin_service_component'] = 'ecf';
            $analyticsRecord['origin_service_instance'] = 'symfony';

            // If the loglevel is above 300, this is an exception.
            if ($record['level'] > 300) {
                $analyticsRecord['event_handle'] = 'symfony.exception.' . $record['channel'];
            } else {
                $analyticsRecord['event_handle'] = 'symfony.event.' . $record['channel'];
            }

            $analyticsRecord['event_version'] = '1';
            $analyticsRecord['event_body']['message'] = $record['message'];
        }

        $sessionId = 'no-session-existing';

        try {
            /* @var $request Request */
            $request = $this->container->get('request');

            if ($request->headers->has('pulse')) {
                $analyticsRecord['pulse'] = self::PULSE_PREFIX . $request->headers->get('pulse');
            } else {

                if (!isset($this->pulse)) {
                    $this->pulse = uniqid(self::PULSE_PREFIX);
                }
                $analyticsRecord['pulse'] = $this->pulse;
            }

            // Field values as requested by the analytics team
            $fields = array ();
            $fields['Browser'] = $request->server->get('HTTP_USER_AGENT');
            $fields['RequestUri'] = $request->getRequestUri();
            $fields['Method'] = $request->getMethod();
            $fields['BaseUrl'] = $request->getBaseUrl();
            $fields['ClientIp'] = $request->getClientIp();
            $fields['Charsets'] = $request->getCharsets();
            $fields['Languages'] = $request->getLanguages();
            $fields['ScriptName'] = $request->getScriptName();
            $fields['Uri'] = $request->getUri();
            $fields['hasPreviousSession'] = $request->hasPreviousSession();
            $fields['isSecure'] = $request->isSecure();
            $fields['isXmlHttpRequest'] = $request->isXmlHttpRequest();

            if ($request->hasSession()) {

                $sessionId = $request->getSession()->getId();
                $microtime = (integer) $request->getSession()->get('_request_time');
                $securedArea = unserialize($request->getSession()->get('_security_secured_area'));

                if (!empty($securedArea)) {
                    /* @var $user CustomerVaultUserInterface */
                    $user = $securedArea->getUser();

                    if (!empty($user)) {
                        $fields[LogEvent::CUSTOMER_ID] = $user->getId();
                    }
                }
            }
        } catch (InactiveScopeException $exception) {
            if (PHP_SAPI != 'cli') {
                $fields['RequestScopeError'] = $exception->getMessage();
            }
        }

        $fields['Session'] = $sessionId;

        // Convert microtime into something DateTime accepts
        $fields['renderStartTime'] = date(sprintf('Y-m-d\TH:i:s.%sO', substr($microtime, 1, 8)));
        $fields['renderStartTimestamp'] = $microtime;
        $analyticsRecord['event_body']['extra']['request'] = $fields;

        return $analyticsRecord;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($record)
    {
        return $this->processRecord($record);
    }
}
