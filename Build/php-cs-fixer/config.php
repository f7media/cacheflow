<?php

use PhpCsFixer\Finder;
use TYPO3\CodingStandards\CsFixerConfig;

$config = CsFixerConfig::create();
$config->setFinder(
    new Finder()
        ->ignoreVCSIgnored(true)
        ->in(dirname(__DIR__, 2) . '/')
);
return $config;
