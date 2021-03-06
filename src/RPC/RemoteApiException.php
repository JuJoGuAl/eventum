<?php

/*
 * This file is part of the Eventum (Issue Tracking System) package.
 *
 * @copyright (c) Eventum Team
 * @license GNU General Public License, version 2 or later (GPL-2+)
 *
 * For the full copyright and license information,
 * please see the COPYING and AUTHORS files
 * that were distributed with this source code.
 */

namespace Eventum\RPC;

use RuntimeException;

final class RemoteApiException extends RuntimeException
{
    private const AUTHENTICATION_FAILED = 'Authentication failed for %s. Your login/password/api key is invalid or you do not have the proper role.';

    public static function authenticationFailed(string $login)
    {
        return new static(sprintf(self::AUTHENTICATION_FAILED, $login));
    }
}
