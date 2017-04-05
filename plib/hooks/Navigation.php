<?php
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
                    [
                        'controller' => 'index',
                        'action' => 'resync',
                    ],
                ],
            ],
        ];
    }
}

