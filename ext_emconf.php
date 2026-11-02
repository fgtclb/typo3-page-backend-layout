<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'FGTCLB: Page Backend Layout',
    'description' => 'Helper for Backend layout overrides based on page doktype',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'FGTCLB',
    'author_email' => 'hello@fgtclb.com',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.3.99',
            'backend' => '13.4.0-14.3.99',
            'fluid' => '13.4.0-14.3.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
