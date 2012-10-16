<?php

$vendorDir = __DIR__.'/../vendor';
require_once $vendorDir.'/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => array($vendorDir.'/symfony/class-loader', $vendorDir),
    'Thrift' => array($vendorDir.'/ebuzzing/thrift', $vendorDir),
));
$loader->register();

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'ICANS\\Component\\IcansLoggingComponent\\')) {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 3)).'.php';
        if (!stream_resolve_include_path($path)) {
            return false;
        }
        require_once $path;
        return true;
    }
});
