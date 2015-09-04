<?php
// Copyright 1999-2014. Parallels IP Holdings GmbH. All Rights Reserved.

if (pm_ProductInfo::isWindows()) {
    if (!file_exists(PRODUCT_ROOT . '\dns\bin\rndc.exe')) {
        echo "BIND DNS Server is not installed.";
        exit(1);
    }
}
