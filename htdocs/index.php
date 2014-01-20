<?php
// Copyright 1999-2014. Parallels IP Holdings GmbH. All Rights Reserved.
pm_Context::init('slave-dns-manager');

$application = new pm_Application();
$application->run();
