<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Packages\Traits;

use Arikaim\Core\Utils\Factory;

/**
 * Console commands trait
*/
trait ConsoleCommands 
{
    /**
     * Get package console commands
     *
     * @return array
     */
    public function getConsoleCommands(): array
    {
        $package = $this->packageRegistry->getPackage($this->getName()); 
        if ($package == false) {
            return [];
        }
        
        $result = [];
        foreach ($package['console_commands'] as $class) {
            $command = Factory::createInstance($class);
            if ($command != null) {
                $item['name'] = $command->getName();
                $item['title'] = $command->getDescription();      
                $item['help'] = 'php cli ' . $command->getName();         
                $result[] = $item;
            }          
        } 

        return $result;      
    }
}
