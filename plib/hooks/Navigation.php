<?php
// Copyright 1999-2017. Parallels IP Holdings GmbH.

class Modules_SlaveDnsManager_Navigation extends pm_Hook_Navigation
{
    public function getNavigation()
    {
        return [
            [
                'controller' => 'index',
                'action' => 'index',
                'label' => pm_Locale::lmsg('listPageTitle'),
                'pages' => [
                    [
                        'controller' => 'index',
                        'action' => 'add',
                        'label' => pm_Locale::lmsg('addPageTitle'),
                    ],
                ],
            ],
        ];
    }
}

