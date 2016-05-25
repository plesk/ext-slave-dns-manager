<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH. All Rights Reserved.

namespace Modules_SlaveDnsManager_ApiRpc\Exception;

class ApiException extends \pm_Exception
{
    /**
     * @var int
     */
    protected $_code = 0;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        $code = $code ?: $this->_code;
        parent::__construct($message, $code, $previous);
    }
}
