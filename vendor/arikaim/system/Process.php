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

use Symfony\Component\Process\Process as SProcess;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * System Process
 */
class Process 
{
    /**
     * Create process
     *
     * @param array|string $command
     * @param array $env
     * @param string $input
     * @param integer $timeout
     * @param array $options
     * @return void
     */
    public static function create($command, array $env = null, $input = null, $timeout = 60, array $options = array())
    {
        $process = new SProcess($command,null,$env,$input,$timeout,$options);
        $process->enableOutput();

        return $process;
    }

    /**
     * Run console command
     *
     * @param string|array $command
     * @param array $env
     * @return mixed
     */
    public static function run($command, array $env = [])
    {
        $process = Self::create($command,$env);
        $process->run();

        return ($process->isSuccessful() == true) ? $process->getOutput() : $process->getErrorOutput();          
    }

    /**
     * Run console command
     *
     * @param array $command
     * @param callable|null $callback
     * @param array $env
     * @return mixed
     */
    public static function start($command, callable $callback = null, array $env = [])
    {
        $process = Self::create($command,$env);
        $process->start($callback);
     
        return $process->getOutput();
    }

    /**
     * Get current script user
     *
     * @return string
     */
    public static function getCurrentUser()
    {
        return posix_getpwuid(posix_geteuid());
    }

    /**
     * Get php executable
     *
     * @return string
     */
    public static function findPhp()
    {
        return (new PhpExecutableFinder)->find();
    }

    /**
     * Reurn true if process is running (Linux only)
     *
     * @param integer $pid
     * @return boolean
     */
    public static function isRunning($pid) 
    {
        return (file_exists("/proc/{$pid}") == true) ? true : false;         
    }

    /**
     * Get process command
     *
     * @param integer $pid
     * @return string
     */
    public static function getCommand($pid) 
    {
        $pid = (int)$pid;
        return trim(shell_exec("ps o comm= $pid"));
    }

    public static function stop(float $timeout = 10, int $signal)
    {
        
    }
}
