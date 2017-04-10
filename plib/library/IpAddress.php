<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
class Modules_SlaveDnsManager_IpAddress
{
    private static $_ipAddresses;

    public static function getAvailable()
    {
        if (isset(self::$_ipAddresses)) {
            return self::$_ipAddresses;
        }
        self::$_ipAddresses = [];

        $request = "<ip><get/></ip>";
        $response = pm_ApiRpc::getService('1.6.5.0')->call($request);
        if ('ok' != $response->ip->get->result->status) {
            throw new pm_Exception("Unable to get server IP. Error: {$response->ip->get->result->errtext}");
        }

        // Get all IP-addresses
        foreach ($response->ip->get->result->addresses->ip_info as $address) {
            $ipAddress = (string)$address->public_ip_address ?: (string)$address->ip_address;
            if (isset($address->default)) {
                array_unshift(self::$_ipAddresses, $ipAddress);
            } else {
                self::$_ipAddresses[] = $ipAddress;
            }
        }

        if (count(self::$_ipAddresses) > 0) {
            return self::$_ipAddresses;
        }

        throw new pm_Exception("Unable to get server IP: empty result.");
    }

    public static function getDefault()
    {
        return reset(self::getAvailable());
    }
}
