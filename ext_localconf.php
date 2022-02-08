<?php
defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// Autoload TypoScript in TYPO3 Backend
ExtensionManagementUtility::addTypoScriptSetup(
    file_get_contents(
        ExtensionManagementUtility::extPath(
            'block_malicious_login_attempts',
            'Configuration/TypoScript/setup.typoscript'
        )
    )
);