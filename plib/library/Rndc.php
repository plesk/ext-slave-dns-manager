<?php
// Copyright 1999-2014. Parallels IP Holdings GmbH. All Rights Reserved.
class Modules_SlaveDnsManager_Rndc
{
    private static $_serverIp = null;

    public function getServerIP()
    {
        if (self::$_serverIp) {
            return self::$_serverIp;
        }

        $request = "<ip><get/></ip>";
        $response = pm_ApiRpc::getService('1.6.5.0')->call($request);
        if ('ok' != $response->ip->get->result->status) {
            throw new pm_Exception("Unable to get server IP. Error: {$response->ip->get->result->errtext}");
        }

        // Get default IP
        foreach ($response->ip->get->result->addresses->ip_info as $address) {
            if (!isset($address->default)) {
                continue;
            }
            return self::$_serverIp = (string)$address->ip_address;
        }

        // Get first IP
        foreach ($response->ip->get->result->addresses->ip_info as $address) {
            return self::$_serverIp = (string)$address->ip_address;
        }

        throw new pm_Exception("Unable to get server IP: empty result.");
    }

    private function _call(Modules_SlaveDnsManager_Slave $slave, $arguments, $verbose = false)
    {
        $arguments = "-c \"{$slave->getConfigPath()}\" {$arguments} 2>&1";
        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $command = '"' . PRODUCT_ROOT . '\dns\bin\rndc.exe"';
        } else {
            $command = '/usr/sbin/rndc';
        }
        exec("{$command} {$arguments}", $out, $code);
        $output = implode("\n", $out);

        if ($verbose) {
            if ($code != 0) {
                throw new pm_Exception("Error code $code: $output");
            }
            return $output;
        }

        if ($code != 0) {
            // Cannot send output header due to possible API-RPC calls
            error_log("Error code $code: $output");
        }

        return $code == 0;
    }

    public function addZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {
            $this->_call($slave, "addzone {$domain} \"{ type slave; file \\\"{$domain}\\\"; masters { {$this->getServerIP()}; }; };\"");
        }
    }

    public function updateZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {
            $result = $this->_call($slave, "refresh {$domain}");
            if (false === $result) {
                $this->addZone($domain, $slave);
            }
        }
    }

    public function deleteZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {
            $this->_call($slave, "delzone {$domain}");
        }
    }

    public function checkStatus(Modules_SlaveDnsManager_Slave $slave)
    {
        return $this->_call($slave, "status", true);
    }
}