#!/usr/bin/env php
<?php

/*
 * This file is part of the IcansLoggingBundle.
 *
 * (c) ICANS GmbH, Valentinskamp 18, 20354 Hamburg/Germany and individual contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*

CAUTION: This file installs the dependencies needed to run the IcansLoggingBundle test suite.

*/

set_time_limit(0);

if (!is_dir($vendorDir = dirname(__FILE__).'/vendor')) {
    mkdir($vendorDir, 0777, true);
}

// optional transport change
$transport = false;
if (isset($argv[1]) && in_array($argv[1], array('--transport=http', '--transport=https', '--transport=git'))) {
    $transport = preg_replace('/^--transport=(.*)$/', '$1', $argv[1]);
}

$deps = array(
    array('Icans', 'http://github.com/ICANS/IcansLoggingComponent.git'),
    array('monolog', 'http://github.com/Seldaek/monolog.git', '1.0.2'),
);

foreach ($deps as $dep) {
    list($name, $url, $rev) = $dep;

    if ($transport) {
        $url = preg_replace('/^(http:|https:|git:)(.*)/', $transport . ':$2', $url);
    }

    echo "> Installing/Updating $name\n";

    $installDir = $vendorDir.'/'.$name;
    if (!is_dir($installDir)) {
        system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));
    }

    system(sprintf('cd %s && git fetch origin && git reset --hard %s', escapeshellarg($installDir), escapeshellarg($rev)));
}
