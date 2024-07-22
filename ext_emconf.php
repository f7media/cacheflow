<?php
$EM_CONF['cacheflow'] = [
    'title' => 'Cacheflow',
    'description' => 'Continuous background refreshing of cached pages.',
    'category' => 'be',
    'state' => 'stable',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 0,
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
