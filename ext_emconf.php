<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Cacheflow',
    'description' => 'Continuous background refreshing of cached pages.',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'David Nax',
    'author_email' => 'dn@f7.de',
    'author_company' => 'F7 Media GmbH',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
