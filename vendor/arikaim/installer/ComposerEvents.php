<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Installer;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Closure;

/**
 * Composer events
 */
class ComposerEvents
{   
    /**
     * On package update callback
     *
     * @var Closure|null
     */
    protected static $onPackageUpdateCallback = null;

    /**
     * On package install callback
     *
     * @var Closure|null
     */
    protected static $onPackageInstallCallback = null;

    /**
     * On post update callback
     *
     * @var Closure|null
     */
    protected static $onPostUpdate = null;

    /**
     * On pre update callback
     *
     * @var Closure|null
     */
    protected static $onPreUpdate = null;

    /**
     * Set on pre update callback
     *
     * @param Closure $callback
     * @return void
     */
    public static function onPreUpdate($callback): void
    {
        Self::$onPreUpdate = $callback;
    }

    /**
     * Set on post update callback
     *
     * @param Closure $callback
     * @return void
     */
    public static function onPostUpdate($callback): void
    {
        Self::$onPostUpdate = $callback;
    }

    /**
     * Set on update callback
     *
     * @param Closure $callback
     * @return void
     */
    public static function onPackageUpdate($callback): void
    {
        Self::$onPackageUpdateCallback = $callback;
    }

    /**
     * Set on install callback
     *
     * @param Closure $callback
     * @return void
    */
    public static function onPackageInstall($callback): void
    {
        Self::$onPackageInstallCallback = $callback;
    }

    /**
     * Post post update event
     *
     * @param Event $event
     * @return void
     */
    public static function postUpdate(Event $event)
    {
        Self::callback(Self::$onPostUpdate,$event);     
    }

     /**
     * Post pre update event
     *
     * @param Event $event
     * @return void
     */
    public static function preUpdate(Event $event)
    {
        Self::callback(Self::$onPreUpdate,$event);     
    }

    /**
     * Composer post-package-install event
     *
     * @param PackageEvent $event
     * @return void
     */
    public static function postPackageInstall(PackageEvent $event)
    {      
        $package = Self::getPackage($event);
        Self::callback(Self::$onPackageInstallCallback,$package);
    }
    
    /**
     * Composer post-package-update event
     *
     * @param PackageEvent $event
     * @return void
    */
    public static function postPackageUpdate(PackageEvent $event)
    {       
        $package = Self::getPackage($event);
        Self::callback(Self::$onPackageUpdateCallback,$package);
    }

    /**
     * Callback helper
     *
     * @param Closure|null $callback
     * @param mixed $event
     * @return void
     */
    private static function callback($callback, $event): void
    {
        if (\is_callable($callback) == true) {
            $callback($event);
        }        
    }

    /**
     * Returns the package associated with $event
     *
     * @param PackageEvent $event Package event
     * @return object
     */
    public static function getPackage(PackageEvent $event)
    {       
        $operation = $event->getOperation();
        $package = \method_exists($operation, 'getPackage') ? $operation->getPackage() : $operation->getInitialPackage();

        return $package;
    }
}
