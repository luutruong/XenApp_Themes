<?php

namespace Truonglv\MobileAppsTheme\XF\Admin\Controller;

use Truonglv\MobileAppsTheme\App;

class Tools extends XFCP_Tools
{
    public function actionAppThemeSetup()
    {
        $dataSource = [
            'light' => App::getDataSource(App::SOURCE_THEME_LIGHT),
            'dark' => App::getDataSource(App::SOURCE_THEME_DARK),
        ];

        if ($this->isPost()) {
            $input = $this->filter([
                'data' => [
                    'light' => 'array',
                    'dark' => 'array'
                ]
            ]);

            $customizeDataSource = [];
            foreach ($input['data'] as $theme => $properties) {
                foreach ($properties as $propertyId => $value) {
                    if (isset($dataSource[$theme][$propertyId])
                        && $dataSource[$theme][$propertyId] !== $value
                    ) {
                        $customizeDataSource[$theme][$propertyId] = $value;
                    }
                }
            }

            $this->app()->registry()->set(App::KEY_DATA_REGISTRY_THEME, $customizeDataSource);

            return $this->redirect($this->buildLink('tools/app-theme-setup'));
        }

        $themed = (array) $this->app()->registry()->get(App::KEY_DATA_REGISTRY_THEME);
        $viewParams = [
            'themed' => $themed,
            'dataSource' => $dataSource,
        ];

        return $this->view(
            '',
            'tmobile_apps_theme_setup',
            $viewParams
        );
    }
}
