<?php

namespace App\Contracts\Abstraction\Composer;

use Composer\Installer\PackageEvent;
use Composer\Package\CompletePackage;
use Wordless\Helpers\Str;

trait ManagePlugin
{
    public static function activatePlugin(PackageEvent $composerEvent)
    {
        self::managePlugin($composerEvent, 'activate');
    }

    public static function deactivatePlugin(PackageEvent $composerEvent)
    {
        self::managePlugin($composerEvent, 'deactivate');
    }

    private static function isWpPluginPackage(CompletePackage $package): bool
    {
        return $package->getType() === 'wordpress-plugin';
    }

    private static function managePlugin(PackageEvent $composerEvent, string $plugin_command)
    {
        $package = self::extractPackageFromEvent($composerEvent);

        if ($package === null || !self::isWpPluginPackage($package)) {
            return;
        }

        $plugin_name = Str::after($package->getName(), '/');
        $vendor_path = $composerEvent->getComposer()->getConfig()->get('vendor-dir');

        if (is_file("$vendor_path/autoload.php")) {
            passthru("php console wp:run \"plugin $plugin_command $plugin_name\"");
        }
    }
}
