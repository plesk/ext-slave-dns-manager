<?php

class rndc {
    private static function _getServerIP() {
        exec("/usr/local/psa/bin/ipmanage -xi", $out, $code);
        $xml = simplexml_load_string(implode('',$out));
        $ip = (string) $xml->ip->ip_address;

        return $ip;
    }
    private static function _getSlaves() {
        $configs = array();

        if ($dh = opendir(__DIR__)) {
            while (($file = readdir($dh)) !== false) {
                if (preg_match('/conf$/', $file) && filetype(__DIR__ . "/" . $file) == 'file') {
                    $configs[] = __DIR__ . "/" . $file;
                }
            }
            closedir($dh);
        } 

        return $configs;
    }

    private static function _callrndc ($command) {
        exec($command, $out, $code);
        if ($code != 0) {
            echo "Error code $code: " . implode('', $out);
        } 

        return $code == 0;
    }

    public static function AddZone ($domain) {
        foreach (self::_getSlaves() as $config) {
            $command = '/usr/sbin/rndc -c ' . $config . ' addzone ' . $domain . " '{ type slave; file \"/var/lib/bind/" . $domain . "\"; masters { " . self::_getServerIP() . "; }; };'";
            return self::_callrndc ($command); 
        }
    }
    public static function UpdateZone ($domain) {
        foreach (self::_getSlaves() as $config) {
            $command = '/usr/sbin/rndc -c ' . $config . ' refresh ' . $domain;
            return self::_callrndc ($command);
        }
    }
    public static function DeleteZone ($domain) {
        foreach (self::_getSlaves() as $config) {
            $command = '/usr/sbin/rndc -c ' . $config . ' delzone ' . $domain;
            return self::_callrndc ($command);
        }
    }
}

$data = json_decode (file_get_contents ('php://stdin'));
foreach ($data as $task) {
    switch ($task->command) {
    case 'create':
        rndc::AddZone(substr ($task->zone->name, 0, -1));
        break;
    case 'update':
        if (rndc::UpdateZone(substr ($task->zone->name, 0, -1)) == false) {
            rndc::AddZone(substr ($task->zone->name, 0, -1));
        }
        break;
    case 'delete':
        rndc::DeleteZone(substr ($task->zone->name, 0, -1));
        break;
    }
}

