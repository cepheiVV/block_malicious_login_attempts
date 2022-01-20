<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'block_malicious_login_attempts',
    'Configuration/TypoScript',
    'Block Malicious Login Attempts: Backend Module'
);