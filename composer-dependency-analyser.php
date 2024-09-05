<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    //// Adjusting scanned paths
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->disableComposerAutoloadPathScan() // disable automatic scan of autoload & autoload-dev paths from composer.json
    ->ignoreErrorsOnPackage('sylius/sylius', [ErrorType::DEV_DEPENDENCY_IN_PROD])
    ->ignoreErrorsOnPackage('sylius/core-bundle', [ErrorType::UNUSED_DEPENDENCY])
;
