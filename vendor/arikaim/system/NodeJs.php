<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
 */
namespace Arikaim\Core\System;

use Arikaim\Core\System\Process;
use Exception;

/**
 * NodeJs commands
 */
class NodeJs
{   
    /**
     * Get version
     *
     * @return string|null
     */
    public static function getVersion(): ?string
    {
        return Self::runCommand(' -v');
    } 

    /**
     * Return true if nodejs is installed
     *
     * @return boolean
     */
    public static function isInstalled(): bool
    {
        return !empty(Self::getVersion());
    }

    /**
     * Run nodejs command
     *
     * @param string $command
     * @param boolean $async
     * @param boolean $realTimeOutput
     * @return mixed
     */
    public static function runCommand(string $command, bool $async = false, bool $realTimeOutput = false)
    {     
        $process = Process::create('node ' . $command,[]);
        try {
            if ($async == true) {
                $process->start();
            } else {
                if ($realTimeOutput == true) {
                    $process->run(function ($type, $buffer) {                       
                        echo $buffer;                        
                    });
                }
                $process->run();
            }
            $output = $process->getOutput();
        } catch(Exception $e) {            
            return $e->getMessage();
        }

        return $output;
    }
}
