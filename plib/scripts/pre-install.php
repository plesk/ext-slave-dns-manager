<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

if (pm_ProductInfo::isWindows()) {
    if (!file_exists(PRODUCT_ROOT . '\dns\bin\rndc.exe')) {
        echo "BIND DNS Server is not installed. To install Slave DNS Manager in Plesk for Windows, copy the \"rndc.exe\" file to the \"" . PRODUCT_ROOT . 'dns\bin\rndc.exe' . "\" folder.";
        exit(1);
    }
}
