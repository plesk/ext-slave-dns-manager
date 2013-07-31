<?php

class Modules_SlaveDnsManager_Rndc
{
    private static $_serverIp = null;

    private function _getServerIP()
    {
        if (self::$_serverIp) {
            return self::$_serverIp;
        }

        $request = "<ip><get/></ip>";
        $response = pm_ApiRpc::getService('1.6.5.0')->call($request);
        if ('ok' != $response->ip->get->result->status) {
            throw new pm_Exception("Unable to get server IP. Error: {$response->ip->get->result->error}");
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

    private function _call($command)
    {
        exec($command, $out, $code);
        if ($code != 0) {
            // Cannot send output header due to possible API-RPC calls
            error_log("Error code $code: " . implode('', $out));
        }

        return $code == 0;
    }

    public function addZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {
            $command = '/usr/sbin/rndc -c ' . $slave->getConfigPath() . ' addzone ' . $domain . " '{ type slave; file \"/var/lib/bind/"
                . $domain . "\"; masters { " . $this->_getServerIP() . "; }; };'";
            $this->_call($command);
        }
    }

    public function updateZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {
            $command = '/usr/sbin/rndc -c ' . $slave->getConfigPath() . ' refresh ' . $domain;
            $result = $this->_call($command);
            if (false === $result) {
                $this->addZone($domain, $slave);
            }
        }
    }

    public function deleteZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {
            $command = '/usr/sbin/rndc -c ' . $slave->getConfigPath() . ' delzone ' . $domain;
            $this->_call($command);
        }
    }
}