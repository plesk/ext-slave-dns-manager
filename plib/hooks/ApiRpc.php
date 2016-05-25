<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH. All Rights Reserved.

use Modules_SlaveDnsManager_ApiRpc\Factory;

class Modules_SlaveDnsManager_ApiRpc extends pm_Hook_ApiRpc
{
    public function call($params)
    {
        if (!isset($params['command'])) {
            throw new Modules_SlaveDnsManager_ApiRpc\Exception\ApiException('Command is not specified' . print_r($params, true));
        }

        $command = $params['command'];
        unset($params['command']);

        return Factory::get($command, $params)->run();
    }
}
