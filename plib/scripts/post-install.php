<?php
// Copyright 1999-2015. Parallels IP Holdings GmbH.
pm_Loader::registerAutoload();
pm_Context::init('slave-dns-manager');

try {
    $script = PRODUCT_ROOT . '/bin/extension --exec ' . pm_Context::getModuleId() . ' slave-dns.php';
    $result = pm_ApiCli::call('server_dns', array('--enable-custom-backend', $script));
} catch (pm_Exception $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
exit(0);
