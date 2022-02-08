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
     * index action
     */
    public function indexAction()
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('block_malicious_login_attempts');
        $failedLoginLimit = $extensionConfiguration["failedLoginLimit"];
        
        $failedLoginTime = 0;
        if(isset($extensionConfiguration["failedLoginTime"])) {
            $failedLoginTime = $extensionConfiguration["failedLoginTime"];
        }

        // get all IP's that reached the limit
        $table = "tx_blockmaliciousloginattempts_failed_login";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->select('ip')
            ->from($table)
            ->addSelectLiteral('COUNT(ip) as count')
            ->addSelectLiteral('COUNT(ip) >= "'.$failedLoginLimit.'" as blocked');

        if ($failedLoginTime > 0) {
            $queryBuilder->addSelectLiteral('count(CASE WHEN time BETWEEN "'.(time()-$failedLoginTime).'" AND "'.time().'" THEN 1 END) as timedFails');
        }
            
        $queryBuilder->orderBy('count', 'DESC');

        $ipList = $queryBuilder->groupBy('ip')->execute()->fetchAll();

        $this->view->assign("ipList", $ipList);
        $this->view->assign("failedLoginLimit", $failedLoginLimit);
        $this->view->assign("failedLoginTime", $failedLoginTime);
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