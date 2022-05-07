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
        $failedLoginLimit = (int)$extensionConfiguration["failedLoginLimit"];
        $blockByUsername = (bool)$extensionConfiguration["blockByUsername"];
        
        $failedLoginTime = 0;
        if(isset($extensionConfiguration["failedLoginTime"])) {
            $failedLoginTime = (int)$extensionConfiguration["failedLoginTime"];
        }



        $ipList = $this->getBlocked("ip", $failedLoginLimit, $failedLoginTime);

        $usernameList = [];
        if ($blockByUsername) {
            $usernameList = $this->getBlocked("username", $failedLoginLimit, $failedLoginTime);
        }

        $this->view->assignMultiple([
            "ipList" => $ipList,
            "usernameList" => $usernameList,
            "failedLoginLimit" => $failedLoginLimit,
            "failedLoginTime" => $failedLoginTime,
            "blockByUsername" => $blockByUsername,
            "currentPage" => "index",
        ]);
    }

    /**
     * @param string $ip
     * @param string $username
     * @param string $type
     */
    public function unblockAction(string $ip, string $username, string $type)
    {
        $value = ($type == "ip") ? $ip : $username;
        $table = "tx_blockmaliciousloginattempts_failed_login";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder
            ->delete($table)
            ->where(
                $queryBuilder->expr()->eq($type, $queryBuilder->createNamedParameter($value))
            )
            ->execute();

        $this->redirect("index");
    }

    protected function getBlocked(string $blockedBy, int $failedLoginLimit, int $failedLoginTime): array
    {
        $table = "tx_blockmaliciousloginattempts_failed_login";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $queryBuilder->select('ip', 'username')->from($table);
        $queryBuilder
            ->addSelectLiteral("COUNT($blockedBy) as count")
            ->addSelectLiteral("COUNT($blockedBy) >= " . $failedLoginLimit . " as blocked");

        if ($failedLoginTime > 0) {
            $queryBuilder->addSelectLiteral('count(CASE WHEN time BETWEEN "'.(time()-$failedLoginTime).'" AND "'.time().'" THEN 1 END) as timedFails');
        }

        $queryBuilder->orderBy('count', 'DESC');
        $queryBuilder->groupBy($blockedBy);

        return $queryBuilder->execute()->fetchAll();
    }
}