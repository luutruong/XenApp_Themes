<?php

namespace Truonglv\MobileAppsTheme\Truonglv\Api\Api\Controller;

class App extends XFCP_App
{
    /**
     * @return array
     */
    protected function getAppInfo()
    {
        $info = parent::getAppInfo();

        $themeData = $this->app()->registry()->get(\Truonglv\MobileAppsTheme\App::KEY_DATA_REGISTRY_THEME);
        if (isset($themeData['dark'])) {
            $info['darkTheme'] = (array) $themeData['dark'];
        }

        if (isset($themeData['light'])) {
            $info['lightTheme'] = (array) $themeData['light'];
        }

        return $info;
    }
}
