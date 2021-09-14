<?php

return [
    'backend' => [
        'itsc/block-malicious-login-attempts' => [
            'target' => \ITSC\BlockMaliciousLoginAttempts\Middleware\BlockMaliciousLoginAttempts::class,
            'before' => [
                'typo3/cms-backend/authentication'
            ],
        ],
    ],
];