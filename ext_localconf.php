<?php

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(file_get_contents(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('block_malicious_login_attempts', 'Configuration/TypoScript/setup.typoscript')));