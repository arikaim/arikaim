<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\App;

use Closure;

/**
 * Post install actions
 */
class PostInstallActions 
{
    /**
     * Run post install actions
     *
     * @param Closure|null $onProgress
     * @param Closure|null $onProgressError
     * @return bool
     */
    public static function run(?Closure $onProgress = null, ?Closure $onProgressError = null): bool
    {
        global $arikaim;

        // Run post install actions on all extensions      
        $extensionManager = $arikaim->get('packages')->create('extension');
        $extensionManager->postInstallAllPackages();

        if (Self::hasActions() == false) {
            if (\is_callable($onProgress) == true) {
                $onProgress('Done.');
            }
            return true;
        }

        $actions = $arikaim->get('config')->loadJsonConfigFile('post-install.json');
        $errors = 0;
        foreach ($actions as $action) {           
            $result = Self::runAction($action);
            if ($result === false) {
                if (\is_callable($onProgressError) == true) {
                    $onProgressError(Self::getPackageName($action));
                }
                $errors++;
            } else {
                if (\is_callable($onProgress) == true) {
                    $onProgress(Self::getPackageName($action));
                }
            }          
        }

        return ($errors == 0);
    }

    /**
     * Check if pst actions fiel exist
     *
     * @return boolean
     */
    public static function hasActions(): bool
    {
        global $arikaim;

        return (bool)$arikaim->get('config')->hasConfigFile('post-install.json');
    } 

    /**
     * Get action package name
     *
     * @param array $action
     * @return string
     */
    protected static function getPackageName(array $action): string
    {
        $theme = $action['theme'] ?? null;
        $extension = $action['extension'] ?? null;

        return (empty($extension) == false) ? $extension : $theme;
    }

    /**
     * Run action
     *
     * @param array $action
     * @return boolean
     */
    public static function runAction(array $action)
    {
        $command = $action['command'] ?? null;
        if (empty($command) == true) {
            return false;
        }

        $extension = $action['extension'] ?? null;
        $packageName = Self::getPackageName($action);
        $packageType = (empty($extension) == false) ? 'extension' : 'template';

        switch($command) {
            case 'set-primary': {              
                return Self::setPrimaryPackage($packageName,$packageType);
            }
        }

        return false;
    }

    /**
     * Run set primary package action
     *
     * @param string $name
     * @param string $type
     * @return boolean
     */
    public static function setPrimaryPackage(string $name, string $type = 'extension'): bool
    {
        global $arikaim;

        $packageManager = $arikaim->get('packages')->create($type);
        if ($packageManager->hasPackage($name) == false) {           
            return false;
        }

        $package = $packageManager->createPackage($name);
        $package->unInstall();        
        $package->install(true);
        $result = $package->setPrimary();

        return $result;      
    }
}
