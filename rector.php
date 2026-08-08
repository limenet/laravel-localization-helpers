<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withSkip([
        // Generated and fixture files are compared verbatim by the test suite.
        __DIR__.'/tests/lang',
        __DIR__.'/tests/mock',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: false,
        instanceOf: true,
        earlyReturn: true,
    )
    ->withPhpSets()
    ->withAttributesSets(symfony: true, phpunit: true)
    ->withComposerBased(phpunit: true, symfony: true, laravel: true);
