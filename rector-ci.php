<?php
declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Privatization\Rector\Class_\FinalizeTestCaseClassRector;

return RectorConfig::configure()
    ->withPhpSets()
    ->withAttributesSets(phpunit: true)
    ->withComposerBased(phpunit: true)
    ->withRules([
        FinalizeTestCaseClassRector::class,
    ])
    ->withSkip([
        __DIR__ . '/rector-ci.php',
    ])
    ->withImportNames();
