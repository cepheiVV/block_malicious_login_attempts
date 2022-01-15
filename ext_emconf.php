<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Block Malicious Login Attempts',
    'description' => 'Blocks IP from accessing the website when multiple login attempts fail',
    'category' => 'misc',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5 || 10.4 || 11.5'
        ]
    ],
    'autoload' => [
        'psr-4' => [
            'ITSC\\BlockMaliciousLoginAttempts\\' => 'Classes'
        ],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '1.3.0',
];

