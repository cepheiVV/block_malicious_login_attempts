<?php

declare(strict_types=1);

namespace ITSC\BlockMaliciousLoginAttempts\Controller;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * BlockedIpController
 */
class BlockedIpController extends ActionController
{

    /**
     *
     */
    public function indexAction()
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('block_malicious_login_attempts');
        $failedLoginLimit = $extensionConfiguration["failedLoginLimit"];

        // get all IP's that reached the limit
        $table = "tx_blockmaliciousloginattempts_failed_login";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $sql = "SELECT ip, COUNT(ip) as count, COUNT(ip) >= ".$failedLoginLimit." as blocked 
                FROM tx_blockmaliciousloginattempts_failed_login 
                WHERE 1=1 
                GROUP BY ip
                ORDER BY count DESC;";
        $ipList = $queryBuilder->getConnection()->prepare($sql)->executeQuery()->fetchAll();

        $this->view->assign("ipList", $ipList);
        $this->view->assign("failedLoginLimit", $failedLoginLimit);
        $this->view->assign("currentPage", "index");
    }

    /**
     * @param string $ip
     */
    public function unblockAction(string $ip)
    {
        $table = "tx_blockmaliciousloginattempts_failed_login";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder
            ->delete($table)
            ->where(
                $queryBuilder->expr()->eq('ip', $queryBuilder->createNamedParameter($ip))
            )
            ->execute();

        $this->redirect("index");
    }
}