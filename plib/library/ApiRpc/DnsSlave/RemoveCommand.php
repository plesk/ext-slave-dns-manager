<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

namespace Modules_SlaveDnsManager_ApiRpc\DnsSlave;

use Modules_SlaveDnsManager_ApiRpc\AbstractCommand;
use Modules_SlaveDnsManager_Slave;

class RemoveCommand extends AbstractCommand
{
    protected function _checkParams()
    {
        $this->_checkRequiredParams(['config']);
    }

    protected function _run()
    {
        $config = $this->_params['config'];
        $slave = new Modules_SlaveDnsManager_Slave($config);

        $slave->remove();

        return [
            'config' => $config,
            'message' => \pm_Locale::lmsg('slaveRemoved'),
        ];
    }
}
