<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class Modules_SlaveDnsManager_Rndc
{
    private function _call(Modules_SlaveDnsManager_Slave $slave, $arguments, $verbose = false)
    {
        $arguments = "-c \"{$slave->getConfigPath()}\" {$arguments} 2>&1";
        if (pm_ProductInfo::isWindows()) {
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

            $settings = pm_Settings::get($slave->getIp());
            list($pleskIp, $rndcPort, $rndcKeyid, $rndcClass, $rndcView) = explode(":", $settings);
 
            $this->_call($slave, "-b \"{$pleskIp}\" -s \"{$slave->getIp()}\" -p \"{$rndcPort}\" -y \"{$rndcKeyid}\" addzone \"{$domain}\" \"{$rndcClass}\" \"{$rndcView}\" \"{ type slave; file \\\"{$domain}\\\"; masters { {$pleskIp}; }; };\"");
        }
    }

    public function updateZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {

            $settings = pm_Settings::get($slave->getIp());
            list($pleskIp, $rndcPort, $rndcKeyid, $rndcClass, $rndcView) = explode(":", $settings);

            $result = $this->_call($slave, "-b \"{$pleskIp}\" -s \"{$slave->getIp()}\" -p \"{$rndcPort}\" -y \"{$rndcKeyid}\" refresh \"{$domain}\" \"{$rndcClass}\" \"{$rndcView}\"");
            if (false === $result) {
                $this->addZone($domain, $slave);
            }
        }
    }

    public function deleteZone($domain, Modules_SlaveDnsManager_Slave $slave = null)
    {
        $slaves = null === $slave ? Modules_SlaveDnsManager_Slave::getList() : [$slave];
        foreach ($slaves as $slave) {

            $settings = pm_Settings::get($slave->getIp());
            list($pleskIp, $rndcPort, $rndcKeyid, $rndcClass, $rndcView) = explode(":", $settings);

            $this->_call($slave, "-b \"{$pleskIp}\" -s \"{$slave->getIp()}\" -p \"{$rndcPort}\" -y \"{$rndcKeyid}\" delzone \"{$domain}\" \"{$rndcClass}\" \"{$rndcView}\"");
        }
    }

    public function checkStatus(Modules_SlaveDnsManager_Slave $slave)
    {
        $settings = pm_Settings::get($slave->getIp());
        list($pleskIp, $rndcPort, $rndcKeyid, $rndcClass, $rndcView) = explode(":", $settings);

        return $this->_call($slave, "-b \"{$pleskIp}\" -s \"{$slave->getIp()}\" -p \"{$rndcPort}\" -y \"{$rndcKeyid}\" status", true);
    }
}
