<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

if (pm_ProductInfo::isWindows()) {
    if (!file_exists(PRODUCT_ROOT . '\dns\bin\rndc.exe')) {
        echo "BIND DNS Server is not installed.";
        exit(1);
    }
}
