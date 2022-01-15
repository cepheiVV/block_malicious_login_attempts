<?php

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postLoginFailureProcessing'][] = \ITSC\BlockMaliciousLoginAttempts\Hooks\PostLoginFailureProcessing::class . '->saveFailedLoginAttempt';

