<?php
// Copyright 1999-2014. Parallels IP Holdings GmbH. All Rights Reserved.
pm_Loader::registerAutoload();
pm_Context::init('slave-dns-manager');

try {
    if (pm_ProductInfo::isWindows()) {
        $cmd = '"' . PRODUCT_ROOT . '\bin\extension.exe"';
    } else {
        $cmd = '"' . PRODUCT_ROOT . '/bin/extension"';
    }

    $script = $cmd . ' --exec ' . pm_Context::getModuleId() . ' slave-dns.php';
    $result = pm_ApiCli::call('server_dns', array('--enable-custom-backend', $script));
} catch (pm_Exception $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
exit(0);
