<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

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
        switch ($command) {
            case 'add-slave' :
                return new DnsSlave\AddCommand($params);
            case 'get-slaves' :
                return new DnsSlave\ListCommand($params);
            case 'remove-slave' :
                return new DnsSlave\RemoveCommand($params);
        }

        throw new ApiException("API command '{$command}' is not available");
    }
}
