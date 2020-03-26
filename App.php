<?php

namespace Truonglv\MobileAppsTheme;

use XF\Util\File;

class App
{
    const KEY_DATA_REGISTRY_THEME = 'tMobileApps_theme';

    const SOURCE_THEME_LIGHT = 'https://raw.githubusercontent.com/eva-design/eva/master/packages/eva/themes/light.json';
    const SOURCE_THEME_DARK = 'https://raw.githubusercontent.com/eva-design/eva/master/packages/eva/themes/dark.json';

    /**
     * @return array
     */
    public static function getUsedProperties()
    {
        return [
            'background-basic-color-1',
            'background-basic-color-2',
            'background-basic-color-3',
            'background-basic-color-4',

            'border-basic-color-1',
            'border-basic-color-2',
            'border-basic-color-3',
            'border-basic-color-4',

            'text-basic-color',
            'text-hint-color',
            'text-primary-color',
            'text-primary-focus-color',
            'text-disabled-color',
            'text-control-color',
            'text-alternate-color',

            'color-basic-default',

            'color-primary-default',
            'color-primary-focus',
            'color-primary-hover',
            'color-primary-active',

            'color-success-default',
            'color-success-hover',
            'color-success-focus',
            'color-success-active',

            'color-info-default',
            'color-info-hover',
            'color-info-focus',
            'color-info-active',

            'color-warning-default',
            'color-warning-hover',
            'color-warning-focus',
            'color-warning-active',

            'color-danger-default',
            'color-danger-hover',
            'color-warning-focus',
            'color-warning-active',

            'color-control-default',
            'color-control-hover',
            'color-control-focus',
            'color-control-active',

            'color-basic-control-transparent-500',
            'color-basic-control-transparent-300',
            'color-control-transparent-focus-border',
            'color-basic-control-transparent-500',
            'color-control-transparent-hover-border',
            'color-basic-control-transparent-400',
            'color-control-transparent-disabled-border',
            'color-control-transparent-disabled',
        ];
    }

    /**
     * @param string $sourceUri
     * @return array
     */
    public static function getDataSource($sourceUri)
    {
        $path = 'internal-data://tmobile_apps_theme/' . \md5($sourceUri) . '.data';
        if (!File::abstractedPathExists($path)) {
            $tempFile = self::download($sourceUri);
            if ($tempFile === false) {
                throw new \InvalidArgumentException('Failed to download remote source. $url='
                    . $sourceUri);
            }

            File::copyFileToAbstractedPath($tempFile, $path);
        }

        $tempLight = File::copyAbstractedPathToTempFile($path);
        $contents = \file_get_contents($tempLight);
        $dataSource = \json_decode(\strval($contents), true);
        if (\json_last_error() > 0) {
            throw new \InvalidArgumentException('Bad json. $jsonError' . \json_last_error_msg());
        }

        $properties = [];
        foreach (self::getUsedProperties() as $propertyId) {
            if (isset($dataSource[$propertyId])) {
                self::attachPropertyValue($dataSource, $properties, $propertyId);
            }
        }

        \uasort($properties, function ($a, $b) {
            $aFirstChar = \substr($a, 0, 1);
            $bFirstChar = \substr($b, 0, 1);
            if ($aFirstChar === '$' && $bFirstChar === '$') {
                return \strlen($a) - \strlen($b);
            } elseif ($aFirstChar === '$' || $bFirstChar === '$') {
                return $aFirstChar === '$';
            }

            return \strlen($a) - \strlen($b);
        });

        return $properties;
    }

    /**
     * @param array $dataSource
     * @param array $properties
     * @param string $propertyId
     * @return void
     */
    protected static function attachPropertyValue(array $dataSource, array &$properties, $propertyId)
    {
        if (!isset($dataSource[$propertyId])) {
            return;
        }

        $value = $dataSource[$propertyId];
        if (\substr($value, 0, 1) === '$') {
            $depends = \substr($value, 1);
            self::attachPropertyValue($dataSource, $properties, $depends);
        }

        $properties[$propertyId] = $value;
    }

    /**
     * @param string $uri
     * @return false|string
     */
    public static function download($uri)
    {
        $client = \XF::app()->http()->client();

        $tempFile = File::getTempFile();

        try {
            $client->get($uri, [
                'sink' => $tempFile
            ]);
        } catch (\Exception $e) {
            \XF::logException($e, false, '[tl] Mobile Apps: Themes ');
        }

        $size = \filesize($tempFile);
        if ($size < 1) {
            return false;
        }

        return $tempFile;
    }
}
