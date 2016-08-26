<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH. All Rights Reserved.

use Modules_SlaveDnsManager_ApiRpc\Factory;

class Modules_SlaveDnsManager_ApiRpc extends pm_Hook_ApiRpc
{
    public function call($data)
    {
        $result = [];
        foreach ($data as $command => $params) {
            $result[$command] = Factory::get($command, $params)->run();
        }
        return $result;
    }
}
