<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => '(FGTCLB) Page Backend Layout',
    'description' => 'Helper for Backend layout overrides based on page doktype',
    'category' => 'be',
    'state' => 'stable',
    'version' => '1.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
