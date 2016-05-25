<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH. All Rights Reserved.

namespace Modules_SlaveDnsManager_ApiRpc;

use Modules_SlaveDnsManager_ApiRpc\Exception\ApiException;

class Factory
{
    /**
     * @param string $command
     * @param array $params
     * @return AbstractCommand
     * @throws ApiException
     */
    public static function get($command, $params)
    {
        throw new ApiException("API command '{$command}' is not available");
    }
}
