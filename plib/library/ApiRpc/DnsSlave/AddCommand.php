<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

namespace Modules_SlaveDnsManager_ApiRpc\DnsSlave;

use Modules_SlaveDnsManager_ApiRpc\AbstractCommand;
use Modules_SlaveDnsManager_ApiRpc\Exception\ApiException;
use Modules_SlaveDnsManager_Slave;

class AddCommand extends AbstractCommand
{
    const AVAILABLE_ALGORITHMS = ['hmac-md5'];
    const DEFAULT_ALGORITHM = 'hmac-md5';
    const DEFAULT_PORT = 953;

    protected function _checkParams()
    {
        $this->_checkRequiredParams(['ip', 'secret']);

        $alg = $this->_params['algorithm'];
        if (!is_null($alg) && !in_array($alg, self::AVAILABLE_ALGORITHMS)) {
            throw new ApiException('Algorithm should be one of \'' . join(', ', self::AVAILABLE_ALGORITHMS) . '\'');
        }

        $port = $this->_params['port'];
        if (!is_null($port) && !is_numeric($port)) {
            throw new ApiException('Port should be numeric value');
        }
    }

    protected function _run()
    {
        $slave = new Modules_SlaveDnsManager_Slave();

        $ip = $this->_params['ip'];
        $slave->save([
            'ip' => $ip,
            'secret' => $this->_params['secret'],
            'port' => $this->_getParam('port', self::DEFAULT_PORT),
            'algorithm' => $this->_getParam('algorithm', self::DEFAULT_ALGORITHM),
        ]);

        return [
            'ip' => $ip,
        ];
    }
}
