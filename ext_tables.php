<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postLoginFailureProcessing'][] = \ITSC\BlockMaliciousLoginAttempts\Hooks\PostLoginFailureProcessing::class . '->saveFailedLoginAttempt';


if (TYPO3_branch == "9.5") {
    // v9
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'ITSC.BlockMaliciousLoginAttempts',
        'system',
        'blockmaliciousloginattempts_blockip',
        'bottom',
        [
            'BlockedIp' => 'index, unblock'
        ],
        [
            'access' => 'admin',
            'icon' => 'EXT:block_malicious_login_attempts/Resources/Public/Icons/module-blocked-ip.svg',
            'labels' => 'LLL:EXT:block_malicious_login_attempts/Resources/Private/Language/locallang_mod.xlf:module.blockedip',
        ]
    );
} else {
    // v10
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'BlockMaliciousLoginAttempts',
        'system',
        'blockmaliciousloginattempts_blockip',
        'bottom',
        [
            \ITSC\BlockMaliciousLoginAttempts\Controller\BlockedIpController::class => 'index, unblock'
        ],
        [
            'access' => 'admin',
            'icon' => 'EXT:block_malicious_login_attempts/Resources/Public/Icons/module-blocked-ip.svg',
            'labels' => 'LLL:EXT:block_malicious_login_attempts/Resources/Private/Language/locallang_mod.xlf:module.blockedip',
        ]
    );
}
