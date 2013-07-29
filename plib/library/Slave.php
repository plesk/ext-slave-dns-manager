<?php

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

    public function getConfig()
    {
        return $this->_config;
    }

    public function getConfigPath()
    {
        return self::_getPath() . $this->_config;
    }

    public function save(array $data)
    {
        $keyAlgorithm = array_key_exists('algorithm', $data) ? $data['algorithm'] : 'hmac-md5';
        $keySecret = $data['secret'];
        $slaveIp = $data['ip'];
        $slavePort = array_key_exists('port', $data) ? $data['port'] : 953;

        $configuration = <<<CONF
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
    }
}