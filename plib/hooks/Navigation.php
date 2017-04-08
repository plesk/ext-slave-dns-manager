<?php
// Copyright 1999-2016. Parallels IP Holdings GmbH.

class Modules_SlaveDnsManager_Navigation extends pm_Hook_Navigation
{
    public function getNavigation()
    {
        return [
            [
                'controller' => 'index',
                'action' => 'index',
                'pages' => [
                    [
                        'controller' => 'index',
                        'action' => 'add',
                    ],
                    [
                        'controller' => 'index',
                        'action' => 'view',
                    ],
                ],
            ],
        ];
    }
}

