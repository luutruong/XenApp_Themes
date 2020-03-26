<?php

namespace Truonglv\MobileAppsTheme;

use XF\Util\File;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\AddOn\StepRunnerUninstallTrait;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function uninstallStep1()
    {
        $this->app()
            ->registry()
            ->delete(App::KEY_DATA_REGISTRY_THEME);

        File::deleteAbstractedDirectory('internal-data://tmobile_apps_theme');
    }
}
