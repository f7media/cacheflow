<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'Cacheflow',
    'description' => 'Continuous background refreshing of cached pages.',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'David Nax',
    'author_email' => 'dn@f7.de',
    'author_company' => 'F7 Media GmbH',
    'version' => '2.1.2',
    'constraints' => [
        'depends' => [
            'typo3' => '14.3.0-14.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
