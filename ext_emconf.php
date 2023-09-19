<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '(FGTCLB) Page Backend Layout',
    'description' => 'Helper for Backend layout overrides based on page doktype',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'FGTCLB',
    'author_email' => 'hello@fgtclb.com',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
