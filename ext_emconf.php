<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Block Malicious Login Attempts',
    'description' => 'Blocks IP from accessing the website when multiple login attempts fail',
    'category' => 'misc',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4'
        ]
    ],
    'autoload' => [
        'psr-4' => [
            'ITSC\\BlockMaliciousLoginAttempts\\' => 'Classes'
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'version' => '1.1.0',
];

