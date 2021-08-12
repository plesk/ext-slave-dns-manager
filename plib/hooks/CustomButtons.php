<?php
// Copyright 1999-2021. Plesk International GmbH. All rights reserved.

/**
 * Class Modules_SlaveDnsManager_CustomButtons
 */
class Modules_SlaveDnsManager_CustomButtons extends pm_Hook_CustomButtons
{
    public function getButtons()
    {
        if (pm_Session::getClient()->isAdmin()) {
            $commonParams = [
                'title'         => pm_Locale::lmsg('leftMenuTitle'),
                'description'   => pm_Locale::lmsg('leftMenuDescription'),
                'icon'          => pm_Context::getBaseUrl() . 'icons/logo.svg',
                'link'          => pm_Context::getActionUrl('index'),
                'contextParams' => true
            ];
            $buttons = [
                array_merge($commonParams, [
                    'place' => self::PLACE_ADMIN_NAVIGATION,
                ]),
                array_merge($commonParams, [
                    'place' => self::PLACE_HOSTING_PANEL_NAVIGATION,
                ]),
            ];
            return $buttons;
        } else {
            return [];
        }
    }
}
