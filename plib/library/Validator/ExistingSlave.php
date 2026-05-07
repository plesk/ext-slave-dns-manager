<?php
// Copyright 1999-2026. WebPros International GmbH. All rights reserved.

class Modules_SlaveDnsManager_Validator_ExistingSlave extends Zend_Validate_Abstract
{
    const EXISTS = 'exists';

    public function __construct()
    {
        $this->_messageTemplates = [
            self::EXISTS => pm_Locale::lmsg('invalidIpExistingSlave'),
        ];
    }

    public function isValid($value)
    {
        $this->_setValue($value);

        $slave = new Modules_SlaveDnsManager_Slave("slave_{$value}.conf");

        if (!file_exists($slave->getConfigPath())) {
            return true;
        }

        $this->_error(self::EXISTS);
        return false;
    }
}

