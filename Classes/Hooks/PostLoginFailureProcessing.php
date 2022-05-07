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

namespace ITSC\BlockMaliciousLoginAttempts\Hooks;

use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * PostLoginFailureProcessing
 */
class PostLoginFailureProcessing
{
    /**
     * @var string
     */
    protected $table = "tx_blockmaliciousloginattempts_failed_login";

    /**
     * get the IP of user if a BE login attempt fails and saves the IP into the DB
     *
     * @param array $params
     * @param BackendUserAuthentication $parent
     * @throws DBALException
     */
    public function saveFailedLoginAttempt(array $params, BackendUserAuthentication $parent)
    {
        if ($parent->loginFailure) {
            $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');
            $username = GeneralUtility::_POST("username");

            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
            $queryBuilder
                ->insert($this->table)
                ->values([
                    'ip' => $ip,
                    'time' => time(),
                    'username' => $username
                ])
                ->execute();
        }
    }
}
