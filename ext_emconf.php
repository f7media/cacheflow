<?php

$EM_CONF['cacheflow'] = [
    'title' => 'Cacheflow',
    'description' => 'Continuous background refreshing of cached pages.',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'David Nax',
    'author_email' => 'dn@f7.de',
    'author_company' => 'F7 Media GmbH',
    'version' => '1.1.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
