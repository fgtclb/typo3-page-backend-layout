<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Page Backend Layout',
    'description' => 'Helper for Backend layout overrides based on page doktype',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'FGTCLB',
    'author_email' => 'hello@fgtclb.com',
    'clearCacheOnLoad' => 0,
    'version' => '1.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
