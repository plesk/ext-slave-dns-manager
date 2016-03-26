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
        $keyAlgorithm = array_key_exists('algorithm', $data) ? $data['algorithm'] : 'hmac-md5';
        $keySecret = $data['secret'];
        $slaveIp = $data['ip'];
        $slavePort = array_key_exists('port', $data) ? $data['port'] : 953;

        $view = new Zend_View();
        $view->setScriptPath(pm_Context::getPlibDir() . 'views/scripts');
        $rndc = new Modules_SlaveDnsManager_Rndc();
        $pleskIp = $view->escape($rndc->getServerIP());
        $slaveConfiguration = $view->partial('index/slave-config.phtml', array('pleskIp' => $pleskIp, 'secret' => $keySecret));
        $slaveConfiguration = trim(html_entity_decode(strip_tags($slaveConfiguration)));
        $slaveConfiguration = preg_replace('/^/m', '    ', $slaveConfiguration);

        $configuration = <<<CONF
/*
$slaveConfiguration
*/

key "rndc-key" {
    algorithm $keyAlgorithm;
    secret "$keySecret";
};

options {
    default-key "rndc-key";
    default-server $slaveIp;
    default-port $slavePort;
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
        }
    }
}
