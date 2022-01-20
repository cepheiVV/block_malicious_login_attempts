<?php

declare(strict_types=1);

/*
 * Copyright (C) 2021 Patrick Crausaz <info@its-crausaz.ch>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

namespace ITSC\BlockMaliciousLoginAttempts\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Class BlockMaliciousLoginAttempts
 * @package ITSC\BlockMaliciousLoginAttempts\Middleware
 */
class BlockMaliciousLoginAttempts implements MiddlewareInterface
{
    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * BlockMaliciousLoginAttempts constructor.
     */
    public function __construct()
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(
            ExtensionConfiguration::class
        )->get('block_malicious_login_attempts');
    }

    /**
     * Checks the client's IP address upon backend login
     * and blocks the access if login fails too often
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isLoginInProgress($request)) {

            $ip = $request->getAttribute('normalizedParams')->getRemoteAddress();
            $ipIsBlocked = $this->testIpAgainstMaliciousIpList($ip);

            if ($ipIsBlocked) {
                $message = $this->extensionConfiguration["lockMessage"] ?? "blocked";
                $message = str_replace("{ip_address}", $ip, $message);
                exit($message);
            }

        }

        return $handler->handle($request);
    }

    /**
     * check whether the ip is blocked
     *
     * @param $ip
     * @return bool
     */
    protected function testIpAgainstMaliciousIpList($ip): bool
    {
        $failedLoginLimit = $this->getFailedLoginLimit();
        $table = "tx_blockmaliciousloginattempts_failed_login";
        $queryBuilder = GeneralUtility::makeInstance(
            ConnectionPool::class
        )->getQueryBuilderForTable($table);

        $attemptedLogins = $queryBuilder
            ->count('uid')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('ip', $queryBuilder->createNamedParameter($ip))
            )
            ->execute()
            ->fetchColumn(0);;

        return $attemptedLogins >= $failedLoginLimit;
    }

    /**
     * Checks if login credentials are currently submitted
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    protected function isLoginInProgress(ServerRequestInterface $request): bool
    {
        $parsedBody = $request->getParsedBody();
        $queryParams = $request->getQueryParams();
        $submitValue = $parsedBody['commandLI'] ?? $queryParams['commandLI'] ?? null;
        $username = $parsedBody['username'] ?? $queryParams['username'] ?? null;
        return !empty($username) || !empty($submitValue);
    }

    /**
     * @return int
     */
    protected function getFailedLoginLimit(): int
    {
        $failedLoginLimit = $this->extensionConfiguration["failedLoginLimit"] ?? 3;
        return (int)$failedLoginLimit;
    }
}
