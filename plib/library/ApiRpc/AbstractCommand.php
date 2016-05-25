<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH. All Rights Reserved.

namespace Modules_SlaveDnsManager_ApiRpc;

use Modules_SlaveDnsManager_ApiRpc\Exception\ApiException;
use Modules_SlaveDnsManager_ApiRpc\Exception\PermissionDeniedException;

abstract class AbstractCommand
{
    /**
     * @var array
     */
    protected $_params;

    abstract protected function _run();

    public function __construct($params)
    {
        $this->_params = $params;
        if (!\pm_Session::getClient()->isAdmin()) {
            throw new PermissionDeniedException('Permission denied');
        }
    }

    public function run()
    {
        $this->_checkParams();
        return $this->_run();
    }

    protected function _checkParams()
    {
    }

    protected function _checkRequiredParams($params)
    {
        $errorParams = [];
        foreach ($params as $param) {
            if (!isset($this->_params[$param])) {
                $errorParams[] = $param;
            }
        }
        if (!empty($errorParams)) {
            throw new ApiException("Required parameters '" . implode(', ', $errorParams) . "' are not specified");
        }
    }

    protected function _getParam($name, $default = '')
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : $default;
    }
}

