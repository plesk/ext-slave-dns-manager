<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.
pm_Loader::registerAutoload();
pm_Context::init('slave-dns-manager');

try {
    (new Modules_SlaveDnsManager_CustomBackendService())->enable();
} catch (pm_Exception $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
exit(0);
