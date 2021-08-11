<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
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

    public function getMasterIp()
    {
        return pm_Settings::get("masterIp-{$this->getIp()}", Modules_SlaveDnsManager_IpAddress::getDefault());
    }

    public function getMasterPublicIp()
    {
        $ipAddress =  pm_Settings::get("masterIp-{$this->getIp()}", Modules_SlaveDnsManager_IpAddress::getDefault());
        return Modules_SlaveDnsManager_IpAddress::getPublic($ipAddress);
    }

    public function getPort()
    {
        return pm_Settings::get("port-{$this->getIp()}", 953);
    }

    public function getRndcKeyId()
    {
        return pm_Settings::get("rndcKeyId-{$this->getIp()}", 'rndc-key');
    }

    public function getRndcClass()
    {
        return pm_Settings::get("rndcClass-{$this->getIp()}", 'IN');
    }

    public function getRndcView()
    {
        return pm_Settings::get("rndcView-{$this->getIp()}", '_default');
    }
    
    public function getMultipleView($view)
    {
        return explode(';', $view);
    }

    public function save(array $data)
    {
        $slaveIp = $data['ip'];
        if (null === $this->_config) {
            $this->_config = "slave_{$slaveIp}.conf";
        }

        $settings = ['masterIp', 'port', 'rndcKeyId', 'rndcClass', 'rndcView'];
        foreach ($settings as $setting) {
            if (array_key_exists($setting, $data)) {
                pm_Settings::set("{$setting}-{$slaveIp}", $data[$setting]);
            }
        }

        $keyAlgorithm = array_key_exists('algorithm', $data) ? $data['algorithm'] : 'hmac-md5';
        $keySecret = $data['secret'];

        $this->_saveConfig($this->getConfigPath(), $this->_renderConfig($slaveIp, $keySecret, $keyAlgorithm));

        $acl = new Modules_SlaveDnsManager_Acl();
        $acl->add($slaveIp);
    }

    private function _renderConfig($slaveIp, $keySecret, $keyAlgorithm)
    {
        $masterIp = $this->getMasterIp();
        $masterPublicIp = $this->getMasterPublicIp();

        $view = new Zend_View();
        $view->setScriptPath(pm_Context::getPlibDir() . 'views/scripts');
        $slaveConfiguration = $view->partial('index/slave-config.phtml', ['masterPublicIp' => $masterPublicIp, 'secret' => $keySecret]);
        $slaveConfiguration = trim(html_entity_decode(strip_tags($slaveConfiguration)));
        $slaveConfiguration = preg_replace('/^/m', '    ', $slaveConfiguration);

        return <<<CONF
/*
$slaveConfiguration
*/

/*
 SYNOPSIS
   rndc [-b source-address] [-s server] [-p port] [-y key_id] {command} zone [class [view]]
 For example:
   rndc -b {$masterIp} -s {$slaveIp} -p {$this->getPort()} -y {$this->getRndcKeyId()} refresh example.com {$this->getRndcClass()} {$this->getRndcView()}
*/

key "{$this->getRndcKeyId()}" {
    algorithm {$keyAlgorithm};
    secret "{$keySecret}";
};

CONF;
    }

    private function _saveConfig($path, $config)
    {
        $old = umask(0077);
        try {
            $result = file_put_contents($path, $config);
        } finally {
            umask($old);
        }

        if (false === $result) {
            throw new pm_Exception("Failed to save configuration {$path}");
        }
    }

    public function remove()
    {
        if (false === unlink($this->getConfigPath())) {
            throw new pm_Exception("Failed to remove configuration {$this->_config}");
        }

        if (preg_match('/slave_(?<slaveIp>.+)\.conf/', $this->_config, $matches)) {
            $acl = new Modules_SlaveDnsManager_Acl();
            $acl->remove($matches['slaveIp']);

            $settings = ['masterIp', 'port', 'rndcKeyId', 'rndcClass', 'rndcView'];
            foreach ($settings as $setting) {
                pm_Settings::set("{$setting}-{$matches['slaveIp']}", null);
            }
        }
    }
}
