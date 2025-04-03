<?php

$EM_CONF[$_EXTKEY] = [
    'title' => '(FGTCLB) Page Backend Layout',
    'description' => 'Helper for Backend layout overrides based on page doktype',
    'category' => 'be',
    'state' => 'stable',
    'author' => 'FGTCLB',
    'author_email' => 'hello@fgtclb.com',
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-13.4.99',
            'backend' => '12.4.0-13.4.99',
            'fluid' => '12.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
