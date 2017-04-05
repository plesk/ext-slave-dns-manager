<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.
class Modules_SlaveDnsManager_Slave
{
    private static function _getPath()
    {
        return pm_Context::getVarDir();
    }

    public static function getList()
    {
        /** @var self[] $slaves */
        $slaves = array();

        if ($dh = opendir(self::_getPath())) {
            while (($file = readdir($dh)) !== false) {
                if (preg_match('/\.conf$/', $file) && filetype(self::_getPath() . "/" . $file) == 'file') {
                    $slaves[] = new self($file);
                }
            }
            closedir($dh);
        }

        return $slaves;
    }

    private $_config = null;

    public function __construct($config = null)
    {
        $this->_config = $config;
    }

    public function getPleskIPs()
    {
        $ips = array();

        $request = "<ip><get/></ip>";
        $response = pm_ApiRpc::getService('1.6.5.0')->call($request);
        if ('ok' != $response->ip->get->result->status) {
            throw new pm_Exception("Unable to get server IP. Error: {$response->ip->get->result->errtext}");
        }

        // Get all IP-addresses
        foreach ($response->ip->get->result->addresses->ip_info as $address) {
            $ips[(string)$address->ip_address] = (string)$address->ip_address;
        }

        if (count($ips) >= 0) {
            return $ips;
        }

        throw new pm_Exception("Unable to get server IP: empty result.");
    }

    // Legacy code for backward compatibility with current ApiRpc
    public function getDefaultPleskIp()
    {
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
            return (string)$address->public_ip_address ?: (string)$address->ip_address;
        }

        // Get first IP
        foreach ($response->ip->get->result->addresses->ip_info as $address) {
            return (string)$address->public_ip_address ?: (string)$address->ip_address;
        }

        throw new pm_Exception("Unable to get server IP: empty result.");
    }

    public function getIp()
    {
        if (preg_match('/^slave_(?<ip>[\d\.]+)\.conf$/', $this->_config, $matches)) {
            return $matches['ip'];
        }
        return $this->_config;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function getConfigPath()
    {
        return self::_getPath() . str_replace('..', '', $this->_config);
    }

    public function content()
    {
        $configPath = $this->getConfigPath();
        $content = file_get_contents($configPath);
        if (false === $content) {
            throw new pm_Exception("Invalid configuration {$configPath}");
        }

        return $content;
    }

    public function save(array $data)
    {

        $pleskIp = array_key_exists('pleskIp', $data) ? $data['pleskIp'] : $this->getDefaultPleskIp();
        $slaveIp = $data['ip'];

        $keyAlgorithm = array_key_exists('algorithm', $data) ? $data['algorithm'] : 'hmac-md5';
        $keySecret = $data['secret'];

        $rndcPort = array_key_exists('port', $data) ? $data['port'] : 953;
        $rndcKeyid = array_key_exists('rndcKeyid', $data) ? $data['rndcKeyid'] : 'rndc-key';
        $rndcClass = array_key_exists('rndcClass', $data) ? $data['rndcClass'] : 'IN';
        $rndcView = array_key_exists('rndcView', $data) ? $data['rndcView'] : '_default';

        pm_Settings::set($slaveIp, "$pleskIp:$rndcPort:$rndcKeyid:$rndcClass:$rndcView");

        $view = new Zend_View();
        $view->setScriptPath(pm_Context::getPlibDir() . 'views/scripts');
        $slaveConfiguration = $view->partial('index/slave-config.phtml', array('pleskIp' => $pleskIp, 'secret' => $keySecret));
        $slaveConfiguration = trim(html_entity_decode(strip_tags($slaveConfiguration)));
        $slaveConfiguration = preg_replace('/^/m', '    ', $slaveConfiguration);

        $configuration = <<<CONF
/*
$slaveConfiguration
*/

/*
** SYNOPSIS
**   rndc [-b source-address] [-s server] [-p port] [-y key_id] {command} zone [class [view]]
** Saved settings:
**   rndc -b $pleskIp -s $slaveIp -p $rndcPort -y $rndcKeyid {command} zone $rndcClass $rndcView
*/
key "$rndcKeyid" {
    algorithm $keyAlgorithm;
    secret "$keySecret";
};

CONF;

        if (null === $this->_config) {
            $this->_config = "slave_$slaveIp.conf";
        }

        $result = file_put_contents($this->getConfigPath(), $configuration);
        if (false === $result) {
            throw new pm_Exception("Failed to save configuration {$this->_config}");
        }

        $acl = new Modules_SlaveDnsManager_Acl();
        $acl->add($slaveIp);
    }

    public function remove()
    {
        if (false === unlink($this->getConfigPath())) {
            throw new pm_Exception("Failed to remove configuration {$this->_config}");
        }

        if (preg_match('/slave_(?<slaveIp>.+)\.conf/', $this->_config, $matches)) {
            $acl = new Modules_SlaveDnsManager_Acl();
            $acl->remove($matches['slaveIp']);

            pm_Settings::set($matches['slaveIp'], "");
        }
    }
}
