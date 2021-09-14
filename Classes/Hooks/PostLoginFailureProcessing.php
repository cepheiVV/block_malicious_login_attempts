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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

class PostLoginFailureProcessing
{
    /**
     * @param array $params
     * @param BackendUserAuthentication $parent
     */
    public function saveFailedLoginAttempt(array $params, BackendUserAuthentication $parent)
    {
        if ($parent->loginFailure) {
            // get IP of user
            $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');

            // save login attempt to DB
            $table = "tx_blockmaliciousloginattempts_failed_login";
            $queryBuilder = GeneralUtility::makeInstance(
                ConnectionPool::class
            )->getQueryBuilderForTable($table);

            $queryBuilder
                ->insert($table)
                ->values([
                    'ip' => $ip,
                    'time' => time(),
                ])
                ->execute();
        }
    }
}