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

use Arikaim\Core\App\Install;

/**
 * Console app
*/
class Console 
{   
    /**
     * Get core commands
     *
     * @return array
     */
    public static function getCoreCommands(): array
    {
        global $arikaim;

        $commands = $arikaim->get('config')->load('console.php',false);

        return (\is_array($commands) == true) ? $commands : [];
    }

    /**
     * Get extensions commands.
     *
     * @return array
     */
    public static function getExtensionsCommands(): array
    {
        global $arikaim;

        if ($arikaim->get('db')->isValidPdoConnection() == false) {
            return [];
        }
        if (Install::isInstalled() == false) {
            return [];
        }

        $extensions = $arikaim->get('packages')->create('extension')->getPackgesRegistry()->getPackagesList([
            'status' => 1    
        ]); 
        
        $commands = [];
        foreach ($extensions as $extension) {
            $commands = \array_merge($commands,$extension['console_commands']);
        }

        return $commands;
    }

    /**
     * Get modules commands
     *
     * @return array
     */
    public static function getModulesCommands(): array
    {
        global $arikaim;

        if ($arikaim->get('db')->isValidPdoConnection() == false) {
            return [];
        }
        if (Install::isInstalled() == false) {
            return [];
        }

        $modules = $arikaim->get('packages')->create('module')->getPackgesRegistry()->getPackagesList([
            'status' => 1    
        ]);   

        $commands = [];
        foreach ($modules as $module) {
            $commands = \array_merge($commands,$module['console_commands']);          
        }

        return $commands;
    }
}
