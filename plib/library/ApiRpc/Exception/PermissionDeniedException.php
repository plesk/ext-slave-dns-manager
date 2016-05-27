<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH. All Rights Reserved.

namespace Modules_SlaveDnsManager_ApiRpc\Exception;

class PermissionDeniedException extends ApiException
{
    protected $_code = 1006; // Plesk constant AERR_PERM_DENIED
}
